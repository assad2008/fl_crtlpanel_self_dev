<?php
/*=============================================================================
FileName: pmc_crmmanage.php
Description:
Author: Tian - tianpengjun@feiliu.com
Created Time: 2014/2/25 18:41:30
Last modified: 2014-04-01 18:05:19
Version:  1.0
=============================================================================*/
    class pmc_crmmanage{

        public $adminlist;

        public function __construct()
        {
        $this->adminlist = array(11,249,237);
        }

        public function index()
            {
            $first = (empty($_GET['start'])) ? 0 : (int)$_GET['start'];
             $gid = $_GET['game_id'];
            $qtype = $_GET['qtype_id'];
            $qid = trim($_GET['question_id']);
            $account =trim($_GET['account']);
            $nick = trim($_GET['nick']);
            $content = trim($_GET['content']);
            $status = trim($_GET['status']);
            $operation = trim($_GET['operation']);
            $is_tel = $_GET['is_tel'];

            $gid && $cont['game_id'] = $gid;
            $qtype &&  $cont['qtype_id'] =$qtype;
            $qid && $cont['question_id'] =$qid;
            $account && $cont['account'] = $account;
            $nick && $cont['nick'] = $nick;
            $content && $cont['content'] = $content;
            $is_tel && $cont['is_tel'] = $is_tel;
             $cont['status'] = $status;
             $cont['operation'] = $operation;

             $startdate = $_GET['startdate']?$_GET['startdate']:date('Y-m-d',strtotime('-7 day'));
             $enddate = $_GET['enddate']?$_GET['enddate']:date('Y-m-d',strtotime('today'));
            $plist = mod_product::getcrmproduct();
             $userlist = mod_crmmanage::getusername();
            $tlist = mod_crmmanage::getqtype();
            $qlist =  mod_crmmanage::getqlist($cont,$startdate,$enddate,$first,PAGE_ROWS);

            foreach ($qlist['list'] as &$value) {
                if ($value['status'] == 'untreated' && $value['operation'] == '1') {
                    if ($value['lastreply_time']) {
                        if($value['lastreply_time'] + 5*24*3600 < time() ) $value['color'] = '#FF3300';
                    }else{
                        if($value['create_time'] + 5*24*3600 < time() ) $value['color'] = '#FF3300';
                    }
                }

                if ($value['status'] == 'untreated' && $value['operation'] == 2) {
                    $value['color'] = 'green';
                }

                if ($value['status'] == 'untreated' && $value['operation'] == 3) {
                     $firstanswer = mod_crmmanage:: getfirstanswerbyqid($value['question_id']);
                     if($firstanswer['creater_id'] == $value['lastreply_id'] )  $value['color'] = '#ffa500';
                }

                foreach ($userlist as $val) {
                    if ($value['create_id'] == $val['user_id']) {
                        $value['create_name'] = $val['truename'];
                    }
                }
            }
            unset($value);
            $total = $qlist['total'];
            foreach ( $tlist as $key => $value) {
                $newtype[$key+1]=$value;
            }
            $newtype = genTree($newtype);
            pm_tpl::assign("ur_here","问题列表");
            pm_tpl::assign('gid',$gid);  //选择的游戏
            pm_tpl::assign('qid',$qid);
            pm_tpl::assign('account',$account);
            pm_tpl::assign('nick',$nick);
            pm_tpl::assign('is_tel',$is_tel);
            pm_tpl::assign('content',$content);
            pm_tpl::assign('status',$status);
            pm_tpl::assign('operation',$operation);
            pm_tpl::assign('qtype',$qtype);
            pm_tpl::assign("qlist",$qlist['list']);
            pm_tpl::assign("tlist",$tlist);
            pm_tpl::assign("ser_tlist",$newtype);
            pm_tpl::assign("startdate",$startdate);
            pm_tpl::assign("enddate",$enddate);
            pm_tpl::assign("plist",$plist);
            pm_tpl::assign('action_link',array('href' => '?c=crmmanage&a=addquestion','text' => '添加问题'));
            //pm_tpl::assign('action_link_1',array('href' => '?c=crmmanage&a=export&query='.urlencode($where),'target' =>'__blank','text' => '导出html'));
            $cont && $url =  http_build_query($cont);
            pm_tpl::assign('page_url', '?c=crmmanage&a=index&startdate='.$startdate.'&enddate='.$enddate.'&'.$url);
            pm_tpl::assign('pages', mod_pager::get_page_number_list($total, $first, PAGE_ROWS));

            pm_tpl::display('crmmanage_index');
            }

        public function delquestion()
        {
        $qid = $_GET['qid'];
            if ($qid) {
                  $qinfo = mod_crmmanage:: getqbyid($qid);
                  if ($qinfo['create_id'] != ADMINUSERID && !in_array(ADMINUSERID,$this->adminlist) ) {
                      mod_login::message('无权删除','?c=crmmanage&a=index');
                  }

                $data['exists'] = 0;
                $status = pm_db::update('crm_question',$data,"question_id = '$qid'");
                if ($status)
                    mod_login::message('删除成功','?c=crmmanage&a=index');
                else
                    mod_login::message('删除失败','?c=crmmanage&a=index');
            }

        }

        public function editquestion()
        {

        if ($_POST) {
            unset($_POST['submit']);
            $status =    pm_db::update('crm_question',$_POST,"question_id = '$_POST[question_id]'");

                if ($status)
                    mod_login::message('编辑成功','?c=crmmanage&a=index');
                else
                    mod_login::message('编辑失败','?c=crmmanage&a=index');
            exit;
        }

            $qid = $_GET['qid'];
            $info = mod_crmmanage::getqbyid($qid);
            $typeinfo = mod_crmmanage::getqtypebyid($info['qtype_id']);
            $plist = mod_product::getcrmproduct();
            $slist = mod_crmmanage::getserverlist($info['game_id']);
            $tlist = mod_crmmanage::getqtype();
            foreach ( $tlist as $key => $value) {
                $newtype[$key+1]=$value;
            }
            $newtype = genTree($newtype);
            pm_tpl::assign('action_link',array('href' => '?c=crmmanage&a=index','text' => '返回问题列表'));
            pm_tpl::assign("info",$info);
            pm_tpl::assign("typeinfo",$typeinfo);
            pm_tpl::assign("tlist",$newtype);
            pm_tpl::assign("json_type",json_encode($newtype));
            pm_tpl::assign("plist",$plist);
            pm_tpl::assign("slist",$slist);
            pm_tpl::assign("ur_here","添加问题");
            pm_tpl::display('crmmanage_editquestion');
        }


        public function addquestion()
        {

        if ($_POST) {
            unset($_POST['submit']);
            $data = $_POST;
            $lastone =  mod_crmmanage::getlastqid();
             $date = substr($lastone,1,8);
            if($date != date('Ymd') )
                $qid = date('Ymd')."00001";
            else
                $qid =  substr($lastone,1)+1;
            $data['question_id']= 'Q'.$qid;
            $data['create_id'] = ADMINUSERID;
            $data['create_time'] = time();
            $data['add_role'] = 1;
            $id = pm_db::insert('crm_question',$data,true);
            if($id)
				mod_login::message('添加成功',"?c=crmmanage&a=answerlist&qid=".$data['question_id']);
            else
				mod_login::message('添加失败');
            exit;
        }



            $plist = mod_product::getcrmproduct();

            $slist =  mod_crmmanage::getserverlist($plist[0]['pid']);

            $tlist = mod_crmmanage::getqtype();
            foreach ( $tlist as $key => $value) {
                $newtype[$key+1]=$value;
            }

            $newtype = genTree($newtype);
            pm_tpl::assign('action_link',array('href' => '?c=crmmanage&a=index','text' => '返回问题列表'));
            pm_tpl::assign("tlist",$newtype);
            pm_tpl::assign("json_type",json_encode($newtype));
            pm_tpl::assign("plist",$plist);
            pm_tpl::assign("slist",$slist);
            pm_tpl::assign("ur_here","添加问题");
            pm_tpl::display('crmmanage_addquestion');

        }


        public function answerlist()
        {
            if ($_POST['rquestion']) {
                if (ADMINUSERID == 249) {
                dump($_POST);
                exit;
                }

                $data['qid']=$_POST['qid'];
                $data['content']=$_POST['reply'];
                $data['create_time']= time();
                $data['creater_id'] = ADMINUSERID;
                $data['reply_type']= 'cus_server';
                pm_db::tran_query("BEGIN");
                $id = pm_db::insert('crm_answer',$data,'tran');
                $reply['lastreply_id'] = ADMINUSERID;
                $reply['lastreply_time'] = time();
                if(isset($_POST['to_server']) && !empty($_POST['to_server']) ){
                    if($_POST['operation'] == 0 && $_POST['status'] == 'untreated') $reply['operation'] = 3;
                }else{
                    if($_POST['operation'] == 3) $reply['operation'] = 0;
                    $reply['status'] = 'processed';
                }
                $status = pm_db::update('crm_question',$reply,"question_id = '$_POST[qid]'",'tran');
                if ($id && $status) {
                    pm_db::tran_query("COMMIT");
                    mod_login::message('回复成功');
                }else{
                    pm_db::tran_query("ROLLBACK");
                    mod_login::message('回复失败');
                }
                pm_db::tran_query("END");
                exit;
            }

            if ($_POST['tooperation']) {
                $data['operation'] = 1;
                $data['status'] = 'untreated';
                 $status = pm_db::update('crm_question',$data,"question_id = '$_POST[qid]'");
                if($status)
                    mod_login::message('转交成功！');
                else
                    mod_login::message('转交失败！');
                exit;
            }


            if ($_POST['cquestion']) {
                $data['status'] = 'closed';
                $data['operation'] = 0;
                 $status = pm_db::update('crm_question',$data,"question_id = '$_POST[qid]'");
                if($status)
                    mod_login::message('问题关闭成功！');
                else
                    mod_login::message('问题关闭失败！');
                exit;
            }

            $qid = $_GET['qid'];
            $info = mod_crmmanage::getqbyid($qid);
            $memberinfo =  mod_member::get_oneamdinbyuser_id($info['create_id']);
            $admin_info =  mod_member::get_oneamdinbyuser_id(ADMINUSERID);
            $proinfo = mod_product::get_one_product($info['game_id']);
            $serinfo = mod_crmmanage::getserverbyid($info['server_id']);
            $info['truename'] =$memberinfo['truename'];
            $info['pname'] =$proinfo['pname'];
            $info['server_name'] = $serinfo['server_name'];
            $info['is_right'] = strpos($admin_info['rights'],'custom-question');
            $tlist = mod_crmmanage::getqtype();
            $alist = mod_crmmanage::getanswerlist($qid);
            $userlist = mod_crmmanage::getusername();
            if($alist) {
                foreach ($alist as &$value) {
                    if ($value['reply_type']!='customer') {
                        foreach ($userlist as $val) {
                            if ($val['user_id'] == $value['creater_id']) {
                                $value['username'] = $val['user_name'];
                            }
                        }
                    }
                }
            }
            pm_tpl::assign("tlist",$tlist);
            pm_tpl::assign("alist",$alist);
            pm_tpl::assign("info",$info);
            pm_tpl::assign("ur_here","回复列表");
            pm_tpl::assign('action_link',array('href' => '?c=crmmanage&a=index','text' => '问题列表'));
            pm_tpl::display('crmmanage_answerlist');
        }


        public function ajaxdelanswer()
        {
         $aid = $_GET['aid'];
          $ainfo =  mod_crmmanage::getanswerbyid($aid);
         if ($ainfo['creater_id'] != ADMINUSERID && !in_array(ADMINUSERID,$this->adminlist) ) {
             echo "norights";
             exit;
         }

         $data['exists'] =0;
         $result =pm_db::update('crm_answer',$data,"answer_id=".$aid);
         if ($result)
             echo "ok";
         else
             echo "error";

        }


        public function ajaxgetserver()
        {
           $pid =  $_GET['pid'];
          $list =  mod_crmmanage::getserverlist($pid);
           echo json_encode($list);
        }



        public function serverlist()
        {

           $_GET['game_id'] !=-1 && $gid = $_GET['game_id'];
            $plist = mod_product::getcrmproduct();
            $slist =  mod_crmmanage::getserverlist($gid);
            if ($slist) {
                foreach ($plist as $value) {
                    foreach ($slist as &$val) {
                        if ($value['pid'] == $val['gid']) {
                            $val['pname'] = $value['pname'];
                        }
                    }
                }
                unset($val);
            }
            pm_tpl::assign('gid',$gid);
            pm_tpl::assign('slist',$slist);
            pm_tpl::assign('plist',$plist);
            pm_tpl::assign("ur_here","区服列表");
            pm_tpl::assign('action_link',array('href' => '?c=crmmanage&a=addserver','text' => '添加区服'));
            pm_tpl::display('crmmanage_serverlist');
        }

        public function delser()
        {
        $sid = $_GET['sid'];
        $result = pm_db::delete('crm_serverlist',"id='$sid'");
        if($result)
            mod_login::message('删除成功');
        else
            mod_login::message('删除失败');
        }

        public function addserver()
            {
            if($_POST){
             unset($_POST['submit']);

            $id = pm_db::insert('crm_serverlist',$_POST,true);

            if($id)
				mod_login::message('添加成功',"?c=crmmanage&a=serverlist");
            else
				mod_login::message('添加失败');
            exit;
            }
            $plist = mod_product::getcrmproduct();

            pm_tpl::assign('plist',$plist);
            pm_tpl::display('crmmanage_addserver');
            }


        public function ajaxser()
        {
           $data['sort'] = $_GET['sort'];
           $id = $_GET['id'];
         pm_db::update('crm_serverlist',$data,"id = '$id'");

        }



    }


?>
