INSERT INTO `oa_template` VALUES (22, '报销打款通知', 'expense_payment', 1, 0, '', '/finance/expense/view/id/{action_id}', '您有一个『{title}』新通知，请及时查看', '您有一个『{title}』新通知，状态：{status}，金额：{amount}。', '', '', '', '', '', '', '', 1, 1, 1783048307, 1783051598, 0);
INSERT INTO `oa_template` VALUES (23, '借支打款通知', 'loan_payment', 1, 0, '', '/finance/loan/view/id/{action_id}', '您有一个『{title}』新通知，请及时查看', '您有一个『{title}』新通知，状态：{status}，金额：{amount}。', '', '', '', '', '', '', '', 1, 1, 1783050818, 1783168252, 0);
INSERT INTO `oa_template` VALUES (24, '业务付款通知', 'ticket_payment', 1, 0, '', '/finance/payment/view/id/{action_id}', '您有一个『{title}』新通知，请及时查看', '您有一个『{title}』新通知，状态：{status}，金额：{amount}。', '', '', '', '', '', '', '', 1, 1, 1783050857, 1783051619, 0);
INSERT INTO `oa_template` VALUES (25, '业务退款通知', 'refund_payment', 1, 0, '', '/finance/refund/view/id/{action_id}', '您有一个『{title}』新通知，请及时查看', '您有一个『{title}』新通知，状态：{status}，金额：{amount}。', '', '', '', '', '', '', '', 1, 1, 1783050957, 1783051610, 0);
INSERT INTO `oa_template` VALUES (26, '业务到账通知', 'invoice_income', 1, 0, '', '/finance/income/view/id/{action_id}', '您有一个『{title}』新通知，请及时查看', '您有一个『{title}』新通知，状态：{status}，金额：{amount}。', '', '', '', '', '', '', '', 1, 1, 1783053471, 0, 0);
INSERT INTO `oa_template` VALUES (27, '借支还款通知', 'loan_back', 1, 0, '', '/finance/loan/view/id/{action_id}', '您有一个『{title}』新通知，请及时查看', '您有一个『{title}』新通知，状态：还款确认，金额：{amount}。', '', '', '', '', '', '', '', 1, 1, 1783167711, 0, 0);
===============================================================================

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

ALTER TABLE `oa_position` 
ADD COLUMN `layouts` mediumtext  NULL COMMENT '首页展示模块' AFTER `title`;

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

ALTER TABLE `oa_customer` 
ADD COLUMN `is_clue` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否是线索：0否,1是' AFTER `is_lock`;

ALTER TABLE `oa_customer` 
ADD COLUMN  `clue_name` varchar(255) NOT NULL DEFAULT '' COMMENT '线索联系人' AFTER `is_clue`;

ALTER TABLE `oa_customer` 
ADD COLUMN  `clue_mobile` char(20) NOT NULL DEFAULT '' COMMENT '线索手机号码' AFTER `clue_name`;

ALTER TABLE `oa_customer_trace` 
ADD COLUMN `is_clue` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否是线索：0否,1是' AFTER `cid`;

ALTER TABLE `oa_invoice_income` 
ADD COLUMN `customer_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联客户ID' AFTER `invoice_id`;

ALTER TABLE `oa_invoice_income` 
ADD COLUMN `contract_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联销售合同ID' AFTER `customer_id`;

ALTER TABLE `oa_invoice_income` 
ADD COLUMN `project_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联项目ID' AFTER `contract_id`;

ALTER TABLE `oa_invoice_income` 
ADD COLUMN `subject_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '收款主体ID' AFTER `project_id`;

ALTER TABLE `oa_invoice_income` 
ADD COLUMN `account_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '收款账户ID' AFTER `subject_id`;

ALTER TABLE `oa_invoice_income` 
ADD COLUMN `fundscate_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '款项类型' AFTER `account_id`;

ALTER TABLE `oa_invoice_income` 
ADD COLUMN `paytype_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '支付方式' AFTER `fundscate_id`;

ALTER TABLE `oa_invoice_income` 
ADD COLUMN `file_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '附件ID，如:1,2,3' AFTER `paytype_id`;

ALTER TABLE `oa_invoice_income` 
ADD COLUMN `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间' AFTER `update_time`;

ALTER TABLE `oa_invoice_income` 
ADD COLUMN `check_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '审核状态:0待审核,1审核中,2审核通过,3审核不通过,4撤销审核' AFTER `delete_time`;

ALTER TABLE `oa_invoice_income` 
ADD COLUMN `check_flow_id` int(11) NOT NULL DEFAULT 0 COMMENT '审核流程id' AFTER `check_status`;

