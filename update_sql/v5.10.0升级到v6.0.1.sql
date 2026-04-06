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