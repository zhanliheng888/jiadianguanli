<?php
namespace app\index\controller;
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


        if (in_array(ACTION_NAME, array('login','logout'))) {  //填入不需要验证权限的方法名
        	
        }else{
        	if(Session::get('user.user_id') > 0 ){
        		$this->check_role();//检查管理员菜单操作权限
        	}else{
        		$this->error('请先登陆',url('index.php/index/user/login'));
        	}
        }

        $admin = Session::get('user');
        if (!empty($admin)) {
           
        
        $condition = $admin['role_list'];
        // 获取角色权限 当角色权限为all为最高管理员 拥有所有权限.将menu表中所有的控制器以及方法循环拼凑为三维数组;
        // p($admin);exit();
        if ($condition == 'all' ) {
            $menu = Db::table('menu')->where('parent_id = 0')->select();
            //先获取所有parent_id为0的 只有parent_id为0的才是控制器;
            // p($menu);exit();
            $role_list = [];
            // 循环所有控制器,将所有控制器下面的方法名查出来,以控制器menu_id为下属方法的parent_id为条件
            foreach ($menu as $k => $v) {
                $role_list[$k]['name'] = $v['name'];
                $menu1 = Db::table('menu')->where('parent_id = '.$v['menu_id'])->select();
                //再次循环所得到的所有方法名
                foreach ($menu1 as $key => $value) {
                    // 将模块名index 拼接控制器名,拼接方法名 得到url
                    $menu_str = 'index/'.$value['controller'].'/'.$value['action'];
                    // 把控制器名存入数组
                    $role_list[$k]['controller'] = $v['controller'];
                    // 把拼接好的url 和方法名存入一个二维数组,此数组于上面controller同级
                    $role_list[$k]['role'][$key]['url'] = $menu_str;

                    $role_list[$k]['role'][$key]['name'] = $value['name'];
                }
            }
        }else{
            // 角色权限不为all 的时候,将所拥有的权限列表中的控制器先找出来,再将所有方法名找出,通过控制器和方法的上下级拼凑成三维数组
            $condition = "menu_id in ($condition)";
            $menu = Db::table('menu')->where($condition.' and parent_id = 0')->select();
            // p($menu);exit();
            $menu2 = Db::table('menu') ->where($condition.' and parent_id!=0')->select();
            // p($menu2);exit();
            $role_list = [];
            foreach ($menu as $k => $v) {
                $role_list[$k]['name'] = $v['name'];

                foreach ($menu2 as $key => $value) {
                    if ($v['menu_id'] == $value['parent_id']) {
                        $menu_str = 'index/'.$value['controller'].'/'.$value['action'];
                        $role_list[$k]['controller'] = $v['controller'];
                        $role_list[$k]['role'][$key]['url'] = $menu_str;
                        $role_list[$k]['role'][$key]['name'] = $value['name'];
                    }
                }
            }
        }
        // p($role_list);exit();
        $this->assign('role_list',$role_list);
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
		}elseif (strstr($act, 'ajax') || in_array($act, $uneed_check)) {
			// 所有增删改查操作不需要验证,增删改查操作请求控制器名必须包含ajax
			return true;

		}else{

			$menu_list = Db::table('menu')->where('menu_id','in',$role_list)->select(); 

			$menu = '';

			foreach ($menu_list as $key => $value) {
				$menu .= strtolower($value['controller'].'@'.$value['action'].',');
			}

			$menu_array = explode(',', $menu);
            // echo $con.'@'.$act;exit();

			if (!in_array(strtolower($con.'@'.$act), $menu_array)) {
				
				$this->error('你没有操作权限,请联系超级管理员分配权限!',url('index/index/index'));
			}

		}

	}
	
}