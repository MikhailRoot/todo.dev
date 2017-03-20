<?php


namespace App\Http\Exceptions;


class DbException extends  \Exception
{
    public function __construct($message, $code=0, \Exception $previous=null)
    {
        parent::__construct("DbException: ". $message, $code, $previous);
    }
}