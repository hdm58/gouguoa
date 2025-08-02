ALTER TABLE `oa_admin` 
ADD COLUMN `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间' AFTER `update_time`;

ALTER TABLE `oa_admin` 
ADD COLUMN `talent_id` int(11) NOT NULL DEFAULT 0 COMMENT '入职申请id' AFTER `delete_time`;

ALTER TABLE `oa_admin` 
MODIFY COLUMN `birthday` varchar(255) NOT NULL DEFAULT '' COMMENT '生日' AFTER `job_number`;

ALTER TABLE `oa_admin` 
MODIFY COLUMN `work_date` varchar(255) NOT NULL DEFAULT '' COMMENT '开始工作时间' AFTER `age`;

ALTER TABLE `oa_admin` 
MODIFY COLUMN `graduate_day` varchar(255) NOT NULL DEFAULT '毕业日期' AFTER `graduate_school`;

UPDATE `oa_admin_rule` SET `pid` = 97, `src` = 'user/user/del', `title` = '删除', `name` = '员工' WHERE `id` = 105;
UPDATE `oa_admin_rule` SET `pid` = 3, `src` = 'user/personal/leave', `title` = '离职申请', `name` = '离职申请' WHERE `id` = 108;
UPDATE `oa_admin_rule` SET `pid` = 108, `src` = 'user/personal/leave_view', `title` = '查看', `name` = '离职申请' WHERE `id` = 111;

ALTER TABLE `oa_template` 
ADD COLUMN `msg_title_3` varchar(500) NOT NULL DEFAULT '' COMMENT '消息模板标题(审批通过抄送)' AFTER `msg_content_2`,
ADD COLUMN `msg_content_3` varchar(500) NOT NULL DEFAULT '' COMMENT '消息模板内容(审批通过抄送)' AFTER `msg_title_3`;

UPDATE `oa_template` SET `msg_title_3` = '{from_user}提交的『请假审批』已被审批通过并抄送给你' WHERE `id` = 2;
UPDATE `oa_template` SET `msg_content_3` = '{from_user}在{create_time}提交的『请假审批』已被审批通过并抄送给你，请及时查看详情。' WHERE `id` = 2;

UPDATE `oa_template` SET `msg_title_3` = '{from_user}提交的『出差审批』已被审批通过并抄送给你' WHERE `id` = 3;
UPDATE `oa_template` SET `msg_content_3` = '{from_user}在{create_time}提交的『出差审批』已被审批通过并抄送给你，请及时查看详情。' WHERE `id` = 3;

UPDATE `oa_template` SET `msg_title_3` = '{from_user}提交的『外出审批』已被审批通过并抄送给你' WHERE `id` = 4;
UPDATE `oa_template` SET `msg_content_3` = '{from_user}在{create_time}提交的『外出审批』已被审批通过并抄送给你，请及时查看详情。' WHERE `id` = 4;

UPDATE `oa_template` SET `msg_title_3` = '{from_user}提交的『加班审批』已被审批通过并抄送给你' WHERE `id` = 5;
UPDATE `oa_template` SET `msg_content_3` = '{from_user}在{create_time}提交的『加班审批』已被审批通过并抄送给你，请及时查看详情。' WHERE `id` = 5;

UPDATE `oa_template` SET `msg_title_3` = '{from_user}提交的『用章审批』已被审批通过并抄送给你' WHERE `id` = 6;
UPDATE `oa_template` SET `msg_content_3` = '{from_user}在{create_time}提交的『用章审批』已被审批通过并抄送给你，请及时查看详情。' WHERE `id` = 6;

UPDATE `oa_template` SET `msg_title_3` = '{from_user}提交的『公文审批』已被审批通过并抄送给你' WHERE `id` = 7;
UPDATE `oa_template` SET `msg_content_3` = '{from_user}在{create_time}提交的『公文审批』已被审批通过并抄送给你，请及时查看详情。' WHERE `id` = 7;

UPDATE `oa_template` SET `msg_title_3` = '{from_user}提交的『报销审批』已被审批通过并抄送给你' WHERE `id` = 8;
UPDATE `oa_template` SET `msg_content_3` = '{from_user}在{create_time}提交的『报销审批』已被审批通过并抄送给你，请及时查看详情。' WHERE `id` = 8;

UPDATE `oa_template` SET `msg_title_3` = '{from_user}提交的『发票审批』已被审批通过并抄送给你' WHERE `id` = 9;
UPDATE `oa_template` SET `msg_content_3` = '{from_user}在{create_time}提交的『发票审批』已被审批通过并抄送给你，请及时查看详情。' WHERE `id` = 9;

UPDATE `oa_template` SET `msg_title_3` = '{from_user}提交的『收票审批』已被审批通过并抄送给你' WHERE `id` = 10;
UPDATE `oa_template` SET `msg_content_3` = '{from_user}在{create_time}提交的『收票审批』已被审批通过并抄送给你，请及时查看详情。' WHERE `id` = 10;

UPDATE `oa_template` SET `msg_title_3` = '{from_user}提交的『无发票回款审批』已被审批通过并抄送给你' WHERE `id` = 11;
UPDATE `oa_template` SET `msg_content_3` = '{from_user}在{create_time}提交的『无发票回款审批』已被审批通过并抄送给你，请及时查看详情。' WHERE `id` = 11;

UPDATE `oa_template` SET `msg_title_3` = '{from_user}提交的『无发票付款审批』已被审批通过并抄送给你' WHERE `id` = 12;
UPDATE `oa_template` SET `msg_content_3` = '{from_user}在{create_time}提交的『无发票付款审批』已被审批通过并抄送给你，请及时查看详情。' WHERE `id` = 12;

UPDATE `oa_template` SET `msg_title_3` = '{from_user}提交的『销售合同审批』已被审批通过并抄送给你' WHERE `id` = 13;
UPDATE `oa_template` SET `msg_content_3` = '{from_user}在{create_time}提交的『销售合同审批』已被审批通过并抄送给你，请及时查看详情。' WHERE `id` = 13;

UPDATE `oa_template` SET `msg_title_3` = '{from_user}提交的『采购合同审批』已被审批通过并抄送给你' WHERE `id` = 14;
UPDATE `oa_template` SET `msg_content_3` = '{from_user}在{create_time}提交的『采购合同审批』已被审批通过并抄送给你，请及时查看详情。' WHERE `id` = 14;

INSERT INTO `oa_template` VALUES (15, '借支审批', 'loan', 2, 0, '', '/finance/loan/view/id/{action_id}', '{from_user}提交了一个『借支审批』，请及时审批', '您有一个新的『借支审批』需要处理。', '您提交的『借支审批』已被审批通过', '您在{create_time}提交的『借支审批』已于{date}被审批通过。', '您提交的『借支审批』已被驳回拒绝', '您在{create_time}提交的『借支审批』已于{date}被驳回拒绝。', '{from_user}提交的『借支审批』已被审批通过并抄送给你', '{from_user}在{create_time}提交的『借支审批』已被审批通过并抄送给你，请及时查看详情。', '', 1, 1, 1733314789, 1753284351, 0);
INSERT INTO `oa_template` VALUES (16, '入职审批', 'talent', 2, 0, '', '/user/talent/view/id/{action_id}', '{from_user}提交了一个『入职审批』，请及时审批', '您有一个新的『入职审批』需要处理。', '您提交的『入职审批』已被审批通过', '您在{create_time}提交的『入职审批』已于{date}被审批通过。', '您提交的『入职审批』已被驳回拒绝', '您在{create_time}提交的『入职审批』已于{date}被驳回拒绝。', '{from_user}提交的『入职审批』已被审批通过并抄送给你', '{from_user}在{create_time}提交的『入职审批』已被审批通过并抄送给你，请及时查看详情。', '', 1, 1, 1733314789, 1753284351, 0);
INSERT INTO `oa_template` VALUES (17, '离职审批', 'personal_quit', 2, 0, '', '/user/personal/leave_view/id/{action_id}', '{from_user}提交了一个『离职审批』，请及时审批', '您有一个新的『离职审批』需要处理。', '您提交的『离职审批』已被审批通过', '您在{create_time}提交的『离职审批』已于{date}被审批通过。', '您提交的『离职审批』已被驳回拒绝', '您在{create_time}提交的『离职审批』已于{date}被驳回拒绝。', '{from_user}提交的『离职审批』已被审批通过并抄送给你', '{from_user}在{create_time}提交的『离职审批』已被审批通过并抄送给你，请及时查看详情。', '', 1, 1, 1733314789, 1753284351, 0);
INSERT INTO `oa_template` VALUES (18, '人事调动审批', 'department_change', 2, 0, '', '/user/personal/change_view/id/{action_id}', '{from_user}提交了一个『人事调动审批』，请及时审批', '您有一个新的『人事调动审批』需要处理。', '您提交的『人事调动审批』已被审批通过', '您在{create_time}提交的『人事调动审批』已于{date}被审批通过。', '您提交的『人事调动审批』已被驳回拒绝', '您在{create_time}提交的『人事调动审批』已于{date}被驳回拒绝。', '{from_user}提交的『人事调动审批』已被审批通过并抄送给你', '{from_user}在{create_time}提交的『人事调动审批』已被审批通过并抄送给你，请及时查看详情。', '', 1, 1, 1733314789, 1753284351, 0);

INSERT INTO `oa_flow_cate` VALUES (14, '借支', 'loan', 4, 'loan', 'icon-zhangbuguanli', '', 0, 1, 0, 1, 1, 0, 1, '/finance/loan/add', '/finance/loan/view', 0, 1, 1, 15, 1723470017, 1753582624);
INSERT INTO `oa_flow_cate` VALUES (15, '入职', 'talent', 5, 'talent', 'icon-yuangongdaoru', '', 0, 1, 0, 1, 1, 0, 1, '/user/talent/add', '/user/talent/view', 0, 1, 1, 16, 1729490152, 1753585631);
INSERT INTO `oa_flow_cate` VALUES (16, '离职', 'personal_quit', 5, 'personal_quit', 'icon-yuangongtongji2', '', 0, 1, 0, 1, 1, 0, 1, '/user/personal/leave_add', '/user/personal/leave_view', 0, 1, 1, 17, 1729490152, 1753585604);
INSERT INTO `oa_flow_cate` VALUES (17, '人事调动', 'department_change', 5, 'department_change', 'icon-yuangongbiandong', '', 0, 1, 0, 1, 1, 0, 1, '/user/personal/change_add', '/user/personal/change_view', 0, 1, 1, 18, 1729490152, 1753585614);

INSERT INTO `oa_flow` VALUES (14, '借支审批', 14, 1, '', '', '', 1, '', 1, 1723470501, 0, 0);
INSERT INTO `oa_flow` VALUES (15, '入职审批', 15, 1, '', '', '', 1, '', 1, 1723470501, 0, 0);
INSERT INTO `oa_flow` VALUES (16, '离职审批', 16, 1, '', '', '', 1, '', 1, 1723470501, 0, 0);
INSERT INTO `oa_flow` VALUES (17, '人事调动审批', 17, 1, '', '', '', 1, '', 1, 1723470501, 0, 0);

INSERT INTO `oa_mobile_menu` VALUES (16, '借支管理', 'icon-a-baoxiao2','purple', '/qiye/finance/loan', 4, 0, 1, 1733153131, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (390, 6, 'finance/loan/datalist', '借支管理', '借支', 'finance', '', 1, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (391, 390, 'finance/loan/add', '新建/编辑', '借支', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (392, 390, 'finance/loan/del', '删除', '借支', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (393, 390, 'finance/loan/view', '查看', '借支', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (394, 262, 'finance/loan/record', '借支记录', '借支记录', 'finance', '', 1, 0, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (395, 3, 'user/talent/datalist', '入职申请', '入职申请', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (396, 395, 'user/talent/add', '新增/编辑', '入职申请', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (397, 395, 'user/talent/view', '查看', '入职申请', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (398, 395, 'user/talent/del', '删除', '入职申请', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (399, 395, 'user/talent/set', '入职', '新员工', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (400, 3, 'user/blacklist/datalist', '人员黑名单', '黑名单', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (401, 400, 'user/blacklist/add', '编辑', '黑名单', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (402, 400, 'user/blacklist/del', '删除', '黑名单', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (403, 106, 'user/personal/change_view', '查看', '人事调动', 'user', '', 2, 1, 1, 0, 0);

UPDATE `oa_admin_group` SET `rules` = '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,206,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,248,249,250,251,252,253,254,255,256,257,258,259,260,261,262,263,264,265,266,267,268,269,270,271,272,273,274,275,276,277,278,279,280,281,282,283,284,285,286,287,288,289,290,291,292,293,294,295,296,297,298,299,300,301,302,303,304,305,306,307,308,309,310,311,312,313,314,315,316,317,318,319,320,321,322,323,324,325,326,327,328,329,330,331,332,333,334,335,336,337,338,339,340,341,342,343,344,345,346,347,348,349,350,351,352,353,354,355,356,357,358,359,360,361,362,363,364,365,366,367,368,369,370,371,372,373,374,375,376,377,378,379,380,381,382,383,384,385,386,387,388,389,390,391,392,393,394,395,396,397,398,399,400,401,402,403' WHERE `id` = 1;

ALTER TABLE `oa_expense` 
ADD COLUMN `loan_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联借支ID' AFTER `did`;

