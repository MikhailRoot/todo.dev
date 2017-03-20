<?php
namespace App;

use App\Http\Request;
use App\Http\Response;
use App\Http\Router;
use App\Http\Controllers;

require_once __DIR__.'/../config/config.php';

$request=new Request();


$routes=include_once(__DIR__.'/../routes/routes.php');
$router=new Router($routes);


$response = $router->dispatchRequest($request);

$response->render();
exit();