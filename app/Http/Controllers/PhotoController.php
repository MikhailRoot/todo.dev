<?php


namespace App\Http\Controllers;


use App\Http\Request;
use App\Http\Response;
use Intervention\Image\ImageManagerStatic as Image;

class PhotoController
{

    public function store(Request $request){

        Image::configure(array('driver' => 'imagick'));
        // handle here photo file upload
        $file=$request->getFile('file');

        // lets generate newfilename
        $imageFileName = sha1(date('c')).'.jpg';

        $filePath2Save = PHOTOS_UPLOAD_DIR . DIRECTORY_SEPARATOR . $imageFileName;

        try{
            $img=Image::make($file['tmp_name']);

            $img->resize(320, 240, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $img->interlace();
            $img->save($filePath2Save);
            $img->destroy();

        }catch (\Exception $e){
            return new Response(['error'=>'wrongFile'],400);
        }

        return new Response(['filename'=>$imageFileName],201);

    }
}