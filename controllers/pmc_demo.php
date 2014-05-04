<?php
/**
* @file pmc_demo.php
* @synopsis  演示页面
* @author Yee, <rlk002@gmail.com>
* @version 1.0
* @date 2013-07-15 14:08:18
*/


class pmc_demo
{
	function __construct()
	{

	}

	public function form()
	{
		pm_tpl::assign('ur_here','表单演示');
		pm_tpl::display('demo_form');
	}

	public function formeditor()
	{
		pm_tpl::assign('ur_here','表单演示 + 富文本');
		pm_tpl::display('demo_formeditor');
	}

	public function formupload()
	{
		pm_tpl::assign('ur_here','表单上传');
		pm_tpl::display('demo_formupload');
	}

	public function charts()
	{
		pm_tpl::assign('ur_here','曲线图演示');
		pm_tpl::display('demo_charts');
	}

	public function getuserinfo()
	{
		$u = $_GET['user'];
		$u = covcharset($u,'utf-8');
		$u = 1099526042290;
		include(PATH_APPLICATION . '/pm_fsockopen_http.php');
            $http = new fsockopen_http;
            $http->setUrl('http://127.0.0.1:8888/getbaglist');
            $data = array('users' => $u, 'serverid' => 0, 'gmuserid' => ADMINUSERID, 'gmusername' => USERNAME);
            $http->setData($data);
            @$ret = $http->request('post');
		//echo $ret;
		//exit();
		debug(json_decode($ret,1));
	}

	public function md()
	{
		$u = $_GET['user'];
		$u = covcharset($u,'utf-8');
		$u = 1003;
		include(PATH_APPLICATION . '/pm_fsockopen_http.php');
            $http = new fsockopen_http;
            $http->setUrl('http://127.0.0.1:8889/modifyrank');
            $data = array('users' => $u, 'type' => '2', 'modifydata'=> '10,500,0|11,10000,0','serverid' => 100, 'gmuserid' => ADMINUSERID, 'gmusername' => USERNAME);
            $http->setData($data);
            @$ret = $http->request('post');
		//echo $ret;
		//exit();
		debug(json_decode($ret,1));
	}

public function delitem()
{
		$u = $_GET['user'];
		$u = covcharset($u,'utf-8');
		$u = 1099526042290;
		include(PATH_APPLICATION . '/pm_fsockopen_http.php');
            $http = new fsockopen_http;
            $http->setUrl('http://127.0.0.1:8899/deleteitem');
            $data = array('users' => $u, 'resid' => 13440006 ,'itemkey' => 293578402086042493,'itemcount' => 1,'serverid' => 0, 'gmuserid' => ADMINUSERID, 'gmusername' => USERNAME);
            $http->setData($data);
            @$ret = $http->request('post');
		echo $ret;
		exit();
		debug(json_decode($ret,1));
}

}
