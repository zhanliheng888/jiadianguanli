<?php
namespace app\common\controller;
use think\Controller;
use think\Db;
use think\Session;
/**
 * 
 */
class Base extends Controller{

	public function __construct(){
		Session::start();
		parent::__construct();

	}
	
	public function _initialize(){

		($this->request->method() == 'POST') ? define('IS_POST', true) : define('IS_POST', false);
		define('MODULE_NAME',$this->request->module());  // 当前模块名称
        define('CONTROLLER_NAME',$this->request->controller()); // 当前控制器名称
        define('ACTION_NAME',$this->request->action()); // 当前操作名称


        if (in_array(ACTION_NAME, array('login','loginajax'))) {  //填入不需要验证权限的方法名
        	
        }else{
        	print_r(Session::get('user'));exit();
        	if(Session::get('user.user_id') > 0 ){
        		$this->check_role();//检查管理员菜单操作权限
        	}else{
        		$this->error('请先登陆',url('index.php/index/user/login'));
        	}
        }
	}

	public function check_role(){
		$con = CONTROLLER_NAME;
		$act = ACTION_NAME;

		$uneed_check = array('login');//填入不需要验证权限的方法名

		$role_list = Session::get('user.role_list');

		if ($con == 'Index' || $role_list == 'all') {
			// 后台首页不需要验证  //超级管理员all不需要验证
			return true;
		}elseif (strstr($act, 'ajax' || in_array($act, $uneed_check))) {
			// 所有ajax请求不需要验证,ajax请求控制器名必须包含ajax
			return true;

		}else{

			$menu_list = Db::table('menu')->where('menu_id','in',$role_list)->select(); 

			$menu = '';

			foreach ($menu_list as $key => $value) {
				$menu .= $value['controller'].'@'.$value['action'].',';
			
			}

			$menu_array = explode(',', $menu);

			if (!in_array($con.'@'.$act, $menu_array)) {
				
				$this->error('你没有操作权限,请联系超级管理员分配权限!',url('index/index/index'));
			}

		}

	}
	
}