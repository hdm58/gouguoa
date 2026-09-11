ALTER TABLE `oa_admin` 
ADD COLUMN `dingtalk_userid` varchar(100) NOT NULL DEFAULT '' COMMENT '钉钉用户ID' AFTER `userid`;