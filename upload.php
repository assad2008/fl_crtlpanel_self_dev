<?php
/**
* @file upload.php
* @synopsis  上传异步脚本
* @author Yee, <rlk002@gmail.com>
* @version 1.0
* @date 2013-07-11 11:02:27
*/

/*
此脚本为UploadJS使用
*/
	require 'init.php';
	$type = $_GET['type'];
	if(!$type)
	{
		$up = initupload($path = PATH_ADMIN . '/data/upload');
		if($_FILES['apkfile']['name'])
		{
			$returns = $up->put($_FILES['apkfile']);
		}
		echo $returns['new_path'];
		exit();
	}
