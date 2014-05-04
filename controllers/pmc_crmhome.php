<?php
/*=============================================================================
FileName: pmc_crmhome.php
Description:
Author: Tian - tianpengjun@feiliu.com
Created Time: 2014/2/25 10:34:30
Last modified: 2014-02-25 17:31:16
Version:  1.0
=============================================================================*/
class pmc_crmhome
{

    public function index()
    {
       Cookie('cur_pid',CUS_MANAGE);
        pm_tpl::display('crmhome_index');
    }

    public function menu()
    {
     $menulist =  mod_menu::menulistbypandr();
        pm_tpl::assign('menus',$menulist);
        pm_tpl::display('cphome_menu');
    }

}
