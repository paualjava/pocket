-- phpMyAdmin SQL Dump
-- version 2.10.3
-- http://www.phpmyadmin.net
-- 
-- 主机: localhost
-- 生成日期: 2016 �?03 �?18 �?02:13
-- 服务器版本: 5.6.11
-- PHP 版本: 5.5.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- 数据库: `test_wm18`
-- 

-- --------------------------------------------------------

-- 
-- 表的结构 `m_pocket_goods`
-- 

CREATE TABLE `m_pocket_goods` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `goods_id` int(10) NOT NULL COMMENT '产品编号',
  `goods_sn` varchar(60) DEFAULT NULL COMMENT '产品号',
  `goods_name` varchar(200) NOT NULL COMMENT '产品名称',
  `goods_img` varchar(200) NOT NULL COMMENT '产品图片',
  `shop_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '价格',
  `market_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '市场价',
  `is_on_sale` tinyint(1) NOT NULL DEFAULT '1' COMMENT '在售',
  `limit_buy` int(10) NOT NULL COMMENT '限购数量',
  `goods_number` smallint(5) NOT NULL DEFAULT '0' COMMENT '商品库存',
  `sort_order` int(10) NOT NULL COMMENT '排序',
  `is_show` tinyint(1) NOT NULL COMMENT '审核显示',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=76 ;

-- 
-- 导出表中的数据 `m_pocket_goods`
-- 

