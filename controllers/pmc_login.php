<?php
/**
* @file pmc_login.php
* @synopsis  产品后台 登录以及首页框架处理
* @author Yee, <assad2008@sina.com>
* @version 1.0
* @date 2012-04-24 10:36:50
 */

!defined('PATH_ADMIN') && exit('Forbidden');

class pmc_login
{
		function __construct()
		{
			pm_tpl::assign('lang',$this->lang());
		}

    public function index()
    {
    	$this->ssologin();
    }

    public function login()
    {
    	$auth = mod_login::instance();
     	if($auth->is_login())//不自动跳转
     	{
     		$this->frame();
     	}
     	$post = $_POST;
     	if($post["submit"])
     	{
     		unset($post['submit']);
     		$login_data = $post;
     		try
     		{
     			if($auth->authenticate($login_data))
     			{
     				if(!$auth->is_login())//不自动跳转
     				{
     					throw new Exception("必须开启cookie.");
     				}
     				mod_member::loginhistory(USERNAME);
     				$this->frame();
            exit;
          }
         }
            catch (Exception $e)
            {
                mod_member::loginhistory($login_data['username'],$e->getMessage());
                pm_tpl::assign('error', $e->getMessage());
                            mod_login::message($e->getMessage(),null,1);
            }
          }
          pm_tpl::assign('timenow',time());
        	pm_tpl::display('login');
    		}

        public function ssologin()
        {
            $auth = mod_login::instance();
      			if($auth->is_login())//不自动跳转
            {
                $this->frame();
            }
            $sign = $_GET['sign'];
            if(!$sign)
            {
                header("Location: http://fladminsso.feiliu.com/?source=" . rawurlencode(BASE_URL . '?c=login&a=ssologin'));
            }
            $ret = ffile_get_contents("http://fladminsso.feiliu.com/client/check/{$sign}");
            $retinfo = json_decode($ret,1);
            if($retinfo['code'] == 0)
            {
                $retuinfo = $retinfo['userinfo'];
                $userdata['username'] = $retuinfo['login_name'];
                $userdata['password'] = $retuinfo['password'];
        				if($auth->authenticate($userdata,'sso'))
        				{
              			mod_member::loginhistory(USERNAME);
                    header("Location: " . BASE_URL);
                    exit();
        				}
            }else
            {
                header("Location: http://fladminsso.feiliu.com/?source=" . rawurlencode(BASE_URL . '?c=login&a=ssologin'));
                exit();
            }
        }

    public function logout()
    {
    	setcookie ( AUTH_KEY . '_admin_auth', '', time () - 35920000, PATH_COOKIE );
      setcookie ('user_company_channel', '', time () - 35920000, PATH_COOKIE );
			exit("<script>window.location.href='http://fladminsso.feiliu.com/logout?source=" . rawurlencode(BASE_URL . '?c=login&a=ssologin'). "';</script>");
    }

    public function frame()
    {
        $sum_user = 0;
        $sum_income = 0;
        $datas = array();
        pm_tpl::assign('plist',$datas);
        pm_tpl::assign('sum_user',$sum_user);
        pm_tpl::assign('sum_income',$sum_income);
        pm_tpl::display('login_frame');
        exit();
    }

    public function top(){
        $user_id = ADMINUSERID;
        $username = USERNAME;
        $msgstatus = mod_pmsys::get_user_msg_check($username);
        $lst = mod_menu::get_user_nav($user_id);
        pm_tpl::assign('nav_list', $lst);
        pm_tpl::assign('msgstatus',$msgstatus);
        $thisdayandtime = date('e Y-m-d l');
        pm_tpl::assign('user_id',$user_id);
        pm_tpl::assign('thisdayandtime', $thisdayandtime);
        pm_tpl::display('top');
    }

    public function drag()
    {
        pm_tpl::display('drag');
    }

    public function menu()
        {
            $menus = mod_menu::getmenulist();
        pm_tpl::assign('menus',$menus);
        pm_tpl::assign('no_help','暂时还没有该部分内容');
        pm_tpl::assign('help_lang', $_CFG['lang']);
        pm_tpl::assign('charset', 'utf-8');
        pm_tpl::assign('admin_id', ADMINUSERID);
        pm_tpl::display('menu');
    }

    public function welcome()
    {
        $data = array();
        $tmp = explode('/', dirname($_SERVER['PHP_SELF']));
        $data['safe_notice'] = (is_array($tmp) && !empty($tmp[count($tmp) - 1]) && $tmp[count($tmp) - 1] == 'admin') ? 1 : 0;
        $adminmsg =  mod_pmsys::get_adminmsg();
        $Threadsconnected = mod_misc::mysqlstatus(3);
        $viewer = array('wangjiang','songkun');
        if(in_array(USERNAME,$viewer))
        {
                    pm_tpl::assign('allowview',1);
                }
        pm_tpl::assign('curver', CUR_VERSION);
        pm_tpl::assign('adminmsg', $adminmsg);
        pm_tpl::assign('data', $data);
        pm_tpl::assign('wt',$w);
        pm_tpl::assign('ur_here', '欢迎页');
        pm_tpl::assign('Threads_connected',$Threadsconnected['Value']);
        pm_tpl::display('start');
        }

        public function calendar()
        {
            include PATH_LANG.'/zh_cn/calendar.php';
            header('Content-type: application/x-javascript; charset=utf-8');
            foreach ($_LANG['calendar_lang'] AS $cal_key => $cal_data)
            {
                echo 'var ' . $cal_key . " = \"" . $cal_data . "\";\r\n";
            }
            include_once(PATH_ADMIN.'/static/js/calendar.js');
        }

        public function lang()
        {
            include PATH_LANG.'/zh_cn/privilege.php';
            include PATH_LANG.'/zh_cn/common.php';
            include PATH_LANG.'/zh_cn/index.php';
            return $_LANG;
        }
}
