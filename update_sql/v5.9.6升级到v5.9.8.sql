ALTER TABLE `oa_seal_cate` ADD COLUMN `dids` varchar(255) NOT NULL DEFAULT '' COMMENT '应用部门' AFTER `title`;

ALTER TABLE `oa_file` 
ADD COLUMN `thumbpath` varchar(255) NOT NULL DEFAULT '' COMMENT '缩略图路径' AFTER `filepath`;