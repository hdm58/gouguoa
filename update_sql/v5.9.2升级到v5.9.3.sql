ALTER TABLE `oa_contract` 
ADD COLUMN `chance_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联销售机会id' AFTER `customer_id`;

ALTER TABLE `oa_contract` 
ADD COLUMN `seal_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '盖章合同附件，如:1,2,3' AFTER `file_ids`;

ALTER TABLE `oa_purchase` 
ADD COLUMN `seal_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '盖章合同附件，如:1,2,3' AFTER `file_ids`;

