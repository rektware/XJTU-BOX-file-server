<?php
error_reporting(E_ALL^E_NOTICE^E_WARNING);
ini_set('upload_max_filesize','40M');
ini_set('post_max_size','40M');

$BASE_URL = "this is the base url";

header('content-type:text/html;charset=utf-8');

if (!is_dir("upload")){
    mkdir("upload");
}

if ($_FILES["file"]["error"]>0){
    echo json_encode(array("result"=>"failed"));
}else{
    $date = new DateTime(null, new DateTimeZone('Asia/Shanghai'));
    $time = date_format($date,"Y-m-d-H-i-s-u");
    $name="upload/".$time.$_FILES["file"]["name"];
    if (file_exists($name))
    {
        echo json_encode(array("result"=>"failed"));
    }
    else
    {
        move_uploaded_file($_FILES["file"]["tmp_name"], $name);
        $res=array("result"=>"succeed","url"=>$BASE_URL.$name);
        echo json_encode($res,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
}


