<?php

use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Di\FactoryDefault;
use Phalcon\Http\Response;


// Use Loader() to autoload our model
$loader = new Loader();

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/html/');

require_once APP_PATH . '/vendor/autoload.php';

$loader->registerDirs(
    [
        APP_PATH . "/models/",
    ]
);

$loader->registerNamespaces(
    [
        'Store\Toys' => APP_PATH . '/models/',
    ]
);

$loader->register();

$container = new FactoryDefault();

// Set up the database service


$container->set(
    'mongo',
    function () {
        $mongo = new MongoDB\Client(
            'mongodb+srv://deekshapandey:Deeksha123@cluster0.whrrrpj.mongodb.net/?retryWrites=true&w=majority'
        );

        return $mongo->api;
    },
    true
);

$app = new Micro($container);



// Retrieves all robots
$app->get(
    '/api/movies',
    function () use ($app) {

        $collection = $this->mongo->movies->find();

        $data = [];

        foreach ($collection as $robot) {
            $data[] = [
                'id'   => $robot->id,
                'name' => $robot->name,
                'type' => $robot->type,
                'year' => $robot->year,
            ];
        }

        echo json_encode($data);
    }
);

// Searches for robots with $name in their name
$app->get(
    '/api/movies/search/{name}',
    function ($name) use ($app) {
        
        $collection = $this->mongo->movies->findOne(['name'=>$name]);
        $data = [];
            $data[] = [
                'id'   => $collection->id,
                'name' => $collection->name,
                'type' => $collection->type,
                'year' => $collection->year
            ];
        echo json_encode($data);
    }
);


// Retrieves robots based on primary key
$app->get(
    '/api/movies/{id:[0-9]+}',
    function ($id) use ($app) {
        $collection = $this->mongo->movies->findOne(['id'=>$id]);
        $data = [];
            $data[] = [
                'id'   => $collection->id,
                'name' => $collection->name,
                'type' => $collection->type,
                'year' => $collection->year
            ];
        echo json_encode($data);
    }
);

// Adds a new robot
$app->post(
    '/api/movies',
    function () use ($app) {

        $robot = $app->request->getJsonRawBody();

        $collection = $this->mongo->movies;

        $value = $collection->insertOne
        (['id'=>$robot[0]->id,'name'=>$robot[0]->name,'type'=>$robot[0]->type,'year'=>$robot[0]->year]);

        print_r($value);die;
        
       
    }
);

// Updates robots based on primary key
$app->put(
    '/api/movies/{id:[0-9]+}',
    function ($id) use ($app) {
        $robot = $app->request->getJsonRawBody();
        $collection = $this->mongo->movies;
        $updateResult = $collection->updateOne(
            ['id'  =>  $id],
            ['$set' => [
                "name" =>$robot[0]->name,
                "type" => $robot[0]->type,
                "year" =>  $robot[0]->year,
    
            ]]
        );
print_r($updateResult);die;
    }
);

// Deletes robots based on primary key
$app->delete(
    '/api/movies/{id:[0-9]+}',
    function ($id) use ($app) {
        $collection = $this->mongo->movies;
        $deleted=$collection->deleteOne(['id' => $id]);
        print_r($deleted);die;

    }
);
$app->handle(
    $_SERVER["REQUEST_URI"]
);
