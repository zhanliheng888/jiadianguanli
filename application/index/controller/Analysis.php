<?php 
namespace app\index\controller;
use think\Db;
/**
* 统计分析
*/
class Analysis extends Base{
	
	// 核算业绩
	public function achievement(){
		// 查出所有销售人员,销售人员的角色id为3
		$user_list = Db::table('user')
				->where('role_id = 3')
				->select();
		$list = [];
		// 循环销售员，得到每个销售员的订单
		foreach ($user_list as $key => $value) {
			$order_list = Db::table('order')->alias('o')->field('o.*,sum(o.num*o.price) as allmoney,t.type_name')
							->join('product p','o.product_number = p.product_number','LEFT')
							->join('product_type t','p.type_id = t.type_id','LEFT')
							->where('user_number',$value['user_number'])
							->group('p.type_id')
							->order('allmoney DESC')
							->select();
				foreach ($order_list as $k => $v) {
					// 拼接成新的数组
					$list[]=[
						'user_number' => $value['user_number'],
						'username'   => $value['username'],
						'type_name'	  => $v['type_name'],
						'allmoney'	  => $v['allmoney'],
						'time'		  => $v['buy_time']
					];
				}
		}
		// 获取$list里面单列allmoney的值
		$sort = array_column($list, 'allmoney');
		// 让list根据allmoney大小进行倒叙排列
		array_multisort($sort, SORT_DESC, $list);  
		$this->assign('list',$list);
		// echo "<pre>";
		// print_r($list);exit();
		$count = Db::table('feedback')->count();
		$tui = Db::table('feedback')->where('handle',1)->count();
		$huan = Db::table('feedback')->where('handle',2)->count();
		$xiu = Db::table('feedback')->where('handle',3)->count(); 
		$xun = Db::table('feedback')->where('handle',4)->count();
		// echo $tui;exit();round($rs[row]/$rs[sum]*100,2)
		$this->assign('tuihuo',round($tui/$count*100,2));
		$this->assign('huanhuo',round($huan/$count*100,2));  
		$this->assign('weixiu',round($xiu/$count*100,2));    
		$this->assign('zixun',round($xun/$count*100,2));  
		return $this->fetch();
	}

	// 报表统计
	public function reportform(){
		// 获取当前年
		$year = date("Y");
		// 获取当月
		$month = date("m");
		// 获取当月最后一天
		$allday = date("t");
		$strat_time = strtotime($year."-".$month."-1");
		$end_time = strtotime($year."-".$month."-".$allday);
		//拼接约束条件,购买日期在本月内
		$where = 'buy_time >'.$strat_time.' and buy_time < '.$end_time;
		// 销量最好的产品
		$hot_list = Db::table('order')->alias('o')->field('sum(o.num) as newnum,p.product_name')
				->join('product p','o.product_number = p.product_number','LEFT')
				->join('product_type t','p.type_id = t.type_id','LEFT')
				->where($where)
		        ->group('p.type_id')
		        ->order('newnum DESC')
		        ->limit(0,5)
		        ->select();
		     $hot1 = '';
		     $hot2 = '';
		     foreach ($hot_list as $key => $value) {
		     	$hot1 .=  '"'.$value['product_name'].'"'.',';
		     	$hot2 .=  '"'.$value['newnum'].'"'.',';
		     }

		$this->assign('hot1',rtrim($hot1));
		$this->assign('hot2',rtrim($hot2));

		// 本月销量最低
		$unhot_list = Db::table('order')->alias('o')->field('sum(o.num) as newnum,p.product_name')
				->join('product p','o.product_number = p.product_number','LEFT')
				->join('product_type t','p.type_id = t.type_id','LEFT')
				->where($where)
		        ->group('p.type_id')
		        ->order('newnum ')
		        ->limit(0,5)
		        ->select();
		     $unhot1 = '';
		     $unhot2 = '';
		     foreach ($unhot_list as $key => $value) {
		     	$unhot1 .=  '"'.$value['product_name'].'"'.',';
		     	$unhot2 .=  '"'.$value['newnum'].'"'.',';
		     }

		$this->assign('unhot1',rtrim($unhot1));
		$this->assign('unhot2',rtrim($unhot2));
		// 本月质量问题最多
		$quality_list = Db::table('feedback')->alias('f')->field('p.product_name,count(*) as newnum')
						->join('after_sale a','f.after_number = a.after_number','LEFT')
						->join('order o','o.order_number = a.order_number','LEFT')
						->join('product p','o.product_number = p.product_number','LEFT')
						->where($where)
						->group('o.product_number')
						->order('newnum DESC')
						->select();
		$quality1 = '';
		$quality2 = '';
		     foreach ($quality_list as $key => $value) {
		     	$quality1 .=  '"'.$value['product_name'].'"'.',';
		     	$quality2 .=  '"'.$value['newnum'].'"'.',';
		     }

		$this->assign('quality1',rtrim($quality1));
		$this->assign('quality2',rtrim($quality2));

		// 本月质量问题最少
		$unquality_list = Db::table('feedback')->alias('f')->field('p.product_name,count(*) as newnum')
						->join('after_sale a','f.after_number = a.after_number','LEFT')
						->join('order o','o.order_number = a.order_number','LEFT')
						->join('product p','o.product_number = p.product_number','LEFT')
						->where($where)
						->group('o.product_number')
						->order('newnum')
						->select();
		$unquality1 = '';
		$unquality2 = '';
		     foreach ($unquality_list as $key => $value) {
		     	$unquality1 .=  '"'.$value['product_name'].'"'.',';
		     	$unquality2 .=  '"'.$value['newnum'].'"'.',';
		     }

		$this->assign('unquality1',rtrim($unquality1));
		$this->assign('unquality2',rtrim($unquality2));
		// echo "<pre>";
		// print_r($quality_list);exit;
		return $this->fetch();
	}
	
	// 产品统计表
	public function other(){

		// 获取当前月数
		$year = date("Y");
		// 获取当天
		$month = date("m");
		// 获取当月最后一天
		$allday = date("t");
		$strat_time = strtotime($year."-".$month."-1");
		$end_time = strtotime($year."-".$month."-".$allday);
		//拼接约束条件,购买日期在本月内
		$where = 'buy_time >'.$strat_time.' and buy_time < '.$end_time;

		$list = Db::table('order')->alias('o')->field('p.product_name,buy_channel,sum(o.num) as newnum,sum(o.num * price) as allmoney')
				->join('product p','o.product_number = p.product_number','LEFT')
				->join('product_type t','p.type_id = t.type_id','LEFT')
				->group('t.type_id')
				->where($where)
				->select();
		$this->assign('month',$year.'-'.$month);
		$this->assign('list',$list);
		return $this->fetch();
	}
}