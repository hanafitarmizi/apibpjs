<?php
function dd($obj,$exit=true)
{
    echo "<pre>";
    print_r($obj);
    echo "</pre>";
    if($exit){
        exit;
    }
}
function jsonResponse($status=false,$message='',$data=NULL)
{
    $result=[
        'status'=>$status,
        'msg'=>$message,
    ];
    if($data!=NULL){
        $result['data']=$data;
    }
    return response()->json($result);
}
function stringToInt($str)
{
    return preg_replace("/[^0-9]/", "",$str);
}
function strRandom($length=5)
{
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}
function setUser($user)
{
    return strlen($user)<3 ? $user.'#'.strRandom(5) : $user;
}