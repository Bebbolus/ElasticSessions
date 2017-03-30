<?php

namespace Bebbolus\ElasticSessions\Repositories;


use Bebbolus\ElasticSessions\Exceptions\EntityNotCreatedException;
use Bebbolus\ElasticSessions\Exceptions\EntityNotUpdatedException;
use Bebbolus\ElasticSessions\Exceptions\EntityNotFoundException;
use Bebbolus\ElasticSessions\Exceptions\MoreEntityWithSameAttributeException;
use Bebbolus\ElasticSessions\Repositories\Support\ELSQueryBuilderTrait;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Common\Exceptions\Missing404Exception;

class SessionRepository {

    use ELSQueryBuilderTrait;

    private $index;
    private $type;
    private $client;
    private $maxResultsSize;


    public function __construct()
    {
        $this->index = config('elasticquent.user_index','edm-user');
        $this->type = config('elasticquent.session_type','session');
        $this->maxResultsSize = config('elasticquent.max_results',20);
        $this->client = $client = ClientBuilder::create()->setHosts(config('elasticquent.config.hosts','10.1.3.7:9200'))->build();
    }


    public function destroyUserSession($email = null)
    {
        if(is_null($email))$arguments['email'] =  \Auth::user()->email;
        else $arguments['email'] = $email;

        try{
            $session = $this->get($arguments);
        }catch(MoreEntityWithSameAttributeException $e){
            foreach ($session = $this->get($arguments) as $element ) {
                $this->forceDestroy($element['_id']);
            }
            return '';
        }
        $this->forceDestroy($session['_id']);
        return '';
    }

    public function destroyBySessionID($sessionId)
    {
        $this->forceDestroy($sessionId);
    }

    public function writeSession($id, $arguments)
    {
        try{
            $this->find($id);
        }catch (EntityNotFoundException $e){
            return $this->indexWithId($id, $arguments);
        }
        return $this->update($id, $arguments);
    }

    public function find($id)
    {
        $params = [
            'index' => $this->index,
            'type' => $this->type,
            'id' => $id
        ];
        try{
            return $this->client->get($params);
        }catch (Missing404Exception $e){
            throw new EntityNotFoundException();
        }

    }


    public function search($term, $page = 0)
    {
        $params = [
            'index' => $this->index,
            'type'  => $this->type,
            'size'  => $this->maxResultsSize,
            'from'  => $page * $this->maxResultsSize,
            '_source_exclude' => 'PAG*'
        ];
        $params['body']['query']['match']['_all'] = $term;
        return $this->client->search($params);
    }



    /**
     * Search from condition function
     *
     * @param array $conditions
     * @param array $requiredField Es. ['DOCUMENT_SIZE', 'DOCUMENT_CODE', 'SITE_CODE', 'SITE_NAME', 'TOWN_CITY_NAME'];
     * @param int $page
     * @return mixed
     * @throws \Exception
     */
    public function get($conditions = [], $requiredField = [], $page = 0)
    {

        $searchParams['index'] = $this->index;
        $searchParams['type'] = $this->type;
        $searchParams['size'] = $this->maxResultsSize;
        $searchParams['from'] = $page * $this->maxResultsSize;
        $searchParams['body']['query']['bool']['must'] = [];
        $searchParams['_source_exclude']= ['PAG*'];

        $searchParams['body']['highlight']['order'] = 'score';
        $searchParams['body']['highlight']['fields'] = ['PAGE_*' => ['number_of_fragments' => 3], 'PAG_*' => ['number_of_fragments' => 3]];
        $searchParams['body']['highlight']['pre_tags'] = ['<strong>'];
        $searchParams['body']['highlight']['post_tags'] = ['</strong>'];

        foreach ($requiredField as $field) {
            $searchParams['body']['query']['bool']['must'][]['exists'] = ['field' => $field];
        }

        $filter = $this->makeFilterCondition($conditions);

        if(!empty($filter))$searchParams['body']['query']['bool']['must'][] = $filter;

        return $this->client->search($searchParams);
    }

    public function indexWithId($id, $content)
    {
        $params['index']    = $this->index;
        $params['type']     = $this->type;
        $params['id']       = $id;
        $params['body']     = $content;

        $response = ($this->client->index($params));

        if($response ['result'] == 'created'){
            return $this->find($response['_id']);
        }
        else {
            throw new EntityNotCreatedException();
        }

    }

    public function index($content)
    {
        $params['index'] = $this->index;
        $params['type'] = $this->type;
        $params['body'] = $content;

        $response = ($this->client->index($params));

        if($response ['result'] == 'created'){
            return $this->find($response['_id']);
        }
        else {
            throw new EntityNotCreatedException();
        }

    }

    public function update($id, $content)
    {

        $this->find($id);


        $params['index'] = $this->index;
        $params['type'] = $this->type;
        $params['id'] = $id;
        $params['body'] = ['doc'=>$content];

        $response = ($this->client->update($params));

        if($response ['result'] == 'updated'){
            return $this->find($response['_id']);
        }
        elseif ($response ['result'] == 'noop'){
            return $this->find($response['_id']);
        }
        else {
            throw new EntityNotUpdatedException();
        }

    }

    public function delete($id)
    {
        $params['index'] = $this->index;
        $params['type'] = $this->type;
        $params['id'] = $id;

        return $this->client->delete($params); //risponde true o false
    }

    public function indexExist()
    {
        return $this->client->indices()->exists(['index'=>$this->index]);
    }



}