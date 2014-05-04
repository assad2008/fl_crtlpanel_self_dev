<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require( APPPATH.'libraries/phpexcel/PHPExcel.php' );
class php_excel
{
	public $obj;
	function __construct()
	{
		$this->obj = new PHPExcel();
	}

	public function set($author = 'Windows',$subject = 'Excel Subject',$title = 'Excel Title')
	{
		$this->obj->getProperties()->setCreator($author)
							 ->setLastModifiedBy($author)
							 ->setTitle($title)
							 ->setSubject($subject)
							 ->setDescription($subject)
							 ->setKeywords("Excel")
							 ->setCategory("Assad");
	}

	public function setcell($array = array(),$sheet = 0)
	{
		$r = $this->obj->setActiveSheetIndex($sheet);
		$celli = 65;
		foreach($array AS $v)
		{
			$r->setCellValue(chr($celli).'1', $v);
			$celli++;
		}
	}

	public function setwdith($array = array(),$sheet = 0)
	{
		if(!$array) return;
		$objActSheet = $this->obj->getActiveSheet($sheet);
		foreach($array AS $v)
		{
			$objActSheet->getColumnDimension($v['cell'])->setWidth($v['wdith']);
		}
	}

	public function make($data,$sheet = 0)
	{
		$i = 2;
		foreach($data AS $v)
		{
			$R = $this->obj->setActiveSheetIndex($sheet);
			$celli = 65;
			foreach($v AS $v1)
			{
				$R->setCellValue(chr($celli).$i, $v1);
				$celli++;	
			}
			$i++;	
		}
	}

	//public function output($filename = 'default')
	public function output($filename = 'default', $sheet= 0)
	{
		$this->obj->getActiveSheet()->setTitle('FeiLiu'.$date.'Excel');
		$this->obj->setActiveSheetIndex($sheet);
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename=Feiliu_'.$filename.'record.xls');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($this->obj, 'Excel5');
		$objWriter->save('php://output');
		exit();
	}
}