ALTER TABLE `oa_invoice_income` 
ADD COLUMN `check_step_sort` int(11) NOT NULL DEFAULT 0 COMMENT '当前审批步骤' AFTER `check_flow_id`;

ALTER TABLE `oa_invoice_income` 
ADD COLUMN `check_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '当前审批人ID，如:1,2,3' AFTER `check_step_sort`;

ALTER TABLE `oa_invoice_income` 
ADD COLUMN `check_last_uid` varchar(500) NOT NULL DEFAULT '' COMMENT '上一审批人' AFTER `check_uids`;

ALTER TABLE `oa_invoice_income` 
ADD COLUMN `check_history_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '历史审批人ID，如:1,2,3' AFTER `check_last_uid`;

ALTER TABLE `oa_invoice_income` 
ADD COLUMN `check_copy_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '抄送人ID，如:1,2,3' AFTER `check_history_uids`;

ALTER TABLE `oa_invoice_income` 
ADD COLUMN `check_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核通过时间' AFTER `check_copy_uids`;

ALTER TABLE `oa_ticket_payment` 
ADD COLUMN `supplier_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联供应商ID' AFTER `ticket_id`;

ALTER TABLE `oa_ticket_payment` 
ADD COLUMN `purchase_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联采购合同ID' AFTER `supplier_id`;

ALTER TABLE `oa_ticket_payment` 
ADD COLUMN `project_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联项目ID' AFTER `purchase_id`;

ALTER TABLE `oa_ticket_payment` 
ADD COLUMN `subject_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '付款主体ID' AFTER `project_id`;

ALTER TABLE `oa_ticket_payment` 
ADD COLUMN `account_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '付款账户ID' AFTER `subject_id`;

ALTER TABLE `oa_ticket_payment` 
ADD COLUMN `paytype_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '支付方式' AFTER `account_id`;

ALTER TABLE `oa_ticket_payment` 
ADD COLUMN `file_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '附件ID，如:1,2,3' AFTER `paytype_id`;

ALTER TABLE `oa_ticket_payment` 
ADD COLUMN `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间' AFTER `update_time`;

ALTER TABLE `oa_ticket_payment` 
ADD COLUMN `check_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '审核状态:0待审核,1审核中,2审核通过,3审核不通过,4撤销审核' AFTER `delete_time`;

ALTER TABLE `oa_ticket_payment` 
ADD COLUMN `check_flow_id` int(11) NOT NULL DEFAULT 0 COMMENT '审核流程id' AFTER `check_status`;

ALTER TABLE `oa_ticket_payment` 
ADD COLUMN `check_step_sort` int(11) NOT NULL DEFAULT 0 COMMENT '当前审批步骤' AFTER `check_flow_id`;

ALTER TABLE `oa_ticket_payment` 
ADD COLUMN `check_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '当前审批人ID，如:1,2,3' AFTER `check_step_sort`;

ALTER TABLE `oa_ticket_payment` 
ADD COLUMN `check_last_uid` varchar(500) NOT NULL DEFAULT '' COMMENT '上一审批人' AFTER `check_uids`;

ALTER TABLE `oa_ticket_payment` 
ADD COLUMN `check_history_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '历史审批人ID，如:1,2,3' AFTER `check_last_uid`;

ALTER TABLE `oa_ticket_payment` 
ADD COLUMN `check_copy_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '抄送人ID，如:1,2,3' AFTER `check_history_uids`;

ALTER TABLE `oa_ticket_payment` 
ADD COLUMN `check_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核通过时间' AFTER `check_copy_uids`;


ALTER TABLE `oa_expense` 
CHANGE COLUMN `pay_status` `status` tinyint NOT NULL DEFAULT 1 COMMENT '打款状态 1待打款,2已打款' AFTER `file_ids`,
CHANGE COLUMN `pay_admin_id` `confirm_uid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '打款人ID' AFTER `status`,
CHANGE COLUMN `pay_time` `confirm_time` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '打款确认时间' AFTER `confirm_uid`,
CHANGE COLUMN `subject_id` `enterprise_id` int NOT NULL DEFAULT 0 COMMENT '报销企业主体' AFTER `id`;

ALTER TABLE `oa_expense` 
ADD COLUMN `account_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '资金账户ID' AFTER `enterprise_id`;

ALTER TABLE `oa_expense` 
ADD COLUMN `paytype_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '支付方式' AFTER `account_id`;
 
