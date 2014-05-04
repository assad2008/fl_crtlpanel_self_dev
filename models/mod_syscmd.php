<?php
	!defined('PATH_ADMIN') && exit('Forbidden');
	class mod_syscmd
	{
		public static function my_exec($cmd, $input='') 
		{
			$proc = proc_open($cmd, array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes); 
			fwrite($pipes[0], $input);
			fclose($pipes[0]); 
			$stdout = stream_get_contents($pipes[1]);
			fclose($pipes[1]); 
			$stderr = stream_get_contents($pipes[2]);
			fclose($pipes[2]); 
  		$rtn = proc_close($proc); 
 	 		return array('stdout' => $stdout, 
               'stderr' => $stderr, 
               'return' => $rtn 
               ); 
		}

		public static function pres($cmd)  //解析结果
		{
			$res =  self::my_exec($cmd, $input = '');
			$res = $res['stdout'];
			$arr = explode(' ',$res);
			$rarr = array();
			foreach($arr AS $v)
			{
				if($v > 0)
				{
					$rarr[] = $v;
				}
			}
			return $rarr;
		}

		// mod_syscmd::runcmdnowait("/usr/local/php/bin/php " PATH_ADMIN . "/script/makeyhm.php {$num}");
		public static function rumcmdnowait($cmd)
		{
			return pclose(popen($cmd, 'r'));
		}

		public static function rumcmd($cmd)
		{
			passthru($cmd);
		}
	}
