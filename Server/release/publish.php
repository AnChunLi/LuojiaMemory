<?php
    header('Content-Type:application/json; charset=utf-8');
    header('Access-Control-Request-Method: POST');
    header('Access-Control-Allow-Origin:*');
    include("../public/config.php");
    if(!isset($_POST['content'])||!isset($_POST['longitude'])||!isset($_POST['latitude'])){
        echo "请传入相关数据！";
        return;
    }
    $content=$_POST['content'];
    $longitude=$_POST['longitude'];
    $latitude=$_POST['latitude'];
    $time=date("Y/m/d H:i:s",time()+8*3600);
    $sql="INSERT INTO releases (content,time,longitude,latitude) VALUES ('$content','$time','$longitude','$latitude')";
    $is=$db->query($sql);
    if($is){
        echo json_encode(array("code"=>0,"msg"=>"insert success"));
    }else{
        echo json_encode(array("code"=>500,"msg"=>"insert fail"));
    }
?>