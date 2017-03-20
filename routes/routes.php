<?php

return [

    ['GET', '/' , 'App\Http\Controllers\PagesController@index'],
    ['GET', '/admin','App\Http\Controllers\AdminController@index'],

    ['REST', '/todo','App\Http\Controllers\TodoController'],
    ['POST', '/todo/photo','App\Http\Controllers\PhotoController@store'],

    ['GET', '/install' , function($request){

        ob_start();

        $Todo=new App\Todo();
        $Todo->CreateTable();

        echo "installation completed";

        return new App\Http\Response(ob_get_clean());
    }],

//    ['GET', '/closure' , function($request){
//
//        ob_start();
//        echo 'CLosure';
//        var_dump($request);
//
//        return new App\Http\Response(ob_get_clean());
//    }],

//    ['GET', '/testcrud' , function($request){
//
//        ob_start();
//
////        $todo=new App\Todo(null,'testname','test@mail.com', 'test description ',0,'');
////        $todo->save();
////        echo "after saving";
////        var_dump($todo);
////        $todo->username='updatedtestname';
////        $todo->save();
////        echo "after updating username";
////        var_dump($todo);
////        echo "destroying";
////        $todo->destroy();
////        var_dump($todo);
////        echo "getting new item";
////        $todo=new \App\Todo(3);
////        $todo->description='super new description';
////        $todo->save();
////        var_dump($todo);
////        echo "test completed";
//
//        return new App\Http\Response(ob_get_clean());
//    }],
//
//    ['GET', '/testselect' , function($request){
//
//        ob_start();
//
//        $todo=new App\Todo();
//        $selectedTodos= $todo->selectTodos(
//            [
//                [
//                    'field'=>'id',
//                    'value'=>'2',
//                    'compare'  =>'>'
//                ]
//            ],
//            ['id'=>'DESC'],
//            2,
//            3
//        );
//        var_dump($selectedTodos);
//        echo "test completed";
//
//        return new App\Http\Response(ob_get_clean());
//    }],

//    ['GET','.*',function($request){
//
//        ob_start();
//        echo 'FallbackClosure';
//        var_dump($request);
//
//        return new App\Http\Response(ob_get_clean());
//    }]

];