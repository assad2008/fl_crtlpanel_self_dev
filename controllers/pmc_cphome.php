<?php
/**
* @file pmc_cphome.php
* @synopsis  DATA INDEX
* @author Yee, <rlk002@gmail.com>
* @version 1.0
* @date 2013-07-22 15:30:07
*/

class pmc_cphome
{
    public $pid;
    function __construct()
    {
        $this->pid = string2int($_GET['id']);
    }

    public function index()
    {
        pm_tpl::display('cphome_index');
    }

    public function menu()
    {
        $menus = mod_menu::getmenulist();
        pm_tpl::assign('menus',$menus);
        pm_tpl::display('cphome_menu');
    }

}
