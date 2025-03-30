ALTER TABLE `oa_admin` 
ADD COLUMN `auth_dids` varchar(500) NOT NULL DEFAULT '' COMMENT '可见部门数据' AFTER `auth_did`;

ALTER TABLE `oa_admin` 
ADD COLUMN `son_dids` varchar(500) NOT NULL DEFAULT '' COMMENT '可见子部门数据' AFTER `auth_dids`;

ALTER TABLE `oa_admin_group` 
ADD COLUMN `mobile_bar` mediumtext  NULL COMMENT '手机端Bar' AFTER `layouts`;

ALTER TABLE `oa_admin_group` 
ADD COLUMN `mobile_menu` mediumtext  NULL COMMENT '手机端工作台' AFTER `mobile_bar`;

ALTER TABLE `oa_project` 
ADD COLUMN `code` varchar(255) NOT NULL DEFAULT '' COMMENT '项目编号' AFTER `name`;

ALTER TABLE `oa_project` 
ADD COLUMN `amount` decimal(15, 2) NULL DEFAULT 0.00 COMMENT '项目金额' AFTER `code`;

ALTER TABLE `oa_project_task` 
ADD COLUMN `did` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属部门' AFTER `director_uid`;

ALTER TABLE `oa_project_document` 
ADD COLUMN `did` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属部门' AFTER `title`;

-- ----------------------------
-- Table structure for oa_comment
-- ----------------------------
DROP TABLE IF EXISTS `oa_comment`;
CREATE TABLE `oa_comment`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `module` varchar(100) NOT NULL DEFAULT '' COMMENT '模块',
  `topic_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联主题id',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '回复内容id',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `to_uids` varchar(100) NOT NULL DEFAULT '' COMMENT '@用户id',
  `content` mediumtext  NULL COMMENT '评论内容',
  `md_content` mediumtext  NULL COMMENT 'markdown评论内容',
  `create_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '添加时间',
  `update_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
  `delete_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '评论表';

-- ----------------------------
-- Table structure for oa_comment_read
-- ----------------------------
DROP TABLE IF EXISTS `oa_comment_read`;
CREATE TABLE `oa_comment_read`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `comment_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联评论id',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '已读人',
  `create_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '添加时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '评论已读回执表';

-- ----------------------------
-- Table structure for oa_edit_log
-- ----------------------------
DROP TABLE IF EXISTS `oa_edit_log`;
CREATE TABLE `oa_edit_log`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '标识',
  `field` varchar(100) NOT NULL DEFAULT '' COMMENT '字段',
  `action_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联id',
  `second_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '第二关联id',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作人',
  `old_content` mediumtext  NULL COMMENT '修改前的内容',
  `new_content` mediumtext  NULL COMMENT '修改后的内容',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '编辑数据记录表';

-- ----------------------------
-- Table structure for oa_mobile_bar
-- ----------------------------
DROP TABLE IF EXISTS `oa_mobile_bar`;
CREATE TABLE `oa_mobile_bar`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '名称',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '图标',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '链接',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序：越大越靠前',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '手机端Bar表';

-- ----------------------------
-- Records of oa_mobile_bar
-- ----------------------------
INSERT INTO `oa_mobile_bar` VALUES (1, '工作台', 'icon-gongzuotai1', '/qiye/index/index', 0, 1, 1723277311, 1733147213);
INSERT INTO `oa_mobile_bar` VALUES (2, '客户', 'icon-kehu', '/qiye/customer/index', 0, 1, 1723277311, 1733147238);
INSERT INTO `oa_mobile_bar` VALUES (3, '合同', 'icon-hetong2', '/qiye/contract/index', 0, 1, 1723277351, 1733147266);
INSERT INTO `oa_mobile_bar` VALUES (4, '项目', 'icon-xiangmu1', '/qiye/project/index', 0, 1, 1723277356, 1733147298);
INSERT INTO `oa_mobile_bar` VALUES (5, '消息', 'icon-xiaoxi1', '/qiye/msg/index', 0, 1, 1723277368, 1733147331);

-- ----------------------------
-- Table structure for oa_mobile_types
-- ----------------------------
DROP TABLE IF EXISTS `oa_mobile_types`;
CREATE TABLE `oa_mobile_types`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '名称',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '图标',
  `bgcolor` varchar(255) NOT NULL DEFAULT '' COMMENT '背景颜色',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序：越大越靠前',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '手机端工作台菜单类型表';

-- ----------------------------
-- Records of oa_mobile_types
-- ----------------------------
INSERT INTO `oa_mobile_types` VALUES (1, '办公审批', '', 'blue', 0, 1, 1723277311, 0);
INSERT INTO `oa_mobile_types` VALUES (2, '效率工具', '', 'green', 0, 1, 1723277311, 0);
INSERT INTO `oa_mobile_types` VALUES (3, '内部管理', '', 'yellow', 0, 1, 1723277351, 0);
INSERT INTO `oa_mobile_types` VALUES (4, '财务管理', '', 'purple', 0, 1, 1723277356, 0);

-- ----------------------------
-- Table structure for oa_mobile_menu
-- ----------------------------
DROP TABLE IF EXISTS `oa_mobile_menu`;
CREATE TABLE `oa_mobile_menu`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '名称',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '图标',
  `bgcolor` varchar(255) NOT NULL DEFAULT '' COMMENT '背景颜色',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '链接',
  `types` int(11) NOT NULL DEFAULT 0 COMMENT '关联菜单类型',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序：越大越靠前',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '手机端菜单表';

