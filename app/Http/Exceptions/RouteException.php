<?php

namespace App\Http\Exceptions;

use \Exception;

class RouteException extends  Exception
{
    public function __construct($message, $code=0, Exception $previous=null)
    {
        parent::__construct("RouteException: ".$message, $code, $previous);
    }
}