<?php
include(PATH_APPLICATION.'/pm_wxinit.php');
!defined('PATH_ADMIN') && exit('Forbidden');
class pmc_test extends pm_wxinit{

	public function entrylist(){
		/*
		//$num = 1 01 100001 130603 0001;
		$chars = 'rk34Q6NDGsWxBzYJPaE2Sh7L0nfdZOwRTtgjClo1vu9KIp8q5yHbmeFUcVMiAX';

		for($i=0;$i<10;$i++){
			$num = (int)('101100001130603000'.$i);
			$int2string = gensign(14);
			$string2int = $num;//string2int($int2string, $chars);
			echo $int2string.'|'.$string2int.'<br />';
		}
		*/
		//mod_test::generate_giving_code(5, 100001, 13, '130830', 100087);
		debug(substr(str_replace("-","","2013-08-09"), 2));

		$sss = mod_test::entry_list_test();
		debug($sss);
		$productid = 20003;
		$total = mod_test::entry_list_count($productid);
		//debug($total);
		$start = (empty($_GET['start'])) ? 0 : (int)$_GET['start'];
		$pagesize = 2;
		$list = mod_test::entry_list($productid, $start, $pagesize);
		debug($list);
	}

	public function entryadd(){
		$data = array(
						'stat_date' => '2013-07-02',
						'plat_id' => 50,
						'coopid' => -1,
						'product_id' => 20003,
						'newuser' => 1,
						'actuser' => 2,
						'payuser' => 3,
						'paysum' => 4,
						'remain_user' => 5
					  );
		try{
			$result = mod_test::add_entry($data);
			debug($result);
		}catch(Exception $e){
			mod_login::message($e->getMessage());
		}
	}

	public function entryedit(){
		$image = '/data0/www/html/gamebi/data/kindeditoruploaddir/image/20130715/20130715152611_21703.jpg';
		$remote = 'assaddir/sss/sde';
		//debug(UPLOAD_PATH);
		$ppath = $this->get_wximg_path($image);
		mod_ftp::uploadpath($image, $remote);
		debug($ppath);
	}

	public function entrydelete(){

	}

	public function readexcl(){
		$data = mod_readfile::parseexcel('/data0/www/html/gamebi/data/testupload/php_test.xls');
		debug($data);
	}

	public function upload(){
		pm_tpl::display('test_upload');
	}

	public function addbatch(){
		$givingid = 100001;
		$counter = 88;
		$rule = "none";
		$remark = "��ע��ע";
		$_ceffectivetime = '2013-08-20';
		$gameid = mod_product::get_cur_pid();
		$createrid = USERNAME;
		$ceffectivetime = strtotime($_ceffectivetime. ' 00:00:00');
		$code_ceffectivetime = substr(str_replace("-","",$_ceffectivetime), 2);
		if($counter < 1 || $counter > 9999){
			mod_login::message("�������������1~9999֮��");
		}
		$current_batchid = mod_test::get_giving_max_batchid($givingid);
		if($current_batchid >= 99){
			mod_login::message("��ǰ����������Ѿ�����99");
		}
		$batchid = $current_batchid + 1;
		mod_test::add_giving_batch($batchid, $givingid, $rule, $ceffectivetime, $remark, $gameid, $createrid);
		mod_test::generate_giving_code($batchid, $givingid, $counter, $code_ceffectivetime, $gameid);
	}

    public  function  test_linux(){
            $last_line = system('cd /data0/www/html/gamebi/&&cat p.php', $retval);
            echo  $last_line;
    }
    public  function  manynews(){
        $arr=array(1825,1827,1828,1829);
        $a = mod_wxmenu::manynews($arr);
        debug($a);
    }


    function get_content(){

        $str = 'dfdfdfdfdas<{name=ddddd}>dsdsdsadsdsdsfdfdfdfdfdfdfdasdsdsdsadsdsdsfdfdfdfdfdfdfdasdsdsdsadsdsdsfdfdfdfdfdfdfdasdsdsdsadsdsdsfdfdfdfdfdfdfd';
        preg_match("/.*<\{name=(.*)\}>.*/i",$str,$arr);
        print_r($arr[1]);
        //pm_tpl::display('test_contetn');
    }


public function ceshi()
    {
    function getRandom($array,$rate){
        $rate = explode(':',$rate);
        $sum = 0;
        $left = 0;
        $right = 0;
        foreach($rate as $value){
            $sum+=$value*10;
        }
        $temp = rand(0,$sum);
        foreach($rate as $key=>$value){
            $right+=$value*10;
            if($left<=$temp && $temp<$right){
                return $array[$key];
            }
            $left+=$value*10;
        }
    }

   /*$array = array(0,1,2,3);
    $rate = '2:1:3:5';
    $a=$b=$c=$d=0;
    for($i=0;$i<1100;$i++){
        if(getRandom($array,$rate)==0){
            $a++;
        }
        if(getRandom($array,$rate)==1){
            $b++;
        }
        if(getRandom($array,$rate)==2){
            $c++;
        }
        if(getRandom($array,$rate)==3){
            $d++;
        }
    }
    echo $a;
    echo "\n";
    echo $b;
    echo "\n";
    echo $c;
    echo "\n";
    echo $d;
    echo "\n";*/

    $array = array(1,0);
    $rate = '9:1';
    $a=$b=0;
    for($i=0;$i<1000;$i++){
        if(getRandom($array,$rate)){
            $a++;
        }
        if(!getRandom($array,$rate)){
            $b++;
        }
    }

    echo $a;
    echo "\n";
    echo $b;



    }

public function testaa()
{
    $aa= pm_db::fetch_all( pm_db::query("select * from gamebi_giving_item"));
    echo "<pre>";
    print_r($aa);

//echo pm_db::query("insert into gamebi_giving_item set itemid='14190001',name='炼武破片',type='2',gameid='100087'");
    //
    //

//echo pm_db::query("insert into gamebi_giving_item set itemid='14190002',name='防具碎片',type='2',gameid='100087'");
//echo pm_db::query("insert into gamebi_giving_item set itemid='14190003',name='饰品碎片',type='2',gameid='100087'");
//echo pm_db::query("insert into gamebi_giving_item set itemid='14191001',name='上古残枪',type='2',gameid='100087'");
//echo pm_db::query("insert into gamebi_giving_item set itemid='14191002',name='上古残扇',type='2',gameid='100087'");
//echo pm_db::query("insert into gamebi_giving_item set itemid='14191003',name='上古残剑',type='2',gameid='100087'");
}


	public function testseq(){
		$seq = $this->generate_question_seq();
		debug($seq);
	}
	
	/**生成CRM问题编号*/
	private function generate_question_seq(){
		$cur_ymd = date("Ymd");
		$filename = "data/crm/seq/CRM_Q_".$cur_ymd.".txt";
		if(file_exists($filename)){
			$r_file=fopen($filename,"r");
			$cur_seq = fgets($r_file);
			$seq = ((int)trim($cur_seq)) + 1;
			fclose($r_file);
			$w_file=fopen($filename,"w");
			fputs($w_file,$seq);
			fclose($w_file);
		}else{
			$file=fopen($filename,"w+");
			$seq = 1;
			fputs($file, $seq);
			fclose($file);
		}
		return "Q".$cur_ymd.substr(strval($seq+100000),1,5); 
	}

}
