SET FOREIGN_KEY_CHECKS=0;

CREATE TABLE `oa_account`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `enterprise_id` int NOT NULL DEFAULT 0 COMMENT '企业主体id',
  `title` varchar(100) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '资金账户名称',
  `account` varchar(100) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '具体账号',
  `initial_amount` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '初始资金',
  `amount` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '当前资金',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` bigint NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COMMENT = '资金账户' ROW_FORMAT = Dynamic;

ALTER TABLE `oa_car` MODIFY COLUMN `thumb` int NOT NULL DEFAULT 0 COMMENT '车辆照片' AFTER `price`;

ALTER TABLE `oa_car` MODIFY COLUMN `types` int NOT NULL DEFAULT 0 COMMENT '车辆类型:0公车,1专车' AFTER `thumb`;

CREATE TABLE `oa_car_use`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '申请主题',
  `car_id` int NOT NULL DEFAULT 0 COMMENT '车辆id',
  `content` mediumtext CHARACTER SET utf8mb4 NULL COMMENT '内容',
  `file_ids` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '附件ids,如:1,2,3',
  `use_time` bigint NOT NULL DEFAULT 0 COMMENT '预期用车日期',
  `out_admin` int NOT NULL DEFAULT 0 COMMENT '交车经手人',
  `out_time` bigint NOT NULL DEFAULT 0 COMMENT '交车日期',
  `back_admin` int NOT NULL DEFAULT 0 COMMENT '还车经手人',
  `back_time` bigint NOT NULL DEFAULT 0 COMMENT '还车日期',
  `status` tinyint NOT NULL DEFAULT 0 COMMENT '状态:0待使用,1已使用,2已归还',
  `admin_id` int NOT NULL DEFAULT 0 COMMENT '创建人',
  `did` int NOT NULL DEFAULT 0 COMMENT '用车部门',
  `create_time` bigint NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint NOT NULL DEFAULT 0 COMMENT '删除时间',
  `check_status` tinyint NOT NULL DEFAULT 0 COMMENT '审核状态:0待审核,1审核中,2审核通过,3审核不通过,4撤销审核',
  `check_flow_id` int NOT NULL DEFAULT 0 COMMENT '审核流程id',
  `check_step_sort` int NOT NULL DEFAULT 0 COMMENT '当前审批步骤',
  `check_uids` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '当前审批人ID，如:1,2,3',
  `check_last_uid` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '上一审批人',
  `check_history_uids` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '历史审批人ID，如:1,2,3',
  `check_copy_uids` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '抄送人ID，如:1,2,3',
  `check_time` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核通过时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COMMENT = '用车申请表' ROW_FORMAT = Dynamic;

CREATE TABLE `oa_contract_product`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `contract_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '合同id',
  `product_price_type` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '执行价格方式',
  `product_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品id',
  `product_price` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '商品单价',
  `product_num` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品数量',
  `product_total` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '商品小计',
  `product_remark` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '商品备注',
  `create_time` bigint NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COMMENT = '销售合同产品表' ROW_FORMAT = Dynamic;

CREATE TABLE `oa_contract_service`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `contract_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '合同id',
  `service_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '服务id',
  `service_price` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '服务单价',
  `service_num` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '服务次数',
  `service_total` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '服务小计',
  `service_remark` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '服务备注',
  `start_time` bigint NOT NULL DEFAULT 0 COMMENT '服务开始时间',
  `end_time` bigint NOT NULL DEFAULT 0 COMMENT '服务结束时间',
  `create_time` bigint NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COMMENT = '销售合同服务表' ROW_FORMAT = Dynamic;

ALTER TABLE `oa_customer` ADD COLUMN `is_clue` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否是线索：0否,1是' AFTER `is_lock`;

ALTER TABLE `oa_customer` ADD COLUMN `clue_name` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '线索联系人' AFTER `is_clue`;

ALTER TABLE `oa_customer` ADD COLUMN `clue_mobile` char(20) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '线索手机号码' AFTER `clue_name`;

ALTER TABLE `oa_customer` ADD COLUMN `customer_time` bigint NOT NULL DEFAULT 0 COMMENT '转成时间' AFTER `clue_mobile`;

ALTER TABLE `oa_customer` DROP COLUMN `contact_first`;

ALTER TABLE `oa_customer_chance` COMMENT = '客户销售机会表';

ALTER TABLE `oa_customer_chance` ADD COLUMN `belong_did` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属部门' AFTER `belong_uid`;

ALTER TABLE `oa_customer_contact` DROP COLUMN `family`;

ALTER TABLE `oa_customer_trace` ADD COLUMN `is_clue` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否是线索：0否,1是' AFTER `cid`;

ALTER TABLE `oa_customer_trace` ADD COLUMN `did` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建部门' AFTER `admin_id`;

ALTER TABLE `oa_disk` ADD COLUMN `share_types` tinyint(1) NOT NULL DEFAULT 0 COMMENT '分享对象：0不分享,1所有人,2部门,3岗位,4人员' AFTER `is_star`;

ALTER TABLE `oa_disk` ADD COLUMN `share_dids` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '分享部门' AFTER `share_types`;

ALTER TABLE `oa_disk` ADD COLUMN `share_pids` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '分享岗位' AFTER `share_dids`;

ALTER TABLE `oa_disk` ADD COLUMN `share_uids` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '分享学员' AFTER `share_pids`;

ALTER TABLE `oa_disk` ADD COLUMN `share_time` bigint NOT NULL DEFAULT 0 COMMENT '分享截止时间' AFTER `share_uids`;

ALTER TABLE `oa_disk` MODIFY COLUMN `is_star` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否标星' AFTER `file_size`;

CREATE TABLE `oa_doc_base`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '类型名称',
  `code` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '类型编号',
  `prefix` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '文号规则前缀名称',
  `prefix_num` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '文号规则前缀数字',
  `types` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '类目类型',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：0禁用 1启用',
  `admin_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '添加时间',
  `update_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
  `delete_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COMMENT = '文号基础表' ROW_FORMAT = COMPACT;

CREATE TABLE `oa_doc_number`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `base_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联基础id',
  `year` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '年份',
  `num` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '字号索引',
  `num_str` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '字号补零',
  `code` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '文号',
  `official_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '收文id',
  `received_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '发文id',
  `admin_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '添加时间',
  `update_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
  `delete_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COMMENT = '文号项目关联表' ROW_FORMAT = COMPACT;

CREATE TABLE `oa_events`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '事件标题',
  `content` mediumtext CHARACTER SET utf8mb4 NULL COMMENT '事件描述',
  `event_time` bigint NOT NULL DEFAULT 0 COMMENT '事件日期',
  `importance` int NOT NULL DEFAULT 0 COMMENT '重要程度',
  `admin_id` int NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` bigint NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COMMENT = '企业大事件' ROW_FORMAT = Dynamic;

ALTER TABLE `oa_expense` ADD COLUMN `enterprise_id` int NOT NULL DEFAULT 0 COMMENT '报销企业主体' AFTER `id`;

ALTER TABLE `oa_expense` ADD COLUMN `account_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '资金账户ID' AFTER `enterprise_id`;

ALTER TABLE `oa_expense` ADD COLUMN `paytype_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '支付方式' AFTER `account_id`;

ALTER TABLE `oa_expense` ADD COLUMN `contract_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联合同ID' AFTER `project_id`;

ALTER TABLE `oa_expense` ADD COLUMN `status` tinyint NOT NULL DEFAULT 1 COMMENT '打款状态 1待打款,2已打款' AFTER `file_ids`;

ALTER TABLE `oa_expense` ADD COLUMN `confirm_uid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '打款人ID' AFTER `status`;

ALTER TABLE `oa_expense` ADD COLUMN `confirm_time` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '打款时间' AFTER `confirm_uid`;

ALTER TABLE `oa_expense` DROP COLUMN `subject_id`;

ALTER TABLE `oa_expense` DROP COLUMN `pay_status`;

ALTER TABLE `oa_expense` DROP COLUMN `pay_admin_id`;

ALTER TABLE `oa_expense` DROP COLUMN `pay_time`;

CREATE TABLE `oa_finance_injection`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `amount` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '金额',
  `enterprise_id` int NOT NULL DEFAULT 0 COMMENT '企业主体id',
  `account_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '资金账户ID',
  `content` mediumtext CHARACTER SET utf8mb4 NULL COMMENT '注资说明',
  `handler_ids` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '经手人ID，如:1,2,3',
  `income_time` bigint NOT NULL DEFAULT 0 COMMENT '到账日期',
  `admin_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` bigint NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COMMENT = '财务注资表' ROW_FORMAT = Dynamic;

CREATE TABLE `oa_finance_log`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET utf8mb4 NOT NULL COMMENT '交易标识',
  `amount` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '交易金额',
  `types` tinyint NOT NULL DEFAULT 0 COMMENT '流水类型 1收入,2支出',
  `action_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '业务ID',
  `action_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '业务发生日期',
  `transaction_no` varchar(32) CHARACTER SET utf8mb4 NOT NULL COMMENT '交易流水编号',
  `enterprise_id` int NOT NULL DEFAULT 0 COMMENT '企业主体id',
  `account_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '资金账户ID',
  `fundscate_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '款项类型',
  `paytype_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '支付方式',
  `admin_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` bigint NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `transaction_no`(`transaction_no` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COMMENT = '财务流水表' ROW_FORMAT = Dynamic;

CREATE TABLE `oa_funds_cate`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '资金类型名称',
  `pid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '分类id',
  `sort` int NOT NULL DEFAULT 0 COMMENT '排序：越大越靠前',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `admin_id` int NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` bigint NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COMMENT = '资金类型' ROW_FORMAT = Dynamic;

CREATE TABLE `oa_income_refund`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `amount` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '金额',
  `income_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '收款ID',
  `customer_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联客户ID',
  `enterprise_id` int NOT NULL DEFAULT 0 COMMENT '企业主体id',
  `account_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '资金账户ID',
  `fundscate_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '款项类型',
  `paytype_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '支付方式',
  `back_time` bigint NOT NULL DEFAULT 0 COMMENT '退还时间',
  `file_ids` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '附件ID，如:1,2,3',
  `remarks` mediumtext CHARACTER SET utf8mb4 NULL COMMENT '备注',
  `status` tinyint NOT NULL DEFAULT 1 COMMENT '退款打款状态 1待打款,2已打款',
  `confirm_uid` bigint NOT NULL DEFAULT 0 COMMENT '打款确认人',
  `confirm_time` bigint NOT NULL DEFAULT 0 COMMENT '打款确认时间',
  `admin_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '退款登记人',
  `did` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属部门',
  `create_time` bigint NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint NOT NULL DEFAULT 0 COMMENT '删除时间',
  `check_status` tinyint NOT NULL DEFAULT 0 COMMENT '审核状态:0待审核,1审核中,2审核通过,3审核不通过,4撤销审核',
  `check_flow_id` int NOT NULL DEFAULT 0 COMMENT '审核流程id',
  `check_step_sort` int NOT NULL DEFAULT 0 COMMENT '当前审批步骤',
  `check_uids` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '当前审批人ID，如:1,2,3',
  `check_last_uid` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '上一审批人',
  `check_history_uids` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '历史审批人ID，如:1,2,3',
  `check_copy_uids` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '抄送人ID，如:1,2,3',
  `check_time` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核通过时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COMMENT = '到账退还记录表' ROW_FORMAT = Dynamic;

ALTER TABLE `oa_invoice` MODIFY COLUMN `enter_status` tinyint UNSIGNED NULL DEFAULT 0 COMMENT '收款状态：0未收款 1部分收款 2全部收款' AFTER `enter_amount`;

ALTER TABLE `oa_invoice` MODIFY COLUMN `enter_time` bigint NOT NULL DEFAULT 0 COMMENT '最新收款时间' AFTER `enter_status`;

ALTER TABLE `oa_invoice_income` COMMENT = '收款记录表';

ALTER TABLE `oa_invoice_income` ADD COLUMN `customer_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联客户ID' AFTER `invoice_id`;

ALTER TABLE `oa_invoice_income` ADD COLUMN `contract_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联销售合同ID' AFTER `customer_id`;

ALTER TABLE `oa_invoice_income` ADD COLUMN `project_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联项目ID' AFTER `contract_id`;

ALTER TABLE `oa_invoice_income` ADD COLUMN `enterprise_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '收款主体ID' AFTER `project_id`;

ALTER TABLE `oa_invoice_income` ADD COLUMN `account_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '收款账户ID' AFTER `enterprise_id`;

ALTER TABLE `oa_invoice_income` ADD COLUMN `fundscate_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '款项类型' AFTER `account_id`;

ALTER TABLE `oa_invoice_income` ADD COLUMN `paytype_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '支付方式' AFTER `fundscate_id`;

ALTER TABLE `oa_invoice_income` ADD COLUMN `transaction_code` varchar(100) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '交易单号' AFTER `paytype_id`;

ALTER TABLE `oa_invoice_income` ADD COLUMN `file_ids` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '附件ID，如:1,2,3' AFTER `transaction_code`;

ALTER TABLE `oa_invoice_income` ADD COLUMN `confirm_uid` bigint NOT NULL DEFAULT 0 COMMENT '到账确认人' AFTER `enter_time`;

ALTER TABLE `oa_invoice_income` ADD COLUMN `confirm_time` bigint NOT NULL DEFAULT 0 COMMENT '到账确认时间' AFTER `confirm_uid`;

ALTER TABLE `oa_invoice_income` ADD COLUMN `back_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '退款状态：0未退款,1部分退款,2已退款' AFTER `status`;

ALTER TABLE `oa_invoice_income` ADD COLUMN `back_amount` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '退款金额' AFTER `back_status`;

ALTER TABLE `oa_invoice_income` ADD COLUMN `did` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '申请部门' AFTER `admin_id`;

ALTER TABLE `oa_invoice_income` ADD COLUMN `delete_time` bigint NOT NULL DEFAULT 0 COMMENT '删除时间' AFTER `update_time`;

ALTER TABLE `oa_invoice_income` ADD COLUMN `check_status` tinyint NOT NULL DEFAULT 0 COMMENT '审核状态:0待审核,1审核中,2审核通过,3审核不通过,4撤销审核' AFTER `delete_time`;

ALTER TABLE `oa_invoice_income` ADD COLUMN `check_flow_id` int NOT NULL DEFAULT 0 COMMENT '审核流程id' AFTER `check_status`;

ALTER TABLE `oa_invoice_income` ADD COLUMN `check_step_sort` int NOT NULL DEFAULT 0 COMMENT '当前审批步骤' AFTER `check_flow_id`;

ALTER TABLE `oa_invoice_income` ADD COLUMN `check_uids` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '当前审批人ID，如:1,2,3' AFTER `check_step_sort`;

ALTER TABLE `oa_invoice_income` ADD COLUMN `check_last_uid` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '上一审批人' AFTER `check_uids`;

ALTER TABLE `oa_invoice_income` ADD COLUMN `check_history_uids` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '历史审批人ID，如:1,2,3' AFTER `check_last_uid`;

ALTER TABLE `oa_invoice_income` ADD COLUMN `check_copy_uids` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '抄送人ID，如:1,2,3' AFTER `check_history_uids`;

ALTER TABLE `oa_invoice_income` ADD COLUMN `check_time` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核通过时间' AFTER `check_copy_uids`;

ALTER TABLE `oa_invoice_income` MODIFY COLUMN `amount` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '收款金额' AFTER `file_ids`;

ALTER TABLE `oa_invoice_income` MODIFY COLUMN `enter_time` bigint NOT NULL DEFAULT 0 COMMENT '收款时间' AFTER `amount`;

ALTER TABLE `oa_invoice_income` MODIFY COLUMN `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：0无效,1未确认,2已确认' AFTER `confirm_time`;

