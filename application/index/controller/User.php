<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Session;

/**
 * Class User 用户管理
 * @package app\index\controller
 */
class User extends Base{
    //登录
    public function login(){
        if (request()->isGet()){
            //登录页面
            return $this->fetch();
        }elseif (request()->isPost()){
            $username = $_POST['username'];
            $password = $_POST['password'];
            $ip=$_SERVER["REMOTE_ADDR"];
            $time = time();
            // echo $ip;
            $result = Db::table('user')->where('username',$username)->find();
            // 首先查询账号是否存在.如果存在则判断上一次登录ip与本地ip是否相同,如果ip不相同将登录时间和错误登录数次为0;如果ip相同则判断错误次数是否大于等于三次,如果大于三次则判断时间;
            if (!$result) {
                $this->error('账号不存在！',url('index/user/login'));
            }else{
                $user_info = Db::table('user')->alias('u')->field('u.*,r.role_list')
                    ->join('role r','u.role_id = r.role_id','LEFT')
                    ->where('username',$username)
                    ->find();
                if ($ip != $user_info['ip']) {
                    Db::table('user')->where('username',$username)
                        ->update(['ip' => $ip,'error' => 0,'login_time'=> 0]);
                }else{
                    if ($user_info['error'] >= 3) {
                        if ($user_info['login_time'] > $time) {
                            $minute =$minute = round(($user_info['login_time'] - $time)/    60);
                            $this->error('您已连续输错密码三次,请在'.$minute.'分钟后重新登录',url('index/user/login'));
                        }
                    }
                }
            }
            $res = Db::table('user')->alias('u')->field('u.*,r.role_list')
                ->join('role r','u.role_id = r.role_id','LEFT')
                ->where('username',$username)
                ->where('password',$password)
                ->find();
            //如果登录成功则查询账号所属角色,将角色的权限存入session;
            if ($res) {
                Session::set('user',$res);
                $this->success('登录成功！',url('index/index/index'));
            }else{
                // 如果密码错误则判断登录错误次数和限制时间
                $user_info = Db::table('user')->where('username',$username)->find();
                if ($user_info['error'] >= 3) {
                    $minute = round(($user_info['login_time'] - $time)/60);
                    $this->error('您已输错密码三次,请在'.$minute.'分钟后重新登录',url('index/user/login'));
                }
                $new_time = $time + (30*60);
                Db::table('user')->where('username',$username)
                    ->update(['error'=>$user_info['error']+1,'login_time' => $new_time]);
                $this->error('密码错误！',url('index/user/login'));
            }
        }

    }

        /*
         * 后台管理员退出
         */
    public function logout(){
        session('admin',null);
        $this->redirect('index/user/login');
    }


}