-- ----------------------------
-- Records of oa_mobile_menu
-- ----------------------------
INSERT INTO `oa_mobile_menu` VALUES (1, '审批申请', 'icon-shenpi','blue', '/qiye/approve/apply', 1, 0, 1, 1733146294, 1733152760, 0);
INSERT INTO `oa_mobile_menu` VALUES (2, '我申请的', 'icon-wodeshenpi1','blue', '/qiye/approve/mylist', 1, 0, 1, 1733152749, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (3, '我处理的', 'icon-chulishenpi','blue', '/qiye/approve/checklist', 1, 0, 1, 1733152798, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (4, '抄送给我', 'icon-chaosong','blue', '/qiye/approve/copylist', 1, 0, 1, 1733152823, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (5, '日程安排', 'icon-richeng','green', '/qiye/index/calendar', 2, 0, 1, 1733152855, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (6, '工作记录', 'icon-jilu','green', '/qiye/index/schedule', 2, 0, 1, 1733152878, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (7, '工作汇报', 'icon-huibao','green', '/qiye/index/work', 2, 0, 1, 1733152906, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (8, '公告通知', 'icon-gonggaotongzhi','yellow', '/qiye/index/note', 3, 0, 1, 1733152965, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (9, '公司新闻', 'icon-gongsixinwen','yellow', '/qiye/index/news', 3, 0, 1, 1733152993, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (10, '会议纪要', 'icon-huiyijiyao','yellow', '/qiye/index/meeting', 3, 0, 1, 1733152993, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (11, '报销管理', 'icon-a-baoxiao3','purple', '/qiye/finance/expense', 4, 0, 1, 1733153019, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (12, '开票管理', 'icon-kaipiao','purple', '/qiye/finance/invoice', 4, 0, 1, 1733153047, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (13, '收票管理', 'icon-shoupiao','purple', '/qiye/finance/ticket', 4, 0, 1, 1733153077, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (14, '回款管理', 'icon-huikuan','purple', '/qiye/finance/income', 4, 0, 1, 1733153106, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (15, '付款管理', 'icon-fukuan','purple', '/qiye/finance/payment', 4, 0, 1, 1733153131, 0, 0);


-- ----------------------------
-- Table structure for oa_admin_rule
-- ----------------------------
DROP TABLE IF EXISTS `oa_admin_rule`;
CREATE TABLE `oa_admin_rule`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父id',
  `src` varchar(255) NOT NULL DEFAULT '' COMMENT 'url链接',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '日志操作名称',
  `module` varchar(255) NOT NULL DEFAULT '' COMMENT '所属模块',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '图标',
  `menu` int(1) NOT NULL DEFAULT 0 COMMENT '是否是菜单,1是,2不是',
  `sort` int(11) NOT NULL DEFAULT 1 COMMENT '越小越靠前',
  `status` int(1) NOT NULL DEFAULT 1 COMMENT '状态,0禁用,1正常',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '菜单及权限表';

-- ----------------------------
-- Records of oa_admin_rule
-- ----------------------------
INSERT INTO `oa_admin_rule` VALUES (1, 0, '', '系统管理', '系统管理', 'home', 'icon-jichupeizhi', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (2, 0, '', '基础数据', '基础数据', 'base', 'icon-hetongshezhi', 1, 2, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (3, 0, '', '人事管理', '人事管理', 'user', 'icon-renshishezhi', 1, 3, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (4, 0, '', '行政办公', '行政办公', 'adm', 'icon-banjiguanli', 1, 4, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (5, 0, '', '个人办公', '个人办公', 'office', 'icon-kaoshijihua', 1, 5, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (6, 0, '', '财务管理', '财务管理', 'finance', 'icon-yuangongtidian', 1, 6, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (7, 0, '', '客户管理', '客户管理', 'customer', 'icon-kehuguanli', 1, 7, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (8, 0, '', '合同管理', '合同管理', 'contract', 'icon-hetongyidong', 1, 8, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (9, 0, '', '项目管理', '项目管理', 'project', 'icon-xiangmuguanli', 1, 9, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (10, 0, '', '知识网盘', '知识网盘', 'disk', 'icon-tikuguanli', 1, 10, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (11, 1, 'home/conf/index', '系统配置', '系统配置', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (12, 11, 'home/conf/add', '新建/编辑', '配置项', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (13, 11, 'home/conf/delete', '删除', '配置项', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (14, 11, 'home/conf/edit', '编辑', '配置详情', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (15, 1, 'home/module/index', '功能模块', '功能模块', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (16, 15, 'home/module/add', '新建/编辑', '功能模块', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (17, 15, 'home/module/del', '删除', '功能模块', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (18, 15, 'home/module/recovery', '恢复', '功能模块', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (19, 15, 'home/module/install', '安装', '功能模块', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (20, 1, 'home/dataauth/index', '模块配置', '模块配置', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (21, 20, 'home/dataauth/edit', '编辑', '模块配置', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (22, 1, 'home/rule/index', '功能节点', '功能节点', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (23, 22, 'home/rule/add', '新建/编辑', '功能节点', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (24, 22, 'home/rule/delete', '删除', '功能节点', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (25, 1, 'mobile/bar/datalist', '移动端配置', '移动端配置', 'home', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (26, 25, 'mobile/bar/datalist', 'BAR菜单', 'BAR菜单', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (27, 26, 'mobile/bar/add', '新建/编辑', 'BAR菜单', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (28, 26, 'mobile/bar/set', '设置', 'BAR菜单', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (29, 25, 'mobile/types/datalist', '工作台菜单类型', '工作台菜单类型', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (30, 29, 'mobile/types/add', '新建/编辑', '工作台菜单类型', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (31, 29, 'mobile/types/del', '删除', '工作台菜单类型', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (32, 29, 'mobile/types/set', '设置', '工作台菜单类型', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (33, 25, 'mobile/menu/datalist', '工作台菜单', '工作台菜单', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (34, 33, 'mobile/menu/add', '新建/编辑', '工作台菜单', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (35, 33, 'mobile/menu/del', '删除', '工作台菜单', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (36, 33, 'mobile/menu/set', '设置', '工作台菜单', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (37, 1, 'home/role/index', '角色权限', '角色权限', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (38, 37, 'home/role/add', '新建/编辑', '角色权限', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (39, 37, 'home/role/delete', '删除', '角色权限', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (40, 1, 'home/log/index', '操作日志', '操作日志', 'home', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (41, 1, 'home/files/index', '附件管理','附件管理', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (42, 41, 'home/files/edit', '编辑附件','附件', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (43, 41, 'home/files/move', '移动附件','附件', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (44, 41, 'home/files/delete', '删除附件','附件', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (45, 41, 'home/files/get_group', '附件分组','附件分组', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (46, 41, 'home/files/add_group', '新建/编辑','附件分组', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (47, 41, 'home/files/del_group', '删除附件分组','附件分组', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (48, 1, 'home/database/database', '备份数据', '数据备份', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (49, 48, 'home/database/backup', '备份数据表', '数据', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (50, 48, 'home/database/optimize', '优化数据表', '数据表', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (51, 48, 'home/database/repair', '修复数据表', '数据表', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (52, 1, 'home/database/backuplist', '还原数据', '数据还原', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (53, 52, 'home/database/import', '还原数据表', '数据', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (54, 52, 'home/database/downfile', '下载备份数据', '备份数据', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (55, 52, 'home/database/del', '删除备份数据', '备份数据', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (56, 1, 'home/task/index', '定时任务', '定时任务', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (57, 56, 'home/task/add', '新建/编辑', '定时任务', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (58, 56, 'home/task/delete', '删除', '定时任务', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (59, 2, '', '公共模块', '公共模块', 'home', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (60, 59, 'home/template/datalist', '消息模板', '消息模板', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (61, 60, 'home/template/add', '新建/编辑', '消息模板', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (62, 60, 'home/template/set', '设置', '消息模板', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (63, 60, 'home/template/view', '查看', '消息模板', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (64, 59, 'adm/flow/modulelist', '审批模块', '审批模块', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (65, 64, 'adm/flow/module_add', '新建/编辑', '审批模块', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (66, 64, 'adm/flow/module_check', '设置', '审批模块', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (67, 59, 'adm/flow/catelist', '审批类型', '审批类型', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (68, 67, 'adm/flow/cate_add', '新建/编辑', '审批类型', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (69, 67, 'adm/flow/cate_check', '设置', '审批类型', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (70, 59, 'adm/flow/datalist', '审批流程', '审批流程', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (71, 70, 'adm/flow/add', '新建/编辑', '审批流程', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (72, 70, 'adm/flow/del', '删除', '审批流程', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (73, 70, 'adm/flow/check', '设置', '审批流程', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (74, 59, 'home/cate/enterprise', '企业主体', '企业主体', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (75, 74, 'home/cate/enterprise_add', '新建/编辑', '企业主体', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (76, 74, 'home/cate/enterprise_set', '设置', '企业主体', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (77, 59, 'home/area/datalist', '全国省市', '全国省市', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (78, 77, 'home/area/add', '新建/编辑', '全国省市', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (79, 77, 'home/area/set', '设置', '全国省市', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (80, 2, '', '人事模块', '人事模块', 'user', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (81, 80, 'user/rewardscate/datalist', '奖罚项目', '奖罚项目', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (82, 81, 'user/rewardscate/add', '新建/编辑', '奖罚项目', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (83, 81, 'user/rewardscate/set', '设置', '奖罚项目', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (84, 80, 'user/carecate/datalist', '关怀项目', '关怀项目', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (85, 84, 'user/carecate/add', '新建/编辑', '关怀项目', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (86, 84, 'user/carecate/set', '设置', '关怀项目', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (87, 80, 'user/basic/datalist', '常规数据', '常规数据', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (88, 87, 'user/basic/add', '新建/编辑', '常规数据', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (89, 87, 'user/basic/set', '设置', '常规数据', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (90, 3, 'user/department/index', '部门架构', '部门', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (91, 90, 'user/department/add', '新建/编辑', '部门', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (92, 90, 'user/department/delete', '删除', '部门', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (93, 3, 'user/position/index', '岗位职称', '岗位职称', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (94, 93, 'user/position/add', '新建/编辑', '岗位职称', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (95, 93, 'user/position/delete', '删除', '岗位职称', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (96, 93, 'user/position/view', '查看', '岗位职称', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (97, 3, 'user/user/index', '企业员工', '员工', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (98, 97, 'user/user/add', '新建/编辑', '员工', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (99, 97, 'user/user/view', '查看', '员工信息', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (100, 97, 'user/user/set', '设置', '员工状态', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (101, 97, 'user/user/reset_psw', '重设密码', '员工密码', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (102, 3, 'user/files/datalist', '员工档案', '员工档案', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (103, 102, 'user/files/add', '编辑', '员工档案', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (104, 102, 'user/files/view', '查看', '员工档案', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (105, 102, 'user/files/set', '设置', '员工档案', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (106, 3, 'user/personal/change', '人事调动', '人事调动', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (107, 106, 'user/personal/change_add', '新建/编辑', '人事调动', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (108, 3, 'user/personal/leave', '离职档案', '离职档案', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (109, 108, 'user/personal/leave_add', '新建/编辑', '离职档案', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (110, 108, 'user/personal/leave_delete', '删除', '离职档案', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (111, 108, 'user/personal/leave_check', '资料交接', '离职资料', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (112, 3, 'user/rewards/datalist', '奖罚管理', '奖罚管理', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (113, 112, 'user/rewards/add', '新建/编辑', '奖罚管理', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (114, 112, 'user/rewards/view', '查看', '奖罚管理', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (115, 112, 'user/rewards/del', '删除', '奖罚管理', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (116, 3, 'user/care/datalist', '员工关怀', '员工关怀', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (117, 116, 'user/care/add', '新建/编辑', '员工关怀', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (118, 116, 'user/care/view', '查看', '员工关怀', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (119, 116, 'user/care/del', '删除', '员工关怀', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (120, 3, 'user/laborcontract/datalist', '员工合同', '员工合同', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (121, 120, 'user/laborcontract/add', '新建/编辑', '员工合同', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (122, 120, 'user/laborcontract/add_renewal', '续签', '员工合同', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (123, 120, 'user/laborcontract/add_change', '变更', '员工合同', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (124, 120, 'user/laborcontract/view', '查看', '员工合同', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (125, 120, 'user/laborcontract/del', '删除', '员工合同', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (126, 120, 'user/laborcontract/set', '设置', '员工合同', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (127, 2, '', '行政模块', '行政模块', 'adm', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (128, 127, 'adm/propertycate/datalist', '资产分类', '资产分类', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (129, 128, 'adm/propertycate/add', '新建/编辑', '资产分类', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (130, 128, 'adm/propertycate/del', '删除', '资产分类', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (131, 127, 'adm/propertybrand/datalist', '资产品牌', '资产品牌', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (132, 131, 'adm/propertybrand/add', '新建/编辑', '资产品牌', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (133, 131, 'adm/propertybrand/check', '设置', '资产品牌', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (134, 127, 'adm/propertyunit/datalist', '资产单位', '资产单位', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (135, 134, 'adm/propertyunit/add', '新建/编辑', '资产单位', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (136, 134, 'adm/propertyunit/check', '设置', '资产单位', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (137, 127, 'adm/sealcate/datalist', '印章管理', '印章', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (138, 137, 'adm/sealcate/add', '新建/编辑', '印章', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (139, 137, 'adm/sealcate/check', '设置', '印章', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (140, 127, 'adm/basic/datalist', '常规数据', '常规数据', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (141, 140, 'adm/basic/add', '新建/编辑', '常规数据', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (142, 140, 'adm/basic/set', '设置', '常规数据', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (143, 4, '', '固定资产', '固定资产', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (144, 143, 'adm/property/datalist', '资产信息', '固定资产', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (145, 144, 'adm/property/add', '新建/编辑', '固定资产', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (146, 144, 'adm/property/check', '设置', '固定资产', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (147, 144, 'adm/property/view', '查看', '固定资产', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (148, 143, 'adm/property/repair_list', '报修记录', '资产报修记录', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (149, 148, 'adm/property/repair_add', '新建/编辑', '资产报修记录', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (150, 148, 'adm/property/repair_view', '查看', '资产报修记录', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (151, 148, 'adm/property/repair_del', '删除', '资产报修记录', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (152, 4, '', '车辆管理', '车辆', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (153, 152, 'adm/car/datalist', '车辆信息', '车辆', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (154, 153, 'adm/car/add', '新建/编辑', '车辆', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (155, 153, 'adm/car/check', '设置', '车辆', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (156, 153, 'adm/car/view', '查看', '车辆', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (157, 152, 'adm/car/repair_list', '车辆维修', '车辆维修记录', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (158, 157, 'adm/car/repair_add', '新建/编辑', '车辆维修记录', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (159, 157, 'adm/car/repair_view', '查看', '车辆维修记录', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (160, 157, 'adm/car/repair_del', '删除', '车辆维修记录', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (161, 152, 'adm/car/protect_list', '车辆保养', '车辆保养记录', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (162, 161, 'adm/car/protect_add', '新建/编辑', '车辆保养记录', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (163, 161, 'adm/car/protect_view', '查看', '车辆保养记录', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (164, 161, 'adm/car/protect_del', '删除', '车辆保养记录', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (165, 152, 'adm/car/mileage_list', '车辆里程', '车辆里程记录', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (166, 165, 'adm/car/mileage_add', '新建/编辑', '车辆里程记录', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (167, 165, 'adm/car/mileage_del', '删除', '车辆里程记录', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (168, 152, 'adm/car/fee_list', '车辆费用', '车辆费用记录', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (169, 168, 'adm/car/fee_add', '新建/编辑', '车辆费用记录', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (170, 168, 'adm/car/fee_view', '查看', '车辆费用记录', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (171, 168, 'adm/car/fee_del', '删除', '车辆费用记录', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (172, 4, '', '会议管理', '会议管理', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (173, 172, 'adm/meeting/room', '会议室管理', '会议室', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (174, 173, 'adm/meeting/room_add', '新建/编辑', '会议室', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (175, 173, 'adm/meeting/room_view', '查看', '会议纪要', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (176, 173, 'adm/meeting/room_check', '设置', '会议室', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (177, 172, 'adm/meeting/records', '会议记录', '会议记录', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (178, 177, 'adm/meeting/records_add', '新建/编辑', '会议记录', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (179, 177, 'adm/meeting/records_view', '查看', '会议纪要', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (180, 177, 'adm/meeting/records_del', '删除', '会议纪要', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (181, 4, '', '公文管理', '公文管理', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (182, 181, 'adm/official/datalist', '公文列表', '公文管理', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (183, 182, 'adm/official/add', '新建/编辑', '公文管理', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (184, 182, 'adm/official/view', '查看', '公文管理', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (185, 182, 'adm/official/del', '删除', '公文管理', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (186, 181, 'adm/official/pending', '待审公文', '公文管理', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (187, 181, 'adm/official/reviewed', '已审公文', '公文管理', 'adm', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (188, 4, '', '用章管理', '用章管理', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (189, 188, 'adm/seal/datalist', '用章申请', '用章申请', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (190, 189, 'adm/seal/add', '新建/编辑', '用章申请', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (191, 189, 'adm/seal/view', '查看', '用章申请', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (192, 189, 'adm/seal/del', '删除', '用章申请', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (193, 188, 'adm/seal/record', '用章记录', '用章记录', 'adm', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (194, 127, 'adm/notecate/datalist', '公告类型', '公告类型', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (195, 194, 'adm/notecate/add', '新建/编辑', '公告类型', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (196, 194, 'adm/notecate/set', '设置', '公告类型', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (197, 4, 'adm/note/datalist', '公告列表', '公告', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (198, 197, 'adm/note/add', '新建/编辑', '公告', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (199, 197, 'adm/note/del', '删除', '公告', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (200, 197, 'adm/note/view', '查看', '公告', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (201, 4, 'adm/news/datalist', '公司新闻', '公司新闻', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (202, 201, 'adm/news/add', '新建/编辑', '公司新闻', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (203, 201, 'adm/news/del', '删除', '公司新闻', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (204, 201, 'adm/news/view', '查看', '公司新闻', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (205, 5, 'oa/plan/datalist', '日程安排', '日程安排', 'oa', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (206, 205, 'oa/plan/add', '新建/编辑', '日程安排', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (207, 205, 'oa/plan/view', '查看', '日程安排', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (208, 205, 'oa/plan/del', '删除', '日程安排', 'oa', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (209, 5, 'oa/plan/calendar', '日程日历', '日程安排', 'oa', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (210, 5, 'oa/schedule/datalist', '工作记录', '工作记录', 'oa', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (211, 210, 'oa/schedule/add', '新建/编辑', '工作记录', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (212, 210, 'oa/schedule/view', '查看', '工作记录', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (213, 210, 'oa/schedule/del', '删除', '工作记录', 'oa', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (214, 5, 'oa/schedule/calendar', '工作日历', '工作日历', 'oa', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (215, 5, 'oa/work/datalist', '工作汇报', '工作汇报', 'oa', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (216, 215, 'oa/work/add', '新建/编辑', '工作汇报', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (217, 215, 'oa/work/send', '发送', '工作汇报', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (218, 215, 'oa/work/view', '查看', '工作汇报', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (219, 215, 'oa/work/del', '删除', '工作汇报', 'oa', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (220, 5, 'oa/note/datalist', '公告通知', '公告通知', 'oa', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (221, 220, 'oa/note/view', '查看', '公告通知', 'oa', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (222, 5, 'oa/news/datalist', '公司新闻', '公司新闻', 'oa', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (223, 222, 'oa/news/view', '查看', '公司新闻', 'oa', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (224, 5, 'oa/meeting/datalist', '会议纪要', '会议纪要', 'oa', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (225, 224, 'oa/meeting/view', '查看', '会议纪要', 'oa', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (226, 2, '', '财务模块', '财务模块', 'finance', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (227, 226, 'finance/expensecate/datalist', '报销类型', '报销类型', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (228, 227, 'finance/expensecate/add', '新建/编辑', '报销类型', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (229, 227, 'finance/expensecate/set', '设置', '报销类型', 'finance', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (230, 226, 'finance/costcate/datalist', '费用类型', '费用类型', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (231, 230, 'finance/costcate/add', '新建/编辑', '费用类型', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (233, 230, 'finance/costcate/set', '设置', '费用类型', 'finance', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (234, 6, 'finance/expense/datalist', '报销管理', '报销', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (235, 234, 'finance/expense/add', '新建/编辑', '报销', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (236, 234, 'finance/expense/del', '删除', '报销', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (237, 234, 'finance/expense/view', '查看', '报销', 'finance', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (238, 6, 'finance/invoice/datalist', '开票管理', '发票', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (239, 238, 'finance/invoice/add', '新建/编辑', '发票', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (240, 238, 'finance/invoice/del', '删除', '发票', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (241, 238, 'finance/invoice/view', '查看', '发票', 'finance', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (242, 6, 'finance/ticket/datalist', '收票管理', '发票', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (243, 242, 'finance/ticket/add', '新建/编辑', '发票', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (244, 242, 'finance/ticket/delete', '删除', '发票', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (245, 242, 'finance/ticket/view', '查看', '发票', 'finance', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (246, 6, 'finance/income/datalist', '回款管理', '回款记录', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (247, 246, 'finance/income/add', '新建/编辑', '回款记录', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (248, 246, 'finance/income/view', '查看', '回款记录', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (249, 246, 'finance/income/del', '删除', '回款记录', 'finance', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (250, 6, 'finance/invoice/datalist_a', '无发票回款', '无发票回款', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (251, 250, 'finance/invoice/add_a', '新建/编辑', '无发票回款', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (252, 250, 'finance/invoice/del_a', '删除', '无发票回款', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (253, 250, 'finance/invoice/view_a', '查看', '无发票回款', 'finance', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (254, 6, 'finance/payment/datalist', '付款管理', '付款记录', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (255, 254, 'finance/payment/add', '新建/编辑', '付款记录', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (256, 254, 'finance/payment/view', '查看', '付款记录', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (257, 254, 'finance/payment/del', '删除', '付款记录', 'finance', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (258, 6, 'finance/ticket/datalist_a', '无发票付款', '无发票付款', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (259, 258, 'finance/ticket/add_a', '新建/编辑', '无发票付款', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (260, 258, 'finance/ticket/del_a', '删除', '无发票付款', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (261, 258, 'finance/ticket/view_a', '查看', '无发票付款', 'finance', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (262, 6, '', '财务统计', '财务统计', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (263, 262, 'finance/expense/record', '报销记录', '报销记录', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (264, 262, 'finance/invoice/record', '开票记录', '开票记录', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (265, 262, 'finance/ticket/record', '收票记录', '收票记录', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (266, 262, 'finance/income/record', '回款记录', '回款记录', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (267, 262, 'finance/payment/record', '付款记录', '付款记录', 'finance', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (268, 2, '', '客户模块', '客户模块', 'customer', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (269, 268, 'customer/industry/datalist', '行业类型', '行业类型', 'home', '', 1, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (270, 269, 'customer/industry/add', '新建/编辑', '行业类型', 'home', '', 2, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (271, 269, 'customer/industry/set', '设置', '行业类型', 'home', '', 2, 0, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (272, 268, 'customer/grade/datalist', '客户等级', '客户等级', 'customer', '', 1, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (273, 272, 'customer/grade/add', '新建/编辑', '客户等级', 'customer', '', 2, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (274, 272, 'customer/grade/set', '设置', '客户等级', 'customer', '', 2,0, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (275, 268, 'customer/source/datalist', '客户渠道', '客户渠道', 'customer', '', 1, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (276, 275, 'customer/source/add', '新建/编辑', '客户渠道', 'customer', '', 2, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (277, 275, 'customer/source/set', '设置', '客户渠道', 'customer', '', 2,0, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (278, 268, 'customer/basic/datalist', '常规数据', '常规数据', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (279, 278, 'customer/basic/add', '新建/编辑', '常规数据', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (280, 278, 'customer/basic/set', '设置', '常规数据', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (281, 7, 'customer/customer/datalist', '客户列表', '客户列表', 'customer', '', 1, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (282, 281, 'customer/customer/add', '新建/编辑', '客户', 'customer', '', 2, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (283, 281, 'customer/customer/view', '查看', '客户', 'customer', '', 2, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (284, 281, 'customer/customer/del', '删除', '客户', 'customer', '', 2, 0, 1, 1556143065, 0);

INSERT INTO `oa_admin_rule` VALUES (285, 7, 'customer/index/rush', '抢 客 宝', '抢客宝', 'customer', '', 1, 0, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (286, 7, 'customer/index/sea', '公海客户', '客户', 'customer', '', 1, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (287, 286, 'customer/index/to_get', '获取', '客户', 'customer', '', 2, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (288, 286, 'customer/index/to_divide', '分配客户', '客户', 'customer', '', 2, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (289, 286, 'customer/index/to_sea', '转入公海', '客户', 'customer', '', 2, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (290, 286, 'customer/index/to_trash', '转入废弃池', '客户', 'customer', '', 2, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (291, 286, 'customer/index/to_revert', '恢复客户', '客户', 'customer', '', 2, 0, 1, 1556143065, 0);

INSERT INTO `oa_admin_rule` VALUES (292, 7, 'customer/index/trash', '废弃客户', '客户', 'customer', '', 1, 0, 1, 1556143065, 0);

INSERT INTO `oa_admin_rule` VALUES (293, 7, 'customer/contact/datalist', '客户联系人', '联系人', 'customer', '', 1, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (294, 286, 'customer/contact/add', '新建/编辑', '联系人', 'customer', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (295, 286, 'customer/contact/del', '删除', '联系人', 'customer', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (296, 286, 'customer/contact/view', '查看', '客户联系人', 'customer', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (297, 7, 'customer/chance/datalist', '机会线索', '机会线索', 'customer', '', 1, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (298, 297, 'customer/chance/add', '新建/编辑', '机会线索', 'customer', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (299, 297, 'customer/chance/view', '查看', '机会线索', 'customer', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (300, 297, 'customer/chance/del', '删除', '机会线索', 'customer', '', 2, 0, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (301, 7, 'customer/trace/datalist', '跟进记录', '跟进记录', 'customer', '', 1, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (302, 301, 'customer/trace/add', '新建/编辑', '跟进记录', 'customer', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (303, 301, 'customer/trace/view', '查看', '跟进记录', 'customer', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (304, 301, 'customer/trace/del', '删除', '跟进记录', 'customer', '', 2, 0, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (305, 2, '', '合同模块', '合同模块', 'contract', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (306, 305, 'contract/cate/datalist', '合同分类', '合同分类', 'contract', '', 1, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (307, 306, 'contract/cate/add', '新建/编辑', '合同分类', 'contract', '', 2, 1, 1, 0, 1656143065);
INSERT INTO `oa_admin_rule` VALUES (308, 306, 'contract/cate/set', '设置', '合同分类', 'contract', '', 2, 1, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (309, 305, 'contract/productcate/datalist', '产品分类', '产品分类', 'contract', '', 1, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (310, 309, 'contract/productcate/add', '新建/编辑', '产品分类', 'contract', '', 2, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (311, 309, 'contract/productcate/del', '删除', '产品分类', 'contract', '', 2, 1, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (312, 305, 'contract/product/datalist', '产品列表', '产品', 'contract', '', 1, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (313, 312, 'contract/product/add', '新建/编辑', '产品', 'contract', '', 2, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (314, 312, 'contract/product/view', '查看', '产品', 'contract', '', 2, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (315, 312, 'contract/product/del', '删除', '产品', 'contract', '', 2, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (316, 312, 'contract/product/set', '设置', '产品', 'contract', '', 2, 1, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (317, 305, 'contract/services/datalist', '服务内容', '服务内容', 'contract', '', 1, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (318, 317, 'contract/services/add', '新建/编辑', '服务内容', 'contract', '', 2, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (319, 317, 'contract/services/set', '设置', '服务内容', 'contract', '', 2, 1, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (320, 305, 'contract/supplier/datalist', '供应商列表', '供应商', 'contract', '', 1, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (321, 320, 'contract/supplier/add', '新建/编辑', '供应商', 'contract', '', 2, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (322, 320, 'contract/supplier/set', '设置', '供应商', 'contract', '', 2, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (323, 320, 'contract/supplier/view', '查看', '供应商', 'contract', '', 2, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (324, 320, 'contract/supplier/del', '删除', '供应商', 'contract', '', 2, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (325, 320, 'contract/supplier/contact_add', '新建/编辑', '供应商联系人', 'contract', '', 2, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (326, 320, 'contract/supplier/contact_del', '删除', '供应商联系人', 'contract', '', 2, 1, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (327, 305, 'contract/purchasedcate/datalist', '采购品分类', '采购品分类', 'contract', '', 1, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (328, 327, 'contract/purchasedcate/add', '新建/编辑', '采购品分类', 'contract', '', 2, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (329, 327, 'contract/purchasedcate/del', '删除', '采购品分类', 'contract', '', 2, 1, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (330, 305, 'contract/purchased/datalist', '采购品列表', '采购品', 'contract', '', 1, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (331, 330, 'contract/purchased/add', '新建/编辑', '采购品', 'contract', '', 2, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (332, 330, 'contract/purchased/view', '查看', '采购品', 'contract', '', 2, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (333, 330, 'contract/purchased/del', '删除', '采购品', 'contract', '', 2, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (334, 330, 'contract/purchased/set', '设置', '采购品', 'contract', '', 2, 1, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (335, 8, 'contract/contract/datalist', '销售合同', '销售合同', 'contract', '', 1, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (336, 335, 'contract/contract/add', '新建/编辑', '合同', 'contract', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (337, 335, 'contract/contract/view', '查看', '合同', 'contract', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (338, 335, 'contract/contract/del', '删除', '合同', 'contract', '', 2, 0, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (339, 8, 'contract/purchase/datalist', '采购合同', '采购合同', 'contract', '', 1, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (340, 339, 'contract/purchase/add', '新建/编辑', '合同', 'contract', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (341, 339, 'contract/purchase/view', '查看', '合同', 'contract', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (342, 339, 'contract/purchase/del', '删除', '合同', 'contract', '', 2, 0, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (343, 8, 'contract/contract/archivelist', '合同归档', '合同归档', 'contract', '', 1, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (344, 343, 'contract/purchase/archivelist', '采购合同归档', '采购合同归档', 'contract', '', 2, 0, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (345, 8, 'contract/contract/stoplist', '中止合同', '中止合同', 'contract', '', 1, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (346, 345, 'contract/purchase/stoplist', '中止采购合同', '中止采购合同', 'contract', '', 2, 0, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (347, 8, 'contract/contract/voidlist', '作废合同', '作废合同', 'contract', '', 1, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (348, 347, 'contract/purchase/voidlist', '作废合同归档', '作废采购合同', 'contract', '', 2, 0, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (349, 2, '', '项目模块', '项目模块', 'project', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (350, 349, 'project/step/datalist', '项目阶段', '项目阶段', 'project', '', 1, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (351, 350, 'project/step/add', '新建/编辑', '项目阶段', 'project', '', 2, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (352, 350, 'project/step/set', '设置', '项目阶段', 'project', '', 2, 1, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (353, 349, 'project/cate/datalist', '项目分类', '项目分类', 'project', '', 1, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (354, 353, 'project/cate/add', '新建/编辑', '项目分类', 'project', '', 2, 1, 1, 0, 1656143065);
INSERT INTO `oa_admin_rule` VALUES (355, 353, 'project/cate/set', '设置', '项目分类', 'project', '', 2, 1, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (356, 349, 'project/work/datalist', '工作类别', '工作类别', 'project', '', 1, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (357, 356, 'project/work/add', '新建/编辑', '工作类别', 'project', '', 2, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (358, 356, 'project/work/set', '设置', '工作类别', 'project', '', 2, 1, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (359, 9, 'project/index/datalist', '项目列表', '项目', 'project', '', 1, 0, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (360, 359, 'project/index/add', '新建', '项目', 'project', '', 2, 0, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (361, 359, 'project/index/edit', '编辑', '项目', 'project', '', 2, 0, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (362, 359, 'project/index/view', '查看', '项目', 'project', '', 2, 0, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (363, 359, 'project/index/del', '删除', '项目', 'project', '', 2, 0, 1, 1656142368, 0);

INSERT INTO `oa_admin_rule` VALUES (364, 9, 'project/task/datalist', '任务列表', '任务', 'project', '', 1, 0, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (365, 364, 'project/task/add', '新建', '任务', 'project', '', 2, 0, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (366, 364, 'project/task/edit', '编辑', '任务', 'project', '', 2, 0, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (367, 364, 'project/task/view', '查看', '任务', 'project', '', 2, 0, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (368, 364, 'project/task/del', '删除', '任务', 'project', '', 2, 0, 1, 1656142368, 0);

INSERT INTO `oa_admin_rule` VALUES (369, 9, 'project/task/hour', '任务工时', '工时', 'project', '', 1, 0, 1, 1656142368, 0);

INSERT INTO `oa_admin_rule` VALUES (370, 9, 'project/document/datalist', '文档列表', '文档', 'project', '', 1, 0, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (371, 370, 'project/document/add', '新建/编辑', '文档', 'project', '', 2, 0, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (372, 370, 'project/document/view', '查看', '文档', 'project', '', 2, 0, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (373, 370, 'project/document/delete', '删除', '文档', 'project', '', 2, 0, 1, 1656142368, 0);

INSERT INTO `oa_admin_rule` VALUES (374, 10, 'disk/index/datalist', '个人文件', '个人文件', 'disk', '', 1, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (375, 374, 'disk/index/add_upload', '新增', '文件', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (376, 374, 'disk/index/add_folder', '新增', '文件夹', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (377, 374, 'disk/index/add_article', '新增/编辑', '在线文档', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (378, 374, 'disk/index/view_article', '查看', '在线文档', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (379, 374, 'disk/index/del', '删除', '文件/文件夹/在线文档', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (380, 374, 'disk/index/rename', '重命名', '文件', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (381, 374, 'disk/index/move', '移动', '文件', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (382, 374, 'disk/index/share', '分享', '文件', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (383, 374, 'disk/index/unshare', '取消分享', '文件', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (384, 374, 'disk/index/star', '标星', '文件', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (385, 374, 'disk/index/unstar', '取消标星', '文件', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (386, 374, 'disk/index/back', '还原', '文件', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (387, 374, 'disk/index/clear', '清除', '文件', 'disk', '', 2, 0, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (388, 10, 'disk/index/sharelist', '共享文件', '共享文件', 'disk', '', 1, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (389, 10, 'disk/index/clearlist', '回 收 站', '回收站文件', 'disk', '', 1, 0, 1, 1656143065, 0);


-- ----------------------------
-- Table structure for oa_admin_group
-- ----------------------------
DROP TABLE IF EXISTS `oa_admin_group`;
CREATE TABLE `oa_admin_group`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `status` int(1) NOT NULL DEFAULT 1,
  `rules` mediumtext  NULL COMMENT '用户组拥有的规则id',
  `layouts` mediumtext  NULL COMMENT '首页展示模块',
  `mobile_bar` mediumtext  NULL COMMENT '手机端Bar',
  `mobile_menu` mediumtext  NULL COMMENT '手机端工作台',
  `desc` mediumtext  NULL COMMENT '备注',
  `create_time` bigint(11) NOT NULL DEFAULT 0,
  `update_time` bigint(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '员工权限分组表';

-- ----------------------------
-- Records of oa_admin_group
-- ----------------------------
INSERT INTO `oa_admin_group` VALUES (1, '超级权限角色', 1, '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,206,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,248,249,250,251,252,253,254,255,256,257,258,259,260,261,262,263,264,265,266,267,268,269,270,271,272,273,274,275,276,277,278,279,280,281,282,283,284,285,286,287,288,289,290,291,292,293,294,295,296,297,298,299,300,301,302,303,304,305,306,307,308,309,310,311,312,313,314,315,316,317,318,319,320,321,322,323,324,325,326,327,328,329,330,331,332,333,334,335,336,337,338,339,340,341,342,343,344,345,346,347,348,349,350,351,352,353,354,355,356,357,358,359,360,361,362,363,364,365,366,367,368,369,370,371,372,373,374,375,376,377,378,379,380,381,382,383,384,385,386,387,388,389', '1,2,3,4,5,6,7,8,9,10,11,12','1,2,3,4,5','1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16','超级权限角色，拥有系统的最高权限，主要用于系统初始化数据而设，不可修改，不可删除。', 0, 0);
INSERT INTO `oa_admin_group` VALUES (2, '管理岗角色', 1, '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,206,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,248,249,250,251,252,253,254,255,256,257,258,259,260,261,262,263,264,265,266,267,268,269,270,271,272,273,274,275,276,277,278,279,280,281,282,283,284,285,286,287,288,289,290,291,292,293,294,295,296,297,298,299,300,301,302,303,304,305,306,307,308,309,310,311,312,313,314,315,316,317,318,319,320,321,322,323,324,325,326,327,328,329,330,331,332,333,334,335,336,337,338,339,340,341,342,343,344,345,346,347,348,349,350,351,352,353,354,355,356,357,358,359,360,361,362,363,364,365,366,367,368,369,370,371,372,373,374,375,376,377,378,379,380,381,382,383,384,385,386,387,388,389','1,2,3,4,5,6,7,8,9,10,11,12','1,2,3,4,5','1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16', '管理岗角色权限，可根据公司的具体需求调整。', 0, 0);
INSERT INTO `oa_admin_group` VALUES (3, '业务岗角色', 1, '4,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,197,198,199,200,201,202,203,204,5,205,206,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,6,234,235,236,237,238,239,240,241,242,243,244,245,246,247,248,249,250,251,252,253,254,255,256,257,258,259,260,261,262,263,264,265,266,267,7,281,282,283,284,285,286,287,288,289,290,291,294,295,296,292,293,297,298,299,300,301,302,303,304,8,335,336,337,338,339,340,341,342,343,344,345,346,347,348,9,359,360,361,362,363,364,365,366,367,368,369,370,371,372,373,10,374,375,376,377,378,379,380,381,382,383,384,385,386,387,388,389','1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16','1,2,3,4,5','1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16', '业务岗角色权限，可根据公司的具体需求调整。', 0, 0);

-- ----------------------------
-- Table structure for oa_template
-- ----------------------------
DROP TABLE IF EXISTS `oa_template`;
CREATE TABLE `oa_template`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '消息模板名称',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '权限标识唯一，字母',
  `types` tinyint(1) NOT NULL DEFAULT 1 COMMENT '类型:1普通消息,2审批消息',
  `check_types` int(11) NOT NULL DEFAULT 0 COMMENT '审批类型:0',
  `remark` varchar(500) NOT NULL DEFAULT '' COMMENT '备注描述，使用场景等',
  `msg_link` varchar(255) NOT NULL DEFAULT '' COMMENT '消息模板链接(审批申请)',
  `msg_title_0` varchar(255) NOT NULL DEFAULT '' COMMENT '消息模板标题(审批申请)',
  `msg_content_0` varchar(500) NOT NULL DEFAULT '' COMMENT '消息模板内容(审批申请)',
  `msg_title_1` varchar(255) NOT NULL DEFAULT '' COMMENT '消息模板标题(审批通过)',
  `msg_content_1` varchar(500) NOT NULL DEFAULT '' COMMENT '消息模板内容(审批通过)',
  `msg_title_2` varchar(255) NOT NULL DEFAULT '' COMMENT '消息模板标题(审批拒绝)',
  `msg_content_2` varchar(500) NOT NULL DEFAULT '' COMMENT '消息模板内容(审批拒绝)',
  `email_link` varchar(255) NOT NULL DEFAULT '' COMMENT '邮箱消息模板链接',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `admin_id` int(11) NOT NULL DEFAULT 0 COMMENT  '创建人',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '消息模板表';

-- ----------------------------
-- Records of oa_template
-- ----------------------------
INSERT INTO `oa_template` VALUES (1, '公告通知', 'note', 1, 0, '', '/adm/note/view/id/{action_id}', '{from_user}发了一个新『公告』，请及时查看', '您有一个新公告：{title}。', '', '', '', '', '', 1, 1, 1733312491, 1733314809, 0);
INSERT INTO `oa_template` VALUES (2, '请假审批', 'leaves', 2, 0, '', '/home/leaves/view/id/{action_id}', '{from_user}提交了一个『请假申请』，请及时审批', '您有一个新的『请假申请』需要处理。', '您提交的『请假申请』已被审批通过。', '您在{create_time}提交的『请假申请』已于{date}被审批通过。', '您提交的『请假申请』已被驳回拒绝。', '您在{create_time}提交的『请假申请』已于{date}被驳回拒绝。', '', 1, 1, 1733312616, 1733314814, 0);
INSERT INTO `oa_template` VALUES (3, '出差审批', 'trips', 2, 0, '', '/home/trips/view/id/{action_id}', '{from_user}提交了一个『出差申请』，请及时审批', '您有一个新的『出差申请』需要处理。', '您提交的『出差申请』已被审批通过。', '您在{create_time}提交的『出差申请』已于{date}被审批通过。', '您提交的『出差申请』已被驳回拒绝。', '您在{create_time}提交的『出差申请』已于{date}被驳回拒绝。', '', 1, 1, 1733312725, 1733314819, 0);
INSERT INTO `oa_template` VALUES (4, '外出审批', 'outs', 2, 0, '', '/home/outs/view/id/{action_id}', '{from_user}提交了一个『外出申请』，请及时审批', '您有一个新的『外出申请』需要处理。', '您提交的『外出申请』已被审批通过。', '您在{create_time}提交的『外出申请』已于{date}被审批通过。', '您提交的『外出申请』已被驳回拒绝。', '您在{create_time}提交的『外出申请』已于{date}被驳回拒绝。', '', 1, 1, 1733312801, 1733314824, 0);
INSERT INTO `oa_template` VALUES (5, '加班审批', 'overtimes', 2, 0, '', '/home/overtimes/view/id/{action_id}', '{from_user}提交了一个『加班申请』，请及时审批', '您有一个新的『加班申请』需要处理。', '您提交的『加班申请』已被审批通过。', '您在{create_time}提交的『加班申请』已于{date}被审批通过。', '您提交的『加班申请』已被驳回拒绝。', '您在{create_time}提交的『加班申请』已于{date}被驳回拒绝。', '', 1, 1, 1733312801, 1733314828, 0);
INSERT INTO `oa_template` VALUES (6, '用章审批', 'seal', 2, 0, '', '/adm/seal/view/id/{action_id}', '{from_user}提交了一个『用章申请』，请及时审批', '您有一个新的『用章申请』需要处理。', '您提交的『公文申请』已被审批通过。', '您在{create_time}提交的『公文申请』已于{date}被审批通过。', '您提交的『用章申请』已被驳回拒绝。', '您在{create_time}提交的『用章申请』已于{date}被驳回拒绝。', '', 1, 1, 1733313018, 1733314834, 0);
INSERT INTO `oa_template` VALUES (7, '公文审批', 'official', 2, 0, '', '/adm/official/view/id/{action_id}', '{from_user}提交了一个『公文申请』，请及时审批', '您有一个新的『公文申请』需要处理。', '您提交的『公文申请』已被审批通过', '您在{create_time}提交的『公文申请』已于{date}被审批通过。', '您提交的『公文申请』已被驳回拒绝', '您在{create_time}提交的『公文申请』已于{date}被驳回拒绝。', '', 1, 1, 1733313078, 1733313262, 0);
INSERT INTO `oa_template` VALUES (8, '报销审批', 'expense', 2, 0, '', '/finance/expense/view/id/{action_id}', '{from_user}提交了一个『报销申请』，请及时审批', '您有一个新的『报销申请』需要处理。', '您提交的『报销申请』已被审批通过', '您在{create_time}提交的『报销申请』已于{date}被审批通过。', '您提交的『报销申请』已被驳回拒绝', '您在{create_time}提交的『报销申请』已于{date}被驳回拒绝。', '', 1, 1, 1733313169, 1733313253, 0);
INSERT INTO `oa_template` VALUES (9, '发票审批', 'invoice', 2, 0, '', '/finance/invoice/view/id/{action_id}', '{from_user}提交了一个『发票申请』，请及时审批', '您有一个新的『发票申请』需要处理。', '您提交的『发票申请』已被审批通过', '您在{create_time}提交的『发票申请』已于{date}被审批通过。', '您提交的『发票申请』已被驳回拒绝', '您在{create_time}提交的『发票申请』已于{date}被驳回拒绝。', '', 1, 1, 1733313245, 0, 0);
INSERT INTO `oa_template` VALUES (10, '收票审批', 'ticket', 2, 0, '', '/finance/ticket/view/id/{action_id}', '{from_user}提交了一个『收票申请』，请及时审批', '您有一个新的『收票申请』需要处理。', '您提交的『收票申请』已被审批通过', '您在{create_time}提交的『收票申请』已于{date}被审批通过。', '您提交的『收票申请』已被驳回拒绝', '您在{create_time}提交的『收票申请』已于{date}被驳回拒绝。', '', 1, 1, 1733313341, 0, 0);
INSERT INTO `oa_template` VALUES (11, '无发票回款审批', 'invoicea', 2, 0, '', '/finance/invoice/view_a/id/{action_id}', '{from_user}提交了一个『无发票回款申请』，请及时审批', '您有一个新的『无发票回款申请』需要处理。', '您提交的『无发票回款申请』已被审批通过', '您在{create_time}提交的『无发票回款申请』已于{date}被审批通过。', '您提交的『无发票回款申请』已被驳回拒绝', '您在{create_time}提交的『无发票回款申请』已于{date}被驳回拒绝。', '', 1, 1, 1733314549, 0, 0);
INSERT INTO `oa_template` VALUES (12, '无发票付款审批', 'ticketa', 2, 0, '', '/finance/ticket/view_a/id/{action_id}', '{from_user}提交了一个『无发票付款申请』，请及时审批', '您有一个新的『无发票付款申请』需要处理。', '您提交的『无发票付款申请』已被审批通过', '您在{create_time}提交的『无发票付款申请』已于{date}被审批通过。', '您提交的『无发票付款申请』已被驳回拒绝', '您在{create_time}提交的『无发票付款申请』已于{date}被驳回拒绝。', '', 1, 1, 1733314607, 0, 0);
INSERT INTO `oa_template` VALUES (13, '销售合同审批', 'contract', 2, 0, '', '/contract/contract/view/id/{action_id}', '{from_user}提交了一个『销售合同审批』，请及时审批', '您有一个新的『销售合同审批』需要处理。', '您提交的『销售合同审批』已被审批通过', '您在{create_time}提交的『销售合同审批』已于{date}被审批通过。', '您提交的『销售合同审批』已被驳回拒绝', '您在{create_time}提交的『销售合同审批』已于{date}被驳回拒绝。', '', 1, 1, 1733314701, 0, 0);
INSERT INTO `oa_template` VALUES (14, '采购合同审批', 'purchase', 2, 0, '', '/contract/purchase/view/id/{action_id}', '{from_user}提交了一个『采购合同审批』，请及时审批', '您有一个新的『采购合同审批』需要处理。', '您提交的『采购合同审批』已被审批通过', '您在{create_time}提交的『采购合同审批』已于{date}被审批通过。', '您提交的『采购合同审批』已被驳回拒绝', '您在{create_time}提交的『采购合同审批』已于{date}被驳回拒绝。', '', 1, 1, 1733314789, 0, 0);

-- ----------------------------
-- Table structure for oa_flow_cate
-- ----------------------------
DROP TABLE IF EXISTS `oa_flow_cate`;
CREATE TABLE `oa_flow_cate`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '审批类型名称',
  `name` varchar(100) NOT NULL COMMENT '审批类型标识,唯一',
  `module_id` int(11) NOT NULL DEFAULT 0 COMMENT '关联审批模块id',
  `check_table` varchar(100) NOT NULL DEFAULT '' COMMENT '关联数据库表名',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '图标',
  `department_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '应用部门ID（空为全部）1,2,3',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序：越大越靠前',
  `form` tinyint(1) NOT NULL DEFAULT 1 COMMENT '预设字段，表单模式：1固定表单,2自定义表单',
  `add_url` varchar(255) NOT NULL DEFAULT '' COMMENT '新建链接：固定表单模式必填',
  `view_url` varchar(255) NOT NULL DEFAULT '' COMMENT '查看链接：固定表单模式必填',
  `form_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '表单id：自定义表单模式必填',
  `is_list` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否列表页显示：0不显示 1显示',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `template_id` int(11) NOT NULL DEFAULT 0 COMMENT '审批消息模板id',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '审批类型';

-- ----------------------------
-- Records of oa_flow_cate
-- ----------------------------
INSERT INTO `oa_flow_cate` VALUES (1, '请假', 'leaves', 1, 'leaves', 'icon-kechengziyuanguanli', '', 0, 1, '/home/leaves/add', '/home/leaves/view', 0, 1, 1, 2, 1723604674, 0);
INSERT INTO `oa_flow_cate` VALUES (2, '出差', 'trips', 1, 'trips', 'icon-jiaoshiguanli', '', 0, 1, '/home/trips/add', '/home/trips/view', 0, 1, 1, 3, 1723799422, 1724138037);
INSERT INTO `oa_flow_cate` VALUES (3, '外出', 'outs', 1, 'outs', 'icon-tuiguangguanli', '', 0, 1, '/home/outs/add', '/home/outs/view', 0, 1, 1, 4,1723800336, 1724138021);
INSERT INTO `oa_flow_cate` VALUES (4, '加班', 'overtimes', 1, 'overtimes', 'icon-xueshengchengji', '', 0, 1, '/home/overtimes/add', '/home/overtimes/view', 0, 1, 1, 5, 1723800393, 1724138004);
INSERT INTO `oa_flow_cate` VALUES (5, '用章', 'seal', 2, 'seal', 'icon-shenpishezhi', '', 0, 1, '/adm/seal/add', '/adm/seal/view', 0, 1, 1, 6, 1723469451, 1724138203);
INSERT INTO `oa_flow_cate` VALUES (6, '公文', 'official_docs', 2, 'official_docs', 'icon-lunwenguanli', '', 0, 1, '/adm/official/add', '/adm/official/view', 0, 1, 1, 7, 1723469614, 1724138182);
INSERT INTO `oa_flow_cate` VALUES (7, '报销', 'expense', 4, 'expense', 'icon-jizhang', '', 0, 1, '/finance/expense/add', '/finance/expense/view', 0, 1, 1, 8, 1723469732, 1724138154);
INSERT INTO `oa_flow_cate` VALUES (8, '发票', 'invoice', 4, 'invoice', 'icon-duizhangdan', '', 0, 1, '/finance/invoice/add', '/finance/invoice/view', 0, 1, 1, 9,1723469814, 1724138127);
INSERT INTO `oa_flow_cate` VALUES (9, '收票', 'ticket', 4, 'ticket', 'icon-yingjiaoqingdan', '', 0, 1, '/finance/ticket/add', '/finance/ticket/view', 0, 1, 1, 10, 1724749856, 1724828690);
INSERT INTO `oa_flow_cate` VALUES (10, '无发票回款', 'invoicea', 4, 'invoice', 'icon-shoufeipeizhi', '', 0, 1, '/finance/invoice/add_a', '/finance/invoice/view_a', 0, 1, 1, 11,1725856435, 1725935194);
INSERT INTO `oa_flow_cate` VALUES (11, '无发票付款', 'ticketa', 4, 'ticket', 'icon-bulujiesuan', '', 0, 1, '/finance/ticket/add_a', '/finance/ticket/view_a', 0, 1, 1, 12,1725856613, 1725935703);
INSERT INTO `oa_flow_cate` VALUES (12, '销售合同', 'contract', 3, 'contract', 'icon-hetongguanli', '', 0, 1, '/contract/contract/add', '/contract/contract/view', 0, 0, 1, 13,1723469917, 1724828537);
INSERT INTO `oa_flow_cate` VALUES (13, '采购合同', 'purchase', 3, 'purchase', 'icon-dianshang', '', 0, 1, '/contract/purchase/add', '/contract/purchase/view', 0, 0, 1, 14,1723470017, 1724828575);

-- ----------------------------
-- Table structure for oa_flow
-- ----------------------------
DROP TABLE IF EXISTS `oa_flow`;
CREATE TABLE `oa_flow`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '审批流程名称',
  `cate_id` tinyint(11) NOT NULL DEFAULT 0 COMMENT '关联审批类型id',
  `check_type` tinyint(4) NOT NULL COMMENT '1自由审批流,2固定审批流,3固定可回退的审批流,4固定条件审批流',
  `department_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '应用部门ID（0为全部）1,2,3',
  `copy_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '抄送人ID',
  `flow_list` varchar(1000) NULL DEFAULT '' COMMENT '流程数据序列化',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态 1启用，0禁用',
  `remark` varchar(500) NOT NULL DEFAULT '' COMMENT '流程说明',
  `admin_id` int(11) NOT NULL COMMENT '创建人ID',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '审批流程表';

-- ----------------------------
-- Records of oa_flow_cate
-- ----------------------------
INSERT INTO `oa_flow` VALUES (1, '请假审批', 1, 1, '', '', '', 1, '', 1, 1723791655, 0, 0);
INSERT INTO `oa_flow` VALUES (2, '出差审批', 2, 1, '', '', '', 1, '', 1, 1723799665, 0, 0);
INSERT INTO `oa_flow` VALUES (3, '外出审批', 3, 1, '', '', '', 1, '', 1, 1723800434, 0, 0);
INSERT INTO `oa_flow` VALUES (4, '加班审批', 4, 1, '', '', '', 1, '', 1, 1723800446, 0, 0);
INSERT INTO `oa_flow` VALUES (5, '用章审批', 5, 1, '', '', '', 1, '', 1, 1723470400, 0, 0);
INSERT INTO `oa_flow` VALUES (6, '公文审批', 6, 1, '', '', '', 1, '', 1, 1723470419, 0, 0);
INSERT INTO `oa_flow` VALUES (7, '报销审批', 7, 1, '', '', '', 1, '', 1, 1723470468, 0, 0);
INSERT INTO `oa_flow` VALUES (8, '发票审批', 8, 1, '', '', '', 1, '', 1, 1723470482, 0, 0);
INSERT INTO `oa_flow` VALUES (9, '收票审批', 9, 1, '', '', '', 1, '', 1, 1723470482, 0, 0);
INSERT INTO `oa_flow` VALUES (10, '无发票回款', 10, 1, '', '', '', 1, '', 1, 1725935073, 0, 0);
INSERT INTO `oa_flow` VALUES (11, '无发票付款', 11, 1, '', '', '', 1, '', 1, 1725935159, 1725935232, 0);
INSERT INTO `oa_flow` VALUES (12, '销售合同审批', 12, 1, '', '', '', 1, '', 1, 1723470490, 0, 0);
INSERT INTO `oa_flow` VALUES (13, '采购合同审批', 13, 1, '', '', '', 1, '', 1, 1723470501, 0, 0);