ALTER TABLE `oa_invoice_income` MODIFY COLUMN `admin_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '申请人' AFTER `remarks`;

ALTER TABLE `oa_loan` ADD COLUMN `enterprise_id` int NOT NULL DEFAULT 0 COMMENT '借支企业主体' AFTER `id`;

ALTER TABLE `oa_loan` ADD COLUMN `account_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '资金账户ID' AFTER `enterprise_id`;

ALTER TABLE `oa_loan` ADD COLUMN `paytype_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '支付方式' AFTER `account_id`;

ALTER TABLE `oa_loan` ADD COLUMN `status` tinyint NOT NULL DEFAULT 1 COMMENT '打款状态 1待打款,2已打款' AFTER `file_ids`;

ALTER TABLE `oa_loan` ADD COLUMN `confirm_uid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '打款人ID' AFTER `status`;

ALTER TABLE `oa_loan` ADD COLUMN `confirm_time` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后打款时间' AFTER `confirm_uid`;

ALTER TABLE `oa_loan` DROP COLUMN `subject_id`;

ALTER TABLE `oa_loan` DROP COLUMN `pay_status`;

ALTER TABLE `oa_loan` DROP COLUMN `pay_admin_id`;

ALTER TABLE `oa_loan` DROP COLUMN `pay_time`;

CREATE TABLE `oa_pay_type`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '付款方式名称',
  `pid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '分类id',
  `sort` int NOT NULL DEFAULT 0 COMMENT '排序：越大越靠前',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `admin_id` int NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` bigint NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COMMENT = '付款方式' ROW_FORMAT = Dynamic;

ALTER TABLE `oa_plan` ADD COLUMN `customer_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '预设字段:关联客户ID' AFTER `type`;

ALTER TABLE `oa_plan` ADD COLUMN `project_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '预设字段:关联项目ID' AFTER `customer_id`;

ALTER TABLE `oa_plan` ADD COLUMN `uids` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '参与人' AFTER `remind_time`;

ALTER TABLE `oa_plan` MODIFY COLUMN `admin_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建员工ID' AFTER `file_ids`;

ALTER TABLE `oa_plan` MODIFY COLUMN `did` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属部门' AFTER `admin_id`;

ALTER TABLE `oa_plan` MODIFY COLUMN `delete_time` bigint NOT NULL DEFAULT 0 COMMENT '删除时间' AFTER `update_time`;

ALTER TABLE `oa_plan` DROP COLUMN `cid`;

ALTER TABLE `oa_plan` DROP COLUMN `cmid`;

ALTER TABLE `oa_plan` DROP COLUMN `ptid`;

ALTER TABLE `oa_position` ADD COLUMN `layouts` mediumtext CHARACTER SET utf8mb4 NULL COMMENT '首页展示模块' AFTER `title`;

CREATE TABLE `oa_problems`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '问题主题',
  `cate_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属分类ID',
  `project_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联项目ID',
  `contract_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联合同ID',
  `customer_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联客户ID',
  `problem_time` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '问题日期',
  `finish_time` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '完成日期',
  `over_time` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '关闭日期',
  `admin_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人员',
  `director_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '负责人员',
  `did` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '负责人部门',
  `emergent` tinyint(1) NOT NULL DEFAULT 1 COMMENT '紧急度：1低,2中,3高',
  `priority` tinyint(1) NOT NULL DEFAULT 1 COMMENT '优先级：1低,2中,3高',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：1进行中,2已分配,3已解决,4已关闭',
  `file_ids` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '附件ids',
  `content` mediumtext CHARACTER SET utf8mb4 NULL COMMENT '问题描述',
  `create_time` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
  `delete_time` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COMMENT = '问题表' ROW_FORMAT = Dynamic;

CREATE TABLE `oa_problems_cate`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '类别名称',
  `pid` int NOT NULL DEFAULT 0 COMMENT '父ID',
  `sort` int NOT NULL DEFAULT 0 COMMENT '排序',
  `content` mediumtext CHARACTER SET utf8mb4 NULL COMMENT '描述',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` bigint NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COMMENT = '问题类别' ROW_FORMAT = Dynamic;

CREATE TABLE `oa_problems_work`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '服务记录主题',
  `problems_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联问题',
  `task_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联任务',
  `director_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '负责人员',
  `did` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '负责人部门',
  `admin_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建员工ID',
  `start_time` int NOT NULL DEFAULT 0 COMMENT '开始时间',
  `end_time` int NOT NULL DEFAULT 0 COMMENT '结束时间',
  `hours` decimal(15, 1) NOT NULL DEFAULT 0.0 COMMENT '工时',
  `work_price` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '所有者的每小时费用',
  `work_total` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '工时总费用',
  `fee` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '其他费用',
  `amount` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '总费用',
  `content` mediumtext CHARACTER SET utf8mb4 NOT NULL COMMENT '描述',
  `delete_time` int NOT NULL DEFAULT 0 COMMENT '删除时间',
  `create_time` int NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COMMENT = '问题服务记录' ROW_FORMAT = Dynamic;

ALTER TABLE `oa_project` ADD COLUMN `score` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '项目评分' AFTER `amount`;

ALTER TABLE `oa_project` ADD COLUMN `importance` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '项目重要程度:1一般,2重要,3非常重要' AFTER `score`;

ALTER TABLE `oa_project` ADD COLUMN `uids` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '项目参与人' AFTER `did`;

ALTER TABLE `oa_project_user` ADD COLUMN `name` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '成员姓名' AFTER `project_id`;

ALTER TABLE `oa_project_user` ADD COLUMN `role` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '担任项目角色' AFTER `name`;

ALTER TABLE `oa_project_user` ADD COLUMN `mobile` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '手机号码' AFTER `role`;

ALTER TABLE `oa_project_user` ADD COLUMN `company` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '所在公司' AFTER `mobile`;

ALTER TABLE `oa_project_user` ADD COLUMN `enter_time` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '入驻项目日期' AFTER `company`;

ALTER TABLE `oa_property` ADD COLUMN `delete_time` bigint NOT NULL DEFAULT 0 COMMENT '删除时间' AFTER `update_time`;

ALTER TABLE `oa_property` MODIFY COLUMN `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态：0闲置,1在用,2维修,3报废,4丢失' AFTER `purchase_id`;

CREATE TABLE `oa_purchase_product`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `purchase_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '合同id',
  `product_price_type` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '执行价格方式',
  `product_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品id',
  `product_price` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '商品单价',
  `product_num` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品数量',
  `product_total` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '商品小计',
  `product_remark` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '商品备注',
  `create_time` bigint NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COMMENT = '采购合同产品表' ROW_FORMAT = Dynamic;

CREATE TABLE `oa_purchase_service`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `purchase_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '合同id',
  `service_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '服务id',
  `service_price` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '服务单价',
  `service_num` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '服务次数',
  `service_total` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '服务小计',
  `service_remark` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '服务备注',
  `start_time` bigint NOT NULL DEFAULT 0 COMMENT '服务开始时间',
  `end_time` bigint NOT NULL DEFAULT 0 COMMENT '服务结束时间',
  `create_time` bigint NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COMMENT = '采购合同服务表' ROW_FORMAT = Dynamic;

CREATE TABLE `oa_receive_docs`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '收文主题',
  `number_id` int NOT NULL DEFAULT 0 COMMENT '收文字号',
  `code` varchar(100) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '收文编号',
  `secrets` int NOT NULL DEFAULT 1 COMMENT '密级程度:1公开,2秘密,3机密',
  `urgency` int NOT NULL DEFAULT 1 COMMENT '紧急程度:1普通,2紧急,3加急',
  `send_dids` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '来文单位',
  `receive_dids` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '接受单位',
  `share_uids` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '分享查阅uid',
  `keywords` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '主题关键词',
  `content` mediumtext CHARACTER SET utf8mb4 NULL COMMENT '公文内容',
  `file_ids` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '公文文件ids,如:1,2,3',
  `receive_time` bigint NOT NULL DEFAULT 0 COMMENT '来文日期',
  `admin_id` int NOT NULL DEFAULT 0 COMMENT '登记人',
  `did` int NOT NULL DEFAULT 0 COMMENT '接受单位',
  `create_time` bigint NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint NOT NULL DEFAULT 0 COMMENT '删除时间',
  `check_status` tinyint NOT NULL DEFAULT 0 COMMENT '审核状态:0待审核,1审核中,2审核通过,3审核不通过,4撤销审核',
  `check_flow_id` int NOT NULL DEFAULT 0 COMMENT '审核流程id',
  `check_step_sort` int NOT NULL DEFAULT 0 COMMENT '当前审批步骤',
  `check_uids` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '当前审批人ID，如:1,2,3',
  `check_last_uid` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '上一审批人',
  `check_history_uids` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '历史审批人ID，如:1,2,3',
  `check_copy_uids` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '抄送人ID，如:1,2,3',
  `check_time` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核通过时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COMMENT = '收文表' ROW_FORMAT = Dynamic;

CREATE TABLE `oa_regulation`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '制度名称',
  `cate_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '制度分类id',
  `use_dids` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '试用部门',
  `content` text CHARACTER SET utf8mb4 NULL COMMENT '制度内容',
  `file_ids` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '产品附件ids,如:1,2,3',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：0禁用 1启用',
  `admin_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` bigint NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COMMENT = '制度表' ROW_FORMAT = Dynamic;

CREATE TABLE `oa_regulation_cate`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '分类名称',
  `pid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '分类id',
  `sort` int NOT NULL DEFAULT 0 COMMENT '排序：越大越靠前',
  `desc` varchar(1000) CHARACTER SET utf8mb4 NULL DEFAULT '' COMMENT '分类说明',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `admin_id` int NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` bigint NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COMMENT = '制度分类' ROW_FORMAT = Dynamic;

CREATE TABLE `oa_salary`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `month_time` bigint NOT NULL DEFAULT 0 COMMENT '工资月份',
  `title` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '工资单标题',
  `exclude_uids` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '排除人员',
  `enterprise_id` int NOT NULL DEFAULT 0 COMMENT '发放工资企业主体id',
  `account_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '资金账户ID',
  `paytype_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '支付方式',
  `amount` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '总金额',
  `salary` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '总工资',
  `social` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '总社保',
  `gongjijin` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '总公积金',
  `tax` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '总个税',
  `status` int NOT NULL DEFAULT 0 COMMENT '状态:0待发放,1已发放',
  `pay_time` bigint NOT NULL DEFAULT 0 COMMENT '发放时间',
  `pay_uid` int NOT NULL DEFAULT 0 COMMENT '发放人',
  `admin_id` int NOT NULL DEFAULT 0 COMMENT '创建人',
  `check_uid` int NOT NULL DEFAULT 0 COMMENT '工资审核人',
  `check_time` bigint NOT NULL DEFAULT 0,
  `create_time` bigint NOT NULL DEFAULT 0,
  `update_time` bigint NOT NULL DEFAULT 0,
  `delete_time` bigint NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COMMENT = '薪资单表' ROW_FORMAT = Dynamic;

CREATE TABLE `oa_salary_records`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `salary_id` int NOT NULL DEFAULT 0 COMMENT '工资单id',
  `uid` int NOT NULL DEFAULT 0 COMMENT '关联员工id',
  `bank` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '银行卡所属银行',
  `bank_no` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '银行卡号',
  `month_time` bigint NOT NULL DEFAULT 0 COMMENT '工资月份',
  `salary_basic` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '基本工资',
  `salary_position` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '岗位工资',
  `salary_performance` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '绩效工资',
  `salary_quanqin` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '全勤奖金',
  `salary_overwork` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '加班工资',
  `salary_meal` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '用餐补贴',
  `salary_phone` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '话费补贴',
  `salary_traffic` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '交通补贴',
  `salary_house` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '住房补贴',
  `salary_fuli` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '节假福利',
  `salary_protecting` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '保竞津贴',
  `salary_bonus` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '奖金',
  `deduct_belate` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '迟到扣除',
  `deduct_leave` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '事假扣除',
  `deduct_absenteeism` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '旷工扣除',
  `deduct_social` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '社保',
  `deduct_gongjijin` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '公积金',
  `deduct_tax` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '个税',
  `total_payable` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '应发工资',
  `total_deduction` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '应扣工资',
  `total_statutory` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '代扣代缴',
  `total_payment` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '实发工资',
  `remark` text CHARACTER SET utf8mb4 NULL COMMENT '备注信息',
  `status` int NOT NULL DEFAULT 0 COMMENT '状态:0待发放,1已发放',
  `pay_time` bigint NOT NULL DEFAULT 0 COMMENT '发放时间',
  `check_uid` int NOT NULL DEFAULT 0 COMMENT '确认人id',
  `check_time` bigint NOT NULL DEFAULT 0,
  `create_time` bigint NOT NULL DEFAULT 0,
  `update_time` bigint NOT NULL DEFAULT 0,
  `delete_time` bigint NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COMMENT = '员工月工资表' ROW_FORMAT = Dynamic;

ALTER TABLE `oa_schedule` COMMENT = '工作日志表';

ALTER TABLE `oa_schedule` MODIFY COLUMN `title` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '工作日志主题' AFTER `id`;

CREATE TABLE `oa_send_docs`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '发文主题',
  `number_id` int NOT NULL DEFAULT 0 COMMENT '发文字号',
  `code` varchar(100) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '发文编号',
  `secrets` int NOT NULL DEFAULT 1 COMMENT '密级程度:1公开,2秘密,3机密',
  `urgency` int NOT NULL DEFAULT 1 COMMENT '紧急程度:1普通,2紧急,3加急',
  `types` int NOT NULL DEFAULT 1 COMMENT '类型:1内部发文,2外部发文',
  `send_dids` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '主送单位',
  `copy_dids` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '抄送单位',
  `share_uids` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '分享查阅uid',
  `keywords` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '主题关键词',
  `desc` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '拟办意见',
  `content` mediumtext CHARACTER SET utf8mb4 NULL COMMENT '发文内容',
  `file_ids` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '公文文件ids,如:1,2,3',
  `draft_uid` int NOT NULL DEFAULT 0 COMMENT '拟稿人',
  `did` int NOT NULL DEFAULT 0 COMMENT '拟稿部门',
  `draft_time` bigint NOT NULL DEFAULT 0 COMMENT '拟稿日期',
  `admin_id` int NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` bigint NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint NOT NULL DEFAULT 0 COMMENT '删除时间',
  `check_status` tinyint NOT NULL DEFAULT 0 COMMENT '审核状态:0待审核,1审核中,2审核通过,3审核不通过,4撤销审核',
  `check_flow_id` int NOT NULL DEFAULT 0 COMMENT '审核流程id',
  `check_step_sort` int NOT NULL DEFAULT 0 COMMENT '当前审批步骤',
  `check_uids` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '当前审批人ID，如:1,2,3',
  `check_last_uid` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '上一审批人',
  `check_history_uids` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '历史审批人ID，如:1,2,3',
  `check_copy_uids` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '抄送人ID，如:1,2,3',
  `check_time` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核通过时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COMMENT = '发文表' ROW_FORMAT = Dynamic;

CREATE TABLE `oa_solutions`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '方案标题',
  `types` int UNSIGNED NOT NULL DEFAULT 1 COMMENT '类型:1解决方案,2变通方法',
  `cate_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属分类ID',
  `problems_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联问题',
  `director_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '所有者	',
  `hours` decimal(15, 1) NOT NULL DEFAULT 0.0 COMMENT '所需工时',
  `cost` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '所需费用',
  `content` mediumtext CHARACTER SET utf8mb4 NULL COMMENT '方案内容',
  `file_ids` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '附件ids',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：1待批准,2已批准,3未批准',
  `status_remark` mediumtext CHARACTER SET utf8mb4 NULL COMMENT '批准备注',
  `check_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '批准人',
  `check_time` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '批准时间',
  `admin_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `update_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后编辑人',
  `create_time` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
  `delete_time` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COMMENT = '解决方案表' ROW_FORMAT = Dynamic;

CREATE TABLE `oa_solutions_cate`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '方案类别名称',
  `pid` int NOT NULL DEFAULT 0 COMMENT '父ID',
  `sort` int NOT NULL DEFAULT 0 COMMENT '排序',
  `content` mediumtext CHARACTER SET utf8mb4 NULL COMMENT '描述',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` bigint NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COMMENT = '方案类别表' ROW_FORMAT = Dynamic;

