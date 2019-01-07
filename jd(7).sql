# Host: localhost  (Version: 5.7.17)
# Date: 2018-12-27 15:05:15
# Generator: MySQL-Front 5.3  (Build 4.234)

/*!40101 SET NAMES utf8 */;

#
# Structure for table "after_sale"
#

DROP TABLE IF EXISTS `after_sale`;
CREATE TABLE `after_sale` (
  `after_id` int(11) NOT NULL AUTO_INCREMENT,
  `after_number` varchar(255) DEFAULT NULL COMMENT '服务单号',
  `order_number` varchar(255) DEFAULT NULL COMMENT '订单编号',
  `problem_type` varchar(255) DEFAULT NULL COMMENT '问题分类',
  `problem_desc` varchar(255) DEFAULT NULL COMMENT '问题详情',
  `user_number` varchar(255) DEFAULT NULL COMMENT '售后人员编号',
  `handle_time` varchar(255) DEFAULT NULL COMMENT '受理时间',
  `status` int(11) DEFAULT '1' COMMENT '受理状态,1为处理中,2为以完结,默认为1',
  PRIMARY KEY (`after_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='售后服务表';

#
# Data for table "after_sale"
#

INSERT INTO `after_sale` VALUES (1,'SV20181200001','OD20181200001','质量问题','漏水','EM00006','1545645410',2),(2,'SV20181200002','OD20181200002','质量问题','不显示','EM00006','1545709356',2),(3,'SV20181200003','OD20181200004','不喜欢','退货','EM00004','1545709726',2),(4,'SV20181200004','OD20181200003','无理由退货','就是想退货','EM00006','1545714111',2),(5,'SV20181200005','OD20181200005','质量问题','质量太差','EM00004','1545714235',2),(6,'SV20181200006','OD20181200006','质量问题','不好用吧','EM00006','1545893340',1),(7,'SV20181200007','OD20181200007','不喜欢','反正就是不喜欢','EM00004','1545893385',1),(8,'SV20181200008','OD20181200008','看着不顺眼','我就是要退货','EM00004','1545893480',1),(9,'SV20181200009','OD20181200009','不喜欢','反正不喜欢','EM00004','1545893567',1);

#
# Structure for table "customer"
#

DROP TABLE IF EXISTS `customer`;
CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_number` varchar(255) DEFAULT NULL COMMENT '客户编号',
  `username` varchar(255) DEFAULT NULL COMMENT '客户名称',
  `contact_tel` varchar(255) DEFAULT NULL COMMENT '联系电话',
  `addr` varchar(255) DEFAULT NULL COMMENT '客户地址',
  `grade` varchar(255) DEFAULT NULL COMMENT '客户等级 1为普通客户,2为vip客户',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='客户表';

#
# Data for table "customer"
#

INSERT INTO `customer` VALUES (1,'TT0200001','张三','15088779696','广东省深圳市福田区','2',1545195688),(2,'TT0200002','李四','13838384438','黄土高坡','1',1545195688),(3,'TT0200003','王五','15079846888','湖南长沙','1',1545195688),(4,'TT0200004','赵六','13055667778','乌鲁木齐','2',1545195688);

#
# Structure for table "department"
#

DROP TABLE IF EXISTS `department`;
CREATE TABLE `department` (
  `department_id` int(11) NOT NULL AUTO_INCREMENT,
  `department_name` varchar(255) DEFAULT NULL COMMENT '部门名称',
  `department_set` varchar(255) DEFAULT NULL COMMENT '部门设置',
  `department_desc` varchar(255) DEFAULT NULL COMMENT '部门说明',
  `type_id` int(11) DEFAULT NULL COMMENT '产品大类,关联产品分类表',
  PRIMARY KEY (`department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='部门表';

#
# Data for table "department"
#

INSERT INTO `department` VALUES (1,'电视事业部',NULL,'负责本公司所有电视机产品售前咨询以及售后服务',1),(2,'空调事业部',NULL,'负责本公司所有空调产品售前咨询以及售后服务',2),(3,'洗衣机事业部',NULL,'负责本公司所有洗衣机产品售前咨询以及售后服务',3);

#
# Structure for table "feedback"
#

DROP TABLE IF EXISTS `feedback`;
CREATE TABLE `feedback` (
  `fd_id` int(11) NOT NULL AUTO_INCREMENT,
  `fb_number` varchar(255) DEFAULT NULL COMMENT '反馈服务单编号',
  `after_number` varchar(255) DEFAULT NULL COMMENT '售后服务单号',
  `user_number` varchar(255) DEFAULT NULL COMMENT '售后人员编号',
  `fd_time` varchar(255) DEFAULT NULL COMMENT '反馈日期',
  `handle` varchar(255) DEFAULT NULL COMMENT '处理类型',
  `cost` varchar(255) DEFAULT NULL COMMENT '费用',
  `desc` varchar(255) DEFAULT NULL COMMENT '处理情况说明',
  `confirm` varchar(255) DEFAULT NULL COMMENT '客户是否确认,1为确认,2为未确认',
  PRIMARY KEY (`fd_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='服务反馈记录表';

#
# Data for table "feedback"
#

INSERT INTO `feedback` VALUES (3,'FB20181200003','SV20181200001','EM00004','1545653572','1','0','不喜欢','1'),(4,'FB20181200004','SV20181200004','EM00006','1545714263','1','20','客户报销邮费 退货','1'),(5,'FB20181200005','SV20181200003','EM00004','1545817023','2','0','huoighioar','1'),(6,'FB20181200006','SV20181200002','EM00006','1545817042','3','100','啦啦啦啦啦啦啦','1'),(9,'FB20181200009','SV20181200005','EM00004','1545818247','4','0','无聊','1');

#
# Structure for table "menu"
#

DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `controller` varchar(255) DEFAULT NULL COMMENT '控制器名',
  `action` varchar(255) DEFAULT NULL COMMENT '方法名',
  `parent_id` int(11) DEFAULT NULL COMMENT '上级ID',
  `name` varchar(255) DEFAULT NULL COMMENT '名称',
  PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='菜单表';

#
# Data for table "menu"
#

INSERT INTO `menu` VALUES (1,'system','index',0,'系统管理'),(2,'system','role',1,'员工、部门及角色管理'),(3,'system','config',1,'配置管理'),(4,'system','product',1,'产品管理'),(5,'system','customer',1,'客户管理'),(6,'sale','index',0,'家电销售'),(7,'sale','commodity',6,'商品查询'),(8,'sale','order',6,'订单管理'),(9,'afterSale','index',0,'售后管理'),(10,'afterSale','service',9,'售后服务受理'),(11,'afterSale','through',9,'售后服务办结'),(12,'analysis','index',0,'统计分析'),(13,'analysis','achievement',12,'核算业绩'),(14,'analysis','reportform',12,'报表统计'),(15,'analysis','other',12,'产品统计表');

#
# Structure for table "order"
#

DROP TABLE IF EXISTS `order`;
CREATE TABLE `order` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_number` varchar(255) DEFAULT NULL COMMENT '产品编号',
  `customer_number` varchar(255) DEFAULT NULL COMMENT '客户编号',
  `user_number` varchar(255) DEFAULT NULL COMMENT '销售人员编号',
  `order_number` varchar(255) DEFAULT NULL COMMENT '订单编号',
  `buy_time` int(11) unsigned DEFAULT NULL COMMENT '购买日期',
  `num` int(11) unsigned DEFAULT NULL COMMENT '数量',
  `buy_channel` varchar(255) DEFAULT NULL COMMENT '购买途径',
  `price` varchar(255) DEFAULT NULL COMMENT '单价',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='订单表';

#
# Data for table "order"
#

INSERT INTO `order` VALUES (1,'PD201800001','TT0200001','EM00005','OD20181200001',1545195688,2,'官方商城','2000'),(2,'PD201800001','TT0200002','EM00005','OD20181200002',1545195688,3,'京东商城','2000'),(3,'PD201800004','TT0200003','EM00005','OD20181200003',1545195688,4,'官方商城','900'),(4,'PD201800006','TT0200003','EM00003','OD20181200004',1545195688,1,'实体店','4222'),(5,'PD201800007','TT0200004','EM00003','OD20181200005',1545195688,2,'实体店','1200'),(6,'PD201800002','TT0200001','EM00003','OD20181200006',1545195688,1,'官方商城','8000'),(7,'PD201800005','TT0200002','EM00003','OD20181200007',1545195688,1,'实体店','2000'),(8,'PD201800008','TT0200003','EM00003','OD20181200008',1545195688,1,'京东商城','999'),(9,'PD201800009','TT0200001','EM00005','OD20181200009',1545195688,2,'京东商城','6000'),(10,'PD201800010','TT0200002','EM00005','OD20181200010',1545195688,3,'实体店','99');

#
# Structure for table "product"
#

DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_number` varchar(255) DEFAULT NULL COMMENT '产品编号',
  `product_name` varchar(255) DEFAULT NULL COMMENT '产品名称',
  `product_img` varchar(255) DEFAULT NULL COMMENT '产品图片',
  `type_id` int(11) DEFAULT NULL COMMENT '产品分类',
  `cost_price` varchar(255) DEFAULT NULL COMMENT '产品成本价',
  `product_price` varchar(255) DEFAULT NULL COMMENT '产品售价',
  `company` varchar(255) DEFAULT NULL COMMENT '计量单位',
  `product_desc` varchar(255) DEFAULT NULL COMMENT '产品规格说明',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='产品表';

#
# Data for table "product"
#

INSERT INTO `product` VALUES (1,'PD201800001','格力空调','20181220\\90d7ed79a4f24e2c78e1983167f75c63.jpg',16,'150','2000','件','1.5p变频冷暖静音挂机智能空调',1545195688),(2,'PD201800002','小米电视机','20181220\\06a198f3059478e470a3ae1825b52a13.jpg',10,'5000','8000','件','一个很牛逼的电视机',1545195688),(4,'PD201800004','黑白电视机','20181220\\90d7ed79a4f24e2c78e1983167f75c63.jpg',30,'500','900','台','随意',1545195688),(5,'PD201800005','u屏电视机','20181220\\90d7ed79a4f24e2c78e1983167f75c63.jpg',8,'1000','2000','台','454154154465454',1545195688),(6,'PD201800006','滚筒式洗衣机','20181220\\90d7ed79a4f24e2c78e1983167f75c63.jpg',18,'600','4222','台','随便写点',1545195688),(7,'PD201800007','机顶盒','20181220\\90d7ed79a4f24e2c78e1983167f75c63.jpg',11,'700','1200','件','天翼机顶盒',1545195688),(8,'PD201800008','美的 全自动洗衣机',NULL,21,'500','999','台','随便写点 规格我也不知道',1545892497),(9,'PD201800009','海尔家用中央空调一拖二',NULL,14,'4000','6000','件','随便写点啥 我也不知道',1545892566),(10,'PD201800010','乐视原装壁挂架',NULL,11,'30','99','一件','随便写点 我也不知道是啥',1545892633),(11,'PD201800011','维诺卡夫压缩机风冷恒温红酒柜',NULL,28,'2000','3380','件','随便写点 我也不知道是啥',1545892723);

#
# Structure for table "product_type"
#

DROP TABLE IF EXISTS `product_type`;
CREATE TABLE `product_type` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(255) DEFAULT NULL COMMENT '产品名称',
  `parent_id` int(11) DEFAULT NULL COMMENT '父类id',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COMMENT='产品分类表';

#
# Data for table "product_type"
#

INSERT INTO `product_type` VALUES (1,'电视机',0,1545195688),(2,'空调',0,1545195688),(3,'洗衣机',0,1545195688),(4,'冰箱',0,1545195688),(5,'曲面电视',1,1545195688),(6,'超博电视',1,1545195688),(7,'OLED电视',1,1545195688),(8,'4K超清电视',1,1545195688),(9,'55英寸',1,1545195688),(10,'65英寸',1,1545195688),(11,'电视配件',1,1545195688),(12,'壁挂式空调',2,1545195688),(13,'柜式空调',2,1545195688),(14,'中央空调',2,1545195688),(15,'一级能效空调',2,1545195688),(16,'变频空调',2,1545195688),(17,'1.5匹空调',2,1545195688),(18,'滚筒式洗衣机',3,1545195688),(19,'洗烘一体机',3,1545195688),(20,'轮波式洗衣机',3,1545195688),(21,'迷你式 洗衣机',3,1545195688),(22,'烘干机',3,1545195688),(23,'多门',4,1545195688),(24,'多开门',4,1545195688),(25,'三门',4,1545195688),(26,'双门',4,1545195688),(27,'冰柜/冷吧',4,1545195688),(28,'酒柜',4,1545195688),(29,'冰箱配件',4,1545195688),(30,'黑白电视机',1,1545309272);

#
# Structure for table "role"
#

DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(255) DEFAULT NULL COMMENT '角色名称',
  `role_list` varchar(255) DEFAULT NULL COMMENT '角色允许进入的页面,关联menu的id,多个 用 , 连接,如果为all 泽为最高权限',
  `role_desc` varchar(255) DEFAULT NULL COMMENT '角色说明',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='角色表';

#
# Data for table "role"
#

INSERT INTO `role` VALUES (1,'系统管理员','1,2,3,4,5,6,7,8,9,10,11,12,13,14,15','系统管理员'),(2,'经理','6,7,8,9,10,11,12,13,14,15','经理'),(3,'销售人员','9,10,11,12,13,14,15','销售人员'),(4,'售后人员','9,10,11,12,13,14,15','售后人员');

#
# Structure for table "user"
#

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL COMMENT '名称',
  `password` varchar(255) DEFAULT NULL COMMENT '密码',
  `logo` varchar(255) DEFAULT NULL COMMENT '员工头像',
  `user_number` varchar(255) DEFAULT NULL COMMENT '员工编号',
  `login_time` int(11) DEFAULT NULL COMMENT '登录时间',
  `ip` varchar(255) DEFAULT NULL COMMENT '登录ip',
  `role_id` varchar(255) DEFAULT NULL COMMENT '所属角色',
  `department_id` int(11) DEFAULT NULL COMMENT '所属部门',
  `error` int(11) DEFAULT '0' COMMENT '错误登录次数',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='员工表';

#
# Data for table "user"
#

INSERT INTO `user` VALUES (1,'最佳损友','123456',NULL,'EM00001',1545819409,'127.0.0.1','1',1,2),(2,'叶谦','123456',NULL,'EM00002',0,'127.0.0.1','2',2,0),(3,'KiKi','123456',NULL,'EM00003',0,'127.0.0.1','3',3,0),(4,'赵日天','123456',NULL,'EM00004',NULL,'','4',3,0),(5,'赵铁柱','123456',NULL,'EM00005',NULL,NULL,'3',2,0),(6,'陈博','123456',NULL,'EM00006',NULL,NULL,'4',3,0);
