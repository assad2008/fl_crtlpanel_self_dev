<?php
	class pmc_crmtest{
		function getAnswerList(){
			$tlist = mod_crmmanage::getqtype();
            foreach ( $tlist as $key => $value) {
                $newtype[$key+1]=$value;
            }
            $newtype = genTree($newtype);
			//$answer_id=mod_crmtest::getAnswerId();
			//debug($alist['answer_id']);
			$alist=mod_crmtest::getanswerlist();
			//debug($alist);
			//$a=mb_substr($alist['3']['content'],0,2);
			pm_tpl::assign("ser_tlist",$newtype);
			pm_tpl::assign("alist",$alist);
			pm_tpl::display('crmtest_getanswerlist');
		}
	}
?>