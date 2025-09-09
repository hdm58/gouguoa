INSERT INTO `oa_mobile_types` VALUES (5, '人事管理', '', '', 0, 1, 1754539327, 1754539333);

INSERT INTO `oa_mobile_menu` VALUES (17, '企业人员', 'icon-kehu', 'cyan', '/qiye/index/admin', 5, 0, 1, 1754539422, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (18, '入职管理', 'icon-wodeshenpi1', 'cyan', '/qiye/approve/talentlist', 5, 0, 1, 1754539501, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (19, '离职管理', 'icon-zuofeibaoming', 'cyan', '/qiye/approve/leavelist', 5, 0, 1, 1754539544, 0, 0);
INSERT INTO `oa_mobile_menu` VALUES (20, '人事调动', 'icon-qiangke', 'cyan', '/qiye/approve/changelist', 5, 0, 1, 1754539578, 0, 0);

UPDATE `oa_admin_group` SET `mobile_menu` = '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,5,17,18,19,20' WHERE `id` = 1;
UPDATE `oa_admin_group` SET `mobile_menu` = '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,5,17,18,19,20' WHERE `id` = 2;
UPDATE `oa_admin_group` SET `mobile_menu` = '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,5,17,18,19,20' WHERE `id` = 3;

