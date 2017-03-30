# ElasticSessions
a package to provide a ElasticSearch Sessions Driver for Laravel PHP Framework

## ElasticSessions Requirements
ElasticSessions is born on top of Elastiquent, you must be running at least Elasticsearch 1.0. Elasticsearch 0.9 and below will not work and are not supported.

_**You need to set-up your own Index and Type for sessions and configure it in .env (ELS_INDEX_USER, ELS_TYPE_SESSION)**_

###Set up the use of this package

To begin configuration, first update the composer.json with the relative requirement and autoload sections: 

Require section for our custom package and third party libraries:

    ...
    "require": {
        ...
        "elasticquent/elasticquent": "dev-master",
        "Bebbolus/ElasticSessions": "master",
    },
    ...
      
Now add the following code on _App\Config\App.php_

add app providers:

    'providers' => [
        ...
        Elasticquent\ElasticquentServiceProvider::class,
        Bebbolus\ElasticSessions\ElasticSessionsServiceProvider::class,
        Bebbolus\ElasticSessions\Providers\SessionServiceProvider::class, #CUSTOM ELASTIC SESSION PROVIDER
    ],
    
add third party Package Facades:

    'aliases' => [
        ...
        'Es' => Elasticquent\ElasticquentElasticsearchFacade::class,
    ],
    
run commands:

    > composer dump-autoload -o
    > composer update
    > php artisan vendor:publish --force

    
The .env of DEFAULT DEVELOPMENT ENVIRONMENT:

    
    SESSION_DRIVER=elastic
   
    ELS_MAX_RESULT=20
    
    ELS_SERVER=10.1.3.7:9200
    ELS_INDEX_USER=edm-sofist-test-user
    ELS_TYPE_SESSION=session
    
**_NB_**

> edit the .env file with the right configuration parameters
> for the application you will develp 
> i.e. all the ELS_* parameters, etc...

_**You need to set-up your own Index and Type for sessions and configure it in .env (ELS_INDEX_USER, ELS_TYPE_SESSION)**_