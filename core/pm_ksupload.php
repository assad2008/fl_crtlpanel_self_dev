<?php
/**
 * Filename: upload.php
 * @abstract 文件上传处理类
 *          
 * @copyright (c) 2011 www.xoyo.com
 * @author 金山软件-KSG网站开发部-范家鹏 fanjiapeng@kingsoft.com
 * @version   2.0  -    2011.7.26
 */

!defined('PATH_ADMIN') && exit('Forbidden');
define ('UPLOAD_FILE_SUCCESS',		   100);    //图片上传成功
define ('UPLOAD_FILE_EMPTY', 		   101);  //上传的附件、文件为空
define ('UPLOAD_FILE_ILLEGAL',  	   102);  //上传文件非法
define ('UPLOAD_FILE_POSTFIX_ILLEGAL', 103);  //上传文件格式非法
define ('UPLOAD_FILE_SIZE_ILLEGAL',    104);  //上传文件大小非法
define ('UPLOAD_FILE_FAILURE',   	   105);  //上传文件失败
define ('UPLOAD_FILE_SIZE_TOOSMALL',   106);  //图片像素太小

class upload {
	//默认配置
	public $_config = array(
			'url'		=> FLASE, 	   //客户端访问地址前缀
			'path'		=> 'upload/',						   //上传物理地址前缀
			'httpsqs'   => FALSE,							   //是否进入HTTPSQS队列
			'fileType'  => 'image',						 	   //类型为图片
			'isCut'     => FALSE,							   //是否需要切图 
			'cutWidth'  => 0,								   //默认等比例缩微切割宽度
			'cutHeight' => 0,								   //默认等比例缩微切割高度
			'allowType' => array(1 => 'gif', 2 => 'jpeg', 
								 3 => 'png', 6 => 'bmp'), 	   //可以上传的图片类型
			'checkType' => TRUE,							   //是否检查图片格式
			'checkSize' => TRUE,							   //是否检查图片大小
			'maxSize'   => 5120000,							   //上传最大值
	);
	
	//getimagesize + $_FILES值
	public  $getData = '';
	//private $backBoolean = FALSE;                              //默认等于假返回值类型
	
	//文件上传配置初始化
	public function __construct ($config = array ()) 
	{
		if (is_array ($config)) 
		{
			foreach ($this->_config as $k => $v) 
			{
				if (isset($config [$k])) 
				{
					$this->_config [$k] = $config [$k];
				}
			}
		}
		unset($config);
	}
	
	//文件原图上传
	public function put($ArraygetFile,$force = true) 
	{
		$this->getData = &$ArraygetFile;
		if (!isset($this->getData['tmp_name']) || !isset($this->getData['name']) || empty($this->getData['tmp_name']) || empty($this->getData['name']) ) 
		{
			return UPLOAD_FILE_EMPTY;
		}
		if (isset ($this->getData['error']) && $this->getData['error'] ) 
		{
			return UPLOAD_FILE_FAILURE;
		}

		if(is_uploaded_file($this->getData['tmp_name'])) 
		{
			$this->checkSafe();
			$this->createNewPath('',$force);
			$this->createDir();
			//图片或者文件都可以上传
			if($this->doUpload()) 
			{
				return $this->getData;
			} else 
			{
				return UPLOAD_FILE_FAILURE;
			}
		}
	}

