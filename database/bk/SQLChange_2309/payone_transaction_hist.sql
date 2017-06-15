/*
Navicat MySQL Data Transfer
Source Host     : localhost:3306
Source Database : virtualpost
Target Host     : localhost:3306
Target Database : virtualpost
Date: 2014-09-23 23:17:28
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for payone_transaction_hist
-- ----------------------------
DROP TABLE IF EXISTS `payone_transaction_hist`;
CREATE TABLE `payone_transaction_hist` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `aid` varchar(20) DEFAULT NULL,
  `txid` varchar(30) DEFAULT NULL,
  `reference` varchar(30) DEFAULT NULL,
  `userid` varchar(30) DEFAULT NULL,
  `customerid` varchar(30) DEFAULT NULL,
  `create_time` bigint(30) DEFAULT NULL,
  `booking_date` bigint(30) DEFAULT NULL,
  `document_date` bigint(30) DEFAULT NULL,
  `document_reference` varchar(30) DEFAULT NULL,
  `param` varchar(30) DEFAULT NULL,
  `event` varchar(30) DEFAULT NULL,
  `clearingtype` varchar(10) DEFAULT NULL,
  `amount` decimal(19,2) DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `invoice_id` varchar(30) DEFAULT NULL,
  `last_update_date` bigint(20) DEFAULT NULL,
  `txaction` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of payone_transaction_hist
-- ----------------------------
INSERT INTO `payone_transaction_hist` VALUES ('2', '23409', '137200201', 'INV_912_crAd_2ST', '53381786', '', '1401581510', '1401580800', '1401580800', '000053', 'paid', '', 'cc', '48.00', 'EUR', '912', '912', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('3', '23409', '137200481', 'INV_884_S6P1_2ST', '53381896', '', '1401581945', '1401580800', '1401580800', 'T59042', 'paid', '', 'cc', '10.00', 'EUR', '884', '884', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('4', '23409', '137200610', 'INV_884_YRuo_2ST', '53381957', '', '1401582197', '1401580800', '1401580800', 'T67069', 'paid', '', 'cc', '1.00', 'EUR', '884', '884', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('5', '23409', '137200705', 'INV_870_1ST', '53381999', '', '1401582410', '1401580800', '1401580800', '362270', 'paid', '', 'cc', '8.00', 'EUR', '870', '870', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('6', '23409', '137200881', 'INV_875_1ST', '53382080', '', '1401582758', '1401580800', '1401580800', '061951', 'paid', '', 'cc', '8.00', 'EUR', '875', '875', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('7', '23409', '137201610', 'INV_862_1ST', '53382258', '', '1401583882', '1401580800', '1401580800', '136920', 'paid', '', 'cc', '8.00', 'EUR', '862', '862', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('8', '23409', '137201611', 'INV_863_1ST', '53382259', '', '1401583884', '1401580800', '1401580800', '103083', 'paid', '', 'cc', '4.00', 'EUR', '863', '863', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('9', '23409', '137201613', 'INV_872_1ST', '53382260', '', '1401583885', '1401580800', '1401580800', '971608', 'paid', '', 'cc', '9.00', 'EUR', '872', '872', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('10', '23409', '137201614', 'INV_873_1ST', '53382261', '', '1401583887', '1401580800', '1401580800', '214797', 'paid', '', 'cc', '9.00', 'EUR', '873', '873', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('11', '23409', '137201615', 'INV_874_1ST', '53382262', '', '1401583890', '1401580800', '1401580800', 'T00216', 'paid', '', 'cc', '9.00', 'EUR', '874', '874', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('12', '23409', '137201616', 'INV_878_1ST', '53382263', '', '1401583891', '1401580800', '1401580800', '013128', 'paid', '', 'cc', '9.00', 'EUR', '878', '878', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('13', '23409', '137201619', 'INV_881_1ST', '53382265', '', '1401583892', '1401580800', '1401580800', '970539', 'paid', '', 'cc', '4.00', 'EUR', '881', '881', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('14', '23409', '137201620', 'INV_882_1ST', '53382266', '', '1401583894', '1401580800', '1401580800', '189337', 'paid', '', 'cc', '9.00', 'EUR', '882', '882', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('15', '23409', '137201623', 'INV_897_1ST', '53382269', '', '1401583898', '1401580800', '1401580800', '307634', 'paid', '', 'cc', '14.00', 'EUR', '897', '897', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('16', '23409', '137201624', 'INV_898_1ST', '53382270', '', '1401583900', '1401580800', '1401580800', '068518', 'paid', '', 'cc', '9.00', 'EUR', '898', '898', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('17', '23409', '137201625', 'INV_899_1ST', '53382271', '', '1401583902', '1401580800', '1401580800', '042316', 'paid', '', 'cc', '9.00', 'EUR', '899', '899', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('18', '23409', '137201627', 'INV_901_1ST', '53382272', '', '1401583903', '1401580800', '1401580800', '002931', 'paid', '', 'cc', '9.00', 'EUR', '901', '901', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('19', '23409', '137201630', 'INV_916_1ST', '53382275', '', '1401583908', '1401580800', '1401580800', '419405', 'paid', '', 'cc', '4.00', 'EUR', '916', '916', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('20', '23409', '137201632', 'INV_920_1ST', '53382277', '', '1401583910', '1401580800', '1401580800', '051100', 'paid', '', 'cc', '8.00', 'EUR', '920', '920', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('21', '23409', '137201634', 'INV_923_1ST', '53382279', '', '1401583913', '1401580800', '1401580800', '046423', 'paid', '', 'cc', '8.00', 'EUR', '923', '923', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('22', '23409', '137201636', 'INV_924_1ST', '53382280', '', '1401583915', '1401580800', '1401580800', '965529', 'paid', '', 'cc', '4.00', 'EUR', '924', '924', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('23', '23409', '137201637', 'INV_925_1ST', '53382281', '', '1401583916', '1401580800', '1401580800', '056572', 'paid', '', 'cc', '4.00', 'EUR', '925', '925', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('24', '23409', '137201847', 'INV_856_yuSb_2ST', '53382365', '', '1401584474', '1401580800', '1401580800', '219610', 'paid', '', 'cc', '2.00', 'EUR', '856', '856', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('25', '23409', '137201848', 'INV_868_bZzS_2ST', '53382366', '', '1401584476', '1401580800', '1401580800', '489180', 'paid', '', 'cc', '9.00', 'EUR', '868', '868', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('26', '23409', '137201850', 'INV_869_xAxs_2ST', '53382368', '', '1401584478', '1401580800', '1401580800', '308136', 'paid', '', 'cc', '0.00', 'EUR', '869', '869', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('27', '23409', '137201851', 'INV_871_vOld_2ST', '53382369', '', '1401584479', '1401580800', '1401580800', '16698B', 'paid', '', 'cc', '4.00', 'EUR', '871', '871', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('28', '23409', '137201852', 'INV_872_WCKn_2ST', '53382370', '', '1401584481', '1401580800', '1401580800', '990675', 'paid', '', 'cc', '18.00', 'EUR', '872', '872', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('29', '23409', '137201855', 'INV_873_0zds_2ST', '53382373', '', '1401584483', '1401580800', '1401580800', '219632', 'paid', '', 'cc', '3.00', 'EUR', '873', '873', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('30', '23409', '137201857', 'INV_874_xEic_2ST', '53382375', '', '1401584485', '1401580800', '1401580800', 'T00691', 'paid', '', 'cc', '7.00', 'EUR', '874', '874', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('31', '23409', '137201859', 'INV_876_TRSU_2ST', '53382376', '', '1401584486', '1401580800', '1401580800', '301227', 'paid', '', 'cc', '10.00', 'EUR', '876', '876', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('32', '23409', '137201860', 'INV_878_yZiA_2ST', '53382377', '', '1401584488', '1401580800', '1401580800', '012807', 'paid', '', 'cc', '12.00', 'EUR', '878', '878', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('33', '23409', '137201861', 'INV_882_8Hvt_2ST', '53382378', '', '1401584490', '1401580800', '1401580800', '220484', 'paid', '', 'cc', '10.00', 'EUR', '882', '882', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('34', '23409', '137201862', 'INV_889_7Gnw_2ST', '53382379', '', '1401584491', '1401580800', '1401580800', '039139', 'paid', '', 'cc', '1.00', 'EUR', '889', '889', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('35', '23409', '137201863', 'INV_901_dBBy_2ST', '53382380', '', '1401584493', '1401580800', '1401580800', '001583', 'paid', '', 'cc', '3.00', 'EUR', '901', '901', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('36', '23409', '137201865', 'INV_904_XnzS_2ST', '53382381', '', '1401584495', '1401580800', '1401580800', '122539', 'paid', '', 'cc', '2.00', 'EUR', '904', '904', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('37', '23409', '137201867', 'INV_913_RB8T_2ST', '53382383', '', '1401584498', '1401580800', '1401580800', '2CMLV0', 'paid', '', 'cc', '1.00', 'EUR', '913', '913', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('38', '23409', '137201869', 'INV_915_oTGr_2ST', '53382385', '', '1401584500', '1401580800', '1401580800', '965661', 'paid', '', 'cc', '0.00', 'EUR', '915', '915', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('39', '23409', '137201871', 'INV_917_lgRu_2ST', '53382386', '', '1401584502', '1401580800', '1401580800', '836791', 'paid', '', 'cc', '3.00', 'EUR', '917', '917', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('40', '23409', '139241351', 'INV_930_1ST', '54366922', '', '1404176407', '1404172800', '1404172800', '286345', 'paid', '', 'cc', '8.00', 'EUR', '930', '930', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('41', '23409', '139241353', 'INV_931_1ST', '54366924', '', '1404176409', '1404172800', '1404172800', '103608', 'paid', '', 'cc', '4.00', 'EUR', '931', '931', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('42', '23409', '139241356', 'INV_932_1ST', '54366925', '', '1404176410', '1404172800', '1404172800', '367283', 'paid', '', 'cc', '8.00', 'EUR', '932', '932', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('43', '23409', '139241358', 'INV_933_1ST', '54366926', '', '1404176411', '1404172800', '1404172800', '432665', 'paid', '', 'cc', '9.00', 'EUR', '933', '933', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('44', '23409', '139241362', 'INV_935_1ST', '54366928', '', '1404176413', '1404172800', '1404172800', '092873', 'paid', '', 'cc', '9.00', 'EUR', '935', '935', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('45', '23409', '139241369', 'INV_936_1ST', '54366929', '', '1404176417', '1404172800', '1404172800', 'T08435', 'paid', '', 'cc', '9.00', 'EUR', '936', '936', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('46', '23409', '139241381', 'INV_938_1ST', '54366932', '', '1404176420', '1404172800', '1404172800', '002822', 'paid', '', 'cc', '9.00', 'EUR', '938', '938', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('47', '23409', '139241383', 'INV_939_1ST', '54366934', '', '1404176421', '1404172800', '1404172800', '006697', 'paid', '', 'cc', '8.00', 'EUR', '939', '939', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('48', '23409', '139241385', 'INV_940_1ST', '54366935', '', '1404176423', '1404172800', '1404172800', '002279', 'paid', '', 'cc', '9.00', 'EUR', '940', '940', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('49', '23409', '139241387', 'INV_941_1ST', '54366937', '', '1404176424', '1404172800', '1404172800', '785428', 'paid', '', 'cc', '9.00', 'EUR', '941', '941', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('50', '23409', '139241392', 'INV_945_1ST', '54366941', '', '1404176429', '1404172800', '1404172800', '088075', 'paid', '', 'cc', '9.00', 'EUR', '945', '945', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('51', '23409', '139241394', 'INV_946_1ST', '54366943', '', '1404176431', '1404172800', '1404172800', '009160', 'paid', '', 'cc', '9.00', 'EUR', '946', '946', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('52', '23409', '139241397', 'INV_948_1ST', '54366946', '', '1404176433', '1404172800', '1404172800', '504210', 'paid', '', 'cc', '4.00', 'EUR', '948', '948', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('53', '23409', '139241400', 'INV_950_1ST', '54366948', '', '1404176435', '1404172800', '1404172800', '068440', 'paid', '', 'cc', '4.00', 'EUR', '950', '950', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('54', '23409', '139241401', 'INV_951_1ST', '54366949', '', '1404176436', '1404172800', '1404172800', '208834', 'paid', '', 'cc', '4.00', 'EUR', '951', '951', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('55', '23409', '139241402', 'INV_952_1ST', '54366950', '', '1404176439', '1404172800', '1404172800', '059921', 'paid', '', 'cc', '8.00', 'EUR', '952', '952', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('56', '23409', '139241404', 'INV_953_1ST', '54366952', '', '1404176441', '1404172800', '1404172800', '241000', 'paid', '', 'cc', '4.00', 'EUR', '953', '953', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('57', '23409', '139241406', 'INV_961_1ST', '54366954', '', '1404176445', '1404172800', '1404172800', '093446', 'paid', '', 'cc', '16.00', 'EUR', '961', '961', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('58', '23409', '139241417', 'INV_1342_1ST', '54366958', '', '1404176452', '1404172800', '1404172800', '004299', 'paid', '', 'cc', '4.00', 'EUR', '1342', '1342', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('59', '23409', '139241421', 'INV_1378_1ST', '54366960', '', '1404176454', '1404172800', '1404172800', '005397', 'paid', '', 'cc', '4.00', 'EUR', '1378', '1378', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('60', '23409', '139241422', 'INV_1387_1ST', '54366961', '', '1404176456', '1404172800', '1404172800', '074245', 'paid', '', 'cc', '4.00', 'EUR', '1387', '1387', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('61', '23409', '139241424', 'INV_1452_1ST', '54366963', '', '1404176460', '1404172800', '1404172800', '510777', 'paid', '', 'cc', '4.00', 'EUR', '1452', '1452', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('62', '23409', '139241425', 'INV_933_iJ0f_2ST', '54366964', '', '1404176462', '1404172800', '1404172800', '811387', 'paid', '', 'cc', '22.00', 'EUR', '933', '933', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('63', '23409', '139241426', 'INV_936_UEd5_2ST', '54366965', '', '1404176465', '1404172800', '1404172800', 'T08481', 'paid', '', 'cc', '9.00', 'EUR', '936', '936', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('64', '23409', '139241429', 'INV_954_vAXV_2ST', '54366967', '', '1404176469', '1404172800', '1404172800', '27238B', 'paid', '', 'cc', '9.00', 'EUR', '954', '954', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('65', '23409', '139241433', 'INV_956_LFdH_2ST', '54366969', '', '1404176472', '1404172800', '1404172800', '504223', 'paid', '', 'cc', '0.00', 'EUR', '956', '956', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('66', '23409', '139241435', 'INV_959_Lqr4_2ST', '54366971', '', '1404176476', '1404172800', '1404172800', '301120', 'paid', '', 'cc', '6.00', 'EUR', '959', '959', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('67', '23409', '139241437', 'INV_961_G7KT_2ST', '54366972', '', '1404176477', '1404172800', '1404172800', '094115', 'paid', '', 'cc', '0.00', 'EUR', '961', '961', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('68', '23409', '139241438', 'INV_1301_UuvJ_2ST', '54366973', '', '1404176481', '1404172800', '1404172800', '606044', 'paid', '', 'cc', '5.00', 'EUR', '1301', '1301', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('69', '23409', '139241440', 'INV_1393_ep3D_2ST', '54366975', '', '1404176485', '1404172800', '1404172800', '297775', 'paid', '', 'cc', '6.00', 'EUR', '1393', '1393', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('70', '23409', '139241441', 'INV_1402_Wnwo_2ST', '54366976', '', '1404176491', '1404172800', '1404172800', '009033', 'paid', '', 'cc', '11.00', 'EUR', '1402', '1402', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('71', '23409', '139241443', 'INV_1422_VS7q_2ST', '54366978', '', '1404176493', '1404172800', '1404172800', '002813', 'paid', '', 'cc', '5.00', 'EUR', '1422', '1422', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('72', '23409', '126499597', '1389014642_1ST', '48464653', '', '1389018318', '1388966400', '1388966400', '349672', 'paid', '', 'cc', '1.00', 'EUR', null, '014642', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('73', '23409', '128617939', '1391075436_1ST', '49407734', '', '1391079121', '1391040000', '1391040000', '566020', 'paid', '', 'cc', '1.00', 'EUR', null, '075436', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('74', '23409', '128702106', '1391171140_1ST', '49445025', '', '1391174743', '1391126400', '1391126400', '973775', 'paid', '', 'cc', '1.00', 'EUR', null, '171140', null, null);
INSERT INTO `payone_transaction_hist` VALUES ('75', null, null, null, null, null, '0', '0', '0', null, null, null, null, null, null, null, null, null, null);