ALTER TABLE `oa_expense` 
ADD COLUMN `balance_cost` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '冲账金额' AFTER `loan_id`;

ALTER TABLE `oa_expense` 
ADD COLUMN `pay_amount` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '需打款金额' AFTER `balance_cost`;
  
-- ----------------------------
-- Table structure for oa_loan
-- ----------------------------
DROP TABLE IF EXISTS `oa_loan`;
CREATE TABLE `oa_loan`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `subject_id` int(11)  NOT NULL DEFAULT 0 COMMENT '借支企业主体',
  `code` varchar(100) NOT NULL DEFAULT '' COMMENT '借支编码',
  `title` varchar(500) NOT NULL DEFAULT '' COMMENT '借款主题',
  `cost` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '借支金额',
  `types` tinyint(4) NOT NULL DEFAULT 1 COMMENT '借支类型：1日常备用金,2项目预支款',
  `loan_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预期借支日期',
  `plan_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预计还款日期',
  `content` varchar(1000) NULL DEFAULT '' COMMENT '借支理由',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '借支人',
  `did` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '借支部门ID',
  `balance_cost` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '已冲账金额',
  `balance_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '冲账状态 0待冲账,1部分冲账,2已冲账',
  `project_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联项目ID',
  `file_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '附件ID，如:1,2,3',
  `pay_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '打款状态 0待打款,1已打款',
  `pay_admin_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '打款人ID',
  `pay_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后打款时间',
  `back_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '还款状态 0待还款,1已还款',
  `back_admin_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '还款操作人ID',
  `back_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后还款时间',
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
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '借支表';

-- ----------------------------
-- Table structure for oa_talent
-- ----------------------------
DROP TABLE IF EXISTS `oa_talent`;
CREATE TABLE `oa_talent`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '姓名',
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT '电子邮箱',
  `mobile` bigint(11) NOT NULL DEFAULT 0 COMMENT '手机号码',
  `sex` int(255) NOT NULL DEFAULT 0 COMMENT '性别:1男,2女',
  `to_did` int(11) NOT NULL DEFAULT 0 COMMENT '所属部门',
  `to_dids` varchar(500) NOT NULL DEFAULT '' COMMENT '次部门',
  `thumb` varchar(255) NOT NULL COMMENT '头像',
  `position_id` int(11) NOT NULL DEFAULT 0 COMMENT '职位id',
  `type` int(1) NOT NULL DEFAULT 0 COMMENT '员工类型:0未设置,1正式,2试用,3实习',
  `position_name` int(11) NOT NULL DEFAULT 0 COMMENT '应聘职务',
  `position_rank` int(11) NOT NULL DEFAULT 0 COMMENT '应聘职级',
  `birthday` varchar(255) NOT NULL DEFAULT '' COMMENT '生日',
  `job_number` varchar(255) NOT NULL DEFAULT '' COMMENT '工号',
  `pid` int(11) NOT NULL DEFAULT 0 COMMENT '上级领导',
  `work_date` varchar(255) NOT NULL DEFAULT '' COMMENT '开始工作时间',
  `work_location` int(11) NOT NULL DEFAULT 0 COMMENT '工作地点',
  `native_place` varchar(255) NOT NULL DEFAULT '' COMMENT '籍贯',
  `nation` varchar(255) NOT NULL DEFAULT '' COMMENT '民族',
  `home_address` varchar(255) NOT NULL DEFAULT '' COMMENT '家庭地址',
  `current_address` varchar(255) NOT NULL DEFAULT '' COMMENT '现居地址',
  `contact` varchar(255) NOT NULL DEFAULT '' COMMENT '紧急联系人',
  `contact_mobile` varchar(255) NOT NULL DEFAULT '' COMMENT '紧急联系人电话',
  `resident_type` int(1) NOT NULL DEFAULT 0 COMMENT '户口性质:1农村户口,2城镇户口',
  `resident_place` varchar(255) NOT NULL DEFAULT '' COMMENT '户口所在地',
  `graduate_school` varchar(255) NOT NULL DEFAULT '' COMMENT '毕业学校',
  `graduate_day` varchar(255) NOT NULL DEFAULT '毕业日期',
  `political` int(1) NOT NULL DEFAULT 1 COMMENT '政治面貌:1中共党员,2团员',
  `marital_status` int(1) NOT NULL DEFAULT 1 COMMENT '婚姻状况:1未婚,2已婚,3离异',
  `idcard` varchar(255) NOT NULL DEFAULT '' COMMENT '身份证',
  `education` varchar(255) NOT NULL DEFAULT '' COMMENT '学位',
  `speciality` varchar(255) NOT NULL DEFAULT '' COMMENT '专业',
  `bank_account` varchar(255) NOT NULL DEFAULT '' COMMENT '银行卡号',
  `social_account` varchar(255) NOT NULL DEFAULT '' COMMENT '社保账号',
  `salary` int(11) NOT NULL DEFAULT 0 COMMENT '期望薪资',
  `salary_remark` varchar(255) NOT NULL DEFAULT '' COMMENT '薪资备注',
  `reference_name` varchar(255) NOT NULL DEFAULT '' COMMENT '推荐人姓名',
  `reference_rel` varchar(255) NOT NULL DEFAULT '' COMMENT '推荐人关系',
  `reference_mobile` varchar(255) NOT NULL DEFAULT '' COMMENT '推荐人联系方式',
  `file_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '档案附件',
  `desc` mediumtext  NULL COMMENT '个人简介',
  `remark` varchar(1000) NULL DEFAULT '' COMMENT '入职评语',
  `entry_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '入职时间',
  `is_staff` int(1) NOT NULL DEFAULT 1 COMMENT '身份类型:1普通员工,2劳务派遣',
  `status` int(1) NOT NULL DEFAULT 1 COMMENT '状态:1正常,2已入职',
  `admin_id` int(11) NOT NULL DEFAULT 0 COMMENT '创建人',
  `did` int(11) NOT NULL DEFAULT 0 COMMENT '创建部门',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '申请时间',
  `check_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '审核状态:0待审核,1审核中,2审核通过,3审核不通过,4撤销审核',
  `check_flow_id` int(11) NOT NULL DEFAULT 0 COMMENT '审核流程id',
  `check_step_sort` int(11) NOT NULL DEFAULT 0 COMMENT '当前审批步骤',
  `check_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '当前审批人ID，如:1,2,3',
  `check_last_uid` varchar(500) NOT NULL DEFAULT '' COMMENT '上一审批人',
  `check_history_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '历史审批人ID，如:1,2,3',
  `check_copy_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '抄送人ID，如:1,2,3',
  `check_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核通过时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新信息时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '入职申请表';

