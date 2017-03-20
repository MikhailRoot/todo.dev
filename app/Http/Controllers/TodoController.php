<?php

namespace App\Http\Controllers;


use App\Http\Auth;
use App\Http\Request;
use App\Http\Response;
use App\Todo;

class TodoController extends Controller
{

    public function index(Request $request)
    {
        $limit  = $request['limit']??3;
        $offset = $request['offset']??0;
        $orderBy = json_decode( ($request['orderBy'] ?? 'false'), true);
        // lets validate orderby
        $orderByRules=[];

        if(is_array($orderBy)){
            foreach($orderBy as $field=>$order)
            {
                if($order==='ASC' || $order==='DESC'){
                    $orderByRules[$field]=$order;
                }
            }
        }
        if(count($orderByRules)<1){
            $orderByRules=[
              'id'=>'DESC'
            ];
        }


        // TODO add smart order by params passed in
        // state username email
        $todo= new Todo();

        $result = $todo->selectTodos([],$orderByRules, $limit, $offset);

        return new Response($result);
    }


    public function show(Request $request, $id)
    {
        $todo = new Todo(intval($id));
        if( is_null($todo->id?? null) )
        {
            return new Response(['error'=>'NotFound'],404);

        }else{

            return new Response($todo);
        }

    }


    public function store(Request $request)
    {
        $email    = substr( filter_var( $request['email'] ?? '',  FILTER_VALIDATE_EMAIL ),0 ,254 );

        $username = substr(  filter_var($request['username'] ?? '',FILTER_SANITIZE_STRING) ,0, 254 );

        $state   = 0; //by default they are not completed

        $description =  substr( filter_var( $request['description'] ?? '',FILTER_SANITIZE_STRING ),0, 3000 );

        $photo = substr( filter_var( $request['photo']?? ' ', FILTER_VALIDATE_REGEXP,['options'=>['regexp'=>'/^[a-z,A-Z,0-9,\-,_]+(\.(?i)(jpg|png|gif|bmp))$/']]), 0 , 254 );

        if(false === $email || strlen($email)<3 || false === $username || strlen($username) <1 || strlen($description)<1 || false === $photo)
        {
            return new Response(['error'=>'WrongRequest'],400);
        }

        $todo = new Todo(null,$username,$email,$description,$state,$photo);

        $todo->save();

        return new Response($todo,201);
    }

    public function update(Request $request, $id)
    {
        $auth=new Auth($request);
        if( $auth->isAuthorized() ){

            $id=intval($id);

            $state   = filter_var($request['state']??0, FILTER_VALIDATE_INT);

            $description =  substr( filter_var( $request['description'] ?? '',FILTER_SANITIZE_STRING ),0, 3000 );

            $photo = substr( filter_var( $request['photo']?? 'nophoto.png', FILTER_VALIDATE_REGEXP,['options'=>['regexp'=>'/^[a-z,A-Z,0-9,\-,_]+(\.(?i)(jpg|png|gif|bmp))$/']]), 0 , 254 );

            if( $id<0 || $state<0 || strlen($description)<1 )
            {
                return new Response(['error'=>'WrongRequest',400]);
            }

            $todo=new Todo($id);

            if( is_null($todo->id?? null) )
            {
                return new Response(['error'=>'NotFound',404]);
            }
            $todo->description=$description;
            $todo->state=$state;
            $todo->photo=$photo;

            $todo->save();

            return new Response($todo,202);

        }else{
            return $auth->makeRequestAuthorizationResponse();
        }

    }

    public function destroy(Request $request, $id)
    {
        $auth=new Auth($request);
        if( $auth->isAuthorized() ){

            $id=intval($id);
            if($id>0){

                $todo=new Todo($id);
                if( !is_null($todo->id ?? null) )
                {
                    $todo->destroy();
                    return new Response(['destroyed'=>$id],202);
                }

            }
            // otherwise 404
            return new Response(['error'=>'NotFound'],404);

        }else{
            return $auth->makeRequestAuthorizationResponse();
        }

    }

}