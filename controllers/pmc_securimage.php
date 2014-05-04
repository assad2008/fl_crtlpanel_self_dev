<?php
/**
* @file ctl_securimage.php
* @synopsis  验证码生产
* @author Yee, <assad2008@sina.com>
* @version 1.0
* @date 2012-04-19 10:38:23
 */

!defined('PATH_ADMIN') && exit('Forbidden');
class pmc_securimage
{
    public function index()
    {
    		$rand = $_GET['rand'];
        $this->showcp($rand);
		}

		public function showcp($rand = '')
		{
			$secode = new mod_code();
			$secode->doimage();
		}
}
?>