-- ----------------------------
-- Table structure for oa_personal_quit
-- ----------------------------
DROP TABLE IF EXISTS `oa_personal_quit`;
CREATE TABLE `oa_personal_quit`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `lead_admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上级领导',
  `connect_id` int(11) NOT NULL DEFAULT 0 COMMENT '资料交接人',
  `connect_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '资料交接时间',
  `connect_uids` varchar(100) NOT NULL DEFAULT '' COMMENT '参与交接人,可多个',
  `file_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '档案附件',
  `quit_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '离职时间',
  `status` int(1) NOT NULL DEFAULT 1 COMMENT '状态:1未交接,2已交接离职',
  `remark` varchar(1000) NULL DEFAULT '' COMMENT '备注信息',
  `admin_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `did` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人所在部门',
  `check_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '审核状态:0待审核,1审核中,2审核通过,3审核不通过,4撤销审核',
  `check_flow_id` int(11) NOT NULL DEFAULT 0 COMMENT '审核流程id',
  `check_step_sort` int(11) NOT NULL DEFAULT 0 COMMENT '当前审批步骤',
  `check_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '当前审批人ID，如:1,2,3',
  `check_last_uid` varchar(500) NOT NULL DEFAULT '' COMMENT '上一审批人',
  `check_history_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '历史审批人ID，如:1,2,3',
  `check_copy_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '抄送人ID，如:1,2,3',
  `check_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核通过时间',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '离职申请表';

