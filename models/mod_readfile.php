<?php
/**
* @file mod_readfile.php
* @synopsis  读取文件模块
* @author Yee, <assad2008@sina.com>
* @version 1.0
* @date 2012-04-18 16:38:22
 */

!defined('PATH_ADMIN') && exit('Forbidden');

class mod_readfile
{
	public static function parseexcel($filepath,$source = 'GBK',$target = 'UTF-8')
	{
	  $data = array();
		require PATH_APPLICATION . '/pm_excel_reader.class.php';
		$data = new Spreadsheet_Excel_Reader(); 
		$data->setOutputEncoding('GBK'); 
		@$data->read($filepath);
		$dataarray =array(); 
		for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) 
		{ 
			for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++)
		 	{ 
     		$dataarray[$i][$j] = $data->sheets[0]['cells'][$i][$j]; 
    	} 
		}

		if($source == $target)
		{
			$data = array_slice($dataarray,1);
		}else
		{
		  $dataarray = ck_iconv_deep($source,$target,$dataarray);
		  $data = array_slice($dataarray,1);
		}

		return $data;
	}

	public static function parsetxt($filepath,$source = 'GBK',$target = 'UTF-8')
	{
	  $data = array();
	  $fp = fopen ($filepath, "r");
	  $i = 1;
	  while (!feof ($fp))
	  {
		$buffer = fgets($fp, 4096);
		$arr = explode(',',$buffer);
		$num = count($arr);
		if($num == 1)
		{
			$data[] = $buffer;
		}elseif($num == 2)
		{
		  $data[$i]['iduser'] = $arr[0];
		  $data[$i]['idpwd'] = $arr[1];
		}
		$i++;
	  }
	  if($data)
	  {
	  	return $data;
	  }else
	  {
	  	return false;
	  }
	}
}
?>
