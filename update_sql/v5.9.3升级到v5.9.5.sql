ALTER TABLE `oa_note` 
ADD COLUMN `sourse` tinyint(1) NOT NULL DEFAULT 1 COMMENT '发布平台:1PC,2手机' AFTER `cate_id`;

