/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50644
 Source Host           : localhost:3306
 Source Schema         : house

 Target Server Type    : MySQL
 Target Server Version : 50644
 File Encoding         : 65001

 Date: 16/11/2021 15:16:59
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for oa_admin
-- ----------------------------
DROP TABLE IF EXISTS `oa_admin`;
CREATE TABLE `oa_admin`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL DEFAULT '' COMMENT '登录用户名',
  `pwd` varchar(100) NOT NULL DEFAULT '' COMMENT '登录密码',
  `salt` varchar(100) NOT NULL DEFAULT '' COMMENT '密码盐',
  `reg_pwd` varchar(100) NOT NULL DEFAULT '' COMMENT '初始密码',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '员工姓名',
  `mobile` bigint(11) NOT NULL DEFAULT 0 COMMENT '手机号码',
  `sex` int(255) NOT NULL DEFAULT 0 COMMENT '性别1男,2女',
  `nickname` varchar(255) NOT NULL DEFAULT '' COMMENT '昵称',
  `thumb` varchar(255) NOT NULL COMMENT '头像',
  `did` int(11) NOT NULL DEFAULT 0 COMMENT '部门id',
  `position_id` int(11) NOT NULL DEFAULT 0 COMMENT '职位id',
  `type` int(1) NOT NULL DEFAULT 0 COMMENT '员工类型：0未设置,1正式,2试用,3实习',
  `desc` text NULL COMMENT '员工个人简介',
  `entry_time` int(11) NOT NULL DEFAULT 0 COMMENT '员工入职日期',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '注册时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新信息时间',
  `last_login_time` int(11) NOT NULL DEFAULT 0 COMMENT '最后登录时间',
  `login_num` int(11) NOT NULL DEFAULT 0 COMMENT '登录次数',
  `last_login_ip` varchar(64) NOT NULL DEFAULT '' COMMENT '最后登录IP',
  `status` int(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除,0禁止登录,1正常,2离职',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '员工表';

-- ----------------------------
-- Table structure for oa_admin_group
-- ----------------------------
DROP TABLE IF EXISTS `oa_admin_group`;
CREATE TABLE `oa_admin_group`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `status` int(1) NOT NULL DEFAULT 1,
  `rules` varchar(1000) NULL DEFAULT '' COMMENT '用户组拥有的规则id， 多个规则\",\"隔开',
  `desc` text NULL COMMENT '备注',
  `create_time` int(11) NOT NULL DEFAULT 0,
  `update_time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '员工权限分组表';

-- ----------------------------
-- Records of cms_admin_group
-- ----------------------------
INSERT INTO `oa_admin_group` VALUES (1, '超级员工权限', 1, '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141',  '超级员工权限，拥有系统的最高权限，不可修改', 0, 0);
INSERT INTO `oa_admin_group` VALUES (2, '人事总监权限', 1, '2,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,3,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,4,78,79,80,81,82,83,84,85,86,87,5,88,89,90,91,6,92,93,94,95,96,7,97,99,100,101,98,8,102,104,105,106,103,107,109,110,111,112,108,113,114,115,116,117,9,118,119,120,122,123,124,125,127,128,129,131,132,133,134', '人力资源部门领导的最高管理权限', 0, 0);

-- ----------------------------
-- Table structure for oa_admin_log
-- ----------------------------
DROP TABLE IF EXISTS `oa_admin_log`;
CREATE TABLE `oa_admin_log`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '姓名',
  `type` varchar(80) NOT NULL DEFAULT '' COMMENT '操作类型',
  `action` varchar(80) NOT NULL DEFAULT '' COMMENT '操作动作',
  `subject` varchar(80) NOT NULL DEFAULT '' COMMENT '操作主体',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '操作标题',
  `content` text NULL COMMENT '操作描述',
  `module` varchar(32) NOT NULL DEFAULT '' COMMENT '模块',
  `controller` varchar(32) NOT NULL DEFAULT '' COMMENT '控制器',
  `function` varchar(32) NOT NULL DEFAULT '' COMMENT '方法',
  `rule_menu` varchar(255) NOT NULL DEFAULT '' COMMENT '节点权限路径',
  `ip` varchar(64) NOT NULL DEFAULT '' COMMENT '登录ip',
  `param_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作数据id',
  `param` text NULL COMMENT '参数json格式',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0删除 1正常',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '员工操作日志表';


-- ----------------------------
-- Table structure for oa_admin_module
-- ----------------------------
DROP TABLE IF EXISTS `oa_admin_module`;
CREATE TABLE `oa_admin_module`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '模块名称',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '模块标识唯一，字母',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '图标',
  `status` int(1) NOT NULL DEFAULT 1 COMMENT '状态,0禁用,1正常',
  `type` int(1) NOT NULL DEFAULT 2 COMMENT '模块类型,2普通模块,1系统模块',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '功能模块表';

-- ----------------------------
-- Records of oa_admin_module
-- ----------------------------
INSERT INTO `oa_admin_module` VALUES (1, '系统模块', 'HOME', '', 1, 1, 1639562910, 0);
INSERT INTO `oa_admin_module` VALUES (2, '用户模块', 'USER', '', 1, 1, 1639562910, 0);
INSERT INTO `oa_admin_module` VALUES (3, '消息模块', 'MSG', '', 1, 1, 1639562910, 0);
INSERT INTO `oa_admin_module` VALUES (4, '公告模块', 'NOTE', '', 1, 1, 1639562910, 0);
INSERT INTO `oa_admin_module` VALUES (5, '知识模块', 'KQ', '', 1, 1, 1639562910, 0);
INSERT INTO `oa_admin_module` VALUES (6, 'OA模块', 'OA', '', 1, 1, 1639562910, 0);
INSERT INTO `oa_admin_module` VALUES (7, '财务模块', 'CF', '', 1, 1, 1639562910, 0);
INSERT INTO `oa_admin_module` VALUES (8, '统计模块', 'BI', '', 1, 1, 1639562910, 0);


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
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '菜单及权限表';


-- ----------------------------
-- Records of oa_admin_rule
-- ----------------------------
INSERT INTO `oa_admin_rule` VALUES (1, 0, '', '系统管理', '系统管理', 'HOME', 'icon-jichupeizhi', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (2, 0, '', '基础数据', '基础数据', 'HOME', 'icon-hetongshezhi', 1, 2, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (3, 0, '', '员工管理', '员工管理', 'USER', 'icon-renshishezhi', 1, 3, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (4, 0, '', '消息通知', '消息通知', 'MSG', 'icon-xiaoxishezhi', 1, 4, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (5, 0, '', '企业公告', '企业公告', 'NOTE', 'icon-zhaoshengbaobiao', 1, 5, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (6, 0, '', '知识文章', '知识文章', 'KQ', 'icon-kecheng', 1, 6, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (7, 0, '', '办公审批', '办公审批', 'OA', 'icon-shenpishezhi', 1, 7, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (8, 0, '', '日常办公', '日常办公', 'OA', 'icon-kaoshijihua', 1, 8, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (9, 0, '', '财务管理', '财务管理', 'CF', 'icon-yuangongtidian', 1, 9, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (10, 0, '', '商业智能', '商业智能', 'BI', 'icon-jiaoxuetongji', 1, 10, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (11, 1, 'home/conf/index', '系统配置', '系统配置', 'HOME', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (12, 11, 'home/conf/add', '新建/编辑', '配置项', 'HOME', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (13, 11, 'home/conf/delete', '删除', '配置项', 'HOME', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (14, 11, 'home/conf/edit', '编辑', '配置详情', 'HOME', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (15, 1, 'home/module/index', '功能模块', '功能模块', 'HOME', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (16, 15, 'home/module/add', '新建/编辑', '功能模块', 'HOME', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (17, 15, 'home/module/disable', '禁用/启用', '功能模块', 'HOME', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (18, 1, 'home/rule/index', '功能节点', '功能节点', 'HOME', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (19, 18, 'home/rule/add', '新建/编辑', '功能节点', 'HOME', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (20, 18, 'home/rule/delete', '删除', '功能节点', 'HOME', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (21, 1, 'home/role/index', '权限角色', '权限角色', 'HOME', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (22, 21, 'home/role/add', '新建/编辑', '权限角色', 'HOME', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (23, 21, 'home/role/delete', '删除', '权限角色', 'HOME', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (24, 1, 'home/log/index', '操作日志', '操作日志', 'HOME', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (25, 1, 'home/database/database', '备份数据', '备份数据', 'HOME', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (26, 25, 'home/database/backup', '备份数据表', '备份数据', 'HOME', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (27, 25, 'home/database/optimize', '优化数据表', '优化数据表', 'HOME', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (28, 25, 'home/database/repair', '修复数据表', '修复数据表', 'HOME', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (29, 1, 'home/database/backuplist', '还原数据', '还原数据', 'HOME', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (30, 29, 'home/database/import', '还原数据表', '还原数据', 'HOME', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (31, 29, 'home/database/downfile', '下载备份数据', '下载备份数据', 'HOME', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (32, 29, 'home/database/del', '删除备份数据', '删除备份数据', 'HOME', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (33, 2, 'home/cate/flow_type', '审批类型', '审批类型', 'HOME', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (34, 33, 'home/cate/flow_type_add', '新建/编辑', '审批类型', 'HOME', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (35, 33, 'home/cate/flow_type_check', '设置', '审批类型', 'HOME', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (36, 2, 'home/flow/index', '审批流程', '审批流程', 'HOME', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (37, 36, 'home/flow/add', '新建/编辑', '审批流程', 'HOME', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (38, 36, 'home/flow/delete', '删除', '审批流程', 'HOME', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (39, 36, 'home/flow/check', '设置', '审批流程', 'HOME', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (40, 2, 'home/cate/expense_cate', '报销类型', '报销类型', 'HOME', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (41, 40, 'home/cate/expense_cate_add', '新建/编辑', '报销类型', 'HOME', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (42, 40, 'home/cate/expense_cate_check', '设置', '报销类型', 'HOME', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (43, 2, 'home/cate/cost_cate', '费用类型', '费用类型', 'HOME', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (44, 43, 'home/cate/cost_cate_add', '新建/编辑', '费用类型', 'HOME', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (45, 43, 'home/cate/cost_cate_check', '设置', '费用类型', 'HOME', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (46, 2, 'home/cate/seal_cate', '印章类型', '印章类型', 'HOME', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (47, 46, 'home/cate/seal_cate_add', '新建/编辑', '印章类型', 'HOME', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (48, 46, 'home/cate/seal_cate_check', '设置', '印章类型', 'HOME', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (49, 2, 'home/cate/car_cate', '车辆类型', '车辆类型', 'HOME', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (50, 49, 'home/cate/car_cate_add', '新建/编辑', '车辆类型', 'HOME', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (51, 49, 'home/cate/car_cate_check', '设置', '车辆类型', 'HOME', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (52, 2, 'home/cate/subject', '发票主体', '发票主体', 'HOME', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (53, 52, 'home/cate/subject_add', '新建/编辑', '发票主体', 'HOME', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (54, 52, 'home/cate/subject_check', '设置', '发票主体', 'HOME', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (55, 2, 'home/cate/note_cate', '公告类型', '公告类型', 'HOME', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (56, 55, 'home/cate/note_cate_add', '新建/编辑', '公告类型', 'HOME', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (57, 55, 'home/cate/note_cate_delete', '删除', '公告类型', 'HOME', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (58, 2, 'home/cate/article_cate', '知识类型', '知识类型', 'HOME', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (59, 58, 'home/cate/article_cate_add', '新建/编辑', '知识类型', 'HOME', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (60, 58, 'home/cate/article_cate_delete', '删除', '知识类型', 'HOME', '', 2, 1, 1, 0, 0);


INSERT INTO `oa_admin_rule` VALUES (61, 3, 'user/department/index', '部门架构', '部门', 'USER', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (62, 61, 'user/department/add', '新建/编辑', '部门', 'USER', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (63, 61, 'user/department/delete', '删除', '部门', 'USER', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (64, 3, 'user/position/index', '岗位职称', '岗位职称', 'USER', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (65, 64, 'user/position/add', '新建/编辑', '岗位职称', 'USER', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (66, 64, 'user/position/delete', '删除', '岗位职称', 'USER', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (67, 64, 'user/position/view', '查看', '岗位职称', 'USER', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (68, 3, 'user/user/index', '企业员工', '员工', 'USER', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (69, 68, 'user/user/add', '新建/编辑', '员工', 'USER', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (70, 68, 'user/user/view', '查看', '员工信息', 'USER', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (71, 68, 'user/user/set', '设置', '员工状态', 'USER', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (72, 68, 'user/user/reset_psw', '重设密码', '员工密码', 'USER', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (73, 3, 'user/personal/change', '人事调动', '人事调动', 'USER', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (74, 73, 'user/personal/change_add', '新建/编辑', '人事调动', 'USER', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (75, 3, 'user/personal/leave', '离职档案', '离职档案', 'USER', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (76, 75, 'user/personal/leave_add', '新建/编辑', '离职档案', 'USER', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (77, 75, 'user/personal/leave_delete', '删除', '离职档案', 'USER', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (78, 4, 'message/index/inbox', '收件箱', '收件箱', 'MSG', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (79, 78, 'message/index/add', '新建/编辑', '消息', 'MSG', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (80, 78, 'message/index/send', '发送', '消息', 'MSG', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (81, 78, 'message/index/save', '保存', '消息到草稿', 'MSG', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (82, 78, 'message/index/reply', '回复', '消息', 'MSG', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (83, 78, 'message/index/read', '查看', '消息', 'MSG', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (84, 78, 'message/index/check', '设置', '消息状态', 'MSG', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (85, 4, 'message/index/sendbox', '发件箱', '发件箱', 'MSG', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (86, 4, 'message/index/draft', '草稿箱', '草稿箱', 'MSG', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (87, 4, 'message/index/rubbish', '垃圾箱', '垃圾箱', 'MSG', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (88, 5, 'note/index/index', '公告列表', '公告', 'NOTE', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (89, 88, 'note/index/add', '新建/编辑', '公告', 'NOTE', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (90, 88, 'note/index/delete', '删除', '公告', 'NOTE', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (91, 88, 'note/index/view', '查看', '公告', 'NOTE', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (92, 6, 'article/index/index', '共享知识', '知识文章', 'KQ', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (93, 6, 'article/index/list', '个人知识', '知识文章', 'KQ', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (94, 93, 'article/index/add', '新建/编辑', '知识文章', 'KQ', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (95, 93, 'article/index/delete', '删除', '知识文章', 'KQ', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (96, 93, 'article/index/view', '查看', '知识文章', 'KQ', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (97, 7, 'oa/approve/index', '我发起的审批', '办公审批', 'OA', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (98, 7, 'oa/approve/list', '待我处理的审批', '办公审批', 'OA', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (99, 97, 'oa/approve/add', '新建/编辑', '办公审批', 'OA', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (100, 97, 'oa/approve/view', '查看', '办公审批', 'OA', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (101, 97, 'oa/approve/check', '审核', '办公审批', 'OA', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (102, 8, 'oa/plan/calendar', '日程日历', '日程安排', 'OA', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (103, 8, 'oa/plan/index', '日程安排', '日程安排', 'OA', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (104, 102, 'oa/plan/add', '新建/编辑', '日程安排', 'OA', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (105, 102, 'oa/plan/delete', '删除', '日程安排', 'OA', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (106, 102, 'oa/plan/detail', '查看', '日程安排', 'OA', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (107, 8, 'oa/schedule/calendar', '工作日历', '工作日历', 'OA', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (108, 8, 'oa/schedule/index', '工作记录', '工作记录', 'OA', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (109, 107, 'oa/schedule/add', '新建/编辑', '工作记录', 'OA', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (110, 107, 'oa/schedule/delete', '删除', '工作记录', 'OA', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (111, 107, 'oa/schedule/detail', '查看', '工作记录', 'OA', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (112, 107, 'oa/schedule/update_labor_time', '更改工时', '工时', 'OA', '', 0, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (113, 8, 'oa/work/index', '工作汇报', '工作汇报', 'OA', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (114, 113, 'oa/work/add', '新建/编辑', '工作汇报', 'OA', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (115, 113, 'oa/work/send', '发送', '工作汇报', 'OA', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (116, 113, 'oa/work/read', '查看', '工作汇报', 'OA', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (117, 113, 'oa/work/delete', '删除', '工作汇报', 'OA', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (118, 9, '', '报销管理', '报销', 'CF', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (119, 118, 'finance/expense/index', '我申请的报销', '报销', 'CF', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (120, 118, 'finance/expense/list', '我负责的报销', '报销', 'CF', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (121, 118, 'finance/expense/checkedlist', '报销打款', '报销', 'CF', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (122, 118, 'finance/expense/add', '新建/编辑', '报销', 'CF', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (123, 118, 'finance/expense/delete', '删除', '报销', 'CF', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (124, 118, 'finance/expense/view', '查看', '报销', 'CF', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (125, 118, 'finance/expense/check', '审核', '报销', 'CF', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (126, 118, 'finance/expense/topay', '打款', '报销', 'CF', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (127, 9, '', '发票管理', '发票', 'CF', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (128, 127, 'finance/invoice/index', '我申请的发票', '发票', 'CF', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (129, 127, 'finance/invoice/list', '我负责的发票', '发票', 'CF', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (130, 127, 'finance/invoice/checkedlist', '发票开具', '发票', 'CF', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (131, 127, 'finance/invoice/add', '新建/编辑', '发票', 'CF', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (132, 127, 'finance/invoice/delete', '删除', '发票', 'CF', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (133, 127, 'finance/invoice/view', '查看', '发票', 'CF', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (134, 127, 'finance/invoice/check', '审核', '发票', 'CF', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (135, 127, 'finance/invoice/open', '开具', '发票', 'CF', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (136, 127, 'finance/invoice/tovoid', '作废', '发票', 'CF', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (137, 9, 'finance/income/index', '到账管理', '到账记录', 'CF', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (138, 137, 'finance/income/add', '新建/编辑', '到账记录', 'CF', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (139, 137, 'finance/income/view', '查看', '到账记录', 'CF', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (140, 137, 'finance/income/delete', '删除', '到账记录', 'CF', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (141, 10, 'business/analysis/index', '智能分析', '智能分析', 'BI', '', 1, 1, 1, 0, 0);

---------------------------------
-- Table structure for oa_article
---------------------------------
DROP TABLE IF EXISTS `oa_article`;
CREATE TABLE `oa_article`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '知识文章标题',
  `article_cate_id` int(11) NOT NULL DEFAULT 0 COMMENT '关联分类id',
  `keywords` varchar(255) NULL DEFAULT '' COMMENT '关键字',
  `desc` varchar(1000) NULL DEFAULT '' COMMENT '摘要',
  `thumb` int(11) NOT NULL DEFAULT 0 COMMENT '缩略图id',
  `uid` int(11) NOT NULL DEFAULT 0 COMMENT '作者',
  `did` int(11) NOT NULL DEFAULT 0 COMMENT '部门',
  `origin_url` varchar(255) NOT NULL DEFAULT '' COMMENT '来源地址',
  `content` text NOT NULL COMMENT '文章内容',
  `read` int(11) NOT NULL DEFAULT 0 COMMENT '阅读量',
  `type` tinyint(2) NOT NULL DEFAULT 0 COMMENT '属性：1精华 2热门 3推荐',
  `is_share` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否分享，0否，1是',
  `status` int(1) NOT NULL DEFAULT 1 COMMENT '状态:1正常-1下架',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `create_time` int(11) NOT NULL DEFAULT 0,
  `update_time` int(11) NOT NULL DEFAULT 0,
  `delete_time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '知识文章表';

-- ----------------------------
-- Records of oa_article
-- ----------------------------
INSERT INTO `oa_article` VALUES (1, '勾股办公是一款简单实用的开源免费的企业办公系统框架', 2, '', '勾股办公是一款基于ThinkPHP6+Layui+MySql打造的，简单实用的开源免费的企业办公系统框架。系统集成了系统设置、人事管理模块、消息管理模块、日常办公、财务管理等基础模块。系统简约，易于功...', 0, 1, 1, '', '<p>勾股办公是一款基于ThinkPHP6 + Layui + MySql打造的，简单实用的开源免费的企业办公系统框架。系统集成了系统设置、人事管理模块、消息管理模块、日常办公、财务管理等基础模块。系统简约，易于功能扩展，方便二次开发，让开发者更专注于业务深度需求的开发，帮助开发者简单高效降低二次开发成本，通过二次开发之后可以用来做CRM，ERP，业务管理等系统。</p>', 1, 2, 1, 1, 1, 1637985280, 1637985340, 0);

-- ----------------------------
-- Table structure for oa_article_cate
-- ----------------------------
DROP TABLE IF EXISTS `oa_article_cate`;
CREATE TABLE `oa_article_cate`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT 0 COMMENT '父类ID',
  `sort` int(5) NOT NULL DEFAULT 0 COMMENT '排序',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '分类标题',
  `desc` varchar(1000) NULL DEFAULT '' COMMENT '描述',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '添加时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '知识文章分类表';

-- ----------------------------
-- Records of oa_article_cate
-- ----------------------------
INSERT INTO `oa_article_cate` VALUES (1, 0, 0, '办公技巧', '', 1637984651, 0);
INSERT INTO `oa_article_cate` VALUES (2, 0, 0, '行业技能', '', 1637984739, 0);

-- ----------------------------
-- Table structure for oa_article_keywords
-- ----------------------------
DROP TABLE IF EXISTS `oa_article_keywords`;
CREATE TABLE `oa_article_keywords`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `aid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '知识文章ID',
  `keywords_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联关键字id',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `aid`(`aid`) USING BTREE,
  INDEX `inid`(`keywords_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '知识文章关联表';

-- ----------------------------
-- Records of oa_article_keywords
-- ----------------------------
INSERT INTO `oa_article_keywords` VALUES (1, 1, 1, 1, 1638093082);

-- ----------------------------
-- Table structure for oa_config
-- ----------------------------
DROP TABLE IF EXISTS `oa_config`;
CREATE TABLE `oa_config`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '配置名称',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '配置标识',
  `content` text NULL COMMENT '配置内容',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COMMENT = '系统配置表';

-- ----------------------------
-- Records of oa_config
-- ----------------------------
INSERT INTO `oa_config` VALUES (1, '网站配置', 'web', 'a:13:{s:2:\"id\";s:1:\"1\";s:11:\"admin_title\";s:8:\"勾股OA\";s:5:\"title\";s:8:\"勾股OA\";s:4:\"logo\";s:52:\"/storage/202111/fc507cc8332d5ef49d9425185e4a9697.jpg\";s:4:\"file\";s:0:\"\";s:6:\"domain\";s:23:\"https://oa.gougucms.com\";s:3:\"icp\";s:23:\"粤ICP备1xxxxxx11号-1\";s:8:\"keywords\";s:8:\"勾股OA\";s:5:\"beian\";s:29:\"粤公网安备1xxxxxx11号-1\";s:4:\"desc\";s:479:\"勾股办公是一款基于ThinkPHP6 + Layui + MySql打造的，简单实用的开源免费的企业办公系统框架。系统集成了系统设置、人事管理模块、消息管理模块、日常办公、财务管理等基础模块。系统简约，易于功能扩展，方便二次开发，让开发者更专注于业务深度需求的开发，帮助开发者简单高效降低二次开发成本，通过二次开发之后可以用来做CRM，ERP，业务管理等系统。 \";s:4:\"code\";s:0:\"\";s:9:\"copyright\";s:32:\"© 2021 gougucms.com MIT license\";s:7:\"version\";s:6:\"1.0.22\";}', 1, 1612514630, 1638010154);
INSERT INTO `oa_config` VALUES (2, '邮箱配置', 'email', 'a:8:{s:2:\"id\";s:1:\"2\";s:4:\"smtp\";s:11:\"smtp.qq.com\";s:9:\"smtp_port\";s:3:\"465\";s:9:\"smtp_user\";s:15:\"gougucms@qq.com\";s:8:\"smtp_pwd\";s:6:\"123456\";s:4:\"from\";s:24:\"勾股CMS系统管理员\";s:5:\"email\";s:18:\"admin@gougucms.com\";s:8:\"template\";s:485:\"<p>勾股办公是一款基于ThinkPHP6 + Layui + MySql打造的，简单实用的开源免费的企业办公系统框架。系统集成了系统设置、人事管理模块、消息管理模块、日常办公、财务管理等基础模块。系统简约，易于功能扩展，方便二次开发，让开发者更专注于业务深度需求的开发，帮助开发者简单高效降低二次开发成本，通过二次开发之后可以用来做CRM，ERP，业务管理等系统。</p>\";}', 1, 1612521657, 1637075205);
INSERT INTO `oa_config` VALUES (3, 'Api Token配置', 'token', 'a:5:{s:2:\"id\";s:1:\"3\";s:3:\"iss\";s:15:\"oa.gougucms.com\";s:3:\"aud\";s:7:\"gouguoa\";s:7:\"secrect\";s:7:\"GOUGUOA\";s:7:\"exptime\";s:4:\"3600\";}', 1, 1627313142, 1638010233);
INSERT INTO `oa_config` VALUES (4, '其他配置', 'other', 'a:3:{s:2:\"id\";s:1:\"5\";s:6:\"author\";s:15:\"勾股工作室\";s:7:\"version\";s:13:\"v1.2021.07.28\";}', 1, 1613725791, 1635953640);

-- ----------------------------
-- Table structure for oa_department
-- ----------------------------
DROP TABLE IF EXISTS `oa_department`;
CREATE TABLE `oa_department`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '部门名称',
  `pid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上级部门id',
  `leader_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '部门负责人ID',
  `phone` varchar(60) NOT NULL DEFAULT '' COMMENT '部门联系电话',
  `remark` varchar(1000) NULL DEFAULT '' COMMENT '备注',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '部门组织';

-- ----------------------------
-- Records of oa_department
-- ----------------------------
INSERT INTO `oa_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (1, '董事会', 0, 0, '13688888888');
INSERT INTO `oa_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (2, '人事部', 1, 0, '13688888889');
INSERT INTO `oa_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (3, '财务部', 1, 0, '13688888898');
INSERT INTO `oa_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (4, '市场部', 1, 0, '13688888978');
INSERT INTO `oa_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (5, '销售部', 1, 0, '13688889868');
INSERT INTO `oa_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (6, '技术部', 1, 0, '13688898858');
INSERT INTO `oa_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (7, '客服部', 1, 0, '13688988848');
INSERT INTO `oa_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (8, '销售一部', 5, 0, '13688998838');
INSERT INTO `oa_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (9, '销售二部', 5, 0, '13688999828');
INSERT INTO `oa_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (10, '销售三部', 5, 0, '13688999918');
INSERT INTO `oa_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (11, '产品部', 6, 0, '13688888886');
INSERT INTO `oa_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (12, '设计部', 6, 0, '13688888876');
INSERT INTO `oa_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (13, '研发部', 6, 0, '13688888666');
INSERT INTO `oa_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (14, '客服一部', 7, 0, '13688888865');
INSERT INTO `oa_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (15, '客服二部', 7, 0, '13688888855');

-- ----------------------------
-- Table structure for oa_department_change
-- ----------------------------
DROP TABLE IF EXISTS `oa_department_change`;
CREATE TABLE `oa_department_change`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `from_did` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '原部门id',
  `to_did` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '调到部门id',
  `remark` varchar(1000) NULL DEFAULT '' COMMENT '备注',
  `admin_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `move_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '调到时间',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '人事调动部门记录表';

-- ----------------------------
-- Table structure for oa_personal_quit
-- ----------------------------
DROP TABLE IF EXISTS `oa_personal_quit`;
CREATE TABLE `oa_personal_quit`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `remark` varchar(1000) NULL DEFAULT '' COMMENT '备注',
  `admin_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `lead_admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '部门负责人',
  `connect_uids` varchar(100) NOT NULL DEFAULT '' COMMENT '交接人',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `quit_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '离职时间',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '人事离职记录表';

-- ----------------------------
-- Table structure for oa_flow_type
-- ----------------------------
DROP TABLE IF EXISTS `oa_flow_type`;
CREATE TABLE `oa_flow_type`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '1假勤,2行政,3财务,4人事,5其他',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '审批名称',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '审批标识',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '图标',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '审批类型';

-- ----------------------------
-- Records of oa_flow_type
-- ----------------------------
INSERT INTO `oa_flow_type` VALUES (1, 1, '请假', 'qingjia', 'icon-kechengziyuanguanli', 1, 1639896302, 0);
INSERT INTO `oa_flow_type` VALUES (2, 1, '出差', 'chuchai', 'icon-jiaoshiguanli', 1, 1641802838, 0);
INSERT INTO `oa_flow_type` VALUES (3, 1, '外出', 'waichu', 'icon-tuiguangguanli', 1, 1641802858, 0);
INSERT INTO `oa_flow_type` VALUES (4, 1, '加班', 'jiaban', 'icon-xueshengchengji', 1, 1641802892, 0);
INSERT INTO `oa_flow_type` VALUES (5, 2, '会议室预定', 'huiyishi', 'icon-kehuguanli', 1, 1641802939, 0);
INSERT INTO `oa_flow_type` VALUES (6, 2, '公文流转', 'gongwen', 'icon-jiaoxuejihua', 1, 1641802976, 0);
INSERT INTO `oa_flow_type` VALUES (7, 2, '物品维修', 'weixiu', 'icon-chuangjianxitong', 1, 1641803005, 0);
INSERT INTO `oa_flow_type` VALUES (8, 2, '用章', 'yongzhang', 'icon-shenpishezhi', 1, 1641804126, 0);
INSERT INTO `oa_flow_type` VALUES (9, 2, '用车', 'yongche', 'icon-dongtaiguanli', 1, 1641804283, 0);
INSERT INTO `oa_flow_type` VALUES (10, 2, '用车归还', 'yongcheguihai', 'icon-kaoheguanli', 1, 1641804411, 0);
INSERT INTO `oa_flow_type` VALUES (11, 3, '借款申请', 'jiekuan', 'icon-zhangbuguanli', 1, 1641804537, 0);
INSERT INTO `oa_flow_type` VALUES (12, 3, '付款申请', 'fukuan', 'icon-gongziguanli', 1, 1641804601, 0);
INSERT INTO `oa_flow_type` VALUES (13, 3, '奖励申请', 'jiangli', 'icon-hetongguanli', 1, 1641804711, 0);
INSERT INTO `oa_flow_type` VALUES (14, 3, '采购', 'caigou', 'icon-xiaoshoupin', 1, 1641804917, 0);
INSERT INTO `oa_flow_type` VALUES (15, 3, '活动经费', 'huodong', 'icon-wangxiaoshezhi1', 1, 1641805110, 0);
INSERT INTO `oa_flow_type` VALUES (16, 4, '入职', 'ruzhi', 'icon-xueshengdaoru', 1, 1641893853, 0);
INSERT INTO `oa_flow_type` VALUES (17, 4, '转正', 'zhuanzheng', 'icon-xueshengyidong', 1, 1641893926, 0);
INSERT INTO `oa_flow_type` VALUES (18, 4, '离职', 'lizhi', 'icon-banjiguanli', 1, 1641894048, 0);
INSERT INTO `oa_flow_type` VALUES (19, 4, '招聘需求', 'zhaopin', 'icon-zhaopinguanli', 1, 1641894080, 0);
INSERT INTO `oa_flow_type` VALUES (20, 6, '报销', 'baoxiao', 'icon-jizhang', 1, 1641804488, 0);
INSERT INTO `oa_flow_type` VALUES (21, 7, '发票', 'fapiao', 'icon-fuwuliebiao', 1, 1642904833, 0);


-- ----------------------------
-- Table structure for oa_cost_cate
-- ----------------------------
DROP TABLE IF EXISTS `oa_cost_cate`;
CREATE TABLE `oa_cost_cate`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '费用类型名称',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '费用类型';

-- ----------------------------
-- Records of oa_cost_cate
-- ----------------------------
INSERT INTO `oa_cost_cate` VALUES (1, '差旅费', 1, 1639898199, 0);
INSERT INTO `oa_cost_cate` VALUES (2, '办公费', 1, 1639898434, 0);
INSERT INTO `oa_cost_cate` VALUES (3, '招待费', 1, 1639898564, 0);
INSERT INTO `oa_cost_cate` VALUES (4, '交通费', 1, 1639898564, 0);
INSERT INTO `oa_cost_cate` VALUES (5, '通讯费', 1, 1639898564, 0);
INSERT INTO `oa_cost_cate` VALUES (6, '采购付款', 1, 1639898564, 0);
INSERT INTO `oa_cost_cate` VALUES (7, '其他', 1, 1639898564, 0);


-- ----------------------------
-- Table structure for oa_seal_cate
-- ----------------------------
DROP TABLE IF EXISTS `oa_seal_cate`;
CREATE TABLE `oa_seal_cate`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '印章类型名称',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '印章类型';

-- ----------------------------
-- Records of oa_seal_cate
-- ----------------------------
INSERT INTO `oa_seal_cate` VALUES (1, '公章', 1, 1639899124, 0);
INSERT INTO `oa_seal_cate` VALUES (2, '合同章', 1, 1639899140, 0);
INSERT INTO `oa_seal_cate` VALUES (3, '法人章', 1, 1639899148, 0);
INSERT INTO `oa_seal_cate` VALUES (4, '其他', 1, 1639899158, 0);

-- ----------------------------
-- Table structure for oa_car_cate
-- ----------------------------
DROP TABLE IF EXISTS `oa_car_cate`;
CREATE TABLE `oa_car_cate`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '车辆名称',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '车辆号码',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '用车类型';

-- ----------------------------
-- Records of oa_car_cate
-- ----------------------------
INSERT INTO `oa_car_cate` VALUES (1, '宝马X5', '粤A55555', 1, 1639900555, 0);
INSERT INTO `oa_car_cate` VALUES (2, '哈弗H6', '粤A66666', 1, 1639900666, 0);
INSERT INTO `oa_car_cate` VALUES (3, '奥迪Q8', '粤A88888', 1, 1639900888, 0);

-- ----------------------------
-- Table structure for oa_expense
-- ----------------------------
DROP TABLE IF EXISTS `oa_expense`;
CREATE TABLE `oa_expense`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(100) NOT NULL DEFAULT '' COMMENT '报销编码',
  `income_month` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '入账月份',
  `expense_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '原始单据日期',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '报销人',
  `did` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '报销部门ID',
  `ptid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预定字段:关联项目ID',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `check_step_sort` int(11) NOT NULL DEFAULT 0 COMMENT '当前审批步骤',
  `check_admin_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '当前审批人ID，如:1,2,3,',
  `flow_admin_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '历史审批人ID，如:1,2,3,',
  `check_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '状态（0待审、1审批中、2通过、3失败、4撤销、5已打款）',
  `last_admin_id` varchar(200) NOT NULL DEFAULT '0' COMMENT '上一审批人',
  `pay_admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '打款人ID',
  `pay_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '打款时间',
  `remark` varchar(1000) NULL DEFAULT '' COMMENT '备注',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '报销表';

-- ----------------------------
-- Table structure for oa_expense_cate
-- ----------------------------
DROP TABLE IF EXISTS `oa_expense_cate`;
CREATE TABLE `oa_expense_cate`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '报销类型名称',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '报销类型';

-- ----------------------------
-- Records of oa_expense_cate
-- ----------------------------
INSERT INTO `oa_expense_cate` VALUES (1, '交通费', 1, 1637987189, 0);
INSERT INTO `oa_expense_cate` VALUES (2, '住宿费', 1, 1637987199, 0);
INSERT INTO `oa_expense_cate` VALUES (3, '餐补费', 1, 1638088518, 0);
INSERT INTO `oa_expense_cate` VALUES (4, '招待费', 1, 1637987199, 0);
INSERT INTO `oa_expense_cate` VALUES (5, '汽油费', 1, 1637987199, 0);
INSERT INTO `oa_expense_cate` VALUES (6, '其他费', 1, 1637987199, 0);

-- ----------------------------
-- Table structure for oa_expense_file_interfix
-- ----------------------------
DROP TABLE IF EXISTS `oa_expense_file_interfix`;
CREATE TABLE `oa_expense_file_interfix`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `exid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '报销ID',
  `file_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '相关联附件id',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '报销模块关联的附件表';

-- ----------------------------
-- Table structure for oa_expense_interfix
-- ----------------------------
DROP TABLE IF EXISTS `oa_expense_interfix`;
CREATE TABLE `oa_expense_interfix`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `exid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '报销ID',
  `amount` decimal(15, 2) NULL DEFAULT 0.00 COMMENT '金额',
  `cate_id` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '报销类型ID',
  `remarks` text NULL COMMENT '备注',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '登记人',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '报销关联数据表';

-- ----------------------------
-- Table structure for oa_file
-- ----------------------------
DROP TABLE IF EXISTS `oa_file`;
CREATE TABLE `oa_file`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `module` varchar(15) NOT NULL DEFAULT '' COMMENT '所属模块',
  `sha1` varchar(60) NOT NULL COMMENT 'sha1',
  `md5` varchar(60) NOT NULL COMMENT 'md5',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '原始文件名',
  `filename` varchar(255) NOT NULL DEFAULT '' COMMENT '文件名',
  `filepath` varchar(255) NOT NULL DEFAULT '' COMMENT '文件路径+文件名',
  `filesize` int(10) NOT NULL DEFAULT 0 COMMENT '文件大小',
  `fileext` varchar(10) NOT NULL DEFAULT '' COMMENT '文件后缀',
  `mimetype` varchar(100) NOT NULL DEFAULT '' COMMENT '文件类型',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上传会员ID',
  `uploadip` varchar(15) NOT NULL DEFAULT '' COMMENT '上传IP',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0未审核1已审核-1不通过',
  `create_time` int(11) NOT NULL DEFAULT 0,
  `admin_id` int(11) NOT NULL COMMENT '审核者id',
  `audit_time` int(11) NOT NULL DEFAULT 0 COMMENT '审核时间',
  `action` varchar(50) NOT NULL DEFAULT '' COMMENT '来源模块功能',
  `use` varchar(255) NULL DEFAULT NULL COMMENT '用处',
  `download` int(11) NOT NULL DEFAULT 0 COMMENT '下载量',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '文件表';

-- ----------------------------
-- Table structure for oa_invoice
-- ----------------------------
DROP TABLE IF EXISTS `oa_invoice`;
CREATE TABLE `oa_invoice`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(100) NOT NULL DEFAULT '' COMMENT '发票号码',
  `cmid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预定字段:关联客户ID',
  `crid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预定字段:关联合同协议号ID',
  `ptid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预定字段:关联项目ID',
  `cash_type` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '付款方式：1现金 2转账 3微信支付 4支付宝 5信用卡 6支票 7其他',
  `is_cash` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '是否到账：0未到账 1部分到账 2全部到账',
  `amount` decimal(15, 2) NULL DEFAULT 0.00 COMMENT '发票金额',
  `enter_amount` decimal(15, 2) NULL DEFAULT 0.00 COMMENT '到账金额',
  `did` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '开发票部门',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发票申请人',
  `check_admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发票审核人',
  `check_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核时间',
  `open_admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发票开具人',
  `open_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发票开具时间',
  `delivery` varchar(100) NOT NULL DEFAULT '' COMMENT '快递单号',
  `type` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '抬头类型：1企业2个人',
  `invoice_subject` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联发票主体ID',
  `invoice_type` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '发票类型：1增值税专用发票2增值税普通发票',
  `invoice_title` varchar(100) NOT NULL DEFAULT '' COMMENT '开票抬头',
  `invoice_phone` varchar(100) NOT NULL DEFAULT '' COMMENT '电话号码',
  `invoice_tax` varchar(100) NOT NULL DEFAULT '' COMMENT '纳税人识别号',
  `invoice_bank` varchar(100) NOT NULL DEFAULT '' COMMENT '开户银行',
  `invoice_account` varchar(100) NOT NULL DEFAULT '' COMMENT '银行账号',
  `invoice_banking` varchar(100) NOT NULL DEFAULT '' COMMENT '银行营业网点',
  `invoice_address` varchar(100) NOT NULL DEFAULT '' COMMENT '地址',
  `check_step_sort` int(11) NOT NULL DEFAULT 0 COMMENT '当前审批步骤',
  `check_admin_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '当前审批人ID，如:1,2,3,',
  `flow_admin_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '历史审批人ID，如:1,2,3,',
  `check_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '发票状态（0待审、1审批中、2通过、3失败、4撤销、5已开具、10已作废）',
  `last_admin_id` varchar(200) NOT NULL DEFAULT '0' COMMENT '上一审批人',
  `check_remark` text NULL COMMENT '审核不通过/撤销的理由',
  `remark` text NULL COMMENT '备注',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1正常',
  `enter_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最新到账时间',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '发票表';

-- ----------------------------
-- Table structure for oa_invoice_income
-- ----------------------------
DROP TABLE IF EXISTS `oa_invoice_income`;
CREATE TABLE `oa_invoice_income`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `inid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发票ID',
  `amount` decimal(15, 2) NULL DEFAULT 0.00 COMMENT '到账金额',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '到账登记人',
  `enter_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '到账时间',
  `remarks` text NULL COMMENT '备注',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1正常 6作废',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '发票到账记录表';

-- ----------------------------
-- Table structure for oa_invoice_subject
-- ----------------------------
DROP TABLE IF EXISTS `oa_invoice_subject`;
CREATE TABLE `oa_invoice_subject`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '主体名称',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '发票主体名称';

-- ----------------------------
-- Records of oa_invoice_subject
-- ----------------------------
INSERT INTO `oa_invoice_subject` VALUES (1, '勾股信息科技有限公司', 1, 1638006751, 0);

-- ----------------------------
-- Table structure for oa_keywords
-- ----------------------------
DROP TABLE IF EXISTS `oa_keywords`;
CREATE TABLE `oa_keywords`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '关键字名称',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '关键字表';

-- ----------------------------
-- Records of oa_keywords
-- ----------------------------
INSERT INTO `oa_keywords` VALUES (1, '勾股OA', 1, 1, 1638006730, 0);

-- ----------------------------
-- Table structure for oa_message
-- ----------------------------
DROP TABLE IF EXISTS `oa_message`;
CREATE TABLE `oa_message`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '消息主题',
  `template` tinyint(2) NOT NULL DEFAULT 0 COMMENT '消息模板，用于前端拼接消息',
  `content` text NULL COMMENT '消息内容',
  `from_uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发送人id',
  `to_uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '接收人id',
  `type` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '阅览人类型：1 人员 2部门 3岗位 4全部',
  `type_user` text NULL COMMENT '人员ID或部门ID或角色ID，全员则为空',
  `send_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发送日期',
  `read_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '阅读时间',
  `pid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '来源发件id',
  `fid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '转发或回复消息关联id',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1已删除消息 0垃圾消息 1正常消息',
  `is_draft` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否是草稿：1正常消息 2草稿消息',
  `delete_source` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '垃圾消息来源： 1已发消息 2草稿消息 3已收消息',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `module_name` varchar(30) NOT NULL COMMENT '模块',
  `controller_name` varchar(30) NOT NULL COMMENT '控制器',
  `action_name` varchar(30) NOT NULL COMMENT '方法',
  `action_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作模块数据的id（针对系统消息）',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '消息表';

-- ----------------------------
-- Table structure for oa_message_file_interfix
-- ----------------------------
DROP TABLE IF EXISTS `oa_message_file_interfix`;
CREATE TABLE `oa_message_file_interfix`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `mid` int(11) UNSIGNED NOT NULL COMMENT '消息id',
  `file_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '相关联附件id',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '消息关联的附件表';

-- ----------------------------
-- Table structure for oa_note
-- ----------------------------
DROP TABLE IF EXISTS `oa_note`;
CREATE TABLE `oa_note`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cate_id` int(11) NOT NULL DEFAULT 0 COMMENT '关联分类ID',
  `title` varchar(225) NULL DEFAULT NULL COMMENT '标题',
  `content` text NOT NULL COMMENT '公告内容',
  `src` varchar(100) NULL DEFAULT NULL COMMENT '关联链接',
  `status` int(1) NOT NULL DEFAULT 1 COMMENT '1可用-1禁用',
  `sort` int(11) NOT NULL DEFAULT 0,
  `start_time` int(11) NOT NULL DEFAULT 0 COMMENT '展示开始时间',
  `end_time` int(11) NOT NULL DEFAULT 0 COMMENT '展示结束时间',
  `create_time` int(11) NOT NULL DEFAULT 0,
  `update_time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '公告';

-- ----------------------------
-- Records of oa_note
-- ----------------------------
INSERT INTO `oa_note` VALUES (1, 1, '欢迎使用勾股OA办公系统', '<p>欢迎使用勾股OA办公系统，勾股办公是一款基于ThinkPHP6 + Layui + MySql打造的，简单实用的开源免费的企业办公系统框架。系统集成了系统设置、人事管理模块、消息管理模块、日常办公、财务管理等基础模块。系统简约，易于功能扩展，方便二次开发，让开发者更专注于业务深度需求的开发，帮助开发者简单高效降低二次开发成本，通过二次开发之后可以用来做CRM，ERP，业务管理等系统。</p>', 'https://oa.gougucms.com', 1, 2, 1635696000, 1924876800, 1637984962, 1637984975);

-- ----------------------------
-- Table structure for oa_note_cate
-- ----------------------------
DROP TABLE IF EXISTS `oa_note_cate`;
CREATE TABLE `oa_note_cate`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT 0 COMMENT '父类ID',
  `sort` int(5) NOT NULL DEFAULT 0 COMMENT '排序',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '添加时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '公告分类';

-- ----------------------------
-- Records of oa_note_cate
-- ----------------------------
INSERT INTO `oa_note_cate` VALUES (1, 0, 1, '普通公告', 1637984265, 1637984299);
INSERT INTO `oa_note_cate` VALUES (2, 0, 2, '紧急公告', 1637984283, 1637984310);

-- ----------------------------
-- Table structure for oa_position
-- ----------------------------
DROP TABLE IF EXISTS `oa_position`;
CREATE TABLE `oa_position`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '岗位名称',
  `work_price` int(10) NOT NULL DEFAULT 0 COMMENT '工时单价',
  `remark` varchar(1000) NULL DEFAULT '' COMMENT '备注',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '岗位职称';

-- ----------------------------
-- Records of oa_position
-- ----------------------------
INSERT INTO `oa_position` VALUES (1, '超级岗位', 1000, '超级岗位，不能轻易修改权限', 1, 0, 0);
INSERT INTO `oa_position` VALUES (2, '人事总监', 1000, '人事部的最大领导', 1, 0, 0);

-- ----------------------------
-- Table structure for oa_position_group
-- ----------------------------
DROP TABLE IF EXISTS `oa_position_group`;
CREATE TABLE `oa_position_group`  (
  `pid` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '岗位id',
  `group_id` int(11) NULL DEFAULT NULL COMMENT '权限id',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  UNIQUE INDEX `pid_group_id`(`pid`, `group_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COMMENT = '权限分组和岗位的关联表';

-- ----------------------------
-- Records of oa_position_group
-- ----------------------------
INSERT INTO `oa_position_group` VALUES (1, 1, 1635755739, 0);
INSERT INTO `oa_position_group` VALUES (2, 2, 1638007427, 0);

-- ----------------------------
-- Table structure for oa_plan
-- ----------------------------
DROP TABLE IF EXISTS `oa_plan`;
CREATE TABLE `oa_plan`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '工作安排主题',
  `type` varchar(100) NOT NULL DEFAULT '' COMMENT '日程优先级',
  `cid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预设字段:关联工作内容类型ID',
  `cmid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预设字段:关联客户ID',
  `ptid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预设字段:关联项目ID',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联创建员工ID',
  `did` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属部门',
  `start_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '开始时间',
  `end_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '结束时间',
  `remind_type` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '提醒类型',
  `remind_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '提醒时间',
  `remark` text NOT NULL COMMENT '描述',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '日程安排';

-- ----------------------------
-- Table structure for oa_schedule
-- ----------------------------
DROP TABLE IF EXISTS `oa_schedule`;
CREATE TABLE `oa_schedule`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '工作记录主题',
  `cid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预设字段:关联工作内容类型ID',
  `cmid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预设字段:关联客户ID',
  `ptid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预设字段:关联项目ID',
  `taid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预设字段:关联任务ID',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联创建员工ID',
  `did` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属部门',
  `start_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '开始时间',
  `end_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '结束时间',
  `labor_time` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '工时',
  `labor_type` int(1) NOT NULL DEFAULT 0 COMMENT '工作类型:1案头2外勤',
  `remark` text NOT NULL COMMENT '描述',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '工作记录';

-- ----------------------------
-- Table structure for oa_schedule_interfix
-- ----------------------------
DROP TABLE IF EXISTS `oa_schedule_interfix`;
CREATE TABLE `oa_schedule_interfix`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `scid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '工作记录ID',
  `file_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '相关联附件id',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联创建员工ID',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '工作记录关联的附件表';


-- ----------------------------
-- Table structure for oa_approve
-- ----------------------------
DROP TABLE IF EXISTS `oa_approve`;
CREATE TABLE `oa_approve`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL DEFAULT 0 COMMENT '审批类型',
  `flow_id` int(11) NOT NULL DEFAULT 0 COMMENT '审批流程ID',
  `content` varchar(500) NOT NULL DEFAULT '' COMMENT '内容',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  `remark` varchar(500) NOT NULL DEFAULT '' COMMENT '备注',
  `detail_time` int(11) NOT NULL DEFAULT 0 COMMENT '时间日期',
  `start_time` int(11) NOT NULL DEFAULT 0 COMMENT '开始时间',
  `end_time` int(11) NOT NULL DEFAULT 0 COMMENT '结束时间',
  `duration` decimal(10, 1) NOT NULL DEFAULT 0.0 COMMENT '时长',
  `create_admin_id` int(10) NOT NULL COMMENT '创建人ID',
  `department_id` int(10) NOT NULL COMMENT '创建人部门ID',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `check_step_sort` int(11) NOT NULL DEFAULT 0 COMMENT '当前审批步骤',
  `check_admin_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '当前审批人ID，如:1,2,3,',
  `flow_admin_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '历史审批人ID，如:1,2,3,',
  `check_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '状态（0待审、1审批中、2通过、3失败、4撤销）',
  `last_admin_id` varchar(200) NOT NULL DEFAULT '0' COMMENT '上一审批人',
  `detail_type` int(11) NOT NULL DEFAULT 0 COMMENT '假期类型:1事假,2年假,3调休假,4病假,5婚假,6丧假,7产假,8陪产假,9其他',
  `other_type` int(11) NOT NULL DEFAULT 0 COMMENT '其他类型:1公告类,2规则制度类,3合同类,4资质更新类,5员工证明,6其他',
  `department_type` int(11) NOT NULL DEFAULT 0 COMMENT '部门类型',
  `position_type` int(11) NOT NULL DEFAULT 0 COMMENT '职位类型',
  `bank` varchar(255) NOT NULL DEFAULT '' COMMENT '银行卡账号',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '地址',
  `num` bigint(12) NOT NULL DEFAULT 0 COMMENT '数量',
  `num1` bigint(12) NOT NULL DEFAULT 0 COMMENT '数量1',
  `amount` decimal(18, 2) NOT NULL DEFAULT 0.00 COMMENT '金额',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '日常审批表';

-- ----------------------------
-- Table structure for oa_flow
-- ----------------------------
DROP TABLE IF EXISTS `oa_flow`;
CREATE TABLE `oa_flow`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '审批流名称',
  `check_type` tinyint(4) NOT NULL COMMENT '1固定审批,2授权审批人',
  `type` tinyint(4) NOT NULL COMMENT '应用模块,1假勤,2行政,3财务,4人事,5其他,6报销,7发票',
  `flow_cate` tinyint(11) NOT NULL DEFAULT 0 COMMENT '应用审批类型id',
  `department_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '应用部门ID（0为全部）1,2,3',
  `user_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '员工ID',
  `remark` varchar(500) NOT NULL DEFAULT '' COMMENT '流程说明',
  `flow_list` varchar(1000) NULL DEFAULT '' COMMENT '流程数据序列化',
  `admin_id` int(11) NOT NULL COMMENT '创建人ID',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态 1启用，0禁用',
  `delete_time` int(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  `delete_user_id` int(11) NOT NULL DEFAULT 0 COMMENT '删除人ID',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '审批流程表';

-- ----------------------------
-- Records of oa_flow
-- ----------------------------
INSERT INTO `oa_flow` VALUES (1, '请假审批', 2, 1, 1, '', '', '请假审批流程', 'a:1:{i:0;a:2:{s:9:\"flow_type\";s:1:\"1\";s:9:\"flow_uids\";s:0:\"\";}}', 1, 1644401970, 1644402071, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (2, '出差审批', 2, 1, 2, '', '', '请假审批流程', 'a:1:{i:0;a:2:{s:9:\"flow_type\";s:1:\"1\";s:9:\"flow_uids\";s:0:\"\";}}', 1, 1644402054, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (3, '外出审批', 2, 1, 3, '', '', '外出审批流程', 'a:1:{i:0;a:2:{s:9:\"flow_type\";s:1:\"1\";s:9:\"flow_uids\";s:0:\"\";}}', 1, 1644402116, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (4, '加班申请审批', 2, 1, 4, '', '', '加班申请审批流程', 'a:1:{i:0;a:2:{s:9:\"flow_type\";s:1:\"1\";s:9:\"flow_uids\";s:0:\"\";}}', 1, 1644402147, 1644456735, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (5, '会议室预定审批', 2, 2, 5, '', '', '会议室预定审批流程', 'a:1:{i:0;a:2:{s:9:\"flow_type\";s:1:\"1\";s:9:\"flow_uids\";s:0:\"\";}}', 1, 1644402193, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (6, '公文流转审批', 2, 2, 6, '', '', '公文流转审批流程', 'a:1:{i:0;a:2:{s:9:\"flow_type\";s:1:\"1\";s:9:\"flow_uids\";s:0:\"\";}}', 1, 1644402386, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (7, '物品维修审批', 2, 2, 7, '', '', '物品维修审批流程', 'a:1:{i:0;a:2:{s:9:\"flow_type\";s:1:\"1\";s:9:\"flow_uids\";s:0:\"\";}}', 1, 1644402473, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (8, '用章审批', 2, 2, 8, '', '', '用章审批流程', 'a:1:{i:0;a:2:{s:9:\"flow_type\";s:1:\"1\";s:9:\"flow_uids\";s:0:\"\";}}', 1, 1644402499, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (9, '用车审批', 2, 2, 9, '', '', '用车审批流程', 'a:1:{i:0;a:2:{s:9:\"flow_type\";s:1:\"1\";s:9:\"flow_uids\";s:0:\"\";}}', 1, 1644402525, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (10, '用车归还审批', 2, 2, 10, '', '', '用车归还审批流程', 'a:1:{i:0;a:2:{s:9:\"flow_type\";s:1:\"1\";s:9:\"flow_uids\";s:0:\"\";}}', 1, 1644402549, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (11, '借款申请审批', 2, 3, 11, '', '', '借款申请审批流程', 'a:1:{i:0;a:2:{s:9:\"flow_type\";s:1:\"1\";s:9:\"flow_uids\";s:0:\"\";}}', 1, 1644402611, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (12, '付款申请审批', 2, 3, 12, '', '', '付款申请审批流程', 'a:1:{i:0;a:2:{s:9:\"flow_type\";s:1:\"1\";s:9:\"flow_uids\";s:0:\"\";}}', 1, 1644402679, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (13, '奖励申请审批', 2, 3, 13, '', '', '奖励申请审批流程', 'a:1:{i:0;a:2:{s:9:\"flow_type\";s:1:\"1\";s:9:\"flow_uids\";s:0:\"\";}}', 1, 1644402705, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (14, '采购申请审批', 2, 3, 14, '', '', '采购申请审批流程', 'a:1:{i:0;a:2:{s:9:\"flow_type\";s:1:\"1\";s:9:\"flow_uids\";s:0:\"\";}}', 1, 1644402739, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (15, '活动经费审批', 2, 3, 15, '', '', '活动经费审批流程', 'a:1:{i:0;a:2:{s:9:\"flow_type\";s:1:\"1\";s:9:\"flow_uids\";s:0:\"\";}}', 1, 1644402762, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (16, '入职申请审批', 2, 4, 16, '', '', '入职申请审批流程', 'a:1:{i:0;a:2:{s:9:\"flow_type\";s:1:\"1\";s:9:\"flow_uids\";s:0:\"\";}}', 1, 1644402791, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (17, '转正申请审批', 2, 4, 17, '', '', '转正申请审批流程', 'a:1:{i:0;a:2:{s:9:\"flow_type\";s:1:\"1\";s:9:\"flow_uids\";s:0:\"\";}}', 1, 1644402812, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (18, '离职申请审批', 2, 4, 18, '', '', '离职申请审批流程', 'a:1:{i:0;a:2:{s:9:\"flow_type\";s:1:\"1\";s:9:\"flow_uids\";s:0:\"\";}}', 1, 1644402834, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (19, '招聘需求审批', 2, 4, 19, '', '', '招聘需求审批流程', 'a:1:{i:0;a:2:{s:9:\"flow_type\";s:1:\"1\";s:9:\"flow_uids\";s:0:\"\";}}', 1, 1644402855, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (20, '报销审批', 2, 6, 20, '', '', '报销审批流程', 'a:1:{i:0;a:2:{s:9:\"flow_type\";s:1:\"1\";s:9:\"flow_uids\";s:0:\"\";}}', 1, 1644490024, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (21, '发票审批', 2, 7, 21, '', '', '报销审批流程', 'a:1:{i:0;a:2:{s:9:\"flow_type\";s:1:\"1\";s:9:\"flow_uids\";s:0:\"\";}}', 1, 1644490053, 0, 1, 0, 0);

-- ----------------------------
-- Table structure for oa_flow_step
-- ----------------------------
DROP TABLE IF EXISTS `oa_flow_step`;
CREATE TABLE `oa_flow_step`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action_id` int(11) NOT NULL COMMENT '审批内容ID',
  `flow_type` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0自由指定,1当前部门负责人，2上一级部门负责人，3指定用户（任意一人），4指定用户（多人会签）',
  `flow_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '审批人ID (使用逗号隔开) 1,2,3',
  `sort` tinyint(4) NOT NULL DEFAULT 0 COMMENT '排序ID',
  `type` tinyint(2) NOT NULL DEFAULT 1 COMMENT '审批类型:1日常审批,2报销审批,3发票审批',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `delete_time` int(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '审批步骤表';

-- ----------------------------
-- Table structure for oa_flow_record
-- ----------------------------
DROP TABLE IF EXISTS `oa_flow_record`;
CREATE TABLE `oa_flow_record`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action_id` int(11) NOT NULL DEFAULT 0 COMMENT '审批内容ID',
  `step_id` int(11) NOT NULL DEFAULT 0 COMMENT '审批步骤ID',
  `check_user_id` int(11) NOT NULL DEFAULT 0 COMMENT '审批人ID',
  `check_time` int(11) NOT NULL COMMENT '审批时间',
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '1审核通过2审核失败3撤销',
  `type` tinyint(2) NOT NULL DEFAULT 1 COMMENT '审批类型:1日常审批,2报销审批,3发票审批',
  `content` varchar(500) NOT NULL DEFAULT '' COMMENT '审核意见',
  `is_invalid` tinyint(1) NOT NULL DEFAULT 0 COMMENT '审批失效（1标记为无效）',
  `delete_time` int(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '审批记录表';

-- ----------------------------
-- Table structure for oa_work
-- ----------------------------
DROP TABLE IF EXISTS `oa_work`;
CREATE TABLE `oa_work`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '类型：1 日报 2周报 3月报',
  `type_user` text NULL COMMENT '接受人员ID',
  `works` text NULL COMMENT '汇报工作内容',
  `plans` text NULL COMMENT '计划工作内容',
  `remark` text NULL COMMENT '其他事项',
  `admin_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '汇报工作表';

-- ----------------------------
-- Table structure for oa_work_file_interfix
-- ----------------------------
DROP TABLE IF EXISTS `oa_work_file_interfix`;
CREATE TABLE `oa_work_file_interfix`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `wid` int(11) UNSIGNED NOT NULL COMMENT '汇报工作id',
  `file_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '相关联附件id',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '汇报工作关联的附件表';

-- ----------------------------
-- Table structure for oa_work_record
-- ----------------------------
DROP TABLE IF EXISTS `oa_work_record`;
CREATE TABLE `oa_work_record`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `wid` int(11) UNSIGNED NOT NULL COMMENT '汇报工作id',
  `from_uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发送人id',
  `to_uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '接收人id',
  `send_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发送日期',
  `read_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '阅读时间',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '汇报工作发送记录表';