<?php

namespace App\Http;


class Response
{
    protected $_version = '1.1';

    protected $_statusCode;

    protected $_statusText;

    protected $_headers;

    protected $_cookies2Send;

    protected $_content;

    protected $_statusTexts = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Reserved',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Reserved for WebDAV advanced collections expired proposal',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];


    public function __construct($content, $statusCode=200 , array $headers=[])
    {

        $this->setStatusCode($statusCode);

        $this->_headers = $headers;

        $this->_cookies2Send=[];

        $this->setContent($content);

    }



    public function render()
    {
        // send status header first then rest of them
        header($this->getStandardStatusHeader());

        foreach($this->getHeaders() as $header => $value){
            header("$header: $value");
        }

        foreach($this->_cookies2Send as $cookie_name => $cookie){
            setcookie($cookie['name'], $cookie['value'], $cookie['time'], $cookie['path'], $cookie['domain'], $cookie['secure'], $cookie['httpOnly'] );
        }

        echo $this->getContent();

    }


    private function getStandardStatusHeader():string
    {
        return trim(sprintf(
            'HTTP/%s %s %s',
            $this->_version,
            $this->_statusCode,
            $this->_statusText
        ));
    }
    /**
     * @return array
     */
    public function getHeaders():array
    {
        return $this->_headers;
    }

    /**
     * @param array $headers
     */
    public function setHeaders($headers)
    {
        $this->_headers = $headers;
    }

    public function addHeader($name, $value)
    {
        $this->_headers[$name]=(string)$value;

    }


    public function setHeader($name , $value)
    {
        $this->_headers[$name]=(string)$value;
    }

    public function addCookie($name, $value, $time=null, $path=null, $domain=null, $secure=null, $httpOnly=null)
    {
        if( is_null($time) ){
            $time=time()+3600; // hour by default
        }

        $this->_cookies2Send[$name]=[
            'name'     => $name,
            'value'    => $value,
            'time'     => $time,
            'path'     => $path,
            'domain'   => $domain,
            'secure'   => $secure,
            'httpOnly' => $httpOnly
        ];

    }

    public function removeCookie($name){

        $this->addCookie($name,'',time()-3600);
    }

    public function addAuthTokenCookie($token)
    {
        if($token)
        {
            $this->addCookie('AuthToken',$token);
        }

    }
    public function removeAuthTokenCookie()
    {
        $this->removeCookie('AuthToken');
    }


    public function redirect($url)
    {
        $this->setHeader('Location', $url);
        $this->setStatusCode(301);
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->_statusCode;
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->_statusCode = (int)$statusCode;

        $this->_statusText = $this->_statusTexts[ (int)($this->_statusCode) ] ?? null;

    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        if(is_string($content)){

            $this->_content = $content;

            $this->setHeader('content-type','text/html; charset=utf-8');

        }elseif( is_object($content) || is_array($content) ){

            $this->_content=json_encode($content,JSON_UNESCAPED_UNICODE);

            $this->setHeader('content-type','application/json; charset=utf-8');

        }else{
            $this->_content='';
        }

    }


}