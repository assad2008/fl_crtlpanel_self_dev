<?php
/**
* @file pmc_dsgserver.php
* @synopsis  斗三国服务端交互
* @author Yee, <rlk002@gmail.com>
* @version 1.0
* @date 2013-08-06 14:58:57
*/


class pm_dsgserver
{
	public $xmlpath;
	public $basexml;
	public $itemxml;
	public $xmlarr = array();
	public $host;
	public $port;
	public $ctype = 'tcp';
	public $handler = null;	

	function __construct($host = '101.226.142.116',$port = 9091)
	{
		$this->host = $host;
		$this->port = $port;
	}

	public function sock()
	{
		$fp = @fsockopen("{$this->ctype}://{$this->host}", $this->port, $errno, $errstr, 30);
		if(!$fp)
		{
			echo "$errstr ({$errno})";
		}else
		{
			$this->handler = $fp;
		}
	}

	public function dissock()
	{
		@fclose($this->handler);
	}

	public function send($data)
	{
		@fwrite($this->handler,$data);
	}

	public function get()
	{
		$ret = @fread($this->handler, 2048);
		return $ret;
	}

	public function dpack()
	{

	}

	public function dunpack()
	{

	}

	public function packheader($op,$len)
	{
		$ret = pack('n2N3',0x1011,1,$len,$op,0);
		echo 'headerlen:' . strlen($ret) . '<br />';
		return $ret;
	}

	public function packgetuser($type,$user)
	{
		$str = '';
		$str = pack('N2',$type,$user);
		$packlen = strlen($str);
		echo 'packlen:' . $packlen . '<br />';
		$header = $this->packheader(101,$packlen);
		return $header.$str;
	}

	function __destruct()
	{
		$this->dissock();
	}
}
