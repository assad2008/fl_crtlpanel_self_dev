<?php
!defined('PATH_ADMIN') && exit('Forbidden');

class rdupload
{
/*
******************************************************************************************
*基本使用方法:
* $file = new cupload;
*$file->max_size = 100 * 1024; //       //限制文件大小 字节
*$file->allow_type="gif/jpg/bmp/png"; //允许上传的文件类型
*$file->input_name="userfile";           //表单中的文本域名称
*$file->save_path="../uploadfile/";      //保存路径
*$file->rand_name=true;                   //随机文件名 (默认)
*if($file->save()) echo "ok"                 //保存文件，成功返回 true
*******************************************************************************************
*/
//变量设置
var $max_size = 102400; //允许上传大小，以表单中的MAX_FILE_SIZE优先
var $time_out = 120;    //脚本超时
var $allow_type =array(); //允许上传的文件类型
var $input_name=""; //文件域的名称
var $save_path=""; //保存路径
var $reset_name=""; //重新设置文件名 (优先 rand_name)
var $rand_name=true;//随机文件名

//回传变量
var $file_name=""; //客户端上传的文件名
var $file_type=""; //文件类型
var $file_size=0; //大小
var $file_tmp_name="";//服务端临时文件
var $file_error_txt="";//错误提示
var $file_ext="";//扩展名
var $file_upload_path="";//最终的上传文件路径 含文件名 如: uploadfile/filename.rar
var $file_save_path = '';
var $file_info_array;
//所有允许上传的文件类型
var $allow_ext_type = array( //允许上传的扩展名和对应的文件类型
   "avi" => "video/x-msvideo",
   "asf" => "video/x-ms-asf",
   "bmp" => "image/bmp",
   "css" => "text/css",
   "gif" => "image/gif",
   "htm" => "text/html",
   "html" =>"text/html",
   "txt" => "text/plain",
   "jpg" => "image/pjpeg",
   "jpeg" => "image/pjpeg",
   "mp3" => "audio/mpeg",
   "pdf" => "application/pdf",
   "png" => "image/x-png",
   "zip" => "application/zip",
   "rar" => "application/octet-stream",
   "doc" => "application/octet-stream",
   "xls" => "application/vnd.ms-excel",
	 
);

//上传图片默认配置
function img_config()
{
   global $gupload_img_type;
   global $gupload_img_size;
   $this->max_size = $gupload_img_size;
   $this->allow_type=$gupload_img_type;
   $this->save_path=SITE_PATH."files/uploadfile/".date("Y-m-d")."/";
   if(!is_dir($this->save_path)) 
    if(!$this->make_dir($this->save_path))
     die("can't crate folders: ".$this->save_path);
}
//上传附件默认配置
function file_config($subdir="")
{
   global $gupload_file_type;
   global $gupload_file_size;
   global $gupload_file_dir;
   $this->max_size = $gupload_file_size;
   $this->allow_type=$gupload_file_type;
   if(empty($subdir))
    $this->save_path=SITE_PATH."files/".$gupload_file_dir."/".date("Y-m-d")."/";
   else
    $this->save_path=SITE_PATH."files/".$gupload_file_dir."/".$subdir."/";
   if(!is_dir($this->save_path)) 
    if(!$this->make_dir($this->save_path))
     die("can't crate folders: ".$this->save_path);
}

//保存文件
/* 至少要设置以下两个变量
var $input_name=""; //文件域的名称
var $save_path=""; //保存路径 
*/
function save()
{
   //获取文件的信息
   if(!$this->get())
		 return false;
   //是否有文件的保存路径
   if(empty($this->save_path))
   {
    $this->file_error_txt="please set the save file path"; return false;
   }
   $this->save_path = str_replace("\\","/",$this->save_path);
   if(substr($this->save_path,strlen($this->save_path)-1,1)!="/")
    $this->save_path = $this->save_path."/";
   //保存: 以实际文件名保存
   if(empty($this->reset_name) && !$this->rand_name)
   {
    $newf = $this->save_path . $this->file_name;
    $this->file_upload_path = $newf;
    if(move_uploaded_file($this->file_tmp_name, $newf))
     return true;
    else
    {$this->file_error_txt="uploaded error."; return false;}
   }
   //保存:随机文件名
   if(empty($this->reset_name) && $this->rand_name)
	 {
		$filerandnames = "file_". $this->get_rand_str(15) . ".". $this->file_ext;
    $newf = $this->save_path .$filerandnames;
		$this->file_upload_path = $newf;
		$this->file_save_path = $filerandnames;
    if(move_uploaded_file($this->file_tmp_name, $newf))
     return true;
    else
    {$this->file_error_txt="uploaded error."; return false;}
   }
   //保存:以重新设置的文件名
   if(!empty($this->reset_name))
   {
    $newf = $this->save_path . $this->reset_name .".". $this->file_ext;
    $this->file_upload_path = $newf;
    if(move_uploaded_file($this->file_tmp_name, $newf))
     return true;
    else
    {$this->file_error_txt="uploaded error."; return false;}
   }
   //完成.
}

//获得并判断上传文件的信息
function get()
{
   if(empty($this->input_name))
	 { $this->file_error_txt="please set the file inputname"; return false; }
   if((int)$this->time_out>0)
    set_time_limit($this->time_out);
   //上传的文件信息
	 $fobj = $_FILES[$this->input_name];
   $this->file_info_array = $fobj;
   $this->file_name = $fobj["name"];
	 $this->file_type = $fobj["type"];
   $this->file_size = $fobj["size"];
   $this->file_tmp_name=$fobj["tmp_name"];
   $file_error = $fobj["error"];
  
   //判断文件错误代码
   if(!$this->check_error_code($file_error))
    return false;
   
   //判断文件是否是通过 HTTP POST 上传的
   if(!is_uploaded_file($this->file_tmp_name))
   {
    $this->file_error_txt="Please upload file from HTTP POST method.";
    return false;
   }

   //检测文件大小
   if($this->file_size > $this->max_size)
   {
    $this->file_error_txt="The uploaded file exceeds the max_size:".(int)($this->max_size/1024)."KB";
    return false;
   }
  
   //检测文件是否有名
   if(empty($this->file_name))
   {
    $this->file_error_txt="need file name.";
    return false;
   }
   //检测文件的扩展名
   $ext = strpos($this->file_name, ".");
	 if($ext==false){$this->file_error_txt="file ext is wrong"; return false;}
	 $ext = strtolower((string)substr($this->file_name, $ext+1, strlen($this->file_name)-$ext));
   	$this->file_ext = $ext;
   $hh = in_array($ext,$this->allow_type);
   if(!in_array($ext,$this->allow_type))
   {
    $this->file_error_txt="*.".$ext." is not allowed to upload"; return false;
	 }

   //检测文件类型
   if(!isset($this->allow_ext_type[$ext]))
   {
    $this->file_error_txt="the file type of *.".$ext." not allowed";
    return false;
   }
   /*else
   {
    $tpe = $this->file_type;
    if($ext == "jpg" ) $tpe = "image/jpeg";
    if($this->allow_ext_type[$ext] != $this->file_type && $this->allow_ext_type[$ext] !=$tpe)
    {
     $this->file_error_txt="the type of .".$ext." not match type:".$this->allow_ext_type[$ext];
     return false;
    }
	 }*/
   //基本上可以上传
   return true;
}

//判断错误代码
function check_error_code($code)
{
   switch($code)
   {
    case 0:
     $this->file_error_txt="";
     return true;
     break;
    case 1:
     $this->file_error_txt="The uploaded file exceeds the upload_max_filesize directive in php.ini.";
     return false;
     break;
    case 2:
     $this->file_error_txt="The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.";
     return false;
     break;
    case 3:
     $this->file_error_txt="The uploaded file was only partially uploaded.";
     return false;
     break;
    case 4:
     $this->file_error_txt="No file was uploaded.";
     return false;
     break;
    case 6:
     $this->file_error_txt="Missing a temporary folder.";
     return false;
     break;
    case 7:
     $this->file_error_txt="Failed to write file to disk.";
     return false;
     break;
    case 8:
     $this->file_error_txt="File upload stopped by extension.";
     return false;
     break;
    default:
     $this->file_error_txt="unknow error.";
     return false;
   } 
}

//得到随机字符串
function get_rand_str($length) {
   $hash="";
   $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
   $max = strlen($chars) - 1;
   mt_srand((double)microtime() * 1000000);
   for($i = 0; $i < $length; $i++) {
    $hash .= $chars[mt_rand(0, $max)];
   }
   return $hash;
}
//建立多层目录
function make_dir($dir, $mode = "0777"){ 
     if( ! $dir ) return false; 
     $dir = str_replace( "\\", "/", $dir ); 
     $mdir = ""; 
     foreach( explode( "/", $dir ) as $val ) { 
         $mdir .= $val."/"; 
         if( $val == ".." || $val == "." || trim( $val ) == "" ) continue; 
         if(!file_exists( $mdir ) ){ 
             if(!@mkdir($mdir, $mode)){ 
               return false; 
             } 
         } 
     } 
     return true; 
} 
}
?>
