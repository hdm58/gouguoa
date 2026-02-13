ALTER TABLE `oa_file` 
ADD COLUMN `thumbpath` varchar(255) NOT NULL DEFAULT '' COMMENT '缩略图路径' AFTER `filepath`;