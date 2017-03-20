<?php

namespace App\Http\Exceptions;

use \Exception;

class ViewException extends Exception
{
    public function __construct($message, $code=0, Exception $previous=null)
    {

        parent::__construct("ViewException: ".$message, $code, $previous);
    }

}