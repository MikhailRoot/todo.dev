<?php

namespace App\Http;

use App\Http\Request;
use App\Http\Exceptions\RouteException;

class Route
{

    /**
     * @var string
     */
    private $_method;

    /**
     * @var string
     */
    private $_routeRegExp;

    /**
     * @var callable
     */
    private $_handler;

    /**
     * @var array
     */
    private $_params;

    /**
     * Route constructor.
     * @param string $method
     * @param string $routeRegularExpression
     * @param  Callable|string $handler
     * @throws \Exception
     */
    public function __construct($method='GET', $routeRegularExpression, $handler)
    {
        $this->_method=$method;

        $this->_params=[];


        if( !is_string($routeRegularExpression)  ){
            throw new RouteException("Error creating Route::{$method} Wrong or empty regexp => '{$routeRegularExpression}'");
        }

        //$this->_routeRegExp='!^'.$routeRegularExpression.'/?$!';
        $this->_routeRegExp = $routeRegularExpression;

        $this->setHandler($handler);
    }

    /**
     * Detects if this route should dispatch request
     * @param \App\Http\Request $request
     * @return bool
     */
    public function shouldDispatch(Request $request)
    {
        if($this->getMethod() === $request->method){

            if( preg_match_all( $this->getRouteRegExp(), $request->path ) )
            {
                return true;
            }
        }
        return false;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * @return string
     */
    public function getRouteRegExp()
    {
        $pathRegex=$this->_routeRegExp;

        $pathRegex=preg_replace('!{\w+?}!','\w+',$pathRegex);

        return '!^'.$pathRegex.'/?$!';
    }

    /**
     * @return array
     */
    public function getRouteParams(Request $request)
    {
        // extract route's params

        $paramValuesRegex = str_replace( '\w+', '(\w+)', $this->getRouteRegExp());
        $paramNamesRegex =  str_replace( '\w+', '{(\w+)}', $this->getRouteRegExp());

        $valuesMatches=[];
        $paramNamesMatches=[];

        if(
            preg_match_all('!{\w+}!',$this->_routeRegExp) < 1 ||
            preg_match_all($paramValuesRegex, $request->path, $valuesMatches) !== preg_match_all($paramNamesRegex, $this->_routeRegExp, $paramNamesMatches)
        ){
            // no params exists
            $this->_params=[];

        }else{ // let's get them

            for($m=1; $m < count( $valuesMatches ?? null ); $m++)
            {
                for($c=0; $c < count($paramNamesMatches[$m]);$c++ ){

                    $paramName  = $paramNamesMatches[$m][$c]?? null;
                    $paramValue = $valuesMatches[$m][$c]?? null;

                    if( null!==$paramName && null!==$paramValue)
                    {
                        $this->_params[$paramName]=$paramValue;
                    }

                }

            }

        }

       return $this->_params;
    }

    public function getRequestWithRouteParams(Request $request)
    {
        return  array_merge( [$request], $this->getRouteParams($request) );
    }

    public function getHandler(){

        if( is_callable($this->_handler) ){
            return $this->_handler;
        }
        elseif( is_string($this->_handler) ){

            $parts=explode('@',$this->_handler);

            $controller = new $parts[0];

            return [$controller, $parts[1]];

        }else{
            throw new RouteException("Error instantiating Handler for {$this->_method} handler {$this->_handler} , for regexp => '{$this->_routeRegExp}'");
        }
    }


    public function setHandler($handler)
    {
        if( is_callable($handler)){

            $this->_handler=$handler;

        }elseif( is_string($handler) ){

            // check syntax of route className@method
            $parts=explode('@',$handler);

            if( isset($parts[0]) && class_exists($parts[0]) ){

                $methods = get_class_methods( $parts[0] );

                if( isset($parts[1]) && false !== array_search($parts[1], $methods) )
                {
                    $this->_handler=$handler;

                }else{
                    throw new RouteException("Error creating Route::{$this->_method} Wrong Handler passed as class: {$handler} ,for regexp => '{$this->_routeRegExp}'");
                }

            }else{
                throw new RouteException("Error creating Route::{$this->_method} Wrong Handler passed as class: {$handler} ,for regexp => '{$this->_routeRegExp}'");
            }


        }else{
            throw new RouteException("Error creating Route::{$this->_method} Wrong Handler for regexp => '{$this->_routeRegExp}'");
        }
    }


}