INSERT INTO `oa_admin_rule` VALUES (497, 3, 'user/personal/statistics', '人事报表', '人事报表', 'user', '', 1, 0, 1, 1788484918, 0);
INSERT INTO `oa_admin_rule` VALUES (498, 497, 'user/personal/in_out', '入职离职报表', '入职离职报表', 'user', '', 2, 0, 1, 1788592788, 0);

INSERT INTO `oa_admin_rule` VALUES (499, 137, 'performance/templates/datalist', '绩效模板', '绩效模板', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (500, 499, 'performance/templates/add', '新建/编辑', '绩效模板', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (501, 499, 'performance/templates/set', '设置', '绩效模板', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (502, 499, 'performance/templates/view', '查看', '绩效模板', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (503, 499, 'performance/templates/del', '删除', '绩效模板', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (504, 137, 'performance/users/datalist', '绩效人员', '绩效人员', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (505, 504, 'performance/users/add', '新建/编辑', '绩效人员', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (506, 504, 'performance/users/view', '查看', '绩效人员', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (507, 504, 'performance/users/del', '删除', '绩效人员', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (508, 4, 'performance/performance/datalist', '员工绩效单', '员工绩效单', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (509, 508, 'performance/performance/add', '新建', '员工绩效单', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (510, 508, 'performance/performance/view', '查看', '员工绩效单', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (511, 508, 'performance/performance/del', '删除', '员工绩效单', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (512, 508, 'performance/performance/edit', '编辑', '员工绩效单', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (513, 508, 'performance/records/add', '新增绩效记录', '绩效记录', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (514, 508, 'performance/records/view', '查看绩效记录', '绩效记录', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (515, 508, 'performance/records/edit', '完善绩效记录', '绩效记录', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (516, 5, 'performance/records/datalist', '员工绩效', '员工绩效', 'office', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (517, 516, 'performance/records/myview', '查看', '员工绩效', 'office', '', 2, 1, 1, 0, 0);

UPDATE `oa_admin_group` SET `rules` = '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,206,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,248,249,250,251,252,253,254,255,256,257,258,259,260,261,262,263,264,265,266,267,268,269,270,271,272,273,274,275,276,277,278,279,280,281,282,283,284,285,286,287,288,289,290,291,292,293,294,295,296,297,298,299,300,301,302,303,304,305,306,307,308,309,310,311,312,313,314,315,316,317,318,319,320,321,322,323,324,325,326,327,328,329,330,331,332,333,334,335,336,337,338,339,340,341,342,343,344,345,346,347,348,349,350,351,352,353,354,355,356,357,358,359,360,361,362,363,364,365,366,367,368,369,370,371,372,373,374,375,376,377,378,379,380,381,382,383,384,385,386,387,388,389,390,391,392,393,394,395,396,397,398,399,400,401,402,403,404,405,406,407,408,409,410,411,412,413,414,415,416,417,418,419,420,421,422,423,424,425,426,427,428,429,430,431,432,433,434,435,436,437,438,439,440,441,442,443,444,445,446,447,448,449,450,451,452,453,454,455,456,457,458,459,460,461,462,463,464,465,466,467,468,469,470,471,472,473,474,475,476,477,478,479,480,481,482,483,484,485,486,487,488,489,490,491,492,493,494,495,496,497,498,499,500,501,502,503,504,505,506,507,508,509,510,511,512,513,514,515,516,517' WHERE `id` = 1;

UPDATE `oa_admin_group` SET `mobile_menu` = '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25';

INSERT INTO `oa_template` VALUES (33, '绩效待完善通知', 'performance_send', 1, 0, '', '/performance/records/edit/id/{action_id}', '您有一个『{title}』新通知，请及时查看', '您有一个『{title}』新通知，状态：{status}，请及时处理。', '', '', '', '', '', '', '', 1, 1, 1788766237, 1788766777, 0);
INSERT INTO `oa_template` VALUES (34, '绩效待审核通知', 'performance_check', 1, 0, '', '/performance/records/view/id/{action_id}', '您有一个『{title}』新通知，请及时查看', '您有一个『{title}』新通知，状态：{status}，请及时处理。', '', '', '', '', '', '', '', 1, 1, 1788766810, 0, 0);
INSERT INTO `oa_template` VALUES (35, '绩效被驳回通知', 'performance_refue', 1, 0, '', '/performance/records/edit/id/{action_id}', '您有一个『{title}』新通知，请及时查看', '您有一个『{title}』新通知，状态：{status}，需要重新完善后再提交审核，请及时处理。', '', '', '', '', '', '', '', 1, 1, 1788767162, 0, 0);
INSERT INTO `oa_template` VALUES (36, '绩效审核通过通知', 'performance_ok', 1, 0, '', '/performance/records/view/id/{action_id}', '您有一个『{title}』新通知，请及时查看', '您有一个『{title}』新通知，状态：{status}，请及时查看。', '', '', '', '', '', '', '', 1, 1, 1788767274, 0, 0);

INSERT INTO `oa_mobile_menu` VALUES (25, '我的绩效', 'icon-hetong2', 'cyan', '/qiye/index/performance', 5, 0, 1, 1788841826, 0, 0);
-- ----------------------------
-- Table structure for oa_performance_templates
-- ----------------------------
DROP TABLE IF EXISTS `oa_performance_templates`;
CREATE TABLE `oa_performance_templates`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '模板名称',
  `content` text NOT NULL COMMENT '模板描述',
  `score` int(3) NOT NULL DEFAULT 0 COMMENT '总分',
  `kpi_serialize` text NULL COMMENT 'KPI内容',
  `kpi_score` int(3) NOT NULL DEFAULT 0 COMMENT 'kpi总分',
  `action_serialize` text NULL COMMENT '行为态度内容',
  `action_score` int(3) NOT NULL DEFAULT 0 COMMENT '行为态度总分',
  `event_serialize` text NULL COMMENT '例外事件内容',
  `event_score` int(3) NOT NULL DEFAULT 0 COMMENT '例外事件总分',
  `status` int(1) NOT NULL DEFAULT 1 COMMENT '状态:0禁用,1正常',
  `admin_id` int(11) NOT NULL DEFAULT 0,
  `create_time` bigint(11) NOT NULL DEFAULT 0,
  `update_time` bigint(11) NOT NULL DEFAULT 0,
  `delete_time` bigint(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '绩效模板表';

-- ----------------------------
-- Table structure for oa_performance_users
-- ----------------------------
DROP TABLE IF EXISTS `oa_performance_users`;
CREATE TABLE `oa_performance_users`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT 0 COMMENT '关联员工id',
  `template_id` int(11) NOT NULL DEFAULT 0 COMMENT '绩效模板ID',
  `kpi_uid` int(11) NOT NULL DEFAULT 0 COMMENT 'KPI收集人',
  `action_uid` int(11) NOT NULL DEFAULT 0 COMMENT '行为评价人',
  `event_uid` int(11) NOT NULL DEFAULT 0 COMMENT '事件收集人',
  `check_uid` int(11) NOT NULL DEFAULT 0 COMMENT '行政部审核人',
  `self_ratio` int(11) NOT NULL DEFAULT 0 COMMENT '自评分比例',
  `admin_id` int(11) NOT NULL DEFAULT 0,
  `create_time` bigint(11) NOT NULL DEFAULT 0,
  `update_time` bigint(11) NOT NULL DEFAULT 0,
  `delete_time` bigint(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '绩效员工表';

-- ----------------------------
-- Table structure for oa_performance
-- ----------------------------
DROP TABLE IF EXISTS `oa_performance`;
CREATE TABLE `oa_performance`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '绩效标题',
  `start_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '开始日期',
  `end_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '结束日期',
  `status` int(1) NOT NULL DEFAULT 0 COMMENT '状态:0待发布,1已发布',
  `admin_id` int(11) NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` bigint(11) NOT NULL DEFAULT 0,
  `update_time` bigint(11) NOT NULL DEFAULT 0,
  `delete_time` bigint(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '绩效表';

-- ----------------------------
-- Table structure for oa_performance_records
-- ----------------------------
DROP TABLE IF EXISTS `oa_performance_records`;
CREATE TABLE `oa_performance_records`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT 0 COMMENT '员工id',
  `performance_id` int(11) NOT NULL DEFAULT 0 COMMENT '绩效单ID',
  `template_id` int(11) NOT NULL DEFAULT 0 COMMENT '绩效模板ID',
  `start_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '开始日期',
  `end_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '结束日期',
  `kpi_serialize` text NULL COMMENT 'KPI内容',
  `kpi_score` int(3) NOT NULL DEFAULT 0 COMMENT 'KPI得分',
  `kpi_uid` int(11) NOT NULL DEFAULT 0 COMMENT 'KPI收集人',
  `kpi_check_time` int(11) NOT NULL DEFAULT 0 COMMENT 'KPI审核时间',
  `action_serialize` text NULL COMMENT '行为态度内容',
  `action_score` int(3) NOT NULL DEFAULT 0 COMMENT '行为态度总分',
  `action_uid` int(11) NOT NULL DEFAULT 0 COMMENT '行为评价人',
  `action_check_time` int(11) NOT NULL DEFAULT 0 COMMENT '行为审核时间',
  `event_serialize` text NULL COMMENT '例外事件内容',
  `event_score` int(3) NOT NULL DEFAULT 0 COMMENT '例外事件总分',
  `event_uid` int(11) NOT NULL DEFAULT 0 COMMENT '事件收集人',
  `event_check_time` int(11) NOT NULL DEFAULT 0 COMMENT '事件审核时间',
  `check_uid` int(11) NOT NULL DEFAULT 0 COMMENT '行政部审核人',
  `check_time` int(11) NOT NULL DEFAULT 0 COMMENT '行政部审核时间',
  `check_remark` text NULL COMMENT '审核理由',
  `score` int(11) NOT NULL DEFAULT 100 COMMENT '满分',
  `final_kpi_score` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '最终kpi得分',
  `final_action_score` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '最终行为态度总分',
  `final_event_score` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '最终例外事件总分',
  `final_score` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '最终得分',
  `status` int(1) NOT NULL DEFAULT 0 COMMENT '状态:0待发布绩效单,1待填写绩效,2待审核绩效,3审核不通过,4已审核绩效',
  `create_time` bigint(11) NOT NULL DEFAULT 0,
  `update_time` bigint(11) NOT NULL DEFAULT 0,
  `delete_time` bigint(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '绩效记录表';