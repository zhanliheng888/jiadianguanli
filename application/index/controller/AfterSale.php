<?php 
namespace app\index\controller;
use think\Db;
use think\Session;
use think\Request;

/**
* 售后管理
*/
class AfterSale extends Base{
	// 售后服务受理
	public function service(){

		$list = Db::table('after_sale')->alias('a')->field('a.*,u.username,o.order_id,c.customer_id')
					->join('user u','a.user_number = u.user_number','LEFT')
					->join('order o','a.order_number = o.order_number','LEFT')
					->join('customer c','o.customer_number = c.customer_number','LEFT')
					->order('a.status')
					->order('c.grade DESC')
					->select();
	
		$this->assign('list',$list);

		return $this->fetch();
	}

	// 查看用户所有订单
	public function allorderajax(){
		$customer_id = input('customer_id');
		$list = Db::table('order')->alias('o')->field('o.*,p.product_name,c.username as customer_name,u.username')
				->join('product p','o.product_number = p.product_number','LEFT')
				->join('customer c','o.customer_number = c.customer_number','LEFT')
				->join('user u','o.user_number = u.user_number','LEFT')
				->where('c.customer_id',$customer_id)
				->select();
		foreach ($list as $key => $value) {
			$list[$key]['allmoney'] = $value['num'] * $value['price'];
		}
		$customer= Db::table('customer')->where('customer_id',$customer_id)->find();
		$this->assign('customer',$customer);
		$this->assign('list',$list);
		return $this->fetch();
	}

	// 添加售后服务单
	public function addServiceAjax(){
		return $this->fetch();
	}


	// 生成售后服务逻辑
	public function addAjax(){
		$data = $_POST;
		$data['handle_time'] = time();
		$data['user_number'] = Session::get('user.user_number');
		//获取添加数据的id ,因为id是自增且唯一的,将id转换成字符串,判断id 长度
			//再拼接当前时间, 拼凑成五位数的服务订单编号 
		
		$after_id = Db::name('after_sale')->insertGetId($data);
			$count = strlen(strval($after_id));
			$num1 = 5-$count;
			$num2 = '';
			for ($i=0; $i <$num1 ; $i++) { 
				$num2.= '0';
			}
			$after_number ='SV'.date('Y').date('m'). $num2.$after_id;
			$res1 = Db::table('after_sale')
						->where('after_id',$after_id)
						->update(['after_number' => $after_number]);
		if ($res1) {
			$this->success('受理成功',url('index/after_sale/service'));
		}else{
			$this->error('受理失败');
		}
	}

	// 售后服务办理
	public function through(){
		
		$list = Db::table('feedback')->alias('f')->field('f.*,u.username')
					->join('user u','f.user_number = u.user_number','LEFT')
					->select();
		$this->assign('list',$list);
		return $this->fetch();
	}

	// 查看售后服务反馈
	public function lookajax(){
		$fd_id = input('fd_id');
		$info = Db::table('feedback')->alias('f')->field('f.*,u.username')
					->join('user u','f.user_number = u.user_number','LEFT')
					->where('fd_id',$fd_id)
					->find();
		$this->assign('info',$info);
		return $this->fetch();
	}

	// 售后服务完结
	public function endAjax(){
		if (request()->isGet()) {
			$after_id = input('after_id');
			$info = Db::table('after_sale')->alias('a')->field('a.*,u.username,o.*')
						->join('user u','a.user_number = u.user_number','LEFT')
						->join('order o','o.order_number = a.order_number','LEFT')
						->where('after_id',$after_id)
						->find();
			$this->assign('info',$info);			
			return $this->fetch();
		}else{
			$data = $_POST;
			$after_id = $data['after_id'];
			$buy_time = $data['buy_time'];
			unset($data['after_id']);
			unset($data['buy_time']);
			$data['fd_time'] = time();
			if ($data['handle'] == 1) {
				if ((time()-$buy_time)/86400 >7) {
					$this->error('提交订单七日后不允许退货!');
				}
			}
			if ($data['handle'] == 2) {
				if ((time()-$buy_time)/86400 >30) {
					$this->error('提交订单30日后不允许换货!');
				}
			}
			if ($data['handle'] == 3 && $data['cost'] == 0) {
				if ((time()-$buy_time)/86400 >365) {
					$this->error('提交订单1年后不免费维修!');
				}
			}

			$fd_id = Db::name('feedback')->insertGetId($data);
			$count = strlen(strval($fd_id));
			$num1 = 5-$count;
			$num2 = '';
			for ($i=0; $i <$num1 ; $i++) { 
				$num2.= '0';
			}
			$fb_number ='FB'.date('Y').date('m'). $num2.$fd_id;
			$res1 = Db::table('feedback')
						->where('fd_id',$fd_id)
						->update(['fb_number' => $fb_number]);

			if ($res1) {
				$res2 = Db::table('after_sale')
					->where('after_id',$after_id)
					->update(['status'=>2]);
			}
			if ($res2) {
				$this->success('办理成功',url('index/after_sale/service'));
			}else{
				$this->error('办理失败');
			}

		}
		
	}
}