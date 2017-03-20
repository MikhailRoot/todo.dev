<?php

namespace App\Http;

use App\Http\Exceptions\RouteException;
use App\Http\Route;

class Router
{
    /** @var Route[] $_routes */
    private $_routes;

    private $_standardRestfullMethods=[
        'index'   => 'GET',
        'show'    => 'GET',
        'store'   => 'POST',
        'update'  => 'PUT',
        'destroy' => 'DELETE'
    ];

    public function __construct(array $routes)
    {
        $this->_routes=[];

        foreach($routes as $route)
        {
            $method  = $route[0] ?? null;
            $regex   = $route[1] ?? null;
            $handler = $route[2] ?? null;
            if( is_null($method) || is_null($regex) || is_null($handler)){
                throw new RouteException("Can not initialize route: ".json_encode($route) );
            }
            $this->{'add'.$method}($regex, $handler);
        }
    }

    public function addGET($regexp,$handler)
    {
        $this->_routes[] = new Route('GET',$regexp, $handler);
    }

    public function addPOST($regexp,$handler)
    {
        $this->_routes[] = new Route('POST',$regexp, $handler);
    }

    public function addPUT($regexp,$handler)
    {
        $this->_routes[] = new Route('PUT',$regexp, $handler);
    }

    public function addDELETE($regexp,$handler)
    {
        $this->_routes[] = new Route('DELETE',$regexp, $handler);
    }

    public function addREST($regexp, $handlerClassName)
    {
        if( class_exists($handlerClassName) )
        {
            $methods=get_class_methods( $handlerClassName );

            // add restfull methods to routes
            foreach($methods as $method){

                if( array_key_exists($method, $this->_standardRestfullMethods) )
                {
                    $currentMethodRegexp=$regexp;

                    if($method ==='show' || $method==='update' || $method==='destroy'){
                        $currentMethodRegexp.='/{id}';
                    }

                    $verb = (string)$this->_standardRestfullMethods[$method];

                     $this->_routes[] = new Route(
                         $verb,
                         $currentMethodRegexp,
                         "{$handlerClassName}@{$method}"
                     );
                }

            }

        }else{
            throw new RouteException("Class {$handlerClassName} not found to be used as Resource handler");
        }

    }

    public function dispatchRequest(Request $request):Response
    {
        foreach($this->_routes as $route)
        {
            if( $route->shouldDispatch($request) ){

                //return call_user_func( $route->getHandler(), $request, $route->getRouteParams($request) ); old way without parameters
                return call_user_func_array( $route->getHandler(),$route->getRequestWithRouteParams($request) );

            }
        }

        return new Response('<h1>404</h1><h2>Page not found</h2>',404);
        // fallback to 404
    }


}