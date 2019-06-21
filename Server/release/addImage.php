<?php
    header('Content-Type:application/json; charset=utf-8');
    header('Access-Control-Allow-Origin:*');
    // include("../public/uploadImg.php");
    if(!isset($_FILES['file']['tmp_name'])){
        echo "未上传图片文件！";
        return;
    }
    // $path="../image";
    // $upload=new uploadImg($_FILES['file'],$path);
    // echo $upload->upload();
    $img=$_FILES['file']['tmp_name'];
    $imgname=$_FILES['file']['name'];
    $namepart=explode('.', $imgname);
    $namefoot=array_pop($namepart);
    $rand=time().mt_rand().'.'.$namefoot;
    $path="../image/{$rand}";
    if($_FILES['file']['error']===0)
    {
        if(move_uploaded_file($img, $path))
        {
            echo json_encode(array("success","code"=>0,"path"=>$path,"msg"=>"图片上传成功！"));
        }else{
            echo json_encode(array("fail","msg"=>"图片上传失败！","code"=>500));
        }
        
    }
?>