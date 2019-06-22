<?php
    header('Content-Type:application/json; charset=utf-8');
    header('Access-Control-Request-Method: GET');
    header('Access-Control-Allow-Origin:*');
    include("../public/config.php");
    $id=$_GET['id'];
    if(isset($_GET['cancellike'])){
        $sql="UPDATE releases SET hot=hot-1 WHERE id = '$id'";
    }else{
        $sql="UPDATE releases SET hot=hot+1 WHERE id = '$id'";
    }
    $result=$db->query($sql);
    if($result){
        echo json_encode(array('code'=>0,'msg'=>'success!'));
    }else{
        echo json_encode(array('code'=>500,'msg'=>'fail!'));
    }
?>