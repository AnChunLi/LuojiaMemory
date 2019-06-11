<?php
    header('Content-Type:application/json; charset=utf-8');
    header('Access-Control-Request-Method: GET');
    header('Access-Control-Allow-Origin:*');
    include("../public/config.php");
    $sql="SELECT * FROM releases ORDER BY hot DESC";
    $result=$db->query($sql);
    $releaseList=[];
    while($row=$result->fetch_assoc()){
        $releaseList[]=$row;
    }
    echo json_encode(array('code'=>0,'msg'=>'动态列表查询成功!','data'=>$releaseList));
?>