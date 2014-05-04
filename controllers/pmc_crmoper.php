<?php
/*=============================================================================
FileName: pmc_crmoper.php
Description:
Author: Tian - tianpengjun@feiliu.com
Created Time: 2014/3/4 18:14:28
Last modified: 2014-03-11 15:13:04
Version:  1.0
=============================================================================*/
    class pmc_crmoper{

            public function index()
            {
            $first = (empty($_GET['start'])) ? 0 : (int)$_GET['start'];
            $cont['game_id'] =  mod_product::get_cur_pid();
            $qtype = $_GET['qtype_id'];
            $qtype && $cont['qtype_id'] = $qtype;
            $cont['operation'] = 1;
             $startdate = $_GET['startdate']?$_GET['startdate']:date('Y-m-d',strtotime('-7 day'));
             $enddate = $_GET['enddate']?$_GET['enddate']:date('Y-m-d',strtotime('today'));
            $tlist = mod_crmmanage::getqtype();
            $qlist =  mod_crmmanage::getqlist($cont,$startdate,$enddate,$first,PAGE_ROWS);
             $where = $qlist['where'];
            $total = $qlist['total'];
            foreach ( $tlist as $key => $value) {
                $newtype[$key+1]=$value;
            }
            $newtype = genTree($newtype);

            pm_tpl::assign("ur_here","问题列表");
            //pm_tpl::assign('gid',$gid);  //选择的游戏
            pm_tpl::assign('qtype',$qtype);
            pm_tpl::assign("qlist",$qlist['list']);
            pm_tpl::assign("tlist",$tlist);
            pm_tpl::assign("ser_tlist",$newtype);
            pm_tpl::assign("startdate",$startdate);
            pm_tpl::assign("enddate",$enddate);
            pm_tpl::assign('action_link_1',array('href' => '?c=crmoper&a=export&query='.urlencode($where),'target' =>'__blank','text' => '导出html'));
            $cont && $url =  http_build_query($cont);
            pm_tpl::assign('page_url', '?c=crmoper&a=index&startdate='.$startdate.'&enddate='.$enddate.'&'.$url);
            pm_tpl::assign('pages', mod_pager::get_page_number_list($total, $first, PAGE_ROWS));
            pm_tpl::display('crmoper_index');

            }


            public function answerlist()
            {

            if ($_POST['rquestion']) {

                $data['qid']=$_POST['qid'];
                $data['content']=$_POST['reply'];
                $data['create_time']= time();
                $data['creater_id'] = ADMINUSERID;
                $data['reply_type']= 'operation';
                pm_db::tran_query("BEGIN");
                $id = pm_db::insert('crm_answer',$data,'tran');
                $reply['operation'] = 2;        //运营已回复
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

            $qid = $_GET['qid'];
            $info = mod_crmmanage::getqbyid($qid);
            $memberinfo =  mod_member::get_oneamdinbyuser_id($info['create_id']);
            $proinfo = mod_product::get_one_product($info['game_id']);
            $serinfo = mod_crmmanage::getserverbyid($info['server_id']);
            $info['truename'] =$memberinfo['truename'];
            $info['pname'] =$proinfo['pname'];
            $info['server_name'] = $serinfo['server_name'];

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
            pm_tpl::assign('action_link',array('href' => '?c=crmoper&a=index','text' => '问题列表'));
            pm_tpl::display('crmoper_answerlist');
            }



            public function export()
            {
            $where = stripcslashes($_GET['query']);
            $sql="SELECT * from crm_question WHERE `exists`= '1' $where order by create_time desc";
            $list =  pm_db::fetch_all_result($sql);
            $plist = mod_product::getcrmproduct();
            $tlist = mod_crmmanage::getqtype();
            $slist =  mod_crmmanage::getserverlist();
            foreach ($list as &$value) {
                foreach ($slist as $val) {
                    if ($value['server_id'] == $val['id']) {
                        $value['server'] = $val['server_name'];
                        }
                    }


                foreach ($plist as $val) {
                    if ($value['game_id'] == $val['pid']) {
                        $value['game'] = $val['pname'];
                    }
                }
                foreach ($tlist as $val) {
                    if ($value['qtype_id'] == $val['id']) {
                        $value['qtype'] = $val['name'];
                    }
                }

                if ($value['status'] == 'untreated') {
                    $value['status_info'] = "未处理";
                }elseif($value['status'] == 'processed'){
                    $value['status_info'] = "已处理";
                }else{
                    $value['status_info'] = "已关闭";
                }

                if ($value['operation'] == 1) {
                $value['status_info'] .="--运营处理中";
                }

                if($value['operation'] == 2){
                $value['status_info'] .="--运营已回复";
                }

            }


            $line = "<tr>";
            foreach ($list as $value) {
                $line .= "<td>".$value['question_id']."</td>";
                $line .= "<td>".$value['title']."</td>";
                $line .= "<td>".$value['content']."</td>";
                $line .= "<td>".$value['game']."</td>";
                $line .= "<td>".$value['server']."</td>";
                $line .= "<td>".$value['channel']."</td>";
                $line .= "<td>".$value['account']."</td>";
                $line .= "<td>".$value['nick']."</td>";
                $line .= "<td>".$value['role_id']."</td>";
                $line .= "<td>".$value['occurs_time']."</td>";
                $line .= "<td>".$value['qtype']."</td>";
                $line .= "<td>".$value['create_time']."</td>";
                $line .= "<td>".$value['status_info']."</td>";
                $line .= "<td>".$value['qq']."</td>";
                $line .= "<td>".$value['mobile']."</td>";
                $line .= "<td>".$value['email']."</td></tr><tr>";
            }

            $content = substr($line,0,-4);
            $template = file_get_contents("template.html");
            $html = str_replace("[content]",$content,$template);
            echo $html;
            }





    }



?>