ALTER TABLE `oa_ticket` MODIFY COLUMN `pay_time` bigint NOT NULL DEFAULT 0 COMMENT '最新付款时间' AFTER `pay_status`;

ALTER TABLE `oa_ticket` DROP COLUMN `cash_type`;

ALTER TABLE `oa_ticket_payment` COMMENT = '付款记录表';

ALTER TABLE `oa_ticket_payment` ADD COLUMN `supplier_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联供应商ID' AFTER `ticket_id`;

ALTER TABLE `oa_ticket_payment` ADD COLUMN `purchase_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联采购合同ID' AFTER `supplier_id`;

ALTER TABLE `oa_ticket_payment` ADD COLUMN `project_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联项目ID' AFTER `purchase_id`;

ALTER TABLE `oa_ticket_payment` ADD COLUMN `enterprise_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '付款主体ID' AFTER `project_id`;

ALTER TABLE `oa_ticket_payment` ADD COLUMN `account_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '付款账户ID' AFTER `enterprise_id`;

ALTER TABLE `oa_ticket_payment` ADD COLUMN `fundscate_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '款项类型' AFTER `account_id`;

ALTER TABLE `oa_ticket_payment` ADD COLUMN `paytype_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '支付方式' AFTER `fundscate_id`;

ALTER TABLE `oa_ticket_payment` ADD COLUMN `transaction_code` varchar(100) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '交易单号' AFTER `paytype_id`;

ALTER TABLE `oa_ticket_payment` ADD COLUMN `file_ids` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '附件ID，如:1,2,3' AFTER `transaction_code`;

ALTER TABLE `oa_ticket_payment` ADD COLUMN `confirm_uid` bigint NOT NULL DEFAULT 0 COMMENT '付款确认人' AFTER `pay_time`;

ALTER TABLE `oa_ticket_payment` ADD COLUMN `confirm_time` bigint NOT NULL DEFAULT 0 COMMENT '付款确认时间' AFTER `confirm_uid`;

ALTER TABLE `oa_ticket_payment` ADD COLUMN `did` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '申请部门' AFTER `admin_id`;

ALTER TABLE `oa_ticket_payment` ADD COLUMN `delete_time` bigint NOT NULL DEFAULT 0 COMMENT '删除时间' AFTER `update_time`;

ALTER TABLE `oa_ticket_payment` ADD COLUMN `check_status` tinyint NOT NULL DEFAULT 0 COMMENT '审核状态:0待审核,1审核中,2审核通过,3审核不通过,4撤销审核' AFTER `delete_time`;

ALTER TABLE `oa_ticket_payment` ADD COLUMN `check_flow_id` int NOT NULL DEFAULT 0 COMMENT '审核流程id' AFTER `check_status`;

ALTER TABLE `oa_ticket_payment` ADD COLUMN `check_step_sort` int NOT NULL DEFAULT 0 COMMENT '当前审批步骤' AFTER `check_flow_id`;

ALTER TABLE `oa_ticket_payment` ADD COLUMN `check_uids` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '当前审批人ID，如:1,2,3' AFTER `check_step_sort`;

ALTER TABLE `oa_ticket_payment` ADD COLUMN `check_last_uid` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '上一审批人' AFTER `check_uids`;

ALTER TABLE `oa_ticket_payment` ADD COLUMN `check_history_uids` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '历史审批人ID，如:1,2,3' AFTER `check_last_uid`;

ALTER TABLE `oa_ticket_payment` ADD COLUMN `check_copy_uids` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '抄送人ID，如:1,2,3' AFTER `check_history_uids`;

ALTER TABLE `oa_ticket_payment` ADD COLUMN `check_time` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核通过时间' AFTER `check_copy_uids`;

ALTER TABLE `oa_ticket_payment` MODIFY COLUMN `pay_time` bigint NOT NULL DEFAULT 0 COMMENT '付款时间' AFTER `amount`;

ALTER TABLE `oa_ticket_payment` MODIFY COLUMN `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：0无效,1未确认,2已确认' AFTER `confirm_time`;

ALTER TABLE `oa_ticket_payment` MODIFY COLUMN `admin_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '付款登记人' AFTER `remarks`;

CREATE TABLE `oa_work_plan`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '计划主题',
  `content` mediumtext CHARACTER SET utf8mb4 NULL COMMENT '计划内容',
  `file_ids` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '计划附件',
  `start_time` bigint NOT NULL DEFAULT 0 COMMENT '开始时间',
  `end_time` bigint NOT NULL DEFAULT 0 COMMENT '结束时间',
  `cate_id` tinyint UNSIGNED NULL DEFAULT 0 COMMENT '计划类型ID',
  `types` tinyint UNSIGNED NULL DEFAULT 0 COMMENT '参与人类型：1人员,2部门,3岗位,4全部',
  `uids` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '人员ids',
  `dids` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '部门ids',
  `pids` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '岗位ids',
  `director_uids` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '负责人ids',
  `endorse_uids` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '批注领导ids',
  `copy_uids` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '操送人员ids',
  `send_time` bigint NOT NULL DEFAULT 0 COMMENT '发送日期',
  `admin_id` int NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` bigint NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COMMENT = '工作计划表' ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `oa_official_docs`;

SET FOREIGN_KEY_CHECKS=1;


-- ----------------------------
-- Records of oa_account
-- ----------------------------
INSERT INTO `oa_account` VALUES (1, 1, '现金账户', '',0.00,0.00, 1, 1764388690, 0, 0);
INSERT INTO `oa_account` VALUES (2, 1, '支付宝账户', '',0.00,0.00, 1, 1764388722, 1764388758, 0);
INSERT INTO `oa_account` VALUES (3, 1, '微信支付账户', '',0.00,0.00, 1, 1764388736, 1764388764, 0);
INSERT INTO `oa_account` VALUES (4, 1, '招商银行账户', '',0.00,0.00, 1, 1764388747, 0, 0);
INSERT INTO `oa_account` VALUES (5, 1, '建设银行账户', '', 0.00,0.00,1, 1764388777, 0, 0);

-- ----------------------------
-- Records of oa_funds_cate
-- ----------------------------
INSERT INTO `oa_funds_cate` VALUES (1, '意向金', 0, 0, 1, 0, 1779630684, 1779630778, 0);
INSERT INTO `oa_funds_cate` VALUES (2, '定金', 0, 0, 1, 0, 1779630690, 1779630782, 0);
INSERT INTO `oa_funds_cate` VALUES (3, '尾款', 0, 0, 1, 0, 1779630699, 1779630820, 0);
INSERT INTO `oa_funds_cate` VALUES (4, '全款', 0, 0, 1, 0, 1779630823, 0, 0);

-- ----------------------------
-- Records of oa_pay_type
-- ----------------------------
INSERT INTO `oa_pay_type` VALUES (1, '银行转账', 0, 0, 1, 0, 1779631152, 1779631180, 0);
INSERT INTO `oa_pay_type` VALUES (2, '现金交易', 0, 0, 1, 0, 1779631159, 1779631185, 0);
INSERT INTO `oa_pay_type` VALUES (3, '支付宝', 0, 0, 1, 0, 1779631165, 0, 0);
INSERT INTO `oa_pay_type` VALUES (4, '微信支付', 0, 0, 1, 0, 1779631173, 0, 0);
INSERT INTO `oa_pay_type` VALUES (5, '汇票交易', 0, 0, 1, 0, 1779631200, 0, 0);
INSERT INTO `oa_pay_type` VALUES (6, '支票交易', 0, 0, 1, 0, 1779631205, 1779631211, 0);
INSERT INTO `oa_pay_type` VALUES (7, '托收', 0, 0, 1, 0, 1779631216, 0, 0);
INSERT INTO `oa_pay_type` VALUES (8, '其他', 0, 0, 1, 0, 1779631219, 0, 0);


-- ----------------------------
-- Table structure for oa_admin_module
-- ----------------------------
DROP TABLE IF EXISTS `oa_admin_module`;
CREATE TABLE `oa_admin_module`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '模块名称',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '模块目录，唯一，字母',
  `desc` varchar(500) NOT NULL DEFAULT '' COMMENT '模块功能描述',
  `type` int(2) NOT NULL DEFAULT 1 COMMENT '状态:1系统模块,2普通模块,3自定义模块',
  `sourse` int(2) NOT NULL DEFAULT 1 COMMENT '来源:1官方,2第三方',
  `status` int(1) NOT NULL DEFAULT 1 COMMENT '状态:0禁用,1正常',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '功能模块表';

-- ----------------------------
-- Records of oa_admin_module
-- ----------------------------
INSERT INTO `oa_admin_module` VALUES (1, '系统模块', 'home','', 1, 1,1, 1639562910, 0);
INSERT INTO `oa_admin_module` VALUES (2, '人事模块', 'user','', 1, 1,1, 1639562910, 0);
INSERT INTO `oa_admin_module` VALUES (3, '行政模块', 'adm','', 1, 1,1, 1639562910, 0);
INSERT INTO `oa_admin_module` VALUES (4, '办公模块', 'office','', 1, 1,1, 1639562910, 0);
INSERT INTO `oa_admin_module` VALUES (5, '客户模块', 'customer','', 2, 1,1, 1639562910, 0);
INSERT INTO `oa_admin_module` VALUES (6, '合同模块', 'contract','', 2, 1,1, 1656142368, 0);
INSERT INTO `oa_admin_module` VALUES (7, '项目模块', 'project','', 2, 1,1, 1656142368, 0);
INSERT INTO `oa_admin_module` VALUES (8, '售后模块', 'service','', 2, 1,1, 1656142368, 0);
INSERT INTO `oa_admin_module` VALUES (9, '财务模块', 'finance','', 2, 1,1, 1639562910, 0);
INSERT INTO `oa_admin_module` VALUES (10, '网盘模块', 'disk','', 2, 1,1, 1656143065, 0);

-- ----------------------------
-- Table structure for oa_admin_rule
-- ----------------------------
DROP TABLE IF EXISTS `oa_admin_rule`;
CREATE TABLE `oa_admin_rule`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父id',
  `src` varchar(255) NOT NULL DEFAULT '' COMMENT 'url链接',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '日志操作名称',
  `module` varchar(255) NOT NULL DEFAULT '' COMMENT '所属模块',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '图标',
  `menu` int(1) NOT NULL DEFAULT 0 COMMENT '是否是菜单,1是,2不是',
  `sort` int(11) NOT NULL DEFAULT 1 COMMENT '越小越靠前',
  `status` int(1) NOT NULL DEFAULT 1 COMMENT '状态,0禁用,1正常',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '菜单及权限表';

