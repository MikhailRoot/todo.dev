<?php

namespace App\Http;

class Request implements  \ArrayAccess
{
    public $method;

    public $path;

    private $_uri;

    private $_server;


    private $_request;

    private $_files;

    private $_cookies;


    private $_raw;

    private $_json;


    public function __construct()
    {
        $this->_server = $_SERVER;

        $this->_request=$_REQUEST;

        $this->_cookies=$_COOKIE;

        $this->_files=$_FILES;

        $this->_raw = file_get_contents('php://input');

        $this->_json =(array)( json_decode($this->_raw) ?? [] );

        $this->_uri = $this->_server['REQUEST_URI'] ?? '/';

        $this->path = parse_url($this->_uri, PHP_URL_PATH);

        if(false === $this->path){
            $this->path = $this->_uri;
        }

        $this->method = $this->_server['REQUEST_METHOD'] ?? 'GET';

    }

    /**
     * Get a data by key
     *
     * @param string $key the key data to retrieve
     * @access public
     */
    public function __get ($key) {

        if( isset($this->_json[$key]) ){

            return $this->_json[$key];

        }else{

            return $this->_request[$key] ?? null;

        }

    }


    public function getAuthToken()
    {
        return $this->_cookies['AuthToken']??null;
    }


    public function getCookie($name){

        return $this->_cookies[$name]?? null;

    }

    public function getFile($name){

        return $this->_files[$name]?? null;

    }

    public function getServerVar($name)
    {
        return $this->_server[$name]??null;
    }

    public function offsetExists($offset)
    {
        return isset($this->_json[$offset]) ||  isset($this->_request[$offset]);
    }

    public function offsetGet($offset)
    {
        if( isset($this->_json[$offset]) ){

            return $this->_json[$offset];

        }else{

            return $this->_request[$offset] ?? null;
        }

    }

    public function offsetSet($offset, $value)
    {
        // does nothing as request is readonly.
    }

    public function offsetUnset($offset)
    {
        // does nothing as request is readonly.
    }


}