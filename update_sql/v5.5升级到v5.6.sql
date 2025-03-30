ALTER TABLE `oa_expense` 
ADD COLUMN `subject_id` int(11)  NOT NULL DEFAULT 0 COMMENT '报销企业主体' AFTER `id`;

ALTER TABLE `oa_ticket` 
ADD COLUMN `cash_type` int(11) UNSIGNED NULL DEFAULT 1 COMMENT '付款类型：1银行,2支付宝,3微信,4现金,5汇票,6支票,7托收,8其他' AFTER `pay_amount`;