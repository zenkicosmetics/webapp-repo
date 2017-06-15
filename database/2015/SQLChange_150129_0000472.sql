/*
Navicat MySQL Data Transfer
Source Host     : localhost:3306
Source Database : clevvermail01
Target Host     : localhost:3306
Target Database : clevvermail01
Date: 2015-03-13 09:14:32
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for vat_case
-- ----------------------------
DROP TABLE IF EXISTS `vat_case`;
CREATE TABLE `vat_case` (
  `vat_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_type` varchar(50) NOT NULL COMMENT 'digital good|local service|shipping',
  `customer_type` varchar(20) NOT NULL DEFAULT '0',
  `vat_case_id` int(11) DEFAULT NULL,
  `rate` float DEFAULT '0',
  `text` varchar(500) DEFAULT NULL,
  `baseon_country_id` int(250) DEFAULT NULL,
  PRIMARY KEY (`vat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8;

-- ----------------------------
-- alter table invoice_detail: added new column
-- ----------------------------
ALTER TABLE `invoice_detail`
	ADD COLUMN `invoice_summary_id` BIGINT NULL AFTER `envelope_id`;

-- ----------------------------
-- Records of vat_case
-- ----------------------------
INSERT INTO `vat_case` VALUES ('2', 'local service', 'private', '38', '0.19', 'Germany', '282');
INSERT INTO `vat_case` VALUES ('3', 'local service', 'private', '38', '0.19', 'Austria', '220');
INSERT INTO `vat_case` VALUES ('4', 'local service', 'private', '38', '0.19', 'Azores', null);
INSERT INTO `vat_case` VALUES ('5', 'local service', 'private', '38', '0.19', 'Belgium', '227');
INSERT INTO `vat_case` VALUES ('6', 'local service', 'private', '38', '0.19', 'Bulgaria', '238');
INSERT INTO `vat_case` VALUES ('7', 'local service', 'private', '38', '0.19', 'Canary Islands', null);
INSERT INTO `vat_case` VALUES ('8', 'local service', 'private', '38', '0.19', 'Croatia', '258');
INSERT INTO `vat_case` VALUES ('9', 'local service', 'private', '38', '0.19', 'Cyprus', '260');
INSERT INTO `vat_case` VALUES ('10', 'local service', 'private', '38', '0.19', 'Czech Republic', '261');
INSERT INTO `vat_case` VALUES ('11', 'local service', 'private', '38', '0.19', 'Denmark', '262');
INSERT INTO `vat_case` VALUES ('12', 'local service', 'private', '38', '0.19', 'Estonia', '271');
INSERT INTO `vat_case` VALUES ('13', 'local service', 'private', '38', '0.19', 'Finland', '276');
INSERT INTO `vat_case` VALUES ('14', 'local service', 'private', '38', '0.19', 'France', '277');
INSERT INTO `vat_case` VALUES ('15', 'local service', 'private', '38', '0.19', 'Greece', '285');
INSERT INTO `vat_case` VALUES ('16', 'local service', 'private', '38', '0.19', 'Hungary', '298');
INSERT INTO `vat_case` VALUES ('17', 'local service', 'private', '38', '0.19', 'Ireland', '304');
INSERT INTO `vat_case` VALUES ('18', 'local service', 'private', '38', '0.19', 'Isle of Man', null);
INSERT INTO `vat_case` VALUES ('19', 'local service', 'private', '38', '0.19', 'Italy', '306');
INSERT INTO `vat_case` VALUES ('20', 'local service', 'private', '38', '0.19', 'Latvia', '317');
INSERT INTO `vat_case` VALUES ('21', 'local service', 'private', '38', '0.19', 'Lithuania', '323');
INSERT INTO `vat_case` VALUES ('22', 'local service', 'private', '38', '0.19', 'Luxembourg', '324');
INSERT INTO `vat_case` VALUES ('23', 'local service', 'private', '38', '0.19', 'Madeira', null);
INSERT INTO `vat_case` VALUES ('24', 'local service', 'private', '38', '0.19', 'Malta', '332');
INSERT INTO `vat_case` VALUES ('25', 'local service', 'private', '38', '0.19', 'Netherlands', '351');
INSERT INTO `vat_case` VALUES ('26', 'local service', 'private', '38', '0.19', 'Poland', '372');
INSERT INTO `vat_case` VALUES ('27', 'local service', 'private', '38', '0.19', 'Portugal', '374');
INSERT INTO `vat_case` VALUES ('28', 'local service', 'private', '38', '0.19', 'Romania', '378');
INSERT INTO `vat_case` VALUES ('29', 'local service', 'private', '38', '0.19', 'Slovakia', '395');
INSERT INTO `vat_case` VALUES ('30', 'local service', 'private', '38', '0.19', 'Slovenia', '396');
INSERT INTO `vat_case` VALUES ('31', 'local service', 'private', '38', '0.19', 'Spain', '403');
INSERT INTO `vat_case` VALUES ('32', 'local service', 'private', '38', '0.19', 'Sweden', '409');
INSERT INTO `vat_case` VALUES ('33', 'local service', 'private', '38', '0.19', 'United Kingdom', '429');
INSERT INTO `vat_case` VALUES ('34', 'local service', 'private', '38', '0.19', 'Gibraltar', '284');
INSERT INTO `vat_case` VALUES ('35', 'local service', 'private', '38', '0.19', 'all other countries', null);

INSERT INTO `vat_case` VALUES ('36', 'local service', 'enterprise', '39', '0.19', 'Germany', '282');
INSERT INTO `vat_case` VALUES ('37', 'local service', 'enterprise', '40', '0', 'Austria', '220');
INSERT INTO `vat_case` VALUES ('38', 'local service', 'enterprise', '40', '0', 'Azores', null);
INSERT INTO `vat_case` VALUES ('39', 'local service', 'enterprise', '40', '0', 'Belgium', '227');
INSERT INTO `vat_case` VALUES ('40', 'local service', 'enterprise', '40', '0', 'Bulgaria', '238');
INSERT INTO `vat_case` VALUES ('41', 'local service', 'enterprise', '40', '0', 'Canary Islands', null);
INSERT INTO `vat_case` VALUES ('42', 'local service', 'enterprise', '40', '0', 'Croatia', '258');
INSERT INTO `vat_case` VALUES ('43', 'local service', 'enterprise', '40', '0', 'Cyprus', '260');
INSERT INTO `vat_case` VALUES ('44', 'local service', 'enterprise', '40', '0', 'Czech Republic', '261');
INSERT INTO `vat_case` VALUES ('45', 'local service', 'enterprise', '40', '0', 'Denmark', '262');
INSERT INTO `vat_case` VALUES ('46', 'local service', 'enterprise', '40', '0', 'Estonia', '271');
INSERT INTO `vat_case` VALUES ('47', 'local service', 'enterprise', '40', '0', 'Finland', '276');
INSERT INTO `vat_case` VALUES ('48', 'local service', 'enterprise', '40', '0', 'France', '277');
INSERT INTO `vat_case` VALUES ('49', 'local service', 'enterprise', '40', '0', 'Greece', '285');
INSERT INTO `vat_case` VALUES ('50', 'local service', 'enterprise', '40', '0', 'Hungary', '298');
INSERT INTO `vat_case` VALUES ('51', 'local service', 'enterprise', '40', '0', 'Ireland', '304');
INSERT INTO `vat_case` VALUES ('52', 'local service', 'enterprise', '40', '0', 'Isle of Man', null);
INSERT INTO `vat_case` VALUES ('53', 'local service', 'enterprise', '40', '0', 'Italy', '306');
INSERT INTO `vat_case` VALUES ('54', 'local service', 'enterprise', '40', '0', 'Latvia', '317');
INSERT INTO `vat_case` VALUES ('55', 'local service', 'enterprise', '40', '0', 'Lithuania', '323');
INSERT INTO `vat_case` VALUES ('56', 'local service', 'enterprise', '40', '0', 'Luxembourg', '324');
INSERT INTO `vat_case` VALUES ('57', 'local service', 'enterprise', '40', '0', 'Madeira', null);
INSERT INTO `vat_case` VALUES ('58', 'local service', 'enterprise', '40', '0', 'Malta', '332');
INSERT INTO `vat_case` VALUES ('59', 'local service', 'enterprise', '40', '0', 'Netherlands', '351');
INSERT INTO `vat_case` VALUES ('60', 'local service', 'enterprise', '40', '0', 'Poland', '372');
INSERT INTO `vat_case` VALUES ('61', 'local service', 'enterprise', '40', '0', 'Portugal', '374');
INSERT INTO `vat_case` VALUES ('62', 'local service', 'enterprise', '40', '0', 'Romania', '378');
INSERT INTO `vat_case` VALUES ('63', 'local service', 'enterprise', '40', '0', 'Slovakia', '395');
INSERT INTO `vat_case` VALUES ('64', 'local service', 'enterprise', '40', '0', 'Slovenia', '396');
INSERT INTO `vat_case` VALUES ('65', 'local service', 'enterprise', '40', '0', 'Spain', '403');
INSERT INTO `vat_case` VALUES ('66', 'local service', 'enterprise', '40', '0', 'Sweden', '409');
INSERT INTO `vat_case` VALUES ('67', 'local service', 'enterprise', '40', '0', 'United Kingdom', '429');
INSERT INTO `vat_case` VALUES ('68', 'local service', 'enterprise', '40', '0', 'Gibraltar', '284');
INSERT INTO `vat_case` VALUES ('69', 'local service', 'enterprise', '41', '0', 'all other countries', '0');
INSERT INTO `vat_case` VALUES ('70', 'shipping', 'private', '42', '0.19', '1.Lieferung von Deutschland nach Deutschland', '1');
INSERT INTO `vat_case` VALUES ('71', 'shipping', 'private', '42', '0.19', '2.Lieferung von European Union nach European Union', '2');
INSERT INTO `vat_case` VALUES ('72', 'shipping', 'private', '42', '0.19', '3.Lieferung von Drittland nach Drittland', '3');
INSERT INTO `vat_case` VALUES ('73', 'shipping', 'private', '42', '0.19', '4.Lieferung von European Union nach Deutschland', '4');
INSERT INTO `vat_case` VALUES ('74', 'shipping', 'private', '42', '0.19', '5.Lieferung von Deutschland nach European Union', '5');
INSERT INTO `vat_case` VALUES ('75', 'shipping', 'private', '42', '0.19', '6.Lieferung von European Union nach Drittland', '6');
INSERT INTO `vat_case` VALUES ('76', 'shipping', 'private', '42', '0.19', '7.Lieferung von Drittland nach European Union', '7');
INSERT INTO `vat_case` VALUES ('77', 'shipping', 'private', '42', '0.19', '8.Lieferung von Deutschland nach Drittland', '8');
INSERT INTO `vat_case` VALUES ('78', 'shipping', 'private', '42', '0.19', '9.Lieferung von Drittland nach Deutschland', '9');
INSERT INTO `vat_case` VALUES ('79', 'shipping', 'enterprise', '43', '0.19', '1.Lieferung von Deutschland nach Deutschland', '1');
INSERT INTO `vat_case` VALUES ('80', 'shipping', 'enterprise', '44', '0', '2.Lieferung von European Union nach European Union', '2');
INSERT INTO `vat_case` VALUES ('81', 'shipping', 'enterprise', '45', '0', '3.Lieferung von Drittland nach Drittland', '3');
INSERT INTO `vat_case` VALUES ('82', 'shipping', 'enterprise', '46', '0.19', '4.Lieferung von European Union nach Deutschland', '4');
INSERT INTO `vat_case` VALUES ('83', 'shipping', 'enterprise', '47', '0', '5.Lieferung von Deutschland nach European Union', '5');
INSERT INTO `vat_case` VALUES ('84', 'shipping', 'enterprise', '48', '0', '6.Lieferung von European Union nach Drittland', '6');
INSERT INTO `vat_case` VALUES ('85', 'shipping', 'enterprise', '49', '0', '7.Lieferung von Drittland nach European Union', '7');
INSERT INTO `vat_case` VALUES ('86', 'shipping', 'enterprise', '50', '0', '8.Lieferung von Deutschland nach Drittland', '8');
INSERT INTO `vat_case` VALUES ('87', 'shipping', 'enterprise', '51', '0.19', '9.Lieferung von Drittland nach Deutschland', '9');


INSERT INTO `vat_case` VALUES ('88', 'digital goods', 'private', '1', '0.19', 'Germany', '282');
INSERT INTO `vat_case` VALUES ('89', 'digital goods', 'private', '2', '0.20', 'Austria', '220');
INSERT INTO `vat_case` VALUES ('90', 'digital goods', 'private', '3', '0.18', 'Azores', null);
INSERT INTO `vat_case` VALUES ('91', 'digital goods', 'private', '4', '0.21', 'Belgium', '227');
INSERT INTO `vat_case` VALUES ('92', 'digital goods', 'private', '5', '0.20', 'Bulgaria', '238');
INSERT INTO `vat_case` VALUES ('93', 'digital goods', 'private', '6', '0.0', 'Canary Islands', null);
INSERT INTO `vat_case` VALUES ('94', 'digital goods', 'private', '7', '0.25', 'Croatia', '258');
INSERT INTO `vat_case` VALUES ('95', 'digital goods', 'private', '8', '0.19', 'Cyprus', '260');
INSERT INTO `vat_case` VALUES ('96', 'digital goods', 'private', '9', '0.21', 'Czech Republic', '261');
INSERT INTO `vat_case` VALUES ('97', 'digital goods', 'private', '10', '0.25', 'Denmark', '262');
INSERT INTO `vat_case` VALUES ('98', 'digital goods', 'private', '11', '0.20', 'Estonia', '271');
INSERT INTO `vat_case` VALUES ('99', 'digital goods', 'private', '12', '0.24', 'Finland', '276');
INSERT INTO `vat_case` VALUES ('100', 'digital goods', 'private', '13', '0.20', 'France', '277');
INSERT INTO `vat_case` VALUES ('101', 'digital goods', 'private', '14', '0.23', 'Greece', '285');
INSERT INTO `vat_case` VALUES ('102', 'digital goods', 'private', '15', '0.27', 'Hungary', '298');
INSERT INTO `vat_case` VALUES ('103', 'digital goods', 'private', '16', '0.23', 'Ireland', '304');
INSERT INTO `vat_case` VALUES ('104', 'digital goods', 'private', '17', '0', 'Isle of Man', null);
INSERT INTO `vat_case` VALUES ('105', 'digital goods', 'private', '18', '0.22', 'Italy', '306');
INSERT INTO `vat_case` VALUES ('106', 'digital goods', 'private', '19', '0.21', 'Latvia', '317');
INSERT INTO `vat_case` VALUES ('107', 'digital goods', 'private', '20', '0.21', 'Lithuania', '323');
INSERT INTO `vat_case` VALUES ('108', 'digital goods', 'private', '21', '0.17', 'Luxembourg', '324');
INSERT INTO `vat_case` VALUES ('109', 'digital goods', 'private', '22', '0.22', 'Madeira', null);
INSERT INTO `vat_case` VALUES ('110', 'digital goods', 'private', '23', '0.18', 'Malta', '332');
INSERT INTO `vat_case` VALUES ('111', 'digital goods', 'private', '24', '0.21', 'Netherlands', '351');
INSERT INTO `vat_case` VALUES ('112', 'digital goods', 'private', '25', '0.23', 'Poland', '372');
INSERT INTO `vat_case` VALUES ('113', 'digital goods', 'private', '26', '0.23', 'Portugal', '374');
INSERT INTO `vat_case` VALUES ('114', 'digital goods', 'private', '27', '0.24', 'Romania', '378');
INSERT INTO `vat_case` VALUES ('115', 'digital goods', 'private', '28', '0.20', 'Slovakia', '395');
INSERT INTO `vat_case` VALUES ('116', 'digital goods', 'private', '29', '0.22', 'Slovenia', '396');
INSERT INTO `vat_case` VALUES ('117', 'digital goods', 'private', '30', '0.21', 'Spain', '403');
INSERT INTO `vat_case` VALUES ('118', 'digital goods', 'private', '31', '0.25', 'Sweden', '409');
INSERT INTO `vat_case` VALUES ('119', 'digital goods', 'private', '32', '0.20', 'United Kingdom', '429');
INSERT INTO `vat_case` VALUES ('200', 'digital goods', 'private', '33', '0', 'Gibraltar', '284');
INSERT INTO `vat_case` VALUES ('201', 'digital goods', 'private', '34', '0', 'all other countries', null);


INSERT INTO `vat_case` VALUES ('202', 'digital goods', 'enterprise', '35', '0.19', 'Germany', '282');
INSERT INTO `vat_case` VALUES ('203', 'digital goods', 'enterprise', '36', '0', 'Austria', '220');
INSERT INTO `vat_case` VALUES ('204', 'digital goods', 'enterprise', '36', '0','Azores', null);
INSERT INTO `vat_case` VALUES ('205', 'digital goods', 'enterprise', '36', '0', 'Belgium', '227');
INSERT INTO `vat_case` VALUES ('206', 'digital goods', 'enterprise', '36', '0', 'Bulgaria', '238');
INSERT INTO `vat_case` VALUES ('207', 'digital goods', 'enterprise', '36', '0', 'Canary Islands', null);
INSERT INTO `vat_case` VALUES ('208', 'digital goods', 'enterprise', '36', '0', 'Croatia', '258');
INSERT INTO `vat_case` VALUES ('209', 'digital goods', 'enterprise', '36', '0', 'Cyprus', '260');
INSERT INTO `vat_case` VALUES ('210', 'digital goods', 'enterprise', '36', '0', 'Czech Republic', '261');
INSERT INTO `vat_case` VALUES ('211', 'digital goods', 'enterprise', '36', '0', 'Denmark', '262');
INSERT INTO `vat_case` VALUES ('212', 'digital goods', 'enterprise', '36', '0', 'Estonia', '271');
INSERT INTO `vat_case` VALUES ('213', 'digital goods', 'enterprise', '36', '0', 'Finland', '276');
INSERT INTO `vat_case` VALUES ('214', 'digital goods', 'enterprise', '36', '0', 'France', '277');
INSERT INTO `vat_case` VALUES ('215', 'digital goods', 'enterprise', '36', '0', 'Greece', '285');
INSERT INTO `vat_case` VALUES ('216', 'digital goods', 'enterprise', '36', '0', 'Hungary', '298');
INSERT INTO `vat_case` VALUES ('217', 'digital goods', 'enterprise', '36', '0', 'Ireland', '304');
INSERT INTO `vat_case` VALUES ('218', 'digital goods', 'enterprise', '36', '0', 'Isle of Man', null);
INSERT INTO `vat_case` VALUES ('219', 'digital goods', 'enterprise', '36', '0', 'Italy', '306');
INSERT INTO `vat_case` VALUES ('220', 'digital goods', 'enterprise', '36', '0', 'Latvia', '317');
INSERT INTO `vat_case` VALUES ('221', 'digital goods', 'enterprise', '36', '0', 'Lithuania', '323');
INSERT INTO `vat_case` VALUES ('222', 'digital goods', 'enterprise', '36', '0', 'Luxembourg', '324');
INSERT INTO `vat_case` VALUES ('223', 'digital goods', 'enterprise', '36', '0', 'Madeira', null);
INSERT INTO `vat_case` VALUES ('224', 'digital goods', 'enterprise', '36', '0', 'Malta', '332');
INSERT INTO `vat_case` VALUES ('225', 'digital goods', 'enterprise', '36', '0', 'Netherlands', '351');
INSERT INTO `vat_case` VALUES ('226', 'digital goods', 'enterprise', '36', '0', 'Poland', '372');
INSERT INTO `vat_case` VALUES ('227', 'digital goods', 'enterprise', '36', '0', 'Portugal', '374');
INSERT INTO `vat_case` VALUES ('228', 'digital goods', 'enterprise', '36', '0', 'Romania', '378');
INSERT INTO `vat_case` VALUES ('229', 'digital goods', 'enterprise', '36', '0', 'Slovakia', '395');
INSERT INTO `vat_case` VALUES ('230', 'digital goods', 'enterprise', '36', '0', 'Slovenia', '396');
INSERT INTO `vat_case` VALUES ('231', 'digital goods', 'enterprise', '36', '0', 'Spain', '403');
INSERT INTO `vat_case` VALUES ('232', 'digital goods', 'enterprise', '36', '0', 'Sweden', '409');
INSERT INTO `vat_case` VALUES ('233', 'digital goods', 'enterprise', '36', '0', 'United Kingdom', '429');
INSERT INTO `vat_case` VALUES ('234', 'digital goods', 'enterprise', '36', '0', 'Gibraltar', '284');
INSERT INTO `vat_case` VALUES ('235', 'digital goods', 'enterprise', '37', '0', 'all other countries', null);