INSERT INTO `m_pocket_goods` VALUES (1, 7372, 'V1612150-1', '兰蔻美肤修护美容液50ml', '', 52.00, 130.00, 1, 0, 31, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (2, 7371, 'V1612140-1', '兰蔻臻白洁面泡沫30mL', '', 41.60, 84.00, 1, 0, 32, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (3, 7370, 'V1612130-1', '兰蔻新立体塑颜焕活精华乳10ML', '', 46.40, 256.00, 1, 0, 18, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (4, 7369, 'V1612120-1', '兰蔻新立体塑颜紧致面霜15mL', '', 63.20, 269.00, 1, 0, 0, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (5, 7368, 'V1612110-1', '兰蔻新立体塑颜紧致晚霜15mL', '', 63.20, 269.00, 1, 0, 75, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (6, 7367, 'V1612100-1', '兰蔻新精华肌底液7mL', '', 63.20, 182.00, 1, 0, 18, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (7, 7366, 'V1612090-1', '兰蔻新立体塑颜美容液50ml', '', 54.40, 130.00, 1, 0, 26, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (8, 7365, 'V1612080-1', '兰蔻新立体塑颜紧致眼部精华乳5mL', '', 44.00, 210.00, 1, 0, 22, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (9, 7364, 'V1612070-1', '兰蔻美肤修护精华乳7ML', '', 34.00, 182.00, 1, 0, 254, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (10, 7363, 'V1612060-1', '兰蔻菁纯臻颜日霜5mL', '', 34.00, 268.00, 1, 0, 89, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (11, 7362, 'V1612050-1', '兰蔻奇迹薄纱粉底液5ml', '', 26.00, 87.00, 1, 0, 188, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (12, 7356, 'V1542120-1', '秘密花园玫瑰鲜活水 体验装', '', 9.90, 79.00, 1, 0, 4407, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (13, 7353, 'V1542110-1', '维您百合康牌芦荟软胶囊2件组', '', 276.00, 276.00, 1, 0, 0, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (14, 7352, 'V1542100-1', '韩束光感亮肤霜（CC霜）40ml', '', 149.00, 169.00, 1, 0, 31, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (15, 7351, 'V1542090-1', '韩束墨菊深度补水八件套组  ', '', 359.00, 399.00, 1, 0, 0, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (16, 7350, 'V1542080-1', '韩束橄榄卸妆水320ml', '', 59.00, 69.00, 1, 0, 5, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (17, 7349, 'V1542070-1', '维您百合康牌越橘叶黄素软胶囊2件组', '', 316.00, 316.00, 1, 0, 0, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (18, 7348, 'V1542060-1', '爱可丽净婴儿专用高效洗衣液（无香型）', '', 5.00, 99.00, 1, 0, 0, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (19, 7269, 'V1503080-1', '维您蛋白粉', '', 199.00, 398.00, 1, 0, 103, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (20, 7268, 'V1503070-1', '维您百合康牌芦荟软胶囊', '', 69.00, 138.00, 1, 0, 904, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (21, 7267, 'V1503060-1', '维您百合康牌越橘叶黄素软胶囊', '', 79.00, 158.00, 1, 0, 981, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (22, 7266, 'V1503050-1', '维您百合康牌葡萄籽大豆提取物维生素E软胶囊', '', 69.00, 138.00, 1, 0, 973, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (23, 7261, 'V1503000-1', '韩束墨菊深度补水睡眠面膜50g', '', 79.00, 95.00, 1, 0, 85, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (24, 7260, 'V1502930-1', '韩束红石榴鲜活水盈六件套', '', 299.00, 598.00, 1, 0, 81, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (25, 7259, 'V1503010-1', '韩束红石榴鲜活倍润面贴膜10片', '', 79.00, 99.00, 1, 0, 77, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (26, 7258, 'V1503030-1', '韩束金盏花温泉花卉去角质素130ml', '', 36.00, 45.00, 1, 0, 6, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (27, 7257, 'V1503020-1', '韩束墨菊深度滋润眼霜15g', '', 159.00, 165.00, 1, 0, 6, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (28, 7255, 'V1502980-1', '韩束墨菊深度补水八件套组  ', '', 259.00, 399.00, 1, 0, 0, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (29, 7254, 'V1502970-1', '韩束雪白肌美白亮肤霜30＃40ml', '', 129.00, 159.00, 1, 0, 7, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (30, 7253, 'V1502960-1', '韩束光感亮肤霜（CC霜）40ml', '', 129.00, 169.00, 1, 0, 31, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (31, 7252, 'V1502950-1', '韩束闪亮雪肌香体膏25g', '', 119.00, 169.00, 1, 0, 58, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (32, 7251, 'V1502940-1', '韩束橄榄卸妆水320ml', '', 49.00, 69.00, 1, 0, 5, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (33, 7250, 'V1502920-1', '韩束墨菊深度补水露120ml ', '', 99.00, 139.00, 1, 0, 53, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (34, 7249, 'V1502910-1', '韩束纤纤伴手礼盒40ml*3', '', 45.90, 69.00, 1, 0, 0, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (35, 7209, 'WM007209', '2016玫瑰鲜活水众筹礼券', '', 300.00, 300.00, 1, 0, 844, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (36, 7208, 'V1505670-1', '秘密花园玫瑰鲜活水100ml +9.5ml', '', 568.00, 568.00, 1, 0, 1002, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (37, 7205, 'V1610840-1', '秘密花园玫瑰鲜活水 体验装', '', 79.00, 79.00, 1, 0, 4409, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (38, 7194, 'V1502900-1', 'Htree谷物大豆蛋白固体饮料', '', 158.00, 258.00, 1, 0, 199, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (39, 7190, 'V1502870-1', '韩束闪亮雪肌香体膏25g', '', 99.00, 169.00, 1, 0, 58, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (40, 7189, 'V1502860-1', '韩束红石榴鲜活水盈六件套', '', 299.00, 598.00, 1, 0, 81, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (41, 7188, 'V1502850-1', '韩束金盏花温泉花卉去角质素130ml', '', 36.00, 45.00, 1, 0, 6, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (42, 7187, 'V1502840-1', '韩束墨菊深度滋润眼霜15g', '', 159.00, 165.00, 1, 0, 6, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (43, 7186, 'V1502830-1', '韩束红石榴鲜活倍润面贴膜10片', '', 79.00, 99.00, 1, 0, 77, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (44, 7185, 'V1502820-1', '韩束墨菊深度补水睡眠面膜50g', '', 79.00, 95.00, 1, 0, 85, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (45, 7182, 'V15411630-1', '乐途350ml直身保温杯(送便携餐具） 深海蓝', '', 1.00, 168.00, 1, 0, 0, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (46, 7181, 'V15411629-1', '乐途350ml直身保温杯(送便携餐具） 樱花粉', '', 1.00, 168.00, 1, 0, 38, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (47, 7180, 'V1541159-1', '素雅秋款居家服 杏色 XL', '', 39.00, 369.00, 1, 0, 7, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (48, 7179, 'V1541153-1', '素雅秋款居家服 杏色 L', '', 39.00, 369.00, 1, 0, 9, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (49, 7174, 'V1541130-1', '龙粹有机稻花香米1kg', '', 1.00, 78.00, 1, 0, 223, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (50, 7142, 'V1610220-1', '超值福袋', '', 99.00, 99.00, 1, 0, 74, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (52, 7121, 'V1610130-1', '伴月星辰珍珠耳环', '', 23800.00, 23800.00, 1, 0, 0, 0, 2);
INSERT INTO `m_pocket_goods` VALUES (53, 7120, 'V1610120-1', '金色圆舞珍珠项链', '', 21800.00, 21800.00, 1, 0, 0, 0, 2);
INSERT INTO `m_pocket_goods` VALUES (54, 7119, 'V1610110-1', '金色圆舞珍珠耳环', '', 23800.00, 23800.00, 1, 0, 0, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (55, 7115, 'V1540830-1', '秘密花园美肌修护BB霜送秘密花园摩洛哥纯阿甘油30ML', '', 128.00, 628.00, 1, 0, 113, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (56, 7113, 'V1540810-1', '秘密花园摩洛哥纯阿甘油30ML', '', 79.00, 480.00, 1, 0, 112, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (57, 7111, 'V1610050-1', '秘密花园摩洛哥纯阿甘油30ML', '', 480.00, 480.00, 1, 0, 112, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (58, 7108, 'V1540780-1', '秘密花园玫瑰鲜活水 30ml', '', 99.00, 169.00, 1, 0, 988, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (59, 7105, 'V1540760-1', '上宜暖宫宝', '', 1.00, 32.80, 1, 0, 1, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (60, 7069, 'V15028039-1', '羊绒真丝保暖三件套 亮紫色', '', 179.00, 379.00, 1, 0, 12, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (61, 7068, 'V15028038-1', '羊绒真丝保暖三件套 铁花灰', '', 179.00, 379.00, 1, 0, 14, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (62, 7059, 'V1502700-1', '雅诗兰黛清澈净白修颜霜15ml', '', 169.00, 230.00, 1, 0, 0, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (63, 7058, 'V1502690-1', '雅诗兰黛 青春抗皱系列 6件套', '', 269.00, 908.00, 1, 0, 0, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (64, 7057, 'V1502680-1', '雅诗兰黛清澈净白修颜霜SPF30+/PA+++', '', 359.00, 460.00, 1, 0, 0, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (65, 7054, 'V1502650-1', '雅诗兰黛鲜亮焕采泡沫洁面乳125mL', '', 229.00, 280.00, 1, 0, 1, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (66, 7052, 'V1502630-1', '雅诗兰黛红石榴晚霜7ML', '', 69.00, 87.00, 1, 0, 1, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (67, 7051, 'V1502620-1', '悦木之源 韦博士灵芝焕能精华水30ml', '', 39.00, 48.00, 1, 0, 9, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (68, 7050, 'V1502610-1', '悦木之源 轻松去油面膜30ml', '', 69.00, 81.00, 1, 0, 7, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (69, 7049, 'V1502600-1', '悦木之源水润畅饮夜间密集滋养面膜30ml', '', 59.00, 65.00, 1, 0, 0, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (70, 7048, 'V1502590-1', '娇韵诗清透润白柔肤水（清爽型）50ml ', '', 69.00, 98.00, 1, 0, 0, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (71, 7047, 'V1502580-1', '贝玲妃  防水彩妆卸妆乳7.5毫升', '', 19.00, 27.00, 1, 0, 0, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (72, 7046, 'V1502570-1', '贝玲妃 玫瑰胭脂水2.5ml', '', 49.00, 69.00, 1, 0, 13, 0, 1);
INSERT INTO `m_pocket_goods` VALUES (73, 7045, 'V1502560-1', '科颜氏 金盏花爽肤水 40ml ', '', 45.00, 53.00, 1, 0, 0, 0, 2);
INSERT INTO `m_pocket_goods` VALUES (75, 7043, 'V1502540-1', '韩束墨菊深度补水八件套组  ', '', 299.00, 399.00, 1, 0, 0, 0, 2);
