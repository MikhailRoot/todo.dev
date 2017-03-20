<?php

namespace App\Http;

use App\Http\Response;
use App\Http\Exceptions\ViewException;

class View extends Response
{

    private $_viewName;

    private $_templateFileName;

    private $_data;


    public function __construct($viewName, $data=[])
    {

        $this->_viewName = $viewName;

        $this->_data=$data;

        if (! defined('TEMPLATES_BASE_PATH') ){
            throw new ViewException("TEMPLATES_BASE_PATH must be defined");
        }

        $this->_templateFileName = TEMPLATES_BASE_PATH . DIRECTORY_SEPARATOR . $this->_viewName . '.php';

        if( !file_exists($this->_templateFileName) ){
            throw new ViewException("{$viewName} can't be located");
        }

        parent::__construct( $this->_renderer() );
    }

    private function _renderer():string
    {
        $rendererContext = function($viewFileName,$data){

            ob_start();
            extract($data);

            require $viewFileName;

            return ob_get_clean();
        };

        return $rendererContext($this->_templateFileName, $this->_data);

    }

}