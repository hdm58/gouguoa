-- ----------------------------
-- Table structure for oa_admin_log_count
-- ----------------------------
DROP TABLE IF EXISTS `oa_admin_log_count`;
CREATE TABLE `oa_admin_log_count`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `year` int(4) NOT NULL COMMENT '年份',
  `date` date NOT NULL COMMENT '日期',
  `num` int(11) NOT NULL DEFAULT 1 COMMENT '统计数',
  `create_time` bigint(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COMMENT = '员工操作日志统计表';

INSERT INTO `oa_admin_rule` VALUES (404, 172, 'adm/meeting/datalist', '会议室预定', '会议室预定', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (405, 404, 'adm/meeting/add', '新增/编辑', '会议室预定', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (406, 404, 'adm/meeting/view', '查看', '会议室预定', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (407, 404, 'adm/meeting/del', '删除', '会议室预定', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (408, 404, 'adm/meeting/room_use', '会议室使用情况', '会议室预定', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (409, 59, 'home/cate/links', '办公工具', '办公工具', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (410, 409, 'home/cate/links_add', '新建/编辑', '办公工具', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (411, 409, 'home/cate/links_del', '删除', '办公工具', 'home', '', 2, 1, 1, 0, 0);

UPDATE `oa_admin_group` SET `rules` = '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,206,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,248,249,250,251,252,253,254,255,256,257,258,259,260,261,262,263,264,265,266,267,268,269,270,271,272,273,274,275,276,277,278,279,280,281,282,283,284,285,286,287,288,289,290,291,292,293,294,295,296,297,298,299,300,301,302,303,304,305,306,307,308,309,310,311,312,313,314,315,316,317,318,319,320,321,322,323,324,325,326,327,328,329,330,331,332,333,334,335,336,337,338,339,340,341,342,343,344,345,346,347,348,349,350,351,352,353,354,355,356,357,358,359,360,361,362,363,364,365,366,367,368,369,370,371,372,373,374,375,376,377,378,379,380,381,382,383,384,385,386,387,388,389,390,391,392,393,394,395,396,397,398,399,400,401,402,403,404,405,406,407,408,409,410,411' ,`layouts` = '1,2,3,4,5,6,7,8,9,10,11,12,13' WHERE `id` = 1;

INSERT INTO `oa_template` VALUES (19, '会议室预定审批', 'meeting_order', 2, 0, '', '/adm/meeting/view/id/{action_id}', '{from_user}提交了一个『会议室预定审批』，请及时审批', '您有一个新的『会议室预定审批』需要处理。', '您提交的『会议室预定审批』已被审批通过', '您在{create_time}提交的『会议室预定审批』已于{date}被审批通过。', '您提交的『会议室预定审批』已被驳回拒绝', '您在{create_time}提交的『会议室预定审批』已于{date}被驳回拒绝。', '{from_user}提交的『会议室预定审批』已被审批通过并抄送给你', '{from_user}在{create_time}提交的『会议室预定审批』已被审批通过并抄送给你，请及时查看详情。', '', 1, 1, 1758550842, 1758582833, 0);
INSERT INTO `oa_flow_cate` VALUES (18, '会议室预定审批', 'meeting_order', 2, 'meeting_order', 'icon-xuetangguanli', '', 0, 1, 0, 1, 1, 0, 1, '/adm/meeting/add', '/adm/meeting/view', 0, 1, 1, 19, 1758544152, 1758582877);
INSERT INTO `oa_flow` VALUES (18, '会议室预定审批', 18, 1, '', '', '', 1, '', 1, 1758550919, 0, 0);

-- ----------------------------
-- Table structure for oa_meeting_order
-- ----------------------------
DROP TABLE IF EXISTS `oa_meeting_order`;
CREATE TABLE `oa_meeting_order`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(225) NULL DEFAULT NULL COMMENT '会议主题',
  `start_date` int(11) NOT NULL DEFAULT 0 COMMENT '会议开始时间',
  `end_date` int(11) NOT NULL DEFAULT 0 COMMENT '会议结束时间',
  `requirements` varchar(500) NOT NULL DEFAULT '' COMMENT '会议需求',
  `room_id` int(11) NOT NULL DEFAULT 0 COMMENT '会议室',
  `num` int(11) NOT NULL DEFAULT 0 COMMENT '人数',
  `remark` varchar(225) NULL DEFAULT NULL COMMENT '备注信息',
  `file_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '相关附件',
  `join_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '与会人员',
  `anchor_id` int(11) NOT NULL DEFAULT 0 COMMENT '主持人id',
  `admin_id` int(11) NOT NULL DEFAULT 0 COMMENT '发布人id',
  `did` int(11) NOT NULL DEFAULT 0 COMMENT '主办部门',
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
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '会议室预定';


INSERT INTO `oa_basic_adm` VALUES (13, '2', '电子屏', 1, 1758584244, 0);
INSERT INTO `oa_basic_adm` VALUES (14, '2', '投影背景', 1, 1758584263, 0);
INSERT INTO `oa_basic_adm` VALUES (15, '2', '电脑', 1, 1758584272, 0);
INSERT INTO `oa_basic_adm` VALUES (16, '2', '视频', 1, 1758584287, 1758584291);
INSERT INTO `oa_basic_adm` VALUES (17, '2', '购买水果', 1, 1758584305, 0);
INSERT INTO `oa_basic_adm` VALUES (18, '2', '订餐', 1, 1758584312, 0);

-- ----------------------------
-- Table structure for oa_links
-- ----------------------------
DROP TABLE IF EXISTS `oa_links`;
CREATE TABLE `oa_links`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '小工具名称',
  `logo` int(11) NOT NULL DEFAULT 0 COMMENT 'logo',
  `url` varchar(255) NULL DEFAULT NULL COMMENT '链接',
  `target` int(1) NOT NULL DEFAULT 1 COMMENT '是否新窗口打开，1是,0否',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `create_time` int(11) NOT NULL DEFAULT 0,
  `update_time` int(11) NOT NULL DEFAULT 0,
  `delete_time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8mb4 COMMENT = '办公工具';

INSERT INTO `oa_links` VALUES (1, 'DeepSeek', 0, 'https://chat.deepseek.com/', 1, 0, 1757759036, 1760144979, 0);
INSERT INTO `oa_links` VALUES (2, '豆包', 0, 'https://www.doubao.com/chat/', 1, 0, 1757759226, 1760141689, 0);
INSERT INTO `oa_links` VALUES (3, '文心一言', 0, 'https://yiyan.baidu.com/', 1, 0, 1760141394, 0, 0);
INSERT INTO `oa_links` VALUES (4, '通义千问', 0, 'https://www.tongyi.com/', 1, 0, 1760141705, 0, 0);
INSERT INTO `oa_links` VALUES (5, 'AIPPT', 0, 'https://www.aippt.cn/', 1, 0, 1760144331, 0, 0);
INSERT INTO `oa_links` VALUES (6, 'ProcessOn', 0, 'https://www.processon.com/', 1, 0, 1760144601, 0, 0);



