ALTER TABLE `oa_flow_record` 
ADD COLUMN `check_files` varchar(500) NOT NULL DEFAULT '' COMMENT '审批附件' AFTER `step_id`;

ALTER TABLE `oa_flow_cate` 
ADD COLUMN `is_copy` int(11) NOT NULL DEFAULT 1 COMMENT '是否支持抄送人' AFTER `sort`;

ALTER TABLE `oa_flow_cate` 
ADD COLUMN `is_file` int(11) NOT NULL DEFAULT 0 COMMENT '审批过程是否支持上传附件' AFTER `is_copy`;

ALTER TABLE `oa_flow_cate` 
ADD COLUMN `is_export` int(11) NOT NULL DEFAULT 0 COMMENT '审批通过后是否支持导出PDF打印' AFTER `is_file`;

ALTER TABLE `oa_flow_cate` 
ADD COLUMN `is_back` int(11) NOT NULL DEFAULT 1 COMMENT '是否支持撤回' AFTER `is_export`;

ALTER TABLE `oa_flow_cate` 
ADD COLUMN `is_reversed` int(11) NOT NULL DEFAULT 0 COMMENT '是否支持反确认' AFTER `is_back`;