	/**
	 *    文件上传开始
	 *
	 *    @param  <string> tmp_name
	 *    @param  <string> path, new_path
	 *    @return <boolean>
	 */
	private function doUpload() 
	{//debug($this->_config['path'] . '/' . $this->getData['new_path']);
		if(move_uploaded_file($this->getData['tmp_name'], $this->_config['path'] . '/' . $this->getData['new_path']))
 		{
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 *    判断上传文件大小是否合法
	 *
	 *    @param  <number> size文件大小值
	 *    @return <boolean>
	 */
	private function checkSize() 
	{
		if($this->_config['checkSize'] && $this->getData['size'] > $this->_config['maxSize'])
	 	{
			return FALSE;
		}
		return TRUE;
	}
	
	/**
	 *    取得文件名后缀
	 *
	 *    @param  <string>  name
	 *    @return <string>	返回后缀名称
	 */
	private function getPostfix() 
	{
		return trim(substr(strrchr($this->getData['name'], '.'), 1, 10));
	}
	
	/**
	 *    检查文件是否为规定上传类型
	 *
	 *    @param  <string> name
	 *    @return <boolean>
	 */
	private function checkFormat() 
	{
		if($this->_config['checkType'] && !in_array(strtolower($this->getPostfix()), $this->_config['allowType'])) 
		{
			return FALSE;
		}
		return TRUE;
	} 
	
	/**
	 *    创建分隔目录
	 *
	 *    @param  <string> path配置目录, new_path完整目录
	 */
	private function createDir() 
	{
		if (!file_exists($path = dirname($this->_config['path'] .'/'. $this->getData['new_path']))) 
		{
			if(!@mkdir($path, 0777, TRUE)) 
			{
				return FALSE;
			}
		}
	}
	
	/**
	 *    生成 force为真则以年月日方式生成，否则不
	 *
	 *    @param  <string> new_name, new_dir, new_path
	 *    @return <string> new_dir, new_path
	 */
	private function createNewPath($forder = '', $force = TRUE) 
	{
		if(!$force)
		{	
			$this->getData['new_dir'] = '/';
			$this->getData['new_path'] = $this->getData['new_dir'] . $this->getData['name'];
		}else
		{
			$time = time();
			$this->getData['new_name'] = md5($time . rand(1, 100)). '.' . strtolower($this->getPostfix());
			$force && $path = date('Ym', $time) . '/';
			$forder && $path = $forder . $path;
		
			$this->getData['new_dir'] = $path . substr($this->getData['new_name'], 0, 2) . '/';
			$this->getData['new_path'] = $this->getData['new_dir'] . $this->getData['new_name'];
		}
	}

	/**
	 *    图片有效性检测
	 *
	 * 	  @param  <string> fullPath 完整全路径地址
	 *    @return  <string> width图片宽度, height图片高度, img_type_number图片数字类型
	 */
	private function checkSafe($fullPath = '') 
	{
		if(!$this->checkFormat()) 
		{
			return UPLOAD_FILE_POSTFIX_ILLEGAL;
		}
		if(!$this->checkSize()) 
		{
			return UPLOAD_FILE_SIZE_ILLEGAL;
		}
		if ($this->_config['fileType'] == 'image') 
		{
			list($this->getData['width'], $this->getData['height'], $this->getData['img_type_number']) = getimagesize($fullPath ? $fullPath : $this->getData['tmp_name']);
			if(!in_array($this->getData['img_type_number'], @array_keys($this->_config['allowType']))) 
			{
				return UPLOAD_FILE_POSTFIX_ILLEGAL;
			}
		}
	}
		
	/**
     *    生成缩微图thumb
	 *
	 *    @param  <string> $oldThumbName  原始文件名 201107/08/08b44de34eb6f5460f374f8bd418b9bd.gif
	 *    @param  <string> $newThumbName  新文件名   201107/08/08b44de34eb6f5460f374f8bd418b9bd_thumb.gif
	 *    @param  <number> $imgTypeNumber 图片数字   gif=>1, jpeg=>2, png=>3 其它类型请自行添加
	 
	 * 注： 多切只需要传文件名即可
	 * @param newThumbName 新文件名  201107/08/08b44de34eb6f5460f374f8bd418b9bd_thumb2.gif
	 *
     */
	public function putThumb($newThumbName = 'thumb', $oldThumbName = '', $imgTypeNumber = '') 
	{
		$oldThumbName = $oldThumbName ? $oldThumbName : $this->getData['new_path'];
		$fullPath = $this->_config['path'] .'/'. $oldThumbName;
		$width = $this->_config['cutWidth']; $height = $this->_config['cutHeight'];

		if($oldThumbName && $this->_config['fileType'] == 'image' && $this->_config['isCut'] && $this->_config['cutWidth'] && $this->_config['cutHeight'] && @file_exists($fullPath)) 
		{
			if(!$this->getData['width'] || !$this->getData['height']) 
			{
				$this->checkSafe($fullPath);
			}

			$imgTypeNumber = $imgTypeNumber > 0 ? $imgTypeNumber : $this->getData['img_type_number'];
			if(!in_array($imgTypeNumber, @array_keys($this->_config['allowType']))) 
			{
				return UPLOAD_FILE_POSTFIX_ILLEGAL;
			}

			switch($imgTypeNumber) 
			{
				case 1:
					$im = imagecreatefromgif($fullPath); //以gif的格式取得图片
				break;
				case 2:
					$im = imagecreatefromjpeg($fullPath); //以jpeg的格式取得图片
				break;
				case 3:
					$im = imagecreatefrompng($fullPath); //以jpeg的格式取得图片
				break;
			}
			
			if(!$im) 
			{
				return FALSE;
			}
			
			$this->_config['cutWidth'] > $this->getData['width'] && $this->_config['cutWidth'] =  $this->getData['width'];
			$this->_config['cutHeight'] > $this->getData['height'] && $this->_config['cutHeight'] = $this->getData['height'];
			if ($this->_config['cutWidth'] && ($this->getData['width'] < $this->getData['height'])) 
			{
    			$width = ($this->_config['cutHeight'] / $this->getData['height']) * $this->getData['width'];
			} else 
			{
    			$height = ($this->_config['cutWidth'] / $this->getData['width']) * $this->getData['height'];
			}

			if (function_exists("imagecreatetruecolor")) 
			{
				$new = imagecreatetruecolor($width, $height);
				$imgTypeNumber == 3 && $this->transparent($new);
				imagecopyresampled($new, $im, 0, 0, 0, 0, $width, $height, $this->getData['width'], $this->getData['height']);
			} else 
			{
				$new = imagecreate($width, $height);
				$imgTypeNumber == 3 && $this->transparent($new);
				imagecopyresized($new, $im, 0, 0, 0, 0, $width, $height, $this->getData['width'], $this->getData['height']);
			}

			$oldThumbNameThumb = trim(substr($oldThumbName, 0, @strrpos($oldThumbName, '.'))) . '_' . $newThumbName;
			
			switch ($imgTypeNumber)
		 	{
				case 1:
					imagegif($new, $this->_config['path'] . $oldThumbNameThumb . '.gif');
				break;
				case 2:
					imagejpeg($new, $this->_config['path'] . $oldThumbNameThumb . '.jpg', 100);
				break;
				case 3:
					imagepng($new, $this->_config['path'] . $oldThumbNameThumb . '.png');
				break;
			}

			imagedestroy($new); imagedestroy($im);
			unset($oldThumbName, $newThumbName, $imgTypeNumber, $newDir, $oldThumbNameThumb, $fullPath, $width, $height);
			return TRUE;
		}

		return FALSE;
	}

	/**
	 *    PNG透明背景图片处理
	 *
	 * 	  @param  <resource> new 资源
	 *	  @param  <int> transparent 如果分配失败则返回 FALSE
	 */	
	private function transparent($new) 
	{
		$transparent = imagecolorallocatealpha($new , 0 , 0 , 0 , 127);
		imagealphablending($new , false);
		imagefill($new , 0 , 0 , $transparent);
		imagesavealpha($new , true);
	}
}