-- ----------------------------
-- Table structure for oa_department_change
-- ----------------------------
DROP TABLE IF EXISTS `oa_department_change`;
CREATE TABLE `oa_department_change`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '员工ID',
  `from_did` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '原部门id',
  `to_did` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '调到部门id',
  `connect_id` int(11) NOT NULL DEFAULT 0 COMMENT '资料交接人',
  `connect_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '资料交接时间',
  `connect_uids` varchar(100) NOT NULL DEFAULT '' COMMENT '参与交接人,可多个',
  `file_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '档案附件',
  `move_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '调动时间',
  `status` int(1) NOT NULL DEFAULT 1 COMMENT '状态:1未调动,2已交接调动',
  `remark` varchar(1000) NULL DEFAULT '' COMMENT '备注信息',
  `admin_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `did` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人所在部门',
  `check_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '审核状态:0待审核,1审核中,2审核通过,3审核不通过,4撤销审核',
  `check_flow_id` int(11) NOT NULL DEFAULT 0 COMMENT '审核流程id',
  `check_step_sort` int(11) NOT NULL DEFAULT 0 COMMENT '当前审批步骤',
  `check_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '当前审批人ID，如:1,2,3',
  `check_last_uid` varchar(500) NOT NULL DEFAULT '' COMMENT '上一审批人',
  `check_history_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '历史审批人ID，如:1,2,3',
  `check_copy_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '抄送人ID，如:1,2,3',
  `check_time` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核通过时间',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '人事调动申请表';

-- ----------------------------
-- Table structure for oa_blacklist
-- ----------------------------
DROP TABLE IF EXISTS `oa_blacklist`;
CREATE TABLE `oa_blacklist`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '姓名',
  `mobile` varchar(255) NOT NULL DEFAULT '' COMMENT '手机号码',
  `idcard` varchar(255) NOT NULL DEFAULT '' COMMENT '身份证',
  `remark` text NULL COMMENT '备注信息',
  `admin_id` int(11) NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '申请时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新信息时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '黑名单表';
