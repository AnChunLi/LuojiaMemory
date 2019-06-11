<?php
    $db=new mysqli("47.99.53.233","LuojiaMemory","4p8SYA7DKRR8mpwH","LuojiaMemory");
    if($db->connect_errno<>0)
	{
		echo "数据库连接失败";
		return;
	}
	$db->query("set names utf8mb4");
?>