-- ----------------------------
-- Records of oa_admin_rule
-- ----------------------------
INSERT INTO `oa_admin_rule` VALUES (1, 0, '', '系统管理', '系统管理', 'home', 'icon-jichupeizhi', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (2, 0, '', '基础数据', '基础数据', 'base', 'icon-hetongshezhi', 1, 2, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (3, 0, '', '人事管理', '人事管理', 'user', 'icon-renshishezhi', 1, 3, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (4, 0, '', '行政办公', '行政办公', 'adm', 'icon-banjiguanli', 1, 4, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (5, 0, '', '日常办公', '日常办公', 'office', 'icon-kaoshijihua', 1, 5, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (6, 0, '', '客户管理', '客户管理', 'customer', 'icon-kehuguanli', 1, 6, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (7, 0, '', '合同管理', '合同管理', 'contract', 'icon-hetongyidong', 1, 7, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (8, 0, '', '项目管理', '项目管理', 'project', 'icon-xiangmuguanli', 1, 8, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (9, 0, '', '售后管理', '售后管理', 'service', 'icon-biaoxingyuangong', 1, 9, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (10, 0, '', '财务管理', '财务管理', 'finance', 'icon-yuangongtidian', 1, 10, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (11, 0, '', '数据分析', '数据分析', 'analysis', 'icon-bingtutongji', 1, 11, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (12, 0, '', '知识网盘', '知识网盘', 'disk', 'icon-tikuguanli', 1, 12, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (13, 1, 'home/conf/index', '系统配置', '系统配置', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (14, 13, 'home/conf/add', '新建/编辑', '配置项', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (15, 13, 'home/conf/delete', '删除', '配置项', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (16, 13, 'home/conf/edit', '编辑', '配置详情', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (17, 1, 'home/module/index', '功能模块', '功能模块', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (18, 17, 'home/module/add', '新建/编辑', '功能模块', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (19, 17, 'home/module/del', '删除', '功能模块', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (20, 17, 'home/module/recovery', '恢复', '功能模块', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (21, 17, 'home/module/install', '安装', '功能模块', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (22, 1, 'home/dataauth/index', '模块配置', '模块配置', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (23, 22, 'home/dataauth/edit', '编辑', '模块配置', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (24, 1, 'home/rule/index', '功能节点', '功能节点', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (25, 24, 'home/rule/add', '新建/编辑', '功能节点', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (26, 24, 'home/rule/delete', '删除', '功能节点', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (27, 1, 'mobile/bar/datalist', '移动端配置', '移动端配置', 'home', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (28, 27, 'mobile/bar/datalist', 'BAR菜单', 'BAR菜单', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (29, 28, 'mobile/bar/add', '新建/编辑', 'BAR菜单', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (30, 28, 'mobile/bar/set', '设置', 'BAR菜单', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (31, 27, 'mobile/types/datalist', '工作台菜单类型', '工作台菜单类型', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (32, 31, 'mobile/types/add', '新建/编辑', '工作台菜单类型', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (33, 31, 'mobile/types/del', '删除', '工作台菜单类型', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (34, 31, 'mobile/types/set', '设置', '工作台菜单类型', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (35, 27, 'mobile/menu/datalist', '工作台菜单', '工作台菜单', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (36, 35, 'mobile/menu/add', '新建/编辑', '工作台菜单', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (37, 35, 'mobile/menu/del', '删除', '工作台菜单', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (38, 35, 'mobile/menu/set', '设置', '工作台菜单', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (39, 1, 'home/role/index', '角色权限', '角色权限', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (40, 39, 'home/role/add', '新建/编辑', '角色权限', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (41, 39, 'home/role/delete', '删除', '角色权限', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (42, 1, 'home/log/index', '操作日志', '操作日志', 'home', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (43, 1, 'home/files/index', '附件管理','附件管理', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (44, 43, 'home/files/edit', '编辑附件','附件', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (45, 43, 'home/files/move', '移动附件','附件', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (46, 43, 'home/files/delete', '删除附件','附件', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (47, 43, 'home/files/get_group', '附件分组','附件分组', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (48, 43, 'home/files/add_group', '新建/编辑','附件分组', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (49, 43, 'home/files/del_group', '删除附件分组','附件分组', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (50, 1, 'home/database/database', '备份数据', '数据备份', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (51, 50, 'home/database/backup', '备份数据表', '数据', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (52, 1, 'home/task/index', '定时任务', '定时任务', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (53, 52, 'home/task/add', '新建/编辑', '定时任务', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (54, 52, 'home/task/delete', '删除', '定时任务', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (55, 2, '', '公共模块', '公共模块', 'home', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (56, 55, 'home/template/datalist', '消息模板', '消息模板', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (57, 56, 'home/template/add', '新建/编辑', '消息模板', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (58, 56, 'home/template/set', '设置', '消息模板', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (59, 56, 'home/template/view', '查看', '消息模板', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (60, 55, 'adm/flow/modulelist', '审批模块', '审批模块', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (61, 60, 'adm/flow/module_add', '新建/编辑', '审批模块', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (62, 60, 'adm/flow/module_check', '设置', '审批模块', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (63, 55, 'adm/flow/catelist', '审批类型', '审批类型', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (64, 63, 'adm/flow/cate_add', '新建/编辑', '审批类型', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (65, 63, 'adm/flow/cate_check', '设置', '审批类型', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (66, 55, 'adm/flow/datalist', '审批流程', '审批流程', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (67, 66, 'adm/flow/add', '新建/编辑', '审批流程', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (68, 66, 'adm/flow/del', '删除', '审批流程', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (69, 66, 'adm/flow/check', '设置', '审批流程', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (70, 55, 'home/cate/enterprise', '企业主体', '企业主体', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (71, 70, 'home/cate/enterprise_add', '新建/编辑', '企业主体', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (72, 70, 'home/cate/enterprise_check', '设置', '企业主体', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (73, 55, 'home/area/datalist', '全国省市', '全国省市', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (74, 73, 'home/area/add', '新建/编辑', '全国省市', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (75, 73, 'home/area/set', '设置', '全国省市', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (76, 55, 'home/cate/links', '办公工具', '办公工具', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (77, 76, 'home/cate/links_add', '新建/编辑', '办公工具', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (78, 76, 'home/cate/links_del', '删除', '办公工具', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (79, 2, '', '人事模块', '人事模块', 'user', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (80, 79, 'user/rewardscate/datalist', '奖罚项目', '奖罚项目', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (81, 80, 'user/rewardscate/add', '新建/编辑', '奖罚项目', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (82, 80, 'user/rewardscate/set', '设置', '奖罚项目', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (83, 79, 'user/carecate/datalist', '关怀项目', '关怀项目', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (84, 83, 'user/carecate/add', '新建/编辑', '关怀项目', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (85, 83, 'user/carecate/set', '设置', '关怀项目', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (86, 79, 'user/basic/datalist', '常规数据', '常规数据', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (87, 86, 'user/basic/add', '新建/编辑', '常规数据', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (88, 86, 'user/basic/set', '设置', '常规数据', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (89, 3, 'user/department/index', '部门架构', '部门', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (90, 89, 'user/department/add', '新建/编辑', '部门', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (91, 89, 'user/department/delete', '删除', '部门', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (92, 3, 'user/position/index', '岗位职称', '岗位职称', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (93, 92, 'user/position/add', '新建/编辑', '岗位职称', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (94, 92, 'user/position/delete', '删除', '岗位职称', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (95, 92, 'user/position/view', '查看', '岗位职称', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (96, 92, 'user/position/layouts', '工作台布局', '工作台布局', 'user', '', 2, 0, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (97, 3, 'user/user/index', '企业员工', '员工', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (98, 97, 'user/user/add', '新建/编辑', '员工', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (99, 97, 'user/user/view', '查看', '员工信息', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (100, 97, 'user/user/set', '设置', '员工状态', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (101, 97, 'user/user/reset_psw', '重设密码', '员工密码', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (102, 97, 'user/user/del', '删除', '员工', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (103, 3, 'user/files/datalist', '员工档案', '员工档案', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (104, 103, 'user/files/add', '编辑', '员工档案', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (105, 103, 'user/files/view', '查看', '员工档案', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (106, 3, 'user/talent/datalist', '入职申请', '入职申请', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (107, 106, 'user/talent/add', '新增/编辑', '入职申请', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (108, 106, 'user/talent/view', '查看', '入职申请', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (109, 106, 'user/talent/del', '删除', '入职申请', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (110, 106, 'user/talent/set', '入职', '新员工', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (111, 3, 'user/personal/change', '人事调动', '人事调动', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (112, 111, 'user/personal/change_add', '新建/编辑', '人事调动', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (113, 111, 'user/personal/change_view', '查看', '人事调动', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (114, 111, 'user/personal/change_delete', '删除', '人事调动', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (115, 3, 'user/personal/leave', '离职申请', '离职申请', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (116, 115, 'user/personal/leave_add', '新建/编辑', '离职申请', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (117, 115, 'user/personal/leave_delete', '删除', '离职申请', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (118, 115, 'user/personal/leave_view', '查看', '离职申请', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (119, 3, 'user/rewards/datalist', '奖罚管理', '奖罚管理', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (120, 119, 'user/rewards/add', '新建/编辑', '奖罚管理', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (121, 119, 'user/rewards/view', '查看', '奖罚管理', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (122, 119, 'user/rewards/del', '删除', '奖罚管理', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (123, 3, 'user/care/datalist', '员工关怀', '员工关怀', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (124, 123, 'user/care/add', '新建/编辑', '员工关怀', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (125, 123, 'user/care/view', '查看', '员工关怀', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (126, 123, 'user/care/del', '删除', '员工关怀', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (127, 3, 'user/laborcontract/datalist', '员工合同', '员工合同', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (128, 127, 'user/laborcontract/add', '新建/编辑', '员工合同', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (129, 127, 'user/laborcontract/add_renewal', '续签', '员工合同', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (130, 127, 'user/laborcontract/add_change', '变更', '员工合同', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (131, 127, 'user/laborcontract/view', '查看', '员工合同', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (132, 127, 'user/laborcontract/del', '删除', '员工合同', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (133, 127, 'user/laborcontract/set', '设置', '员工合同', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (134, 3, 'user/blacklist/datalist', '人员黑名单', '黑名单', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (135, 134, 'user/blacklist/add', '编辑', '黑名单', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (136, 134, 'user/blacklist/del', '删除', '黑名单', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (137, 2, '', '行政模块', '行政模块', 'adm', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (138, 137, 'adm/propertycate/datalist', '资产分类', '资产分类', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (139, 138, 'adm/propertycate/add', '新建/编辑', '资产分类', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (140, 138, 'adm/propertycate/del', '删除', '资产分类', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (141, 137, 'adm/propertybrand/datalist', '资产品牌', '资产品牌', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (142, 141, 'adm/propertybrand/add', '新建/编辑', '资产品牌', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (143, 141, 'adm/propertybrand/check', '设置', '资产品牌', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (144, 137, 'adm/propertyunit/datalist', '资产单位', '资产单位', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (145, 144, 'adm/propertyunit/add', '新建/编辑', '资产单位', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (146, 144, 'adm/propertyunit/check', '设置', '资产单位', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (147, 137, 'adm/sealcate/datalist', '印章管理', '印章', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (148, 147, 'adm/sealcate/add', '新建/编辑', '印章', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (149, 147, 'adm/sealcate/check', '设置', '印章', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (150, 137, 'adm/basic/datalist', '常规数据', '常规数据', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (151, 150, 'adm/basic/add', '新建/编辑', '常规数据', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (152, 150, 'adm/basic/set', '设置', '常规数据', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (153, 4, '', '固定资产', '固定资产', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (154, 153, 'adm/property/datalist', '资产信息', '固定资产', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (155, 154, 'adm/property/add', '新建/编辑', '固定资产', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (156, 154, 'adm/property/check', '设置', '固定资产', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (157, 154, 'adm/property/view', '查看', '固定资产', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (158, 153, 'adm/property/repair_list', '报修记录', '资产报修记录', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (159, 158, 'adm/property/repair_add', '新建/编辑', '资产报修记录', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (160, 158, 'adm/property/repair_view', '查看', '资产报修记录', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (161, 158, 'adm/property/repair_del', '删除', '资产报修记录', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (162, 4, '', '车辆管理', '车辆', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (163, 162, 'adm/car/datalist', '车辆信息', '车辆', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (164, 163, 'adm/car/add', '新建/编辑', '车辆', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (165, 163, 'adm/car/del', '删除', '车辆', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (166, 163, 'adm/car/view', '查看', '车辆', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (167, 162, 'adm/car/repair_list', '车辆维修', '车辆维修记录', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (168, 167, 'adm/car/repair_add', '新建/编辑', '车辆维修记录', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (169, 167, 'adm/car/repair_view', '查看', '车辆维修记录', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (170, 167, 'adm/car/repair_del', '删除', '车辆维修记录', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (171, 162, 'adm/car/protect_list', '车辆保养', '车辆保养记录', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (172, 171, 'adm/car/protect_add', '新建/编辑', '车辆保养记录', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (173, 171, 'adm/car/protect_view', '查看', '车辆保养记录', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (174, 171, 'adm/car/protect_del', '删除', '车辆保养记录', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (175, 162, 'adm/car/mileage_list', '车辆里程', '车辆里程记录', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (176, 175, 'adm/car/mileage_add', '新建/编辑', '车辆里程记录', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (177, 175, 'adm/car/mileage_del', '删除', '车辆里程记录', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (178, 162, 'adm/car/fee_list', '车辆费用', '车辆费用记录', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (179, 178, 'adm/car/fee_add', '新建/编辑', '车辆费用记录', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (180, 178, 'adm/car/fee_view', '查看', '车辆费用记录', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (181, 178, 'adm/car/fee_del', '删除', '车辆费用记录', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (182, 162, 'adm/car/record', '用车记录', '用车记录', 'adm', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (183, 137, 'adm/regulationcate/datalist', '制度类型', '制度类型', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (184, 183, 'adm/regulationcate/add', '新建/编辑', '制度类型', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (185, 183, 'adm/regulationcate/set', '设置', '制度类型', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (186, 4, 'adm/regulation/datalist', '规章制度', '规章制度', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (187, 186, 'adm/regulation/add', '新建/编辑', '规章制度', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (188, 186, 'adm/regulation/del', '删除', '规章制度', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (189, 186, 'adm/regulation/view', '查看', '规章制度', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (190, 137, 'adm/notecate/datalist', '公告类型', '公告类型', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (191, 190, 'adm/notecate/add', '新建/编辑', '公告类型', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (192, 190, 'adm/notecate/set', '设置', '公告类型', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (193, 4, 'adm/note/datalist', '公告列表', '公告', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (194, 193, 'adm/note/add', '新建/编辑', '公告', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (195, 193, 'adm/note/del', '删除', '公告', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (196, 193, 'adm/note/view', '查看', '公告', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (197, 4, 'adm/news/datalist', '公司新闻', '公司新闻', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (198, 197, 'adm/news/add', '新建/编辑', '公司新闻', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (199, 197, 'adm/news/del', '删除', '公司新闻', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (200, 197, 'adm/news/view', '查看', '公司新闻', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (201, 4, 'adm/events/datalist', '公司大事记', '公司大事记', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (202, 201, 'adm/events/add', '新建/编辑', '大事记', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (203, 201, 'adm/events/del', '删除', '大事记', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (204, 137, 'adm/official/number_list', '文号配置','文号配置', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (205, 204, 'adm/official/number_add', '新增/编辑','文号配置', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (206, 204, 'adm/official/number_view', '查看','文号配置', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (207, 204, 'adm/official/number_set', '设置','文号配置', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (210, 4, 'adm/seal/record', '用章记录', '用章记录', 'adm', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (211, 4, 'adm/meeting/room', '会议室管理', '会议室', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (212, 211, 'adm/meeting/room_add', '新建/编辑', '会议室', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (213, 211, 'adm/meeting/room_view', '查看', '会议纪要', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (214, 211, 'adm/meeting/room_check', '设置', '会议室', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (215, 211, 'adm/meeting/room_use', '使用情况', '会议室', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (216, 4, 'adm/salary/datalist', '员工薪资', '薪资', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (217, 216, 'adm/salary/add', '新建/编辑', '薪资', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (218, 216, 'adm/salary/view', '查看', '薪资', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (219, 216, 'adm/salary/del', '删除', '薪资', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (220, 216, 'adm/salary/records_add', '新增/编辑', '员工工资', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (221, 216, 'adm/salary/records_view', '查看', '员工工资', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (222, 216, 'adm/salary/records_del', '删除', '员工工资', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (223, 5, 'adm/car/apply_list', '用车申请', '用车', 'oa', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (224, 223, 'adm/car/apply_add', '新建/编辑', '用车', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (225, 223, 'adm/car/apply_view', '查看', '用车', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (226, 223, 'adm/car/apply_del', '删除', '用车', 'oa', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (235, 5, 'adm/seal/datalist', '用章申请', '用章申请', 'oa', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (236, 235, 'adm/seal/add', '新建/编辑', '用章申请', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (237, 235, 'adm/seal/view', '查看', '用章申请', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (238, 235, 'adm/seal/del', '删除', '用章申请', 'oa', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (239, 5, 'adm/meeting/datalist', '会议室预定', '会议室预定', 'oa', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (240, 239, 'adm/meeting/add', '新增/编辑', '会议室预定', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (241, 239, 'adm/meeting/view', '查看', '会议室预定', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (242, 239, 'adm/meeting/del', '删除', '会议室预定', 'oa', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (243, 5, 'adm/meeting/records', '会议记录', '会议记录', 'oa', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (244, 243, 'adm/meeting/records_add', '新建/编辑', '会议记录', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (245, 243, 'adm/meeting/records_view', '查看', '会议纪要', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (246, 243, 'adm/meeting/records_del', '删除', '会议纪要', 'oa', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (247, 5, 'oa/plan/calendar', '日程安排', '日程安排', 'oa', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (248, 247, 'oa/plan/add', '新建/编辑', '日程安排', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (249, 247, 'oa/plan/view', '查看', '日程安排', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (250, 247, 'oa/plan/del', '删除', '日程安排', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (251, 247, 'oa/plan/datalist', '日程列表', '日程安排', 'oa', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (252, 5, 'oa/schedule/calendar', '工作日志', '工作日志', 'oa', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (253, 252, 'oa/schedule/add', '新建/编辑', '工作日志', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (254, 252, 'oa/schedule/view', '查看', '工作日志', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (255, 252, 'oa/schedule/del', '删除', '工作日志', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (256, 252, 'oa/schedule/datalist', '工作日志', '工作日志', 'oa', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (257, 5, 'oa/work/datalist', '工作汇报', '工作汇报', 'oa', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (258, 257, 'oa/work/add', '新建/编辑', '工作汇报', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (259, 257, 'oa/work/send', '发送', '工作汇报', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (260, 257, 'oa/work/view', '查看', '工作汇报', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (261, 257, 'oa/work/del', '删除', '工作汇报', 'oa', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (262, 5, 'oa/workplan/datalist', '工作计划', '工作计划', 'oa', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (263, 262, 'oa/workplan/add', '新建/编辑', '工作计划', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (264, 262, 'oa/workplan/view', '查看', '工作计划', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (265, 262, 'oa/workplan/del', '删除', '工作计划', 'oa', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (266, 5, 'oa/salary/datalist', '我的薪资', '薪资', 'oa', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (267, 266, 'oa/salary/view', '详情', '薪资', 'oa', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (268, 2, '', '客户模块', '客户模块', 'customer', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (269, 268, 'customer/industry/datalist', '行业类型', '行业类型', 'home', '', 1, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (270, 269, 'customer/industry/add', '新建/编辑', '行业类型', 'home', '', 2, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (271, 269, 'customer/industry/set', '设置', '行业类型', 'home', '', 2, 0, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (272, 268, 'customer/grade/datalist', '客户等级', '客户等级', 'customer', '', 1, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (273, 272, 'customer/grade/add', '新建/编辑', '客户等级', 'customer', '', 2, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (274, 272, 'customer/grade/set', '设置', '客户等级', 'customer', '', 2,0, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (275, 268, 'customer/source/datalist', '客户渠道', '客户渠道', 'customer', '', 1, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (276, 275, 'customer/source/add', '新建/编辑', '客户渠道', 'customer', '', 2, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (277, 275, 'customer/source/set', '设置', '客户渠道', 'customer', '', 2,0, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (278, 268, 'customer/basic/datalist', '常规数据', '常规数据', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (279, 278, 'customer/basic/add', '新建/编辑', '常规数据', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (280, 278, 'customer/basic/set', '设置', '常规数据', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (281, 6, 'customer/customer/datalist', '客户列表', '客户列表', 'customer', '', 1, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (282, 281, 'customer/customer/add', '新建/编辑', '客户', 'customer', '', 2, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (283, 281, 'customer/customer/view', '查看', '客户', 'customer', '', 2, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (284, 281, 'customer/customer/del', '删除', '客户', 'customer', '', 2, 0, 1, 1556143065, 0);

INSERT INTO `oa_admin_rule` VALUES (285, 6, 'customer/index/rush', '抢 客 宝', '抢客宝', 'customer', '', 1, 0, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (286, 6, 'customer/index/sea', '公海客户', '客户', 'customer', '', 1, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (287, 286, 'customer/index/to_get', '获取', '客户', 'customer', '', 2, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (288, 286, 'customer/index/to_divide', '分配客户', '客户', 'customer', '', 2, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (289, 286, 'customer/index/to_sea', '转入公海', '客户', 'customer', '', 2, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (290, 286, 'customer/index/to_trash', '转入废弃池', '客户', 'customer', '', 2, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (291, 286, 'customer/index/to_revert', '恢复客户', '客户', 'customer', '', 2, 0, 1, 1556143065, 0);

INSERT INTO `oa_admin_rule` VALUES (292, 6, 'customer/index/trash', '废弃客户', '客户', 'customer', '', 1, 0, 1, 1556143065, 0);

INSERT INTO `oa_admin_rule` VALUES (293, 6, 'customer/contact/datalist', '客户联系人', '联系人', 'customer', '', 1, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (294, 293, 'customer/contact/add', '新建/编辑', '联系人', 'customer', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (295, 293, 'customer/contact/del', '删除', '联系人', 'customer', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (296, 293, 'customer/contact/view', '查看', '客户联系人', 'customer', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (297, 6, 'customer/chance/datalist', '销售机会', '销售机会', 'customer', '', 1, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (298, 297, 'customer/chance/add', '新建/编辑', '销售机会', 'customer', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (299, 297, 'customer/chance/view', '查看', '销售机会', 'customer', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (300, 297, 'customer/chance/del', '删除', '销售机会', 'customer', '', 2, 0, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (301, 6, 'customer/trace/datalist', '客户跟进', '客户跟进', 'customer', '', 1, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (302, 301, 'customer/trace/add', '新建/编辑', '客户跟进', 'customer', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (303, 301, 'customer/trace/view', '查看', '客户跟进', 'customer', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (304, 301, 'customer/trace/del', '删除', '客户跟进', 'customer', '', 2, 0, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (305, 6, 'customer/clue/datalist', '线索列表', '线索列表', 'customer', '', 1, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (306, 305, 'customer/clue/add', '新建/编辑', '线索', 'customer', '', 2, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (307, 305, 'customer/clue/view', '查看', '线索', 'customer', '', 2, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (308, 305, 'customer/clue/del', '删除', '线索', 'customer', '', 2, 0, 1, 1556143065, 0);

INSERT INTO `oa_admin_rule` VALUES (309, 6, 'customer/clue/sealist', '公海线索', '线索', 'customer', '', 1, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (310, 309, 'customer/clue/to_get', '获取', '线索', 'customer', '', 2, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (311, 309, 'customer/clue/to_sea', '转出', '线索', 'customer', '', 2, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (312, 309, 'customer/clue/to_transfer', '转移', '线索', 'customer', '', 2, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (313, 309, 'customer/clue/to_allot', '分配', '线索', 'customer', '', 2, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (314, 309, 'customer/clue/to_customer', '转为客户', '线索', 'customer', '', 2, 0, 1, 1556143065, 0);

INSERT INTO `oa_admin_rule` VALUES (315, 6, 'customer/follow/datalist', '线索跟进', '线索跟进', 'customer', '', 1, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (316, 315, 'customer/follow/add', '新建/编辑', '线索跟进', 'customer', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (317, 315, 'customer/follow/view', '查看', '线索跟进', 'customer', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (318, 315, 'customer/follow/del', '删除', '线索跟进', 'customer', '', 2, 0, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (319, 2, '', '合同模块', '合同模块', 'contract', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (320, 319, 'contract/cate/datalist', '合同分类', '合同分类', 'contract', '', 1, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (321, 320, 'contract/cate/add', '新建/编辑', '合同分类', 'contract', '', 2, 1, 1, 0, 1656143065);
INSERT INTO `oa_admin_rule` VALUES (322, 320, 'contract/cate/set', '设置', '合同分类', 'contract', '', 2, 1, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (323, 319, 'contract/productcate/datalist', '产品分类', '产品分类', 'contract', '', 1, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (324, 323, 'contract/productcate/add', '新建/编辑', '产品分类', 'contract', '', 2, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (325, 323, 'contract/productcate/del', '删除', '产品分类', 'contract', '', 2, 1, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (326, 319, 'contract/product/datalist', '产品列表', '产品', 'contract', '', 1, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (327, 326, 'contract/product/add', '新建/编辑', '产品', 'contract', '', 2, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (328, 326, 'contract/product/view', '查看', '产品', 'contract', '', 2, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (329, 326, 'contract/product/del', '删除', '产品', 'contract', '', 2, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (330, 326, 'contract/product/set', '设置', '产品', 'contract', '', 2, 1, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (331, 319, 'contract/services/datalist', '服务内容', '服务内容', 'contract', '', 1, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (332, 331, 'contract/services/add', '新建/编辑', '服务内容', 'contract', '', 2, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (333, 331, 'contract/services/set', '设置', '服务内容', 'contract', '', 2, 1, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (334, 319, 'contract/supplier/datalist', '供应商列表', '供应商', 'contract', '', 1, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (335, 334, 'contract/supplier/add', '新建/编辑', '供应商', 'contract', '', 2, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (336, 334, 'contract/supplier/set', '设置', '供应商', 'contract', '', 2, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (337, 334, 'contract/supplier/view', '查看', '供应商', 'contract', '', 2, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (338, 334, 'contract/supplier/del', '删除', '供应商', 'contract', '', 2, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (339, 334, 'contract/supplier/contact_add', '新建/编辑', '供应商联系人', 'contract', '', 2, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (340, 334, 'contract/supplier/contact_del', '删除', '供应商联系人', 'contract', '', 2, 1, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (341, 319, 'contract/purchasedcate/datalist', '采购品分类', '采购品分类', 'contract', '', 1, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (342, 341, 'contract/purchasedcate/add', '新建/编辑', '采购品分类', 'contract', '', 2, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (343, 341, 'contract/purchasedcate/del', '删除', '采购品分类', 'contract', '', 2, 1, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (344, 319, 'contract/purchased/datalist', '采购品列表', '采购品', 'contract', '', 1, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (345, 344, 'contract/purchased/add', '新建/编辑', '采购品', 'contract', '', 2, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (346, 344, 'contract/purchased/view', '查看', '采购品', 'contract', '', 2, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (347, 344, 'contract/purchased/del', '删除', '采购品', 'contract', '', 2, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (348, 344, 'contract/purchased/set', '设置', '采购品', 'contract', '', 2, 1, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (349, 7, 'contract/contract/datalist', '销售合同', '销售合同', 'contract', '', 1, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (350, 349, 'contract/contract/add', '新建/编辑', '合同', 'contract', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (351, 349, 'contract/contract/view', '查看', '合同', 'contract', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (352, 349, 'contract/contract/del', '删除', '合同', 'contract', '', 2, 0, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (353, 7, 'contract/purchase/datalist', '采购合同', '采购合同', 'contract', '', 1, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (354, 353, 'contract/purchase/add', '新建/编辑', '合同', 'contract', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (355, 353, 'contract/purchase/view', '查看', '合同', 'contract', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (356, 353, 'contract/purchase/del', '删除', '合同', 'contract', '', 2, 0, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (357, 7, 'contract/contract/archivelist', '合同归档', '合同归档', 'contract', '', 1, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (358, 357, 'contract/purchase/archivelist', '采购合同归档', '采购合同归档', 'contract', '', 2, 0, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (359, 7, 'contract/contract/stoplist', '中止合同', '中止合同', 'contract', '', 1, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (360, 359, 'contract/purchase/stoplist', '中止采购合同', '中止采购合同', 'contract', '', 2, 0, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (361, 7, 'contract/contract/voidlist', '作废合同', '作废合同', 'contract', '', 1, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (362, 361, 'contract/purchase/voidlist', '作废合同归档', '作废采购合同', 'contract', '', 2, 0, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (363, 2, '', '项目模块', '项目模块', 'project', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (364, 363, 'project/step/datalist', '项目阶段', '项目阶段', 'project', '', 1, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (365, 364, 'project/step/add', '新建/编辑', '项目阶段', 'project', '', 2, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (366, 364, 'project/step/set', '设置', '项目阶段', 'project', '', 2, 1, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (367, 363, 'project/cate/datalist', '项目分类', '项目分类', 'project', '', 1, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (368, 367, 'project/cate/add', '新建/编辑', '项目分类', 'project', '', 2, 1, 1, 0, 1656143065);
INSERT INTO `oa_admin_rule` VALUES (369, 367, 'project/cate/set', '设置', '项目分类', 'project', '', 2, 1, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (370, 363, 'project/work/datalist', '工作类别', '工作类别', 'project', '', 1, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (371, 370, 'project/work/add', '新建/编辑', '工作类别', 'project', '', 2, 1, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (372, 370, 'project/work/set', '设置', '工作类别', 'project', '', 2, 1, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (373, 8, 'project/index/datalist', '项目列表', '项目', 'project', '', 1, 0, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (374, 373, 'project/index/add', '新建', '项目', 'project', '', 2, 0, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (375, 373, 'project/index/edit', '编辑', '项目', 'project', '', 2, 0, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (376, 373, 'project/index/view', '查看', '项目', 'project', '', 2, 0, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (377, 373, 'project/index/del', '删除', '项目', 'project', '', 2, 0, 1, 1656142368, 0);

INSERT INTO `oa_admin_rule` VALUES (378, 8, 'project/task/datalist', '任务列表', '任务', 'project', '', 1, 0, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (379, 378, 'project/task/add', '新建', '任务', 'project', '', 2, 0, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (380, 378, 'project/task/edit', '编辑', '任务', 'project', '', 2, 0, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (381, 378, 'project/task/view', '查看', '任务', 'project', '', 2, 0, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (382, 378, 'project/task/del', '删除', '任务', 'project', '', 2, 0, 1, 1656142368, 0);

INSERT INTO `oa_admin_rule` VALUES (383, 8, 'project/task/hour', '任务工时', '工时', 'project', '', 1, 0, 1, 1656142368, 0);

INSERT INTO `oa_admin_rule` VALUES (384, 8, 'project/document/datalist', '文档列表', '文档', 'project', '', 1, 0, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (385, 384, 'project/document/add', '新建/编辑', '文档', 'project', '', 2, 0, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (386, 384, 'project/document/view', '查看', '文档', 'project', '', 2, 0, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (387, 384, 'project/document/delete', '删除', '文档', 'project', '', 2, 0, 1, 1656142368, 0);

INSERT INTO `oa_admin_rule` VALUES (388, 2, '', '售后模块', '售后模块', 'service', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (389, 388, 'service/problemscate/datalist', '问题类型', '问题类型', 'service', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (390, 389, 'service/problemscate/add', '新建/编辑', '问题类型', 'service', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (391, 389, 'service/problemscate/set', '设置', '问题类型', 'service', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (392, 388, 'service/solutionscate/datalist', '方案类型', '方案类型', 'service', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (393, 392, 'service/solutionscate/add', '新建/编辑', '方案类型', 'service', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (394, 392, 'service/solutionscate/set', '设置', '方案类型', 'service', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (395, 9, 'service/problems/datalist', '售后问题', '售后问题', 'service', '', 1, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (396, 395, 'service/problems/add', '新建/编辑', '售后问题', 'service', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (397, 395, 'service/problems/view', '查看', '售后问题', 'service', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (398, 395, 'service/problems/del', '删除', '售后问题', 'service', '', 2, 0, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (399, 9, 'service/solutions/datalist', '解决方案', '解决方案', 'service', '', 1, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (400, 399, 'service/solutions/add', '新建/编辑', '解决方案', 'service', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (401, 399, 'service/solutions/view', '查看', '解决方案', 'service', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (402, 399, 'service/solutions/del', '删除', '解决方案', 'service', '', 2, 0, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (403, 9, 'service/problemswork/datalist', '服务记录', '服务记录', 'service', '', 1, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (404, 403, 'service/problemswork/add', '新建/编辑', '服务记录', 'service', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (405, 403, 'service/problemswork/view', '查看', '服务记录', 'service', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (406, 403, 'service/problemswork/del', '删除', '服务记录', 'service', '', 2, 0, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (407, 2, '', '财务模块', '财务模块', 'finance', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (408, 407, 'finance/expensecate/datalist', '报销类型', '报销类型', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (409, 408, 'finance/expensecate/add', '新建/编辑', '报销类型', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (410, 408, 'finance/expensecate/set', '设置', '报销类型', 'finance', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (411, 407, 'finance/costcate/datalist', '费用类型', '费用类型', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (412, 411, 'finance/costcate/add', '新建/编辑', '费用类型', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (413, 411, 'finance/costcate/set', '设置', '费用类型', 'finance', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (414, 407, 'finance/account/datalist', '资金账户', '资金账户', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (415, 414, 'finance/account/add', '新建/编辑', '资金账户', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (416, 414, 'finance/account/set', '设置', '资金账户', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (417, 414, 'finance/account/income', '收入', '账户收入', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (418, 414, 'finance/account/payout', '支出', '账户支出', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (419, 414, 'finance/account/injection', '注资', '账户注资', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (420, 419, 'finance/account/injection_add', '新建/编辑', '账户注资', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (421, 419, 'finance/account/injection_del', '删除', '账户注资', 'finance', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (422, 407, 'finance/fundscate/datalist', '资金类型', '资金类型', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (423, 422, 'finance/fundscate/add', '新建/编辑', '资金类型', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (424, 422, 'finance/fundscate/set', '设置', '资金类型', 'finance', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (425, 407, 'finance/paytype/datalist', '付款方式', '付款方式', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (426, 425, 'finance/paytype/add', '新建/编辑', '付款方式', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (427, 425, 'finance/paytype/set', '设置', '付款方式', 'finance', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (428, 10, 'finance/loan/datalist', '借支管理', '借支', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (429, 428, 'finance/loan/add', '新建/编辑', '借支', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (430, 428, 'finance/loan/del', '删除', '借支', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (431, 428, 'finance/loan/view', '查看', '借支', 'finance', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (432, 10, 'finance/expense/datalist', '报销管理', '报销', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (433, 432, 'finance/expense/add', '新建/编辑', '报销', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (434, 432, 'finance/expense/del', '删除', '报销', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (435, 432, 'finance/expense/view', '查看', '报销', 'finance', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (436, 10, 'finance/invoice/datalist', '销项发票', '发票', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (437, 436, 'finance/invoice/add', '新建/编辑', '发票', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (438, 436, 'finance/invoice/del', '删除', '发票', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (439, 436, 'finance/invoice/view', '查看', '发票', 'finance', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (440, 10, 'finance/ticket/datalist', '进项发票', '发票', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (441, 440, 'finance/ticket/add', '新建/编辑', '发票', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (442, 440, 'finance/ticket/del', '删除', '发票', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (443, 440, 'finance/ticket/view', '查看', '发票', 'finance', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (444, 10, 'finance/income/datalist', '收款管理', '收款记录', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (445, 444, 'finance/income/add', '新建/编辑', '收款记录', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (446, 444, 'finance/income/view', '查看', '收款记录', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (447, 444, 'finance/income/del', '删除', '收款记录', 'finance', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (448, 10, 'finance/payment/datalist', '付款管理', '付款记录', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (449, 448, 'finance/payment/add', '新建/编辑', '付款记录', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (450, 448, 'finance/payment/view', '查看', '付款记录', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (451, 448, 'finance/payment/del', '删除', '付款记录', 'finance', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (452, 10, 'finance/refund/datalist', '退款管理', '退款', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (453, 452, 'finance/refund/add', '新建/编辑', '退款', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (454, 452, 'finance/refund/view', '查看', '退款', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (455, 452, 'finance/refund/del', '删除', '退款', 'finance', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (456, 10, '', '财务统计', '财务统计', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (457, 456, 'finance/expense/record', '报销记录', '报销记录', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (458, 456, 'finance/loan/record', '借支记录', '借支记录', 'finance', '', 1, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (459, 456, 'finance/invoice/record', '销项发票记录', '开票记录', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (460, 456, 'finance/ticket/record', '进项发票记录', '收票记录', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (461, 456, 'finance/income/record', '收款记录', '收款记录', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (462, 456, 'finance/payment/record', '付款记录', '付款记录', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (463, 456, 'finance/refund/record', '退款记录', '退款记录', 'finance', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (464, 11, 'analysis/customer/datalist', '客户属性分析', '客户属性分析', 'analysis', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (465, 11, 'analysis/customer/followlist', '客户跟进分析', '客户跟进分析', 'analysis', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (466, 11, 'analysis/order/datalist', '销售订单分析', '销售订单分析', 'analysis', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (467, 11, 'analysis/order/salelist', '销售订单排行', '销售订单排行', 'analysis', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (468, 11, 'analysis/order/incomelist', '销售收款排行', '销售收款排行', 'analysis', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (469, 11, 'analysis/finance/invoicelist', '发票数据分析', '发票数据分析', 'analysis', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (470, 11, 'analysis/finance/paymentlist', '收付款数据分析', '收付款数据分析', 'analysis', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (471, 11, 'analysis/finance/accountlist', '财务台账分析', '财务台账分析', 'analysis', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (472, 471, 'analysis/finance/incomelist', '财务收入流水', '财务收入流水', 'analysis', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (473, 471, 'analysis/finance/payoutlist', '财务支出流水', '财务支出流水', 'analysis', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (474, 12, 'disk/index/datalist', '个人空间', '个人空间', 'disk', '', 1, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (475, 474, 'disk/index/add_upload', '新增', '文件', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (476, 474, 'disk/index/add_folder', '新增', '文件夹', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (477, 474, 'disk/index/add_article', '新增/编辑', '在线文档', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (478, 474, 'disk/index/view_article', '查看', '在线文档', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (479, 474, 'disk/index/del', '删除', '文件/文件夹/在线文档', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (480, 474, 'disk/index/rename', '重命名', '文件', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (481, 474, 'disk/index/move', '移动', '文件', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (482, 474, 'disk/index/star', '标星', '文件', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (483, 474, 'disk/index/unstar', '取消标星', '文件', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (484, 474, 'disk/index/back', '还原', '文件', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (485, 474, 'disk/index/clear', '清除', '文件', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (486, 474, 'disk/index/share', '分享', '文件', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (487, 474, 'disk/index/unshare', '取消分享', '文件', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (488, 474, 'disk/index/starlist', '标星文件', '文件', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (489, 474, 'disk/index/mysharelist', '我分享的文件', '文件', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (490, 474, 'disk/index/tosharelist', '别人分享的文件', '文件', 'disk', '', 2, 0, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (491, 12, 'disk/index/sharelist', '共享空间', '共享空间', 'disk', '', 1, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (492, 491, 'disk/index/add_group', '新建/编辑','共享空间', 'disk', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (493, 491, 'disk/index/del_group', '删除','共享空间', 'disk', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (494, 12, 'disk/index/clearlist', '回 收 站', '回收站文件', 'disk', '', 1, 0, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (495, 4, 'adm/attendance/datalist', '假勤记录', '假勤记录', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (496, 5, 'oa/attendance/datalist', '我的假勤', '我的假勤', 'oa', '', 1, 1, 1, 0, 0);

UPDATE `oa_admin_group` SET `rules` = '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,206,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,248,249,250,251,252,253,254,255,256,257,258,259,260,261,262,263,264,265,266,267,268,269,270,271,272,273,274,275,276,277,278,279,280,281,282,283,284,285,286,287,288,289,290,291,292,293,294,295,296,297,298,299,300,301,302,303,304,305,306,307,308,309,310,311,312,313,314,315,316,317,318,319,320,321,322,323,324,325,326,327,328,329,330,331,332,333,334,335,336,337,338,339,340,341,342,343,344,345,346,347,348,349,350,351,352,353,354,355,356,357,358,359,360,361,362,363,364,365,366,367,368,369,370,371,372,373,374,375,376,377,378,379,380,381,382,383,384,385,386,387,388,389,390,391,392,393,394,395,396,397,398,399,400,401,402,403,404,405,406,407,408,409,410,411,412,413,414,415,416,417,418,419,420,421,422,423,424,425,426,427,428,429,430,431,432,433,434,435,436,437,438,439,440,441,442,443,444,445,446,447,448,449,450,451,452,453,454,455,456,457,458,459,460,461,462,463,464,465,466,467,468,469,470,471,472,473,474,475,476,477,478,479,480,481,482,483,484,485,486,487,488,489,490,491,492,493,494,495,496' WHERE `id` = 1;

UPDATE `oa_admin_group` SET `mobile_menu` = '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,5,17,18,19,20,21,22,23,24' WHERE `id` = 1;

-- ----------------------------
-- Table structure for oa_data_auth
-- ----------------------------
DROP TABLE IF EXISTS `oa_data_auth`;
CREATE TABLE `oa_data_auth`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '权限名称',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '权限标识唯一，字母',
  `desc` mediumtext  NULL COMMENT '备注描述',
  `module` varchar(255) NOT NULL DEFAULT '' COMMENT '所属模块，唯一，字母',
  `uids` mediumtext  NULL COMMENT '权限用户，1,2,3',
  `conf_1` mediumtext  NULL COMMENT '配置字段1，可作为预配置内容',
  `conf_2` mediumtext  NULL COMMENT '配置字段2，可作为预配置内容',
  `conf_3` mediumtext  NULL COMMENT '配置字段3，可作为预配置内容',
  `conf_4` mediumtext  NULL COMMENT '配置字段4，可作为预配置内容',
  `conf_5` mediumtext  NULL COMMENT '配置字段5，可作为预配置内容',
  `conf_6` mediumtext  NULL COMMENT '配置字段6，可作为预配置内容',
  `conf_7` mediumtext  NULL COMMENT '配置字段7，可作为预配置内容',
  `conf_8` mediumtext  NULL COMMENT '配置字段8，可作为预配置内容',
  `conf_9` mediumtext  NULL COMMENT '配置字段9，可作为预配置内容',
  `conf_10` mediumtext  NULL COMMENT '配置字段9，可作为预配置内容',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '数据权限表';

-- ----------------------------
-- Records of  oa_data_auth
-- ----------------------------
INSERT INTO `oa_data_auth` VALUES (1, '办公模块', 'office_admin', '办公模块相关数据权限配置。', 'office', '', '', '3', '', '', '', '', '', '', '', '', 1656143065, 1724830718);
INSERT INTO `oa_data_auth` VALUES (2, '人事模块', 'human_admin', '人事模块相关数据权限配置。', 'human', '', '', '', '', '', '', '', '', '', '', '', 1656143065, 1724830718);
INSERT INTO `oa_data_auth` VALUES (3, '客户模块', 'customer_admin', '客户模块相关数据权限配置。', 'customer', '', '', '10', '1000', '100', '', '', '', '', '', '', 1656143065, 1724830738);
INSERT INTO `oa_data_auth` VALUES (4, '合同模块', 'contract_admin', '合同模块相关数据权限配置。', 'contract', '', '', '1', '1', '1', '1', '1', '1', '', '', '30', 1656143065, 1724830772);
INSERT INTO `oa_data_auth` VALUES (5, '项目模块', 'project_admin', '项目模块相关数据权限配置。', 'project', '', '', '', '', '', '', '', '', '', '', '3', 1656143065, 0);
INSERT INTO `oa_data_auth` VALUES (6, '售后模块', 'service_admin', '售后模块相关数据权限配置。', 'service', '', '', '', '', '', '', '', '', '', '', '3', 1656143065, 0);
INSERT INTO `oa_data_auth` VALUES (7, '财务模块', 'finance_admin', '财务到账相关数据权限配置。', 'finance', '', '', '', '', '', '', '', '', '', '', '', 1656143065, 0);
INSERT INTO `oa_data_auth` VALUES (8, '网盘模块', 'disk_admin', '网盘模块相关数据权限配置。', 'disk', '', '', '', '', '', '', '', '', '', '', '', 1656143065, 0);

-- ----------------------------
-- Table structure for oa_template
-- ----------------------------
DROP TABLE IF EXISTS `oa_template`;
CREATE TABLE `oa_template`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '消息模板名称',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '权限标识唯一，字母',
  `types` tinyint(1) NOT NULL DEFAULT 1 COMMENT '类型:1普通消息,2审批消息',
  `check_types` int(11) NOT NULL DEFAULT 0 COMMENT '审批类型:0',
  `remark` varchar(500) NOT NULL DEFAULT '' COMMENT '备注描述，使用场景等',
  `msg_link` varchar(255) NOT NULL DEFAULT '' COMMENT '消息模板链接(审批申请)',
  `msg_title_0` varchar(255) NOT NULL DEFAULT '' COMMENT '消息模板标题(审批申请发审批人)',
  `msg_content_0` varchar(500) NOT NULL DEFAULT '' COMMENT '消息模板内容(审批申请发审批人)',
  `msg_title_1` varchar(255) NOT NULL DEFAULT '' COMMENT '消息模板标题(审批通过发申请人)',
  `msg_content_1` varchar(500) NOT NULL DEFAULT '' COMMENT '消息模板内容(审批通过发申请人)',
  `msg_title_2` varchar(255) NOT NULL DEFAULT '' COMMENT '消息模板标题(审批拒绝发申请人)',
  `msg_content_2` varchar(500) NOT NULL DEFAULT '' COMMENT '消息模板内容(审批拒绝发申请人)',
  `msg_title_3` varchar(255) NOT NULL DEFAULT '' COMMENT '消息模板标题(审批通过发抄送人)',
  `msg_content_3` varchar(500) NOT NULL DEFAULT '' COMMENT '消息模板内容(审批通过发抄送人)',
  `email_link` varchar(255) NOT NULL DEFAULT '' COMMENT '邮箱消息模板链接',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `admin_id` int(11) NOT NULL DEFAULT 0 COMMENT  '创建人',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '消息模板表';

-- ----------------------------
-- Records of oa_template
-- ----------------------------
INSERT INTO `oa_template` VALUES (1, '公告通知', 'note', 1, 0, '', '/oa/note/view/id/{action_id}', '{from_user}发了一个新『公告』，请及时查看', '您有一个新公告：{title}。', '', '', '', '', '', '', '', 1, 1, 1733312491, 1733314809, 0);
INSERT INTO `oa_template` VALUES (2, '工作汇报通知', 'work', 1, 0, '', '/oa/work/view/id/{action_id}', '{from_user}给您发了一份『工作汇报』，请及时查看', '您有一份新的工作汇报待查看。', '', '', '', '', '', '', '', 1, 1, 1760576534, 1760577087, 0);
INSERT INTO `oa_template` VALUES (3, '工资发放通知', 'salary', 1, 0, '', '/oa/salary/view/id/{action_id}', '您有一个『{title}』新通知，请及时查看', '您有一个『{title}』新通知，金额：{amount}。', '', '', '', '', '', '', '', 1, 1, 1784533901, 0, 0);
INSERT INTO `oa_template` VALUES (4, '会议记录通知', 'meeting_records', 1, 0, '', '/adm/meeting/records_view/id/{action_id}', '{from_user}给您发了一份『会议记录』，请及时查看', '您有一份新的会议记录待查看。\n会议日期：{meeting_date}\n会议主题：{title}', '', '', '', '', '', '', '', 1, 1, 1783437950, 1783438952, 0);
INSERT INTO `oa_template` VALUES (5, '售后问题指派通知', 'problems', 1, 0, '', '/service/problems/view/id/{action_id}', '您有一个售后问题指派通知，请及时查看', '有一个新的售后问题指派给你。\n问题标题：{title}\n创建时间：{create_time}', '', '', '', '', '', '', '', 1, 1, 1783496789, 1783517290, 0);
INSERT INTO `oa_template` VALUES (6, '项目通知', 'project', 1, 0, '', '/project/index/view/id/{action_id}', '您有一个{title}通知，请及时查看', '{text}\n项目名称：{name}\n项目负责人：{director_name}', '', '', '', '', '', '', '', 1, 1, 1783439468, 1783495568, 0);
INSERT INTO `oa_template` VALUES (7, '任务通知', 'task', 1, 0, '', '/project/task/view/id/{action_id}', '您有一个{title}通知，请及时查看', '{text}\n任务标题：{name}\n任务负责人：{director_name}', '', '', '', '', '', '', '', 1, 1, 1783482937, 1783495573, 0);
INSERT INTO `oa_template` VALUES (8, '借支打款通知', 'loan_payment', 1, 0, '', '/finance/loan/view/id/{action_id}', '您有一个『{title}』新通知，请及时查看', '您有一个『{title}』新通知，状态：{status}，金额：{amount}。', '', '', '', '', '', '', '', 1, 1, 1783050818, 1783168252, 0);
INSERT INTO `oa_template` VALUES (9, '报销打款通知', 'expense_payment', 1, 0, '', '/finance/expense/view/id/{action_id}', '您有一个『{title}』新通知，请及时查看', '您有一个『{title}』新通知，状态：{status}，金额：{amount}。', '', '', '', '', '', '', '', 1, 1, 1783048307, 1783051598, 0);
INSERT INTO `oa_template` VALUES (10, '业务付款通知', 'ticket_payment', 1, 0, '', '/finance/payment/view/id/{action_id}', '您有一个『{title}』新通知，请及时查看', '您有一个『{title}』新通知，状态：{status}，金额：{amount}。', '', '', '', '', '', '', '', 1, 1, 1783050857, 1783051619, 0);
INSERT INTO `oa_template` VALUES (11, '业务退款通知', 'refund_payment', 1, 0, '', '/finance/refund/view/id/{action_id}', '您有一个『{title}』新通知，请及时查看', '您有一个『{title}』新通知，状态：{status}，金额：{amount}。', '', '', '', '', '', '', '', 1, 1, 1783050957, 1783051610, 0);
INSERT INTO `oa_template` VALUES (12, '业务到账通知', 'invoice_income', 1, 0, '', '/finance/income/view/id/{action_id}', '您有一个『{title}』新通知，请及时查看', '您有一个『{title}』新通知，状态：{status}，金额：{amount}。', '', '', '', '', '', '', '', 1, 1, 1783053471, 0, 0);
INSERT INTO `oa_template` VALUES (13, '借支还款通知', 'loan_back', 1, 0, '', '/finance/loan/view/id/{action_id}', '您有一个『{title}』新通知，请及时查看', '您有一个『{title}』新通知，状态：还款确认，金额：{amount}。', '', '', '', '', '', '', '', 1, 1, 1783167711, 0, 0);

INSERT INTO `oa_template` VALUES (14, '入职审批', 'talent', 2, 0, '', '/user/talent/view/id/{action_id}', '{from_user}提交了一个『入职审批』，请及时审批', '您有一个新的『入职审批』需要处理。', '您提交的『入职审批』已被审批通过', '您在{create_time}提交的『入职审批』已于{date}被审批通过。', '您提交的『入职审批』已被驳回拒绝', '您在{create_time}提交的『入职审批』已于{date}被驳回拒绝。', '{from_user}提交的『入职审批』已被审批通过并抄送给你', '{from_user}在{create_time}提交的『入职审批』已被审批通过并抄送给你，请及时查看详情。', '', 1, 1, 1733314789, 1753284351, 0);
INSERT INTO `oa_template` VALUES (15, '离职审批', 'personal_quit', 2, 0, '', '/user/personal/leave_view/id/{action_id}', '{from_user}提交了一个『离职审批』，请及时审批', '您有一个新的『离职审批』需要处理。', '您提交的『离职审批』已被审批通过', '您在{create_time}提交的『离职审批』已于{date}被审批通过。', '您提交的『离职审批』已被驳回拒绝', '您在{create_time}提交的『离职审批』已于{date}被驳回拒绝。', '{from_user}提交的『离职审批』已被审批通过并抄送给你', '{from_user}在{create_time}提交的『离职审批』已被审批通过并抄送给你，请及时查看详情。', '', 1, 1, 1733314789, 1753284351, 0);
INSERT INTO `oa_template` VALUES (16, '人事调动审批', 'department_change', 2, 0, '', '/user/personal/change_view/id/{action_id}', '{from_user}提交了一个『人事调动审批』，请及时审批', '您有一个新的『人事调动审批』需要处理。', '您提交的『人事调动审批』已被审批通过', '您在{create_time}提交的『人事调动审批』已于{date}被审批通过。', '您提交的『人事调动审批』已被驳回拒绝', '您在{create_time}提交的『人事调动审批』已于{date}被驳回拒绝。', '{from_user}提交的『人事调动审批』已被审批通过并抄送给你', '{from_user}在{create_time}提交的『人事调动审批』已被审批通过并抄送给你，请及时查看详情。', '', 1, 1, 1733314789, 1753284351, 0);
INSERT INTO `oa_template` VALUES (17, '会议室预定审批', 'meeting_order', 2, 0, '', '/adm/meeting/view/id/{action_id}', '{from_user}提交了一个『会议室预定审批』，请及时审批', '您有一个新的『会议室预定审批』需要处理。', '您提交的『会议室预定审批』已被审批通过', '您在{create_time}提交的『会议室预定审批』已于{date}被审批通过。', '您提交的『会议室预定审批』已被驳回拒绝', '您在{create_time}提交的『会议室预定审批』已于{date}被驳回拒绝。', '{from_user}提交的『会议室预定审批』已被审批通过并抄送给你', '{from_user}在{create_time}提交的『会议室预定审批』已被审批通过并抄送给你，请及时查看详情。', '', 1, 1, 1758550842, 1758582833, 0);
INSERT INTO `oa_template` VALUES (18, '用章审批', 'seal', 2, 0, '', '/adm/seal/view/id/{action_id}', '{from_user}提交了一个『用章申请』，请及时审批', '您有一个新的『用章申请』需要处理。', '您提交的『用章申请』已被审批通过。', '您在{create_time}提交的『用章申请』已于{date}被审批通过。', '您提交的『用章申请』已被驳回拒绝。', '您在{create_time}提交的『用章申请』已于{date}被驳回拒绝。', '{from_user}提交的『用章审批』已被审批通过并抄送给你', '{from_user}在{create_time}提交的『用章审批』已被审批通过并抄送给你，请及时查看详情。', '', 1, 1, 1733313018, 1733314834, 0);
INSERT INTO `oa_template` VALUES (19, '用车审批', 'car_use', 2, 0, '', '/adm/car/apply_view/id/{action_id}', '{from_user}提交了一个『用车申请』，请及时审批', '您有一个新的『用车申请』需要处理。', '您提交的『公文申请』已被审批通过。', '您在{create_time}提交的『用车申请』已于{date}被审批通过。', '您提交的『用车申请』已被驳回拒绝。', '您在{create_time}提交的『用车申请』已于{date}被驳回拒绝。', '{from_user}提交的『用车审批』已被审批通过并抄送给你', '{from_user}在{create_time}提交的『用车审批』已被审批通过并抄送给你，请及时查看详情。', '', 1, 1, 1784218415, 1784533828, 0);
INSERT INTO `oa_template` VALUES (20, '请假审批', 'leaves', 2, 0, '', '/home/leaves/view/id/{action_id}', '{from_user}提交了一个『请假申请』，请及时审批', '您有一个新的『请假申请』需要处理。', '您提交的『请假申请』已被审批通过。', '您在{create_time}提交的『请假申请』已于{date}被审批通过。', '您提交的『请假申请』已被驳回拒绝。', '您在{create_time}提交的『请假申请』已于{date}被驳回拒绝。', '{from_user}提交的『请假审批』已被审批通过并抄送给你', '{from_user}在{create_time}提交的『请假审批』已被审批通过并抄送给你，请及时查看详情。', '', 1, 1, 1733312616, 1733314814, 0);
INSERT INTO `oa_template` VALUES (21, '出差审批', 'trips', 2, 0, '', '/home/trips/view/id/{action_id}', '{from_user}提交了一个『出差申请』，请及时审批', '您有一个新的『出差申请』需要处理。', '您提交的『出差申请』已被审批通过。', '您在{create_time}提交的『出差申请』已于{date}被审批通过。', '您提交的『出差申请』已被驳回拒绝。', '您在{create_time}提交的『出差申请』已于{date}被驳回拒绝。', '{from_user}提交的『出差审批』已被审批通过并抄送给你', '{from_user}在{create_time}提交的『出差审批』已被审批通过并抄送给你，请及时查看详情。', '', 1, 1, 1733312725, 1733314819, 0);
INSERT INTO `oa_template` VALUES (22, '外出审批', 'outs', 2, 0, '', '/home/outs/view/id/{action_id}', '{from_user}提交了一个『外出申请』，请及时审批', '您有一个新的『外出申请』需要处理。', '您提交的『外出申请』已被审批通过。', '您在{create_time}提交的『外出申请』已于{date}被审批通过。', '您提交的『外出申请』已被驳回拒绝。', '您在{create_time}提交的『外出申请』已于{date}被驳回拒绝。', '{from_user}提交的『外出审批』已被审批通过并抄送给你', '{from_user}在{create_time}提交的『外出审批』已被审批通过并抄送给你，请及时查看详情。', '', 1, 1, 1733312801, 1733314824, 0);
INSERT INTO `oa_template` VALUES (23, '加班审批', 'overtimes', 2, 0, '', '/home/overtimes/view/id/{action_id}', '{from_user}提交了一个『加班申请』，请及时审批', '您有一个新的『加班申请』需要处理。', '您提交的『加班申请』已被审批通过。', '您在{create_time}提交的『加班申请』已于{date}被审批通过。', '您提交的『加班申请』已被驳回拒绝。', '您在{create_time}提交的『加班申请』已于{date}被驳回拒绝。', '{from_user}提交的『加班审批』已被审批通过并抄送给你', '{from_user}在{create_time}提交的『加班审批』已被审批通过并抄送给你，请及时查看详情。', '', 1, 1, 1733312801, 1733314828, 0);
INSERT INTO `oa_template` VALUES (24, '销售合同审批', 'contract', 2, 0, '', '/contract/contract/view/id/{action_id}', '{from_user}提交了一个『销售合同审批』，请及时审批', '您有一个新的『销售合同审批』需要处理。', '您提交的『销售合同审批』已被审批通过', '您在{create_time}提交的『销售合同审批』已于{date}被审批通过。', '您提交的『销售合同审批』已被驳回拒绝', '您在{create_time}提交的『销售合同审批』已于{date}被驳回拒绝。', '{from_user}提交的『销售合同审批』已被审批通过并抄送给你', '{from_user}在{create_time}提交的『销售合同审批』已被审批通过并抄送给你，请及时查看详情。', '', 1, 1, 1733314701, 0, 0);
INSERT INTO `oa_template` VALUES (25, '采购合同审批', 'purchase', 2, 0, '', '/contract/purchase/view/id/{action_id}', '{from_user}提交了一个『采购合同审批』，请及时审批', '您有一个新的『采购合同审批』需要处理。', '您提交的『采购合同审批』已被审批通过', '您在{create_time}提交的『采购合同审批』已于{date}被审批通过。', '您提交的『采购合同审批』已被驳回拒绝', '您在{create_time}提交的『采购合同审批』已于{date}被驳回拒绝。', '{from_user}提交的『采购合同审批』已被审批通过并抄送给你', '{from_user}在{create_time}提交的『采购合同审批』已被审批通过并抄送给你，请及时查看详情。', '', 1, 1, 1733314789, 1753284351, 0);

INSERT INTO `oa_template` VALUES (26, '借支审批', 'loan', 2, 0, '', '/finance/loan/view/id/{action_id}', '{from_user}提交了一个『借支审批』，请及时审批', '您有一个新的『借支审批』需要处理。', '您提交的『借支审批』已被审批通过', '您在{create_time}提交的『借支审批』已于{date}被审批通过。', '您提交的『借支审批』已被驳回拒绝', '您在{create_time}提交的『借支审批』已于{date}被驳回拒绝。', '{from_user}提交的『借支审批』已被审批通过并抄送给你', '{from_user}在{create_time}提交的『借支审批』已被审批通过并抄送给你，请及时查看详情。', '', 1, 1, 1733314789, 1753284351, 0);
INSERT INTO `oa_template` VALUES (27, '报销审批', 'expense', 2, 0, '', '/finance/expense/view/id/{action_id}', '{from_user}提交了一个『报销申请』，请及时审批', '您有一个新的『报销申请』需要处理。', '您提交的『报销申请』已被审批通过', '您在{create_time}提交的『报销申请』已于{date}被审批通过。', '您提交的『报销申请』已被驳回拒绝', '您在{create_time}提交的『报销申请』已于{date}被驳回拒绝。', '{from_user}提交的『报销审批』已被审批通过并抄送给你', '{from_user}在{create_time}提交的『报销审批』已被审批通过并抄送给你，请及时查看详情。', '', 1, 1, 1733313169, 1733313253, 0);
INSERT INTO `oa_template` VALUES (28, '销项发票审批', 'invoice', 2, 0, '', '/finance/invoice/view/id/{action_id}', '{from_user}提交了一个『销项发票申请』，请及时审批', '您有一个新的『销项发票申请』需要处理。', '您提交的『发票申请』已被审批通过', '您在{create_time}提交的『销项发票申请』已于{date}被审批通过。', '您提交的『销项发票申请』已被驳回拒绝', '您在{create_time}提交的『销项发票申请』已于{date}被驳回拒绝。', '{from_user}提交的『销项发票审批』已被审批通过并抄送给你', '{from_user}在{create_time}提交的『销项发票审批』已被审批通过并抄送给你，请及时查看详情。', '', 1, 1, 1733313245, 0, 0);
INSERT INTO `oa_template` VALUES (29, '进项发票审批', 'ticket', 2, 0, '', '/finance/ticket/view/id/{action_id}', '{from_user}提交了一个『进项发票申请』，请及时审批', '您有一个新的『进项发票申请』需要处理。', '您提交的『进项发票申请』已被审批通过', '您在{create_time}提交的『进项发票申请』已于{date}被审批通过。', '您提交的『进项发票申请』已被驳回拒绝', '您在{create_time}提交的『进项发票申请』已于{date}被驳回拒绝。', '{from_user}提交的『进项发票审批』已被审批通过并抄送给你', '{from_user}在{create_time}提交的『进项发票审批』已被审批通过并抄送给你，请及时查看详情。', '', 1, 1, 1733313341, 0, 0);
INSERT INTO `oa_template` VALUES (30, '收款审批', 'income', 2, 0, '', '/finance/income/view/id/{action_id}', '{from_user}提交了一个『收款申请』，请及时审批', '您有一个新的『收款申请』需要处理。', '您提交的『收款申请』已被审批通过', '您在{create_time}提交的『收款申请』已于{date}被审批通过。', '您提交的『收款申请』已被驳回拒绝', '您在{create_time}提交的『无发票收款申请』已于{date}被驳回拒绝。', '{from_user}提交的『收款审批』已被审批通过并抄送给你', '{from_user}在{create_time}提交的『收款审批』已被审批通过并抄送给你，请及时查看详情。', '', 1, 1, 1733314549, 0, 0);
INSERT INTO `oa_template` VALUES (31, '付款审批', 'payment', 2, 0, '', '/finance/payment/view/id/{action_id}', '{from_user}提交了一个『付款申请』，请及时审批', '您有一个新的『付款申请』需要处理。', '您提交的『付款申请』已被审批通过', '您在{create_time}提交的『付款申请』已于{date}被审批通过。', '您提交的『付款申请』已被驳回拒绝', '您在{create_time}提交的『付款申请』已于{date}被驳回拒绝。', '{from_user}提交的『付款审批』已被审批通过并抄送给你', '{from_user}在{create_time}提交的『付款审批』已被审批通过并抄送给你，请及时查看详情。', '', 1, 1, 1733314607, 0, 0);
INSERT INTO `oa_template` VALUES (32, '退款审批', 'income_refund', 2, 0, '', '/finance/refund/view_a/id/{action_id}', '{from_user}提交了一个『退款申请』，请及时审批', '您有一个新的『退款申请』需要处理。', '您提交的『退款申请』已被审批通过', '您在{create_time}提交的『退款申请』已于{date}被审批通过。', '您提交的『退款申请』已被驳回拒绝', '您在{create_time}提交的『退款申请』已于{date}被驳回拒绝。', '{from_user}提交的『退款申请』已被审批通过并抄送给你', '{from_user}在{create_time}提交的『退款申请』已被审批通过并抄送给你，请及时查看详情。', '', 1, 1, 1733314607, 0, 0);

-- ----------------------------
-- Table structure for oa_flow_cate
-- ----------------------------
DROP TABLE IF EXISTS `oa_flow_cate`;
CREATE TABLE `oa_flow_cate`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '审批类型名称',
  `name` varchar(100) NOT NULL COMMENT '审批类型标识,唯一',
  `module_id` int(11) NOT NULL DEFAULT 0 COMMENT '关联审批模块id',
  `check_table` varchar(100) NOT NULL DEFAULT '' COMMENT '关联数据库表名',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '图标',
  `department_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '应用部门ID（空为全部）1,2,3',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序：越大越靠前',
  `is_copy` int(11) NOT NULL DEFAULT 1 COMMENT '是否支持抄送人',
  `is_file` int(11) NOT NULL DEFAULT 0 COMMENT '审批过程是否支持上传附件',
  `is_export` int(11) NOT NULL DEFAULT 0 COMMENT '审批通过后是否支持导出PDF打印',
  `is_back` int(11) NOT NULL DEFAULT 1 COMMENT '是否支持撤回',
  `is_reversed` int(11) NOT NULL DEFAULT 0 COMMENT '是否支持反确认',
  `form` tinyint(1) NOT NULL DEFAULT 1 COMMENT '预设字段，表单模式：1固定表单,2自定义表单',
  `add_url` varchar(255) NOT NULL DEFAULT '' COMMENT '新建链接：固定表单模式必填',
  `view_url` varchar(255) NOT NULL DEFAULT '' COMMENT '查看链接：固定表单模式必填',
  `form_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '表单id：自定义表单模式必填',
  `is_list` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否列表页显示：0不显示 1显示',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `template_id` int(11) NOT NULL DEFAULT 0 COMMENT '审批消息模板id',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '审批类型';

-- ----------------------------
-- Records of oa_flow_cate
-- ----------------------------
INSERT INTO `oa_flow_cate` VALUES (1, '请假', 'leaves', 1, 'leaves', 'icon-kechengziyuanguanli', '', 0, 1, 0, 0, 1, 0, 1, '/home/leaves/add', '/home/leaves/view', 0, 1, 1, 20, 1723604674, 1784818567);
INSERT INTO `oa_flow_cate` VALUES (2, '出差', 'trips', 1, 'trips', 'icon-jiaoshiguanli', '', 0, 1, 0, 0, 1, 0, 1, '/home/trips/add', '/home/trips/view', 0, 1, 1, 21, 1723799422, 1784818582);
INSERT INTO `oa_flow_cate` VALUES (3, '外出', 'outs', 1, 'outs', 'icon-tuiguangguanli', '', 0, 1, 0, 0, 1, 0, 1, '/home/outs/add', '/home/outs/view', 0, 1, 1, 22, 1723800336, 1784818609);
INSERT INTO `oa_flow_cate` VALUES (4, '加班', 'overtimes', 1, 'overtimes', 'icon-xueshengchengji', '', 0, 1, 0, 0, 1, 0, 1, '/home/overtimes/add', '/home/overtimes/view', 0, 1, 1, 23, 1723800393, 1784818694);
INSERT INTO `oa_flow_cate` VALUES (5, '用章', 'seal', 2, 'seal', 'icon-shenpishezhi', '', 0, 1, 0, 0, 1, 0, 1, '/adm/seal/add', '/adm/seal/view', 0, 1, 1, 18, 1723469451, 1784818908);
INSERT INTO `oa_flow_cate` VALUES (6, '借支', 'loan', 4, 'loan', 'icon-zhangbuguanli', '', 0, 1, 0, 1, 1, 0, 1, '/finance/loan/add', '/finance/loan/view', 0, 1, 1, 26, 1723470017, 1784818933);
INSERT INTO `oa_flow_cate` VALUES (7, '报销', 'expense', 4, 'expense', 'icon-jizhang', '', 0, 1, 0, 0, 1, 0, 1, '/finance/expense/add', '/finance/expense/view', 0, 1, 1, 27, 1723469732, 1784819066);
INSERT INTO `oa_flow_cate` VALUES (8, '销项发票', 'invoice', 4, 'invoice', 'icon-duizhangdan', '', 0, 1, 0, 0, 1, 0, 1, '/finance/invoice/add', '/finance/invoice/view', 0, 1, 1, 28, 1723469814, 1784819054);
INSERT INTO `oa_flow_cate` VALUES (9, '进项发票', 'ticket', 4, 'ticket', 'icon-yingjiaoqingdan', '', 0, 1, 0, 0, 1, 0, 1, '/finance/ticket/add', '/finance/ticket/view', 0, 1, 1, 29, 1724749856, 1784819042);
INSERT INTO `oa_flow_cate` VALUES (10, '收款', 'income', 4, 'invoice_income', 'icon-shoufeipeizhi', '', 0, 1, 0, 0, 1, 0, 1, '/finance/income/add', '/finance/income/view', 0, 1, 1, 30, 1725856435, 1784819032);
INSERT INTO `oa_flow_cate` VALUES (11, '付款', 'payment', 4, 'ticket_payment', 'icon-bulujiesuan', '', 0, 1, 0, 0, 1, 0, 1, '/finance/payment/add', '/finance/payment/view', 0, 1, 1, 31, 1725856613, 1784819020);
INSERT INTO `oa_flow_cate` VALUES (12, '退款', 'income_refund', 4, 'income_refund', 'icon-shoufeipeizhi', '', 0, 1, 0, 0, 1, 0, 1, '/finance/refund/add', '/finance/refund/view', 0, 1, 1, 32, 1725856435, 1784819013);
INSERT INTO `oa_flow_cate` VALUES (13, '销售合同', 'contract', 3, 'contract', 'icon-hetongguanli', '', 0, 0, 0, 0, 1, 0, 1, '/contract/contract/add', '/contract/contract/view', 0, 1, 1, 24, 1723469917, 1784819003);
INSERT INTO `oa_flow_cate` VALUES (14, '采购合同', 'purchase', 3, 'purchase', 'icon-dianshang', '', 0, 0, 0, 0, 1, 0, 1, '/contract/purchase/add', '/contract/purchase/view', 0, 1, 1, 25, 1723470017, 1784818995);
INSERT INTO `oa_flow_cate` VALUES (15, '入职', 'talent', 5, 'talent', 'icon-yuangongdaoru', '', 0, 1, 0, 1, 1, 0, 1, '/user/talent/add', '/user/talent/view', 0, 1, 1, 14, 1729490152, 1784818979);
INSERT INTO `oa_flow_cate` VALUES (16, '离职', 'personal_quit', 5, 'personal_quit', 'icon-yuangongtongji2', '', 0, 1, 0, 1, 1, 0, 1, '/user/personal/leave_add', '/user/personal/leave_view', 0, 1, 1, 15, 1729490152, 1784818971);
INSERT INTO `oa_flow_cate` VALUES (17, '人事调动', 'department_change', 5, 'department_change', 'icon-yuangongbiandong', '', 0, 1, 0, 1, 1, 0, 1, '/user/personal/change_add', '/user/personal/change_view', 0, 1, 1, 16, 1729490152, 1784818963);
INSERT INTO `oa_flow_cate` VALUES (18, '会议室预定', 'meeting_order', 2, 'meeting_order', 'icon-xuetangguanli', '', 0, 1, 0, 1, 1, 0, 1, '/adm/meeting/add', '/adm/meeting/view', 0, 1, 1, 17, 1758544152, 1784818948);
INSERT INTO `oa_flow_cate` VALUES (19, '用车审批', 'car_use', 2, 'car_use', 'icon-daqiajilu', '', 0, 1, 0, 1, 1, 0, 1, '/adm/car/apply_add', '/adm/car/apply_view', 0, 1, 1, 19, 1784819434, 1784819560);

-- ----------------------------
-- Table structure for oa_flow
-- ----------------------------
DROP TABLE IF EXISTS `oa_flow`;
CREATE TABLE `oa_flow`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '审批流程名称',
  `cate_id` tinyint(11) NOT NULL DEFAULT 0 COMMENT '关联审批类型id',
  `check_type` tinyint(4) NOT NULL COMMENT '1自由审批流,2固定审批流,3固定可回退的审批流,4固定条件审批流',
  `department_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '应用部门ID（0为全部）1,2,3',
  `copy_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '抄送人ID',
  `flow_list` varchar(1000) NULL DEFAULT '' COMMENT '流程数据序列化',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态 1启用，0禁用',
  `remark` varchar(500) NOT NULL DEFAULT '' COMMENT '流程说明',
  `admin_id` int(11) NOT NULL COMMENT '创建人ID',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '审批流程表';

-- ----------------------------
-- Records of oa_flow
-- ----------------------------
INSERT INTO `oa_flow` VALUES (1, '请假审批', 1, 1, '', '', '', 1, '', 1, 1723791655, 0, 0);
INSERT INTO `oa_flow` VALUES (2, '出差审批', 2, 1, '', '', '', 1, '', 1, 1723799665, 0, 0);
INSERT INTO `oa_flow` VALUES (3, '外出审批', 3, 1, '', '', '', 1, '', 1, 1723800434, 0, 0);
INSERT INTO `oa_flow` VALUES (4, '加班审批', 4, 1, '', '', '', 1, '', 1, 1723800446, 0, 0);
INSERT INTO `oa_flow` VALUES (5, '用章审批', 5, 1, '', '', '', 1, '', 1, 1723470400, 0, 0);
INSERT INTO `oa_flow` VALUES (6, '用车审批', 19, 1, '', '', '', 1, '', 1, 1723470419, 1784819697, 0);
INSERT INTO `oa_flow` VALUES (7, '会议室预定审批', 18, 1, '', '', '', 1, '', 1, 1758550919, 0, 0);
INSERT INTO `oa_flow` VALUES (8, '入职审批', 15, 1, '', '', '', 1, '', 1, 1723470501, 0, 0);
INSERT INTO `oa_flow` VALUES (9, '离职审批', 16, 1, '', '', '', 1, '', 1, 1723470501, 0, 0);
INSERT INTO `oa_flow` VALUES (10, '人事调动审批', 17, 1, '', '', '', 1, '', 1, 1723470501, 1784819850, 0);
INSERT INTO `oa_flow` VALUES (11, '销售合同审批', 13, 1, '', '', '', 1, '', 1, 1723470490, 1784819779, 0);
INSERT INTO `oa_flow` VALUES (12, '采购合同审批', 14, 1, '', '', '', 1, '', 1, 1723470501, 1784819791, 0);
INSERT INTO `oa_flow` VALUES (13, '借支审批', 6, 1, '', '', '', 1, '', 1, 1723470501, 1784819806, 0);
INSERT INTO `oa_flow` VALUES (14, '报销审批', 7, 1, '', '', '', 1, '', 1, 1723470468, 0, 0);
INSERT INTO `oa_flow` VALUES (15, '销项发票审批', 8, 1, '', '', '', 1, '', 1, 1723470482, 1784819727, 0);
INSERT INTO `oa_flow` VALUES (16, '进项发票审批', 9, 1, '', '', '', 1, '', 1, 1723470482, 1784819741, 0);
INSERT INTO `oa_flow` VALUES (17, '收款审批', 10, 1, '', '', '', 1, '', 1, 1725935073, 0, 0);
INSERT INTO `oa_flow` VALUES (18, '付款审批', 11, 1, '', '', '', 1, '', 1, 1725935159, 0, 0);
INSERT INTO `oa_flow` VALUES (19, '退款审批', 12, 1, '', '', '', 1, '', 1, 1723470482, 1784819871, 0);

-- ----------------------------
-- Table structure for oa_basic_adm
-- ----------------------------
DROP TABLE IF EXISTS `oa_basic_adm`;
CREATE TABLE `oa_basic_adm`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `types` varchar(100) NOT NULL DEFAULT '' COMMENT '数据类型:1车辆费用类型,2',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '名称',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '行政模块常规数据';

-- ----------------------------
-- Records of oa_basic_adm
-- ----------------------------
INSERT INTO `oa_basic_adm` VALUES (1, '1', '燃油费', 1, 1706840194, 0);
INSERT INTO `oa_basic_adm` VALUES (2, '1', '停车费', 1, 1706840194, 0);
INSERT INTO `oa_basic_adm` VALUES (3, '1', '洗车费', 1, 1706840194, 0);
INSERT INTO `oa_basic_adm` VALUES (4, '1', '保养费', 1, 1706840194, 0);
INSERT INTO `oa_basic_adm` VALUES (5, '1', '维修费', 1, 1706840194, 0);
INSERT INTO `oa_basic_adm` VALUES (6, '1', '过路费', 1, 1706840194, 0);
INSERT INTO `oa_basic_adm` VALUES (7, '1', '过桥费', 1, 1706840194, 0);
INSERT INTO `oa_basic_adm` VALUES (8, '1', '养路费', 1, 1706840194, 0);
INSERT INTO `oa_basic_adm` VALUES (9, '1', '保险费', 1, 1706840194, 0);
INSERT INTO `oa_basic_adm` VALUES (10, '1', '年检费', 1, 1706840194, 0);
INSERT INTO `oa_basic_adm` VALUES (11, '1', '违章费', 1, 1706840194, 0);
INSERT INTO `oa_basic_adm` VALUES (12, '1', '其他费', 1, 1706840194, 0);
INSERT INTO `oa_basic_adm` VALUES (13, '2', '工作计划', 1, 1758584244, 0);
INSERT INTO `oa_basic_adm` VALUES (14, '2', '项目计划', 1, 1758584244, 0);
INSERT INTO `oa_basic_adm` VALUES (15, '2', '销售计划', 1, 1758584244, 0);
INSERT INTO `oa_basic_adm` VALUES (16, '2', '采购计划', 1, 1758584244, 0);
INSERT INTO `oa_basic_adm` VALUES (17, '2', '经营计划', 1, 1758584244, 0);
INSERT INTO `oa_basic_adm` VALUES (18, '2', '生产计划', 1, 1758584244, 0);
INSERT INTO `oa_basic_adm` VALUES (19, '3', '投影背景', 1, 1758584263, 0);
INSERT INTO `oa_basic_adm` VALUES (20, '3', '电脑', 1, 1758584272, 0);
INSERT INTO `oa_basic_adm` VALUES (21, '3', '视频', 1, 1758584287, 1758584291);
INSERT INTO `oa_basic_adm` VALUES (22, '3', '购买水果', 1, 1758584305, 0);
INSERT INTO `oa_basic_adm` VALUES (23, '3', '订餐', 1, 1758584312, 0);

-- ----------------------------
-- Table structure for oa_mobile_menu
-- ----------------------------
DROP TABLE IF EXISTS `oa_mobile_menu`;
CREATE TABLE `oa_mobile_menu`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '名称',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '图标',
  `bgcolor` varchar(255) NOT NULL DEFAULT '' COMMENT '背景颜色',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '链接',
  `types` int(11) NOT NULL DEFAULT 0 COMMENT '关联菜单类型',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序：越大越靠前',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '手机端菜单表';

-- ----------------------------
-- Records of oa_mobile_menu
-- ----------------------------
INSERT INTO `oa_mobile_menu` VALUES (1, '审批申请', 'icon-shenpi','blue', '/qiye/approve/apply', 1, 0, 1, 1733146294, 1733152760, 0);
INSERT INTO `oa_mobile_menu` VALUES (2, '我申请的', 'icon-wodeshenpi1','blue', '/qiye/approve/mylist', 1, 0, 1, 1733152749, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (3, '我处理的', 'icon-chulishenpi','blue', '/qiye/approve/checklist', 1, 0, 1, 1733152798, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (4, '抄送给我', 'icon-chaosong','blue', '/qiye/approve/copylist', 1, 0, 1, 1733152823, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (5, '工作计划', 'icon-xiangmurenwu','green', '/qiye/index/workplan', 2, 0, 1, 1733152855, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (6, '日程安排', 'icon-richeng','green', '/qiye/index/calendar', 2, 0, 1, 1733152855, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (7, '工作日志', 'icon-jilu','green', '/qiye/index/schedule', 2, 0, 1, 1733152878, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (8, '工作汇报', 'icon-huibao','green', '/qiye/index/work', 2, 0, 1, 1733152906, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (9, '公告通知', 'icon-gonggaotongzhi','yellow', '/qiye/index/note', 3, 0, 1, 1733152965, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (10, '公司新闻', 'icon-gongsixinwen','yellow', '/qiye/index/news', 3, 0, 1, 1733152993, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (11, '规章制度', 'icon-guidanghetong','yellow', '/qiye/index/regulation', 3, 0, 1, 1733152993, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (12, '会议记录', 'icon-huiyijiyao','yellow', '/qiye/index/meeting', 3, 0, 1, 1733152993, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (13, '借支管理', 'icon-a-baoxiao2','purple', '/qiye/finance/loan', 4, 0, 1, 1733153131, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (14, '报销管理', 'icon-a-baoxiao3','purple', '/qiye/finance/expense', 4, 0, 1, 1733153019, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (15, '销项发票', 'icon-kaipiao','purple', '/qiye/finance/invoice', 4, 0, 1, 1733153047, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (16, '进项管理', 'icon-shoupiao','purple', '/qiye/finance/ticket', 4, 0, 1, 1733153077, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (17, '收款管理', 'icon-huikuan','purple', '/qiye/finance/income', 4, 0, 1, 1733153106, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (18, '付款管理', 'icon-fukuan','purple', '/qiye/finance/payment', 4, 0, 1, 1733153131, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (19, '退款管理', 'icon-shoukuanzuofei','purple', '/qiye/finance/refund', 4, 0, 1, 1733153131, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (20, '企业人员', 'icon-kehu', 'cyan', '/qiye/index/admin', 5, 0, 1, 1754539422, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (21, '入职管理', 'icon-wodeshenpi1', 'cyan', '/qiye/approve/talentlist', 5, 0, 1, 1754539501, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (22, '离职管理', 'icon-zuofeibaoming', 'cyan', '/qiye/approve/leavelist', 5, 0, 1, 1754539544, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (23, '人事调动', 'icon-qiangke', 'cyan', '/qiye/approve/changelist', 5, 0, 1, 1754539578, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (24, '我的薪资', 'icon-fukuanshenqing', 'cyan', '/qiye/index/salary', 5, 0, 1, 1754539578, 0, 0);


UPDATE `oa_loan` SET `enterprise_id` = 1;
UPDATE `oa_expense` SET `enterprise_id` = 1;