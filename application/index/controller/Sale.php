<?php 
namespace app\index\controller;
use think\Db;
use think\Session;
use think\Validate;
use think\Request;

/**
* 家电销售管理
*/
class Sale extends Base{
	//商品列表
	public function commodity($type_id = 0){
        $where = '1 = 1';
        if ($type_id) {
           $where .= ' and p.type_id = '.$type_id;
        }
		$list = Db::table('product')->alias('p')->field('p.*,t.type_name')
				->join('product_type t','p.type_id = t.type_id','LEFT')
                ->where($where)
				->order('p.type_id')
				->select();
        $type = Db::table('product_type')->where('parent_id != 0')->select();
        $this->assign('type',$type);
		$this->assign('list',$list);
		return $this->fetch();
	}

	// 商品详细信息
	public function lookproductajax(){
		$product_id = input('product_id');
		$info = Db::table('product')->alias('p')->field('p.*,t.type_name')
				->join('product_type t','p.type_id = t.type_id','LEFT')
				->where('p.product_id',$product_id)
				->find();
		// print_r($info);
		$this->assign('info',$info);
		return $this->fetch();
	}

	// 订单管理
	public function order(){
		$list = Db::table('order')->alias('o')->field('o.*,p.product_name,c.username as customer_name,u.username')
				->join('product p','o.product_number = p.product_number','LEFT')
				->join('customer c','o.customer_number = c.customer_number','LEFT')
				->join('user u','o.user_number = u.user_number','LEFT')
				->select();
		foreach ($list as $key => $value) {
			$list[$key]['allmoney'] = $value['num'] * $value['price'];
		}
		// echo "<pre>";
		// print_r($list);exit();
		$this->assign('list',$list);
		return $this->fetch();
	}

	// 订单详细信息
	public function lookOrderAjax(){
		$order_id = input('order_id');
		$info = Db::table('order')->alias('o')->field('o.*,p.*,c.*,u.*,c.username as customer_name,u.username as name,d.department_name,t.type_name')
				->join('product p','o.product_number = p.product_number','LEFT')
				->join('customer c','o.customer_number = c.customer_number','LEFT')
				->join('user u','o.user_number = u.user_number','LEFT')
				->join('department d','u.department_id = d.department_id','LEFT')
				->join('product_type t','t.type_id = p.type_id','LEFT')
                ->where('o.order_id',$order_id)
				->find();
				$info['allmoney'] = $info['num']*$info['price'];
               
		$this->assign('info',$info);
		return $this->fetch();
	}
	//批量导入
	public function excelAjax(){		
			return $this->fetch();
	}
	// excel 导入数据库
	public function uploadAjax(Request $request){
        header("Content-type: text/html; charset=utf-8");
        $file = request()->file('files');      //获取上传的文件
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');     
        //移动到指定目录
        $questionName = $_FILES["files"]["name"];
        $filename = str_replace(strrchr($questionName, "."),"",$questionName);
        // die();
        //上传文件成功进行入库操作
        if ($info) {
            //获取文件名称
            $exclePath = $info->getsaveName();  //获取文件名
            $file = ROOT_PATH . 'public' . DS . 'uploads' . DS . $exclePath;   //上传文件的地址

            //设置sheet
            $sheet = 0;

            //实例化PHPExcel类
            require '../vendor/excel/Classes/PHPExcel.php';
            $objRead = new \PHPExcel_Reader_Excel2007();   //建立reader对象
            if (!$objRead->canRead($file)) {
                $objRead = new \PHPExcel_Reader_Excel5();
                if (!$objRead->canRead($file)) {
                    die('No Excel!');
                }
            }

            $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');
            $obj = $objRead->load($file);  //建立excel对象
            $currSheet = $obj->getSheet($sheet);   //获取指定的sheet表
            $columnH = $currSheet->getHighestColumn();   //取得最大的列号
            $columnCnt = array_search($columnH, $cellName);
            $rowCnt = $currSheet->getHighestRow();   //获取总行数

            $data = array();
            for ($_row = 2; $_row <= $rowCnt; $_row++) {  //读取内容
                for ($_column = 0; $_column <= $columnCnt; $_column++) {
                    $cellId = $cellName[$_column] . $_row;
                    //$cellValue = $currSheet->getCell($cellId)->getValue();
                    $cellValue = $currSheet->getCell($cellId)->getCalculatedValue();  #获取公式计算的值
                    if ($cellValue instanceof PHPExcel_RichText) {   //富文本转换字符串
                        $cellValue = $cellValue->__toString();
                    }

                    $data[$_row][$cellName[$_column]] = $cellValue;
                }
            }
            $sql = [];
            foreach ($data as $key => $value) {
            	
                // $sql[$key]['customer_number'] = $filename;
                $sql[$key]['product_number'] = $value['A'];
                $sql[$key]['customer_number'] = $value['B'];
                $sql[$key]['user_number'] = $value['C'];
                $sql[$key]['order_number'] = $value['D'];
                $sql[$key]['buy_time'] = $value['E'];
                $sql[$key]['num'] = $value['F'];
                $sql[$key]['buy_channel'] = $value['G'];
                $sql[$key]['price'] = $value['H'];
                
            }
         
            $res = Db::table('order')->insertAll($sql);
            if ($res) {
                $this->success('添加成功', url('index/sale/order'));
            } else {
                $this->error('添加失败', url('index/sale/order'));
			}
        }
    }
}