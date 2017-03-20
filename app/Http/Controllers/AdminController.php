<?php

namespace App\Http\Controllers;


use App\Http\Request;
use App\Http\Response;
use App\Http\View;
use App\Http\Auth;

class AdminController
{

    public function index(Request $request)
    {
        $auth=new Auth($request);
        if( $auth->isAuthorized() ){

            $response = new View('admin/ui',[]);
            $response->addAuthTokenCookie( $auth->calculateAuthTokenForUser( $auth->getAuthorizedUser() ) );

            return $response;

        }else{
            return $auth->makeRequestAuthorizationResponse();
        }

    }
//    public function index(Request $request, $page_name)
//    {
////        $args=func_get_args();
////        ob_start();
////        echo "ADMINController pagename !";
////        echo $page_name .'ROCKS!';
////        echo "request";
////        var_dump($request);
////        echo "ARGS!";
////        var_dump($args);
////
////        return new Response(ob_get_clean());
//    }


}