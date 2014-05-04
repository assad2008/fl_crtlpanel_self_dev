<?php
/**
* @file mod_misc.php
* @synopsis  PM后台 其他功能
* @author Yee, <assad2008@sina.com>
* @version 1.0
* @date 2012-06-05 20:31:14
 */


	class mod_misc
	{
		public static function get_weather()
		{
			$data = gethttp('http://m.weather.com.cn/data/101010100.html');
			return json_decode($data,1);
		}

		public static function deving()
		{
			$link = array(array('text' => '开始页面','href'=> "?c=login&a=welcome"));
			mod_login::message('该功能正在开发...',$link,0,0);
		}

		public static function mysqlstatus($type = 1)
		{
			switch ($type) 
			{
				case 1:
					$sql = "show variables like 'max_connections'";
					break;
				case 2:
					$sql = "show global status like 'Max_used_connections'";
					break;
				case 3:
					$sql = "show global status like '%Threads_connected%'";
					break;
				default:
					break;
			}
			$ret = pm_db::fetch_result($sql);
			return $ret;
		}

		public static function offlinecount_list($start, $orderid, $productid, $staus, $zhanghao, $start_date, $end_date, $card_type = false,$perpage = PAGE_ROWS)
		{
			$db = oralceinit(2);  //oracle初始化
			$where = '';
			if(empty($start_date) && empty($end_date))
			{
				if(!empty($orderid))
				{
					$where .= " AND ORDER_ID = '$orderid' ";
				}
				if(!empty($productid))
				{
					$where .= " AND GAME_ID = '$productid' ";
				}
				if(!empty($zhanghao))
				{
					$where .= " AND GAME_USERNAME = '$zhanghao' ";
				}
				switch($staus)
				{
					case '0':
						$where .= " AND ORDER_STAUS = '0' ";
						break;
					case '1':
						$where .= " AND ORDER_STAUS = '1' ";
						break;
					case '2':
						$where .= " AND ORDER_STAUS = '2' ";
						break;
					case '3':
						$where .= " AND ORDER_STAUS = '3' ";
						break;
					case '10':
						$where .= " AND ORDER_STAUS = '10' ";
						break;
					case '99':
						$where .= " ";
						break;
					default:
						$where .= " AND ORDER_STAUS = '10' ";
						break;
				}
			}elseif($start_date && $end_date )
			{
				$start_date = $start_date." 00:00:00";
				$end_date = $end_date." 23:59:59";
				if(!empty($orderid))
				{
					$where .= " AND ORDER_ID = '$orderid' ";
				}
				if(!empty($productid))
				{
					$where .= " AND GAME_ID = '$productid' ";
				}
				if(!empty($zhanghao))
				{
					$where .= " AND GAME_USERNAME = '$zhanghao' ";
				}
				switch($staus)
				{
					case '0':
						$where .= " AND ORDER_STAUS = '0' ";
						break;
					case '1':
						$where .= " AND ORDER_STAUS = '1' ";
						break;
					case '2':
						$where .= " AND ORDER_STAUS = '2' ";
						break;
					case '3':
						$where .= " AND ORDER_STAUS = '3' ";
						break;
					case '10':
						$where .= " AND ORDER_STAUS = '10' ";
						break;
					case '99':
						$where .= " ";
						break;
					default:
						$where .= " AND ORDER_STAUS = '10' ";
						break;
				}

				$where .= " AND PAY_UPDAETIME > to_date('$start_date','yyyy-mm-dd hh24:mi:ss') AND  PAY_UPDAETIME < to_date('$end_date','yyyy-mm-dd hh24:mi:ss') ";
			}
				if($card_type)
				{
					$where .= " AND pay_type=$card_type";
				}
			/*
			 $sql = "SELECT od.*, to_char(od.ORDER_SUM,'fm9999999990.00') je, to_char(od.PAY_UPDAETIME,'yyyy-mm-dd hh24:mi:ss') p_update FROM TBL_PAY_ORDER od WHERE 1=1 $where AND IS_EXIST = 1 ORDER BY ORDER_ID DESC";
			 */
			$sql = "SELECT od.*, tt.cardname, to_char(od.ORDER_SUM,'fm9999999990.00') je, to_char(od.PAY_UPDAETIME,'yyyy-mm-dd hh24:mi:ss') p_update FROM TBL_PAY_ORDER od  LEFT JOIN (select distinct  t.thiredcardid,t.cardname  from tbl_pay_card t) tt ON od.PAY_TYPE = tt.thiredcardid  WHERE 1=1 $where AND IS_EXIST = 1 ORDER BY ORDER_ID DESC";

			$sql_limit = $db->_limit($sql, $perpage, $start);
			$query = $db->Select($sql_limit);
			$data = array();
			$totaljinge = 0.0;
			while($row = $db->FetchArray($query, $skip = 0, $maxrows = -1))
			{
				$row['pay_updatetime'] = strtotime($row['P_UPDATE']);
				$row['je'] = $row['JE'];
				$row['je_float'] = (float)$row['je'];
				$data[] = $row;
			}
			$totalnum = "SELECT COUNT(ORDER_SUM) num FROM (SELECT od.* FROM TBL_PAY_ORDER od WHERE 1=1 $where ORDER BY ORDER_ID DESC)";
			$num = $db->FetchArray($db->query($totalnum));
			$totalje = "SELECT SUM(ORDER_SUM) totalje FROM (SELECT od.* FROM TBL_PAY_ORDER od WHERE 1=1 $where ORDER BY ORDER_ID DESC)";
			$totalje = $db->FetchArray($db->query($totalje));
			if($data)
			{
				$output = array();
				$output['data'] = $data;
				$output['total'] = $num['NUM'];
				$output['totalje'] = $totalje['TOTALJE'];
				$output['totalje'] = number_format($output['totalje'], 2, '.', '');		
				return $output;
			}else
			{
				return null;
			}
		}

		public static function daycount_list($start, $productid, $staus, $start_date, $end_date, $card_type='',$perpage = PAGE_ROWS)
		{
			$db = oralceinit(2);  //oracle初始化
			$where = '';
			if(!empty($productid))
			{
				$where .= " AND GAME_ID = '$productid' ";
			}
			switch($staus)
			{
				case '0':
					$where .= " AND ORDER_STAUS = '0' ";
					break;
				case '1':
					$where .= " AND ORDER_STAUS = '1' ";
					break;
				case '2':
					$where .= " AND ORDER_STAUS = '2' ";
					break;
				case '3':
					$where .= " AND ORDER_STAUS = '3' ";
					break;
				case '10':
					$where .= " AND ORDER_STAUS = '10' ";
					break;
				case '99':
					$where .= " ";
					break;
				default:
					$where .= " AND ORDER_STAUS = '10' ";
					break;
			}
			if($start_date && $end_date )
			{
				$where .= " AND PAY_UPDAETIME > to_date('$start_date','yyyy-mm-dd hh24:mi:ss') AND  PAY_UPDAETIME < to_date('$end_date','yyyy-mm-dd hh24:mi:ss') ";
			}
			$sql = "SELECT count(to_char(PAY_UPDAETIME,'yyyy-mm-dd')) count_bishu,  sum(ORDER_SUM) ordersum, to_char(PAY_UPDAETIME,'yyyy-mm-dd') p_update FROM TBL_PAY_ORDER 
WHERE  IS_EXIST = 1 $where GROUP BY to_char(PAY_UPDAETIME,'yyyy-mm-dd') order by to_char(PAY_UPDAETIME,'yyyy-mm-dd') desc ";
			//$sql_limit = $db->_limit($sql, $perpage, $start);
			$query = $db->Select($sql);
			$data = array();
			while($row = $db->FetchArray($query, $skip = 0, $maxrows = -1))
			{
				$data[] = $row;
			}
			if($data)
			{
				return $data;
			}else
			{
				return null;
			}
		}

		public static function getproductidlists()  //产品列表
		{
			$db = oralceinit(2);
			$sql = "SELECT g.ID, g.NAME FROM TBL_PAY_GAME g ORDER BY g.ID";
			$query = $db->Select($sql);
			while($row = $db->FetchArray($query, $skip = 0, $maxrows = -1))
			{
				if(!$row)
				{
					return false;
				}
				$data[] = $row;
			}
			return $data;
		}

		public static function get_card_type_list()
		{
			$db = oralceinit(2);
			$sql = "select t.cardname,t.card_type_id from tbl_pay_card t group by t.card_type_id,t.cardname";
			$query = $db->Select($sql);
			while($row = $db->FetchArray($query, $skip = 0, $maxrows = -1))
			{
				if(!$row)
				{
					return false;
				}
				$data[] = $row;
			}
			return $data;
		}

		public static function qianghaotablist($start, $start_date, $end_date, $perpage= '')
		{
			$db= oralceinit();
			$where= " AND THEDATE > $start_date AND THEDATE < $end_date ";

			$sql= "SELECT * FROM PDT_STAT_QIANGHAO_REPORT WHERE 1=1 $where ORDER BY THEDATE ";
			$sql_limit= $db->_limit($sql, $perpage, $start);
			$query= $db->Select($sql_limit);
			$data= array();
			while($row = $db->FetchArray($query, $skip = 0, $maxrows = -1))
			{
				$row['THEDATE_GSH'] = date('Y-m-d', strtotime($row['THEDATE']));
				$data[] = $row;
			}
			$totalnum = "SELECT COUNT(THEDATE) num FROM (SELECT p.* FROM PDT_STAT_QIANGHAO_REPORT p WHERE 1=1 $where ORDER BY THEDATE DESC)";
			$num = $db->FetchArray($db->query($totalnum));
			if($data)
			{
				$output= array();
				$output['data']= $data;
				$output['total'] = $num['NUM'];
				return $output;
			}else
			{
				return null;
			}
		}

		public static function gamecoop_data($start, $gecp_name, $plat, $type, $sortcoop, $perpage= 20)
		{
			$where= '';
			if(!empty($gecp_name))
			{
				$where .= " AND gecp_name LIKE '%$gecp_name%' ";
			}
			if(!empty($plat))
			{
				$where .= " AND gecp_plat = '$plat' ";
			}
			if(!empty($type))
			{
				$where .= " AND gecp_type = '$type' ";
			}
			if(!empty($sortcoop))
			{
				$where .= "ORDER BY gecp_coopid ";
			}else
			{
				$where .= "ORDER BY gecp_coopid DESC ";
			}
			if($start > -1 && $perpage > 0)
			{
				$where .= " LIMIT {$start}, {$perpage} ";
			}
			$sql= "SELECT SQL_CALC_FOUND_ROWS * FROM pm_gamecoop WHERE 1=1 $where ";
			$query= pm_db::query($sql);
			$total= pm_db::fetch_result("SELECT FOUND_ROWS() AS rows");
			$data= pm_db::fetch_all($query);
			if($data)
			{
				$output= array();
				$output['data']= $data;
				$output['total']= $total['rows'];
				return $output;
			}else
			{
				return null;
			}
		}

		////预装包信息列表
		public static function yzapk_infomanage($start, $perpage= 20)
		{
			$where= '';
		
			if($start > -1 && $perpage > 0)
			{
				$where .= " LIMIT {$start}, {$perpage} ";
			}
			$sql= "SELECT SQL_CALC_FOUND_ROWS * FROM ins_infomanage WHERE 1=1 $where ";
			$query= pm_yzdb::query($sql);
			$total= pm_yzdb::fetch_result("SELECT FOUND_ROWS() AS rows");
			$data= pm_yzdb::fetch_all($query);
			if($data)
			{
				$output= array();
				$output['data']= $data;
				$output['total']= $total['rows'];
				return $output;
			}else
			{
				return null;
			}
		}
	

		public static function edit_gamecoop($data, $id)
		{
			return pm_db::update('pm_gamecoop', $data, "gecp_id =  '$id' ");
		}

		public static function get_gamecoop_list($id)
		{
			$sql = "SELECT * FROM pm_gamecoop WHERE gecp_id = '$id' ";
			return pm_db::fetch_result($sql);
		}

		public static function add_gamecoop($data)
		{
			return pm_db::insert('pm_gamecoop',$data);
		}

		public static function get_one_channel($id)
		{
			$sql = "SELECT * FROM pm_gamecoop WHERE gecp_coopid = '$id' ";
			return pm_db::fetch_result($sql);
		}

		public static function del_gamecoop($id)
		{
			return pm_db::query("UPDATE pm_gamecoop SET gecp_status= 0 WHERE gecp_id= '$id' ");
		}

		public static function ghzcjsontoarr()
		{
			require PATH_ADMIN .'/core/pm_curl.class.php';
			$curl = new Curl();
			//debug(PATH_ADMIN);
			//$res = $curl->get('http://111.161.38.115:13000/DataAnalysisNew/Status?cmd=11' ,array() ,array(), '' );
			if(empty($res))
			{
					/*
					$res = 'null({"channelList":[["fl","100205","100052","100091","100044","100088","100086","100051","100048","100096","100087","100045","100110","100119","100066","100089","100122","100107","100108","100123","100049","100063","100113","100111","100116","100134","100050","100068","100097","100127","100064","100130","100084","100126","100131","100090","100057","100083","100109","100132","100094","100106","100112","100135","100124","100058","100099","100060","100072","100065","100061","100117","100136","100073","100129","100092","100067","100078","100079","100075","100146","100147","100148","100150","100120","100121","100071","100133","100114","100081","100054","100156","100098","100125","100082","100173","100178","100164","100053","100167","100166","100174","100185","100184","100182","100180","100172","100187","100186","100183","100157","100189","100171","100192","100118","100160","100076","100191","100056","100193","100175"],["dcn","100144","100145"],["91","100137"],["uc","100151","100153"],["pp","100153","100139"],["wl","wl"],["mi","100154"]]}) ';
					 */
				$res = 'null({"channelList":[["fl","100205","100052","100091","100044","100088","100086","100051","100048","100096","100087","100045","100110","100119","100066","100089","100122","100107","100108","100123","100049","100063","100113","100111","100116","100134","100050","100068","100097","100127","100064","100130","100084","100126","100131","100090","100057","100083","100109","100132","100094","100106","100112","100135","100124","100058","100099","100060","100072","100065","100061","100117","100136","100073","100129","100092","100067","100078","100079","100075","100146","100147","100148","100150","100120","100121","100071","100133","100114","100081","100054","100156","100098","100125","100082","100173","100178","100164","100053","100167","100166","100174","100185","100184","100182","100180","100172","100187","100186","100183","100157","100189","100171","100192","100118","100160","100076","100191","100056","100193","100175","100165","100077"],["dcn","100144","100145"],["91","100137"],["uc","100151","100153"],["pp","100153","100139"],["wl","wl"],["mi","100154"]]}) ';
			}
			$res_json = substr($res, 5);
		
			//$pos = strpos($res_json, ')');
			$pos = strrpos($res_json, ')', -1);
			$res_json = substr($res_json,0, $pos);
			$arr = json_decode($res_json);
			foreach($arr as $ghzcdata)
			{
				foreach($ghzcdata as $k=> $v)
				{
					$pcoop = $v[0];
					foreach($v as $key => $value)
					{
						$row[$pcoop][] = $value;
					}
				}
			}
			return $row;
		}

		public static function arrwithin($arr)
		{
			$data = '(';
			foreach($arr as $key=> $value)
			{
				$data .= ("'".$value."',");
			}
			$data = substr($data, 0, strrpos($data, ','));
			$data .= ')';
			return $data;
		}

		public static function get_ghzc_data($plat, $type, $sort, $starttime = '',$endtime = '', $coop ='' )
		{
			//$cache = &cache_server();
			$where = '';
			if(!empty($plat))
			{
				$where .= " AND gc.gecp_plat = '$plat' ";
			}
			if(!empty($type))
			{
				$where .= " AND gc.gecp_type = '$type' ";
			}

			if(!empty($coop))
			{
				if($coop == 99)
				{
				
				}else
				{
					$key = $plat.$type.$sort.$coop.$starttime.$endtime;
					$data = array();
					//$data = $cache->get($key);
					if(!$data)
					{
						$data = self::ghzcjsontoarr();
						//debug($data);
						//$cache->set($key,$data,86400*2);
					}
		
					$coop_data = $data[$coop];
					//debug($coop_data);
					$coop_data_str = self::arrwithin($coop_data);
					$where .= "AND gz.channelid IN $coop_data_str ";
				}
			}

			$sql = "SELECT gz.channelid,gc.gecp_name,SUM(gz.charge) AS c,SUM(gz.registernum) AS rn,SUM(gz.chargenum) AS cn FROM pm_ghzcdata AS gz LEFT JOIN pm_gamecoop AS gc ON gz.channelid=gc.gecp_coopid WHERE 1=1 $where ";
			if($starttime) $sql .= " AND thedate>='$starttime'";
			if($endtime) $sql .= " AND thedate<='$endtime'";
			switch($sort)
			{
				case '2':
					$sql .= " GROUP BY channelid ORDER BY cn DESC ";
					break;
				case '3':
					$sql .= " GROUP BY channelid ORDER BY rn DESC ";
					break;
				default :
					$sql .= " GROUP BY channelid ORDER BY c DESC ";
					break;
			}

			$query = pm_db::query($sql);
			$data = array();
			while($row = pm_db::fetch_one($query))
			{
				$data[] = $row;
			}
			return $data;
		}

		public static function get_ghzc_data_channel($channel,$starttime = '',$endtime = '')
		{
			$sql = "SELECT gz.thedate,SUM(gz.charge) AS c,SUM(gz.registernum) AS rn,SUM(gz.chargenum) AS cn FROM pm_ghzcdata AS gz WHERE 1=1";
			$sql .= " AND gz.channelid='$channel'";
			if($starttime) $sql .= " AND thedate>='$starttime'";
			if($endtime) $sql .= " AND thedate<='$endtime'";
			$sql .= "GROUP BY gz.thedate ORDER BY gz.thedate ASC";
			$query = pm_db::query($sql);
			$data = array();
			while($row = pm_db::fetch_one($query))
			{
				$row['thedate'] = date('Y-m-d',strtotime($row['thedate']));
				$data[] = $row;
			}
			return $data;		
		}

		public static function as_channel_data($channel,$starttime = '',$endtime = '')
		{
			$sql = "SELECT SUM(charge) AS c,SUM(registernum) AS rn, SUM(chargenum) AS cn FROM pm_ghzcdata WHERE channelid='$channel'";
			if($starttime) $sql .= " AND thedate>='$starttime'";
			if($endtime) $sql .= " AND thedate<='$endtime'";
			return pm_db::fetch_result($sql);
		}

		public static function get_coopinfo_boss($coop_id)
		{
			$db = oralceinit(2);
			$query = $db->fetch_first("SELECT * FROM tbl_subcooperatotr WHERE ");
		}

		public function add_yzapk($data)
		{
			$result = pm_yzdb::insert('ins_infomanage',$data);
			$id = pm_yzdb::insert_id();
			return $id;
		}

		public function get_allcoop()
		{
			$sql = "SELECT * FROM ins_coop ";
			$query = pm_yzdb::query($sql);
			$data = pm_yzdb::fetch_all($query);
			return $data;
		}

		public static function edityzapklist($data, $id)
		{
			return pm_yzdb::update('ins_infomanage', $data, "ins_im_id =  '$id' ");
		}

		public static function get_one_yzapk($id)
		{
			$sql = "SELECT * FROM ins_infomanage LEFT JOIN ins_coop ON ins_coop_id = ins_im_coopid	WHERE ins_im_id = '$id' ";
			$data = pm_yzdb::fetch_result($sql);
			return $data;
		}

		public static function add_apktool($data)
		{
			$ret = pm_yzdb::insert('ins_res',$data);
			return $ret;
		}

		public static function count_md5nums($md5, $coopid)
		{
			$sql = "SELECT count('ins_im_id') nums FROM ins_infomanage WHERE ins_im_md5= '$md5' AND ins_im_coopid = '$coopid' ";
			$data = pm_yzdb::result_first($sql);
			return $data;
		}
			

	}//class_end
?>