ALTER TABLE `oa_loan` 
CHANGE COLUMN `pay_status` `status` tinyint NOT NULL DEFAULT 1 COMMENT '打款状态 1待打款,2已打款' AFTER `file_ids`,
CHANGE COLUMN `pay_admin_id` `confirm_uid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '打款人ID' AFTER `status`,
CHANGE COLUMN `pay_time` `confirm_time` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '打款确认时间' AFTER `confirm_uid`,
CHANGE COLUMN `subject_id` `enterprise_id` int NOT NULL DEFAULT 0 COMMENT '报销企业主体' AFTER `id`;

ALTER TABLE `oa_loan` 
ADD COLUMN `account_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '资金账户ID' AFTER `enterprise_id`;

ALTER TABLE `oa_loan` 
ADD COLUMN `paytype_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '支付方式' AFTER `account_id`;
-- ----------------------------
-- Table structure for oa_problems_cate
-- ----------------------------
DROP TABLE IF EXISTS `oa_problems_cate`;
CREATE TABLE `oa_problems_cate`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '类别名称',
  `pid` int(10) NOT NULL DEFAULT 0 COMMENT '父ID',
  `sort` int(10) NOT NULL DEFAULT 0 COMMENT '排序',
  `content` mediumtext  NULL COMMENT '描述',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '问题类别';


-- ----------------------------
-- Table structure for oa_problems
-- ----------------------------
DROP TABLE IF EXISTS `oa_problems`;
CREATE TABLE `oa_problems`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '问题主题',
  `cate_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属分类ID',
  `project_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联项目ID',
  `contract_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联合同ID',
  `customer_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联客户ID',
  `problem_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '问题日期',
  `finish_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '完成日期',
  `over_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关闭日期',
  `admin_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人员',
  `director_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '负责人员',
  `emergent` tinyint(1) NOT NULL DEFAULT 1 COMMENT '紧急度：1低,2中,3高',
  `priority` tinyint(1) NOT NULL DEFAULT 1 COMMENT '优先级：1低,2中,3高',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：1进行中,2已分配,3已解决,4已关闭',
  `file_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '附件ids',
  `content` mediumtext  NULL COMMENT '问题描述',
  `create_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
  `delete_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '问题表';

-- ----------------------------
-- Table structure for oa_problems_work
-- ----------------------------
DROP TABLE IF EXISTS `oa_problems_work`;
CREATE TABLE `oa_problems_work`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '服务记录主题',
  `problems_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联问题',
  `task_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联任务',
  `director_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '负责人员',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建员工ID',
  `did` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属部门',
  `start_time` int(11) NOT NULL DEFAULT 0 COMMENT '开始时间',
  `end_time` int(11) NOT NULL DEFAULT 0 COMMENT '结束时间',
  `hours` decimal(15, 1) NOT NULL DEFAULT 0.0 COMMENT '工时',
  `work_price` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '所有者的每小时费用',
  `work_total` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '工时总费用',
  `fee` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '其他费用',
  `amount` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '总费用',
  `content` mediumtext NOT NULL COMMENT '描述',
  `delete_time` int(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '问题服务记录';

-- ----------------------------
-- Table structure for oa_solutions_cate
-- ----------------------------
DROP TABLE IF EXISTS `oa_solutions_cate`;
CREATE TABLE `oa_solutions_cate`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '方案类别名称',
  `pid` int(10) NOT NULL DEFAULT 0 COMMENT '父ID',
  `sort` int(10) NOT NULL DEFAULT 0 COMMENT '排序',
  `content` mediumtext  NULL COMMENT '描述',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '方案类别表';

-- ----------------------------
-- Table structure for oa_solutions
-- ----------------------------
DROP TABLE IF EXISTS `oa_solutions`;
CREATE TABLE `oa_solutions`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '方案标题',
  `types` int(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '类型:1解决方案,2变通方法',
  `cate_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属分类ID',
  `problems_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联问题',
  `director_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所有者	',
  `hours` decimal(15, 1) NOT NULL DEFAULT 0.0 COMMENT '所需工时',
  `cost` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '所需费用',
  `content` mediumtext  NULL COMMENT '方案内容',
  `file_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '附件ids',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：1待批准,2已批准,3未批准',
  `status_remark` mediumtext  NULL COMMENT '批准备注',
  `check_id` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '批准人',
  `check_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '批准时间',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `update_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后编辑人',
  `create_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
  `delete_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '解决方案表';

-- ----------------------------
-- Table structure for oa_account
-- ----------------------------
DROP TABLE IF EXISTS `oa_account`;
CREATE TABLE `oa_account`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `enterprise_id` int(11) NOT NULL DEFAULT 0 COMMENT '企业主体id',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '资金账户名称',
  `account` varchar(100) NOT NULL DEFAULT '' COMMENT '具体账号',
  `initial_amount` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '初始资金',
  `amount` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '当前资金',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '资金账户';

-- ----------------------------
-- Records of oa_account
-- ----------------------------
INSERT INTO `oa_account` VALUES (1, 1, '现金账户', '',0.00,0.00, 1, 1764388690, 0, 0);
INSERT INTO `oa_account` VALUES (2, 1, '支付宝账户', '',0.00,0.00, 1, 1764388722, 1764388758, 0);
INSERT INTO `oa_account` VALUES (3, 1, '微信支付账户', '',0.00,0.00, 1, 1764388736, 1764388764, 0);
INSERT INTO `oa_account` VALUES (4, 1, '招商银行账户', '',0.00,0.00, 1, 1764388747, 0, 0);
INSERT INTO `oa_account` VALUES (5, 1, '建设银行账户', '', 0.00,0.00,1, 1764388777, 0, 0);

-- ----------------------------
-- Table structure for oa_funds_cate
-- ----------------------------
DROP TABLE IF EXISTS `oa_funds_cate`;
CREATE TABLE `oa_funds_cate`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '资金类型名称',
  `pid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '分类id',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序：越大越靠前',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `admin_id` int(11) NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '资金类型';

-- ----------------------------
-- Records of oa_funds_cate
-- ----------------------------
INSERT INTO `oa_funds_cate` VALUES (1, '意向金', 0, 0, 1, 0, 1779630684, 1779630778, 0);
INSERT INTO `oa_funds_cate` VALUES (2, '定金', 0, 0, 1, 0, 1779630690, 1779630782, 0);
INSERT INTO `oa_funds_cate` VALUES (3, '尾款', 0, 0, 1, 0, 1779630699, 1779630820, 0);
INSERT INTO `oa_funds_cate` VALUES (4, '全款', 0, 0, 1, 0, 1779630823, 0, 0);

-- ----------------------------
-- Table structure for oa_pay_type
-- ----------------------------
DROP TABLE IF EXISTS `oa_pay_type`;
CREATE TABLE `oa_pay_type`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '付款方式名称',
  `pid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '分类id',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序：越大越靠前',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `admin_id` int(11) NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '付款方式';

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
-- Table structure for oa_income_refund
-- ----------------------------
DROP TABLE IF EXISTS `oa_income_refund`;
CREATE TABLE `oa_income_refund`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `amount` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '金额',
  `income_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '收款ID',
  `enterprise_id` int(11) NOT NULL DEFAULT 0 COMMENT '企业主体id',
  `account_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '资金账户ID',
  `fundscate_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '款项类型',
  `payment_types` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '支付方式',
  `back_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '退还时间',
  `file_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '附件ID，如:1,2,3',
  `remarks` mediumtext  NULL COMMENT '备注',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '退款打款状态 1待打款,2已打款',
  `confirm_uid` bigint(11) NOT NULL DEFAULT 0 COMMENT '打款确认人',
  `confirm_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '打款确认时间',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '退款登记人',
  `did` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属部门',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  `check_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '审核状态:0待审核,1审核中,2审核通过,3审核不通过,4撤销审核',
  `check_flow_id` int(11) NOT NULL DEFAULT 0 COMMENT '审核流程id',
  `check_step_sort` int(11) NOT NULL DEFAULT 0 COMMENT '当前审批步骤',
  `check_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '当前审批人ID，如:1,2,3',
  `check_last_uid` varchar(500) NOT NULL DEFAULT '' COMMENT '上一审批人',
  `check_history_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '历史审批人ID，如:1,2,3',
  `check_copy_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '抄送人ID，如:1,2,3',
  `check_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核通过时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '到账退还记录表';

-- ----------------------------
-- Table structure for oa_finance_injection
-- ----------------------------
DROP TABLE IF EXISTS `oa_finance_injection`;
CREATE TABLE `oa_finance_injection`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `amount` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '金额',
  `enterprise_id` int(11) NOT NULL DEFAULT 0 COMMENT '企业主体id',
  `account_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '资金账户ID',
  `content` mediumtext  NULL COMMENT '注资说明',
  `handler_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '经手人ID，如:1,2,3',
  `income_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '到账日期',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '财务注资表';

-- ----------------------------
-- Table structure for oa_finance_log
-- ----------------------------
DROP TABLE IF EXISTS `oa_finance_log`;
CREATE TABLE `oa_finance_log`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL COMMENT '交易标识',
  `amount` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '交易金额',
  `types` tinyint(4) NOT NULL DEFAULT 0 COMMENT '流水类型 1收入,2支出',
  `action_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '业务ID',
  `action_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '业务发生日期',
  `transaction_no` varchar(32) NOT NULL UNIQUE COMMENT '交易流水编号',
  `enterprise_id` int(11) NOT NULL DEFAULT 0 COMMENT '企业主体id',
  `account_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '资金账户ID',
  `fundscate_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '款项类型',
  `paytype_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '支付方式',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '财务流水表';