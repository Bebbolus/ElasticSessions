<?php

namespace Bebbolus\ElasticSessions;

use Bebbolus\ElasticSessions\Exceptions\EntityNotFoundException;
use Bebbolus\ElasticSessions\Repositories\SessionRepository;
use SessionHandlerInterface;

class ElasticSearchSessionHandler implements SessionHandlerInterface
{
    public function open($savePath, $sessionName) {} //tobe void => used for file driver implementation
    public function close() {} //tobe void => used for file driver implementation
    public function read($sessionId)
    {
        $repo = new SessionRepository();
        try{
            $session = $repo->find($sessionId);
        }catch (EntityNotFoundException $e){
            return '';
        }

        return $session['_source']['data'];
    }
    public function write($sessionId, $data)
    {
        $repo = new SessionRepository();
        $arguments=[
            'data' => $data
        ];

        if(auth()->check()){
            $arguments['email'] = auth()->user()->getUserEmail();
        }

        return $repo->writeSession($sessionId, $arguments);
    }

    public function destroy($sessionId) {
        $repo = new SessionRepository();
        try{
            return $repo->delete($sessionId);
        }catch (EntityNotFoundException $e){
            return '';
        }
    }
    public function gc($lifetime) {}
}