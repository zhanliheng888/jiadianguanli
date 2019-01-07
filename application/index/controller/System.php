<?php 
namespace app\index\controller;
use think\Db;
use think\Session;
use think\Validate;
use think\Request;
/**
* 
*/
class System extends Base{
	
	// 角色管理
	public function role(){
		//多表联查查看角色所属部门以及角色
		$user = Db::table('user')->alias('u')->field('u.*,r.role_name,d.department_name')
				->join('role r','u.role_id = r.role_id','LEFT')
				->join('department d','u.department_id = d.department_id','LEFT')
				->select();
		$this->assign('user',$user);
		return $this->fetch();
	}

	// 查看角色详细信息
	public function lookajax(){
		$user_id = input('user_id');
		// return $user_id;
		$user_info = Db::table('user')->alias('u')->field('u.*,r.*,d.*,p.type_name')
				->join('role r','u.role_id = r.role_id','LEFT')
				->join('department d','u.department_id = d.department_id','LEFT')
				->join('product_type p','d.type_id =  p.type_id','LEFT')
				->where('u.user_id',$user_id)
				->find();
		$this->assign('user_info',$user_info);
		// echo "<pre>";
		// print_r($user_info);
		return $this->fetch();
	}

	// 配置管理
	public function config (){
		$product = Db::table('product')->alias('p')->field('p.*,t.type_name')
					->join('product_type t','p.type_id = t.type_id','LEFT')
					->select();
		$this->assign('product',$product);
		return $this->fetch();
	}

	// 修改配置页面
	public function editajax(){
		$product_id = input('product_id');
		$info = Db::table('product')
				->where('product_id',$product_id)
				->find();
		$type = Db::table('product_type')->where('parent_id',0)->select();
		$this->assign('type',$type);
		$this->assign('info',$info);
		return $this->fetch();
	}

	//修改配置
	public function getEditAjax(){
	 	$data = $_POST;
	 	unset($data['parent']);
	 	$product_id = $data['product_id'];
	 	$res = Db::table('product')->where('product_id',$product_id)->update($data);
	 	if ($res) {
	 		$this->success('修改成功!',url('index/system/config'));
	 	}
	 } 

	//二级分类异步接口
	public function selectajax(){
		// 根据大类id 查出下面分类.转换成json数据返回给前端
		$type_id = $_POST['type_id'];
		$list = Db::table('product_type')->where('parent_id',$type_id)->select();
		$arr = [
			'code' => 1,
			'msg'  =>'',
			'data' => $list
		];
		echo json_encode($arr);
		
	}

	// 产品管理
	public function product(){
		$list = Db::table('product')->alias('p')->field('p.*,t.type_name')
				->join('product_type t','p.type_id = t.type_id','LEFT')
				->select();
		$this->assign('list',$list);
		return $this->fetch();
	}

	// 添加产品
	public function addAjax(){
		// 当为get请求的时候跳转到添加页面 ,当为post请求时完成添加逻辑
		if (request()->isGet()) {
			$type = Db::table('product_type')->where('parent_id',0)->select();
			$this->assign('type',$type);
			return $this->fetch();
		}elseif (request()->isPost()) {
			$data = $_POST;
			unset($data['parent']);
			unset($data['file']);
			$data['create_time'] = time();
			//获取添加数据的id ,因为id是自增且唯一的,将id转换成字符串,判断id 长度
			//再拼接当前时间, 拼凑成五位数的产品编号 
			$product_id = Db::name('product')->insertGetId($data);
			$count = strlen(strval($product_id));
			$num1 = 5-$count;
			$num2 = '';
			for ($i=0; $i <$num1 ; $i++) { 
				$num2.= '0';
			}
			$product_number ='PD'.date('Y'). $num2.$product_id;
			$res1 = Db::table('product')
						->where('product_id',$product_id)
						->update(['product_number' => $product_number]);
			$files = request()->file('file');
				if ($files) {
					
						$info = $files
							->validate([
								'size' => 1000 * 1024,
								'ext' => 'jpg,png,gif,jpeg'])
							->move(ROOT_PATH . 'public/static/upload');
						// echo $info->getSaveName();exit();
						if ($info) {

							$product_img= $info->getSaveName();
							$res2 = Db::table('product')
								->where('product_id',$product_id)
								->update(['product_img' => $product_img]);
						
						} else {
							$this->error($info->getError());
						}
					
				}

				if ($res1) {
						$this->success('添加成功',url('index/system/product'));
				}else{
						$this->error('添加失败',url('index/system/product'));
				}
		}
	}

	// 添加产品分类
	public function addTypeAjax(){
		if (request()->isGet()) {
			$type = Db::table('product_type')->where('parent_id',0)->select();
			$this->assign('type',$type);	
			return $this->fetch();
		}elseif (request()->isPost()) {
			$data = $_POST;
			$data['create_time'] = time();
			$res = Db::table('product_type')->insert($data);
			if ($res) {
				$this->success('添加成功',url('index/system/product'));
			}else{
				$this->error('添加失败',url('index/system/product'));
			}
		}
	}

	// 产品编辑
	public function editProductAjax(){
		if (request()->isGet()) {
			$product_id = input('product_id');
			$info = Db::table('product')->alias('p')->field('p.*,t.type_name')
				->join('product_type t','p.type_id = t.type_id','LEFT')
				->where('product_id',$product_id)
				->find();
			$this->assign('info',$info);

			return $this->fetch();
		}elseif (request()->isPost()){
			$data = $_POST;
			$product_id = $data['product_id'];
			$res = Db::table('product')->update($data);
			$files = request()->file('file');
				if ($files) {
					
						$info = $files
							->validate([
								'size' => 1000 * 1024,
								'ext' => 'jpg,png,gif,jpeg'])
							->move(ROOT_PATH . 'public/static/upload');
						// echo $info->getSaveName();exit();
						if ($info) {

							$product_img= $info->getSaveName();
							 Db::table('product')
								->where('product_id',$product_id)
								->update(['product_img' => $product_img]);
						
						} else {
							$this->error($info->getError());
						}
				}
				if ($res) {
						$this->success('修改成功',url('index/system/product'));
				}else{
					$this->error('修改失败',url('index/system/product'));
				}
		}
	}

	// 删除产品
	public function delProductAjax(){
		$product_id = input('product_id');
		$res = Db::table('product')->where('product_id',$product_id)->delete();
		if ($res) {
			$this->success('删除成功',url('index/system/product'));
		}else{
			$this->error('删除失败',url('index/system/product'));
		}
	}

	// excel批量导入
	public function excelAjax(){
		return $this->fetch();
	}

	// excel导入数据库
	public function uploadAjax(Request $request)
    {
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
                $sql[$key]['product_name'] = $value['B'];
                $sql[$key]['cost_price'] = $value['C'];
                $sql[$key]['product_price'] = $value['D'];
                $sql[$key]['product_desc'] = $value['E'];
                
            }
         
            $res = Db::table('product')->insertAll($sql);
            if ($res) {
                $this->success('添加成功', url('index/system/product'));
            } else {
                $this->error('添加失败', url('index/system/product'));
			}
        }
    }



	// 客户管理
	public function customer(){
		$list = Db::table('customer')->select();
		foreach ($list as $key => $value) {
			if (strstr($value['customer_number'], 'T01')) {
				$list[$key]['nature'] = '单位客户';
			}
			if (strstr($value['customer_number'], 'T02')) {
				$list[$key]['nature'] = '个人家庭客户';
			}
			
		}
		$this->assign('list',$list);
		return $this->fetch();
	}
	//客户信息更新
	public function editcustomerajax(){
		if (request()->isGet()) {
			$customer_id = input('customer_id');
			$info = Db::table('customer')->where('customer_id',$customer_id)->find();
			
			$this->assign('info',$info);
			return $this->fetch();
		}elseif (request()->isPost()) {
			$data = $_POST;
			$customer_id = $data['customer_id'];
			$res = Db::table('customer')->where('customer_id',$customer_id)->update($data);
			if ($res) {
				$this->success('修改成功',url('index/system/customer'));
			}else{
				$this->error('修改失败',url('index/system/customer'));
			}
		}
		
	}
}