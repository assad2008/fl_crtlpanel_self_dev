<?php
/**
 * PHPExcel  导出功能
 * 
 **/
/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** Include PHPExcel */

require_once PATH_LIB.'/Excelclasses/PHPExcel.php';


class   php_excelexport{

		function __construct(){
		}

    // Create new PHPExcel object   创建一个excel对象
        function  create_excel($data,$title,$filename=''){
            $objPHPExcel = new PHPExcel();
            self::excelexport($objPHPExcel,$data,$title,$filename);
        }

		// Set document properties   设置文档属性
		function excelexport($objPHPExcel,$data,$title,$filename){
			$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
										 ->setLastModifiedBy("Maarten Balliauw")
										 ->setTitle("PHPExcel Test Document")
										 ->setSubject("PHPExcel Test Document")
										 ->setDescription("Test document for PHPExcel, generated using PHP classes.")
										 ->setKeywords("office PHPExcel php")
										 ->setCategory("Test result file");
			self::adddata($objPHPExcel,$data,$title,$filename);
		}		 

		// Add some data  添加一些数据
		function adddata($objPHPExcel,$data,$title,$sheet_filename){
			
			$alphabet = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
			
			//设置表头
			$title_str="";
			$i=0;
			foreach($title as $v){
				$str = "->setCellValue('".$alphabet[$i++]."1', '".$v."')  ";
				$title_str .= $str;
			}
			//$objPHPExcel 是个excel对象
			$obj_str ="$"."objPHPExcel->setActiveSheetIndex(0)".$title_str.";";
			$save_obj_str="<?php?>";
			$save_obj_str="<?php $obj_str ?>";
			$filename = PATH_LIB.'/obj_str.txt';
			
			
			// 写入的字符(表头)
			file_put_contents($filename,$save_obj_str);
			include PATH_LIB.'/obj_str.txt';

			//设置表数据
			$data_str = '';
			for($k=0;$k<count($data);$k++){
					$col=0;
					foreach($data[$k] as $val){
							$line = $k+2;
							$str_data = "->setCellValue('$alphabet[$col]$line','$val')";
							$data_str .= $str_data;
							$col++;
					}
			}
			$obj_strfordata ="$"."objPHPExcel->setActiveSheetIndex(0)".$data_str.";";
			$save_obj_str="<?php?>";
			$save_obj_str="<?php $obj_strfordata ?>";
			$filename =PATH_LIB .'/obj_str.txt';
			
			file_put_contents($filename,$save_obj_str);
			include PATH_LIB.'/obj_str.txt';

			self::renamesheet($objPHPExcel,$sheet_filename);
		}
		
		
		// Rename worksheet    重命名文件
		function renamesheet($objPHPExcel,$filename){
            //$WorkSheetName = 'worksheet'.date('YmdHis',time());
			$filename?$WorkSheetName = $filename:$WorkSheetName='sheet'.date('Ymd',time());
			$objPHPExcel->getActiveSheet()->setTitle($WorkSheetName);
			self::createsheet($objPHPExcel,$WorkSheetName);
            //echo $WorkSheetName.'2ww';die;

		}
			
		//生成文件
		function createsheet($objPHPExcel,$WorkSheetName){

			// 设置活动板指数的第一页，这样Excel打开这个作为第一片
			$objPHPExcel->setActiveSheetIndex(0);

			// Save Excel 2007 file    保存文件
			$callStartTime = microtime(true);

            $file = PATH_LIB.'/excel/'.$WorkSheetName.'.xlsx';
            //$file = getcwd().'/'.$WorkSheetName.'.xlsx';

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			//$objWriter->save(PATH_LIB.'/excel/'.$WorkSheetName.'.xlsx');
            $objWriter->save($file);
            $callEndTime = microtime(true);
			$callTime = $callEndTime - $callStartTime;

			//echo date('H:i:s') , " File written to " , $WorkSheetName , EOL;
			/*
				//输出写入文件花费的时间（秒）
				echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
			*/

			/*
				//输出内存使用情况
				echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;
			*/
			// Save Excel 95 file  保存文件
			//echo date('H:i:s') , " Write to Excel5 format" , EOL;
			$callStartTime = microtime(true);

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save($file);
			$callEndTime = microtime(true);
			$callTime = $callEndTime - $callStartTime;
			//echo '创建成功 ' , $file, EOL;
		}

		
		//输出生成文件详情（生成时间，内存使用情况）	
		function echo_detail(){
				
				echo date('H:i:s') , " File written to " , $WorkSheetName , EOL;
				echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
				// Echo memory usage
				echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;

				// Echo memory peak usage
				echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;

				// Echo done  生成完成
				echo date('H:i:s') , " Done writing files" , EOL;
				echo 'Files have been created in ' , getcwd() , EOL;
		}
}

/*
范例

$title = array('actuer'=>'活跃','newadd'=>'新增');
$data = array(array('actuser'=>10234,'newadd'=>'87%'),array('actuser'=>10234,'newadd'=>'87%'),array('actuser'=>10234,'newadd'=>'87%'));
$fliename = 'exceltest';  //注意名字中不能有特殊符号（如：2013-12-13是错的  20131213正确的）
new Excel_export($data,$title,$filename);
*/



