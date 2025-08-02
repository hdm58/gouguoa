ALTER TABLE `oa_disk` ADD COLUMN `group_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '分享空间id' AFTER `action_id`;

DROP TABLE IF EXISTS `oa_disk_group`;
CREATE TABLE `oa_disk_group`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '分享空间名称',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `director_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '管理人员',
  `group_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '群组成员',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '网盘分享空间表';

INSERT INTO `oa_disk_group` VALUES (1000, '入职培训', 1, '', '', 1749030795, 1749031338, 0);
INSERT INTO `oa_disk_group` VALUES (1001, '项目文件', 1, '', '', 1749030795, 1749031338, 0);
INSERT INTO `oa_disk_group` VALUES (1002, '常用软件', 1, '', '', 1749030795, 1749031338, 0);
INSERT INTO `oa_disk_group` VALUES (1003, '团建照片', 1, '', '', 1749030795, 1749031338, 0);

UPDATE `oa_admin_rule` SET `pid` = 74, `src` = 'home/cate/enterprise_check', `title` = '设置', `name` = '企业主体', `module` = 'home', `icon` = '', `menu` = 2, `sort` = 1, `status` = 1, `create_time` = 0, `update_time` = 0 WHERE `id` = 76;

UPDATE `oa_admin_rule` SET `pid` = 10, `src` = 'disk/index/datalist', `title` = '我的空间', `name` = '我的空间', `module` = 'disk', `icon` = '', `menu` = 1, `sort` = 0, `status` = 1, `create_time` = 1656143065, `update_time` = 0 WHERE `id` = 374;

UPDATE `oa_admin_rule` SET `pid` = 374, `src` = 'disk/index/star', `title` = '标星', `name` = '文件', `module` = 'disk', `icon` = '', `menu` = 2, `sort` = 0, `status` = 1, `create_time` = 1656143065, `update_time` = 0 WHERE `id` = 382;

UPDATE `oa_admin_rule` SET `pid` = 374, `src` = 'disk/index/unstar', `title` = '取消标星', `name` = '文件', `module` = 'disk', `icon` = '', `menu` = 2, `sort` = 0, `status` = 1, `create_time` = 1656143065, `update_time` = 0 WHERE `id` = 383;

UPDATE `oa_admin_rule` SET `pid` = 374, `src` = 'disk/index/back', `title` = '还原', `name` = '文件', `module` = 'disk', `icon` = '', `menu` = 2, `sort` = 0, `status` = 1, `create_time` = 1656143065, `update_time` = 0 WHERE `id` = 384;

UPDATE `oa_admin_rule` SET `pid` = 374, `src` = 'disk/index/clear', `title` = '清除', `name` = '文件', `module` = 'disk', `icon` = '', `menu` = 2, `sort` = 0, `status` = 1, `create_time` = 1656143065, `update_time` = 0 WHERE `id` = 385;

UPDATE `oa_admin_rule` SET `pid` = 10, `src` = 'disk/index/sharelist', `title` = '共享空间', `name` = '共享空间', `module` = 'disk', `icon` = '', `menu` = 1, `sort` = 0, `status` = 1, `create_time` = 1656143065, `update_time` = 0 WHERE `id` = 386;

UPDATE `oa_admin_rule` SET `pid` = 386, `src` = 'disk/index/add_group', `title` = '新建/编辑', `name` = '共享空间', `module` = 'disk', `icon` = '', `menu` = 2, `sort` = 1, `status` = 1, `create_time` = 0, `update_time` = 0 WHERE `id` = 387;

UPDATE `oa_admin_rule` SET `pid` = 386, `src` = 'disk/index/del_group', `title` = '删除', `name` = '共享空间', `module` = 'disk', `icon` = '', `menu` = 2, `sort` = 1, `status` = 1, `create_time` = 0, `update_time` = 0 WHERE `id` = 388;