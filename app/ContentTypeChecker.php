<?php
namespace App;
use Illuminate\Http\Request;

class ContentTypeChecker
{
public function isNotApplicationJsonContentType(Request &$request){
    $content_type = $request->header('Content-Type');
    if($content_type != "application/json"){
        return true;
    }else{
        return false;
    }

}
public function setWrongContentTypeResponse(){
    $response = response(json_encode(['message'=>' Unsupported Media Type - the api redeives only application/json Content-Type'],JSON_UNESCAPED_SLASHES),415);
    $response->header('access-control-allow-content-type','application/json');
    return $response;
}

}
