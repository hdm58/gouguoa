ALTER TABLE `oa_disk` 
ADD COLUMN `share_types` tinyint NOT NULL DEFAULT 0 COMMENT '分享对象：0不分享,1所有人,2部门,3岗位,4人员' AFTER `is_star`;

ALTER TABLE `oa_disk` 
ADD COLUMN `share_dids` varchar(255) NOT NULL DEFAULT '' COMMENT '分享部门' AFTER `share_types`;

ALTER TABLE `oa_disk` 
ADD COLUMN `share_pids` varchar(255) NOT NULL DEFAULT '' COMMENT '分享岗位' AFTER `share_dids`;

ALTER TABLE `oa_disk` 
ADD COLUMN `share_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '分享学员' AFTER `share_pids`;

ALTER TABLE `oa_disk` 
ADD COLUMN `share_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '分享截止时间' AFTER `share_uids`;

UPDATE `oa_disk` SET `file_ext` = 'folder' WHERE `types` = 2;
UPDATE `oa_disk` SET `file_ext` = 'article' WHERE `types` = 1;

ALTER TABLE `oa_project` 
ADD COLUMN `score` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '项目评分' AFTER `amount`;

ALTER TABLE `oa_project` 
ADD COLUMN `importance` int(2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '项目重要程度:1一般,2重要,3非常重要' AFTER `score`;

ALTER TABLE `oa_project` 
ADD COLUMN `uids` varchar(500) NOT NULL DEFAULT '' COMMENT '项目参与人' AFTER `did`;

ALTER TABLE `oa_project_user` 
ADD COLUMN `name` varchar(255) NOT NULL DEFAULT '' COMMENT '成员姓名' AFTER `project_id`;

ALTER TABLE `oa_project_user` 
ADD COLUMN `role` varchar(255) NOT NULL DEFAULT '' COMMENT '担任项目角色' AFTER `name`;

ALTER TABLE `oa_project_user` 
ADD COLUMN `mobile` varchar(255) NOT NULL DEFAULT '' COMMENT '手机号码' AFTER `role`;

ALTER TABLE `oa_project_user` 
ADD COLUMN `company` varchar(255)  NOT NULL DEFAULT '' COMMENT '所在公司' AFTER `mobile`;

ALTER TABLE `oa_project_user` 
ADD COLUMN `enter_time` varchar(255) NOT NULL DEFAULT '' COMMENT '入驻项目日期' AFTER `company`;