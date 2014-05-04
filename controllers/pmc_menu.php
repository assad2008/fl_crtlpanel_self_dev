<?php
    /**
     * @file pmc_menu.php
     * @synopsis  产品后台 目录管理
     * @author Yee, <assad2008@sina.com>
     * @version 1.0
     * @date 2012-04-22 14:23:44
     */

    !defined('PATH_ADMIN') && exit('Forbidden');
    class pmc_menu
        {
        public $rightuserallow;
        function __construct()
            {
            $this->rightuserallow = array('1','11','249');
            }

        public function menulist()
            {
            $list = mod_menu::get_menu_list();
            $action_link = array('href' => '?c=menu&a=addmenu','text' => '添加菜单');
            pm_tpl::assign('full_page',1);
            pm_tpl::assign('ur_here','菜单管理');
            pm_tpl::assign('action_link',$action_link);
            pm_tpl::assign('menulist',$list);
            pm_tpl::display('menu_menulist');
            }

        public function addmenu()
            {
            if($_POST['submit'])
                {
                try
                    {
                    $data = array();
                    $data['parent_id'] = $_POST['parent_id'];
                    $data['menu_name'] = $_POST['menu_name'];
                    $data['act_url'] = $_POST['act_url'];
                    $data['actioncode'] = $_POST['actioncode'];
                    $data['level'] = $_POST['level'];
                    $data['sort'] = $_POST['sort'];
                    if($data['parent_id'] == 0 && $data['level'] != 1)
                        {
                        mod_login::message('菜单级别和父级选择不正确');
                        }
                    if($data['level'] < 2 && $data['act_url'])
                        {
                        mod_login::message('一级，二级菜单不应该有对应URL');
                        }
                    $data['is_show'] = $_POST['is_show'];
                    $data['addtime'] = time();
                    $data['adduser'] = USERNAME;
                    $data['sort'] = $_POST['sort'] ? $_POST['sort'] : 255;
                    $data['level'] = $data['level'] == 2 ? 3 : $data['level'];
                    mod_menu::addmenu($data);
                    mod_login::message("添加菜单成功",'?c=menu&a=menulist');
                    }
                catch ( Exception $e)
                    {
                    mod_login::message($e->getMessage());
                    }
                }else
                    {
                    $parentmenu = mod_menu::get_parent_menu();
                    $parentmenustr = '';
                    $parentmenustr = '[';
                    foreach($parentmenu AS $v)
                        {
                        $parentmenustr .= "[{$v['menu_id']},'{$v['menu_name']}'],";
                        }
                    $parentmenustr = substr($parentmenustr,0,-1);
                    $parentmenustr .= ']';
                    pm_tpl::assign('parent_menu',$parentmenustr);
                    $action_link = array('href' => '?c=menu&a=menulist','text' => '菜单管理');
                    pm_tpl::assign('action_link',$action_link);
                    pm_tpl::assign('ur_here','添加菜单');
                    pm_tpl::display('menu_addmenu');
                    }
            }
        public function edit_pro_menu()
            {
            $pid= $_GET['pid'];
            $type=$_GET['type'];
            $mid=$_GET['mid'];
            mod_menu::edit_pro_menu($mid,$pid,$type);

            }

        public function editmenu()
            {
            $menu_id = $_GET['mid'];
            $menuinfo = mod_menu::get_one_menu($menu_id);
            if(!$menuinfo)
                {
                mod_login::message('对不起，该目录不存在');
                }
            if($_POST['submit'])
                {
                try
                    {
                    $data = array();
                    $data['parent_id'] = $_POST['parent_id'];
                    $data['menu_name'] = $_POST['menu_name'];
                    $data['act_url'] = $_POST['act_url'];
                    $data['actioncode'] = $_POST['actioncode'];
                    $data['is_show'] = $_POST['is_show'];
                    $data['sort'] = $_POST['sort'];
                    $data['level'] = $_POST['level'];
                    if($data['parent_id'] == 0 && $data['level'] != 1)
                        {
                        mod_login::message('菜单级别和父级选择不正确');
                        }
                    if($data['level'] < 2 && $data['act_url'])
                        {
                        mod_login::message('一级，二级菜单不应该有对应URL');
                        }
                    $data['level'] = $data['level'] == 2 ? 3 : $data['level'];
                    $ret = mod_menu::editmenu($data,$menu_id);
                    if($ret)
                        {
                        mod_login::message("编辑菜单成功",'?c=menu&a=menulist');
                        }else
                        {
                        mod_login::message("编辑菜单失败");
                        }
                    }
                catch ( Exception $e)
                    {
                    mod_login::message($e->getMessage());
                    }
                }else
                    {
                    $menuinfo = mod_menu::get_one_menu($menu_id);
                    //print_r($menuinfo);
                    $parentmenu = mod_menu::get_parent_menu();

                    $products=mod_menu::get_products();


                    $topmenu =array(
                        array(
                            'pid' => 8888888,
                            'pname' => '~客服管理~'
                        ),
                        array(
                            'pid' =>9999999,
                            'pname' => '~产品汇总~'
                        )
                    );
                    $products = array_merge($products,$topmenu);
                    $checked=mod_menu::get_proid($menu_id);
                    if (is_array($checked)) {
                        foreach ($products as &$value) {
                            foreach ($checked as $v) {
                                if ($value['pid']==$v['product_id']) {
                                    $value['checked']=1;
                                }
                            }
                        }
                    }

                    $parentmenustr = '';
                    $parentmenustr = '[';
                    foreach($parentmenu AS $v)
                        {
                        $parentmenustr .= "[{$v['menu_id']},'{$v['menu_name']}'],";
                        }
                    $parentmenustr = substr($parentmenustr,0,-1);
                    $parentmenustr .= ']';
                    pm_tpl::assign('parent_menu',$parentmenustr);
                    pm_tpl::assign('menu',$menuinfo);
                    $action_link = array('href' => '?c=menu&a=menulist','text' => '菜单管理');
                    pm_tpl::assign('action_link',$action_link);
                    pm_tpl::assign('ur_here','编辑菜单');
                    pm_tpl::assign('products',$products);
                    pm_tpl::assign('prolist',$checked);
                    pm_tpl::display('menu_editmenu');
                    }
            }

        public function delmenu()
            {
            $menu_id = $_GET['mid'];
            $menuinfo = mod_menu::get_one_menu($menu_id);
            if(!$menuinfo)
                {
                mod_login::message('对不起，该目录不存在');
                }
            if($menuinfo['parent_id'] == 0)
                {
                $sons = mod_menu::get_son_menu($menu_id);
                if($sons)
                    {
                    mod_login::message('请先删除子目录');
                    }
                }
            mod_menu::delmenu($menu_id);
            mod_login::message('删除目录成功');
            }

        public function rightlist()
            {
            $rightlist = mod_menu::get_user_action();
            $prl = mod_menu::get_parent_right();
            pm_tpl::assign('prl',$prl);
            pm_tpl::assign('priv_arr',$rightlist);
            $action_link = array('href' => '?c=menu&a=addright','text' => '添加权限');
            pm_tpl::assign('action_link',$action_link);
            pm_tpl::assign('ur_here','权限列表');
            pm_tpl::display('menu_rightlist');
            }

        public function addright()
            {
            if(!in_array(ADMINUSERID,$this->rightuserallow))
                {
                mod_login::message('抱歉，您无权添加权限记录');
                }
            if($_POST['submit'])
                {
                if(!$_POST['action_name'])
                    {
                    mod_login::message('请输入权限名称');
                    }
                if($_POST['parent_id'] != 0 && !$_POST['action_code'])
                    {
                    mod_login::message('请输入权限操作码');
                    }
                $post = $_POST;
                unset($post['submit']);
                mod_menu::addright($post);
                mod_login::message('添加权限成功','?c=menu&a=rightlist');
                }else
                    {
                    $prl = mod_menu::get_parent_right();
                    pm_tpl::assign('prl',$prl);
                    pm_tpl::assign('ur_here','添加权限');
                    $action_link = array('href' => '?c=menu&a=rightlist','text' => '返回列表');
                    pm_tpl::assign('action_link',$action_link);
                    pm_tpl::display('menu_addright');
                    }
            }

        public function editright()
            {
            if(!in_array(ADMINUSERID,$this->rightuserallow))
                {
                mod_login::message('抱歉，您无权编辑权限记录');
                }
            $action_id = $_GET['id'];
            if(!$action_id)
                {
                mod_login::message('请选择要编辑的权限记录');
                }
            if($_POST['submit'])
                {
                $params = $_POST;
                unset($params['submit']);
                mod_menu::editright($params,$action_id);
                mod_login::message('编辑权限记录成功','?c=menu&a=rightlist');
                }else
                {
                $info = mod_menu::get_one_right($action_id);
                $prl = mod_menu::get_parent_right();
                $action_link = array('href' => '?c=menu&a=rightlist','text' => '返回列表');
                pm_tpl::assign('action_link',$action_link);
                pm_tpl::assign('prl',$prl);
                pm_tpl::assign('info',$info);
                pm_tpl::assign('ur_here','编辑权限');
                pm_tpl::display('menu_editright');
                }
            }

        public function pldelright()
            {
            if(!in_array(ADMINUSERID,$this->rightuserallow))
                {
                mod_login::message('抱歉，您无权删除权限记录');
                }
            if($_POST['submit'])
                {
                $post = $_POST;
                $delparams = $post['action_code'];
                if(!$delparams)
                    {
                    mod_login::message('未选择任何权限记录');
                    }
                mod_menu::pldel_rights($delparams);
                mod_login::message('批量删除权限成功');
                }else
                    {
                    mod_login::message('非法提交');
                    }
            }
        }
