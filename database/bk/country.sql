/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50614
Source Host           : localhost:3306
Source Database       : virtual_post

Target Server Type    : MYSQL
Target Server Version : 50614
File Encoding         : 65001

Date: 2014-07-28 20:19:00
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `country`
-- ----------------------------
DROP TABLE IF EXISTS `country`;
CREATE TABLE `country` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_code` varchar(11) DEFAULT NULL,
  `country_name` varchar(50) DEFAULT NULL,
  `eu_member_flag` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=441 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of country
-- ----------------------------
INSERT INTO `country` VALUES ('208', 'AF', 'Afghanistan', '0');
INSERT INTO `country` VALUES ('209', 'AL', 'Albania', '0');
INSERT INTO `country` VALUES ('210', 'DZ', 'Algeria', '0');
INSERT INTO `country` VALUES ('211', 'AS', 'American Samoa', '0');
INSERT INTO `country` VALUES ('212', 'AD', 'Andorra', '0');
INSERT INTO `country` VALUES ('213', 'AO', 'Angola', '0');
INSERT INTO `country` VALUES ('214', 'AI', 'Anguilla', '0');
INSERT INTO `country` VALUES ('215', 'AG', 'Antigua and Barbuda', '0');
INSERT INTO `country` VALUES ('216', 'AR', 'Argentina', '0');
INSERT INTO `country` VALUES ('217', 'AM', 'Armenia', '0');
INSERT INTO `country` VALUES ('218', 'AW', 'Aruba', '0');
INSERT INTO `country` VALUES ('219', 'AU', 'Australia', '0');
INSERT INTO `country` VALUES ('220', 'AT', 'Austria', '1');
INSERT INTO `country` VALUES ('221', 'AZ', 'Azerbaijan', '0');
INSERT INTO `country` VALUES ('222', 'BS', 'Bahamas', '0');
INSERT INTO `country` VALUES ('223', 'BH', 'Bahrain', '0');
INSERT INTO `country` VALUES ('224', 'BD', 'Bangladesh', '0');
INSERT INTO `country` VALUES ('225', 'BB', 'Barbados', '0');
INSERT INTO `country` VALUES ('226', 'BY', 'Belarus', '0');
INSERT INTO `country` VALUES ('227', 'BE', 'Belgium', '1');
INSERT INTO `country` VALUES ('228', 'BZ', 'Belize', '0');
INSERT INTO `country` VALUES ('229', 'BJ', 'Benin', '0');
INSERT INTO `country` VALUES ('230', 'BM', 'Bermuda', '0');
INSERT INTO `country` VALUES ('231', 'BT', 'Bhutan', '0');
INSERT INTO `country` VALUES ('232', 'BO', 'Bolivia', '0');
INSERT INTO `country` VALUES ('233', 'BA', 'Bosnia-Herzegovina', '0');
INSERT INTO `country` VALUES ('234', 'BW', 'Botswana', '0');
INSERT INTO `country` VALUES ('235', '', 'Bouvet Island', '0');
INSERT INTO `country` VALUES ('236', 'BR', 'Brazil', '0');
INSERT INTO `country` VALUES ('237', 'BN', 'Brunei', '0');
INSERT INTO `country` VALUES ('238', 'BG', 'Bulgaria', '1');
INSERT INTO `country` VALUES ('239', 'BF', 'Burkina Faso', '0');
INSERT INTO `country` VALUES ('240', 'BI', 'Burundi', '0');
INSERT INTO `country` VALUES ('241', 'KH', 'Cambodia', '0');
INSERT INTO `country` VALUES ('242', 'CM', 'Cameroon', '0');
INSERT INTO `country` VALUES ('243', 'CA', 'Canada', '0');
INSERT INTO `country` VALUES ('244', 'CV', 'Cape Verde', '0');
INSERT INTO `country` VALUES ('245', 'KY', 'Cayman Islands', '0');
INSERT INTO `country` VALUES ('246', 'CF', 'Central African Republic', '0');
INSERT INTO `country` VALUES ('247', 'TD', 'Chad', '0');
INSERT INTO `country` VALUES ('248', 'CL', 'Chile', '0');
INSERT INTO `country` VALUES ('249', 'CN', 'China', '0');
INSERT INTO `country` VALUES ('250', '', 'Christmas Island', '0');
INSERT INTO `country` VALUES ('251', 'CC', 'Cocos (Keeling) Islands', '0');
INSERT INTO `country` VALUES ('252', 'CO', 'Colombia', '0');
INSERT INTO `country` VALUES ('253', 'KM', 'Comoros', '0');
INSERT INTO `country` VALUES ('254', 'CD', 'Congo, Democratic Republic of the (Zaire)', '0');
INSERT INTO `country` VALUES ('255', 'CG', 'Congo, Republic of', '0');
INSERT INTO `country` VALUES ('256', 'CK', 'Cook Islands', '0');
INSERT INTO `country` VALUES ('257', 'CR', 'Costa Rica', '0');
INSERT INTO `country` VALUES ('258', 'HR', 'Croatia', '1');
INSERT INTO `country` VALUES ('259', 'CU', 'Cuba', '0');
INSERT INTO `country` VALUES ('260', 'CY', 'Cyprus', '1');
INSERT INTO `country` VALUES ('261', 'CZ', 'Czech Republic', '1');
INSERT INTO `country` VALUES ('262', 'DK', 'Denmark', '1');
INSERT INTO `country` VALUES ('263', 'DJ', 'Djibouti', '0');
INSERT INTO `country` VALUES ('264', 'DM', 'Dominica', '0');
INSERT INTO `country` VALUES ('265', 'DO', 'Dominican Republic', '0');
INSERT INTO `country` VALUES ('266', 'EC', 'Ecuador', '0');
INSERT INTO `country` VALUES ('267', 'EG', 'Egypt', '0');
INSERT INTO `country` VALUES ('268', 'SV', 'El Salvador', '0');
INSERT INTO `country` VALUES ('269', 'GQ', 'Equatorial Guinea', '0');
INSERT INTO `country` VALUES ('270', 'ER', 'Eritrea', '0');
INSERT INTO `country` VALUES ('271', 'EE', 'Estonia', '1');
INSERT INTO `country` VALUES ('272', 'ET', 'Ethiopia', '0');
INSERT INTO `country` VALUES ('273', 'FK', 'Falkland Islands', '0');
INSERT INTO `country` VALUES ('274', 'FO', 'Faroe Islands', '0');
INSERT INTO `country` VALUES ('275', 'FJ', 'Fiji', '0');
INSERT INTO `country` VALUES ('276', 'FI', 'Finland', '1');
INSERT INTO `country` VALUES ('277', 'FR', 'France', '1');
INSERT INTO `country` VALUES ('278', 'GF', 'French Guiana', '0');
INSERT INTO `country` VALUES ('279', 'GA', 'Gabon', '0');
INSERT INTO `country` VALUES ('280', 'GM', 'Gambia', '0');
INSERT INTO `country` VALUES ('281', 'GE', 'Georgia', '0');
INSERT INTO `country` VALUES ('282', 'DE', 'Germany', '1');
INSERT INTO `country` VALUES ('283', 'GH', 'Ghana', '0');
INSERT INTO `country` VALUES ('284', 'GI', 'Gibraltar', '0');
INSERT INTO `country` VALUES ('285', 'GR', 'Greece', '1');
INSERT INTO `country` VALUES ('286', 'GL', 'Greenland', '0');
INSERT INTO `country` VALUES ('287', 'GD', 'Grenada', '0');
INSERT INTO `country` VALUES ('288', 'GP', 'Guadeloupe (French)', '0');
INSERT INTO `country` VALUES ('289', 'GU', 'Guam (USA)', '0');
INSERT INTO `country` VALUES ('290', 'GT', 'Guatemala', '0');
INSERT INTO `country` VALUES ('291', 'GN', 'Guinea', '0');
INSERT INTO `country` VALUES ('292', 'GW', 'Guinea Bissau', '0');
INSERT INTO `country` VALUES ('293', 'GY', 'Guyana', '0');
INSERT INTO `country` VALUES ('294', 'HT', 'Haiti', '0');
INSERT INTO `country` VALUES ('295', 'VA', 'Holy See', '0');
INSERT INTO `country` VALUES ('296', 'HN', 'Honduras', '0');
INSERT INTO `country` VALUES ('297', 'HK', 'Hong Kong', '0');
INSERT INTO `country` VALUES ('298', 'HU', 'Hungary', '1');
INSERT INTO `country` VALUES ('299', 'IS', 'Iceland', '0');
INSERT INTO `country` VALUES ('300', 'IN', 'India', '0');
INSERT INTO `country` VALUES ('301', 'ID', 'Indonesia', '0');
INSERT INTO `country` VALUES ('302', 'IR', 'Iran', '0');
INSERT INTO `country` VALUES ('303', 'IQ', 'Iraq', '0');
INSERT INTO `country` VALUES ('304', 'IE', 'Ireland', '1');
INSERT INTO `country` VALUES ('305', 'IL', 'Israel', '0');
INSERT INTO `country` VALUES ('306', 'IT', 'Italy', '1');
INSERT INTO `country` VALUES ('307', '', 'Ivory Coast (Cote D`Ivoire)', '0');
INSERT INTO `country` VALUES ('308', 'JM', 'Jamaica', '0');
INSERT INTO `country` VALUES ('309', 'JP', 'Japan', '0');
INSERT INTO `country` VALUES ('310', 'JO', 'Jordan', '0');
INSERT INTO `country` VALUES ('311', 'KZ', 'Kazakhstan', '0');
INSERT INTO `country` VALUES ('312', 'KE', 'Kenya', '0');
INSERT INTO `country` VALUES ('313', 'KI', 'Kiribati', '0');
INSERT INTO `country` VALUES ('314', 'KW', 'Kuwait', '0');
INSERT INTO `country` VALUES ('315', 'KG', 'Kyrgyzstan', '0');
INSERT INTO `country` VALUES ('316', 'LA', 'Laos', '0');
INSERT INTO `country` VALUES ('317', 'LV', 'Latvia', '1');
INSERT INTO `country` VALUES ('318', 'LB', 'Lebanon', '0');
INSERT INTO `country` VALUES ('319', 'LS', 'Lesotho', '0');
INSERT INTO `country` VALUES ('320', 'LR', 'Liberia', '0');
INSERT INTO `country` VALUES ('321', 'LY', 'Libya', '0');
INSERT INTO `country` VALUES ('322', 'LI', 'Liechtenstein', '0');
INSERT INTO `country` VALUES ('323', 'LT', 'Lithuania', '1');
INSERT INTO `country` VALUES ('324', 'LU', 'Luxembourg', '1');
INSERT INTO `country` VALUES ('325', 'MO', 'Macau', '0');
INSERT INTO `country` VALUES ('326', 'MK', 'Macedonia', '0');
INSERT INTO `country` VALUES ('327', 'MG', 'Madagascar', '0');
INSERT INTO `country` VALUES ('328', 'MW', 'Malawi', '0');
INSERT INTO `country` VALUES ('329', 'MY', 'Malaysia', '0');
INSERT INTO `country` VALUES ('330', 'MV', 'Maldives', '0');
INSERT INTO `country` VALUES ('331', 'ML', 'Mali', '0');
INSERT INTO `country` VALUES ('332', 'MT', 'Malta', '1');
INSERT INTO `country` VALUES ('333', 'MH', 'Marshall Islands', '0');
INSERT INTO `country` VALUES ('334', 'MQ', 'Martinique (French)', '0');
INSERT INTO `country` VALUES ('335', 'MR', 'Mauritania', '0');
INSERT INTO `country` VALUES ('336', 'MU', 'Mauritius', '0');
INSERT INTO `country` VALUES ('337', 'YT', 'Mayotte', '0');
INSERT INTO `country` VALUES ('338', 'MX', 'Mexico', '0');
INSERT INTO `country` VALUES ('339', 'FM', 'Micronesia', '0');
INSERT INTO `country` VALUES ('340', 'MD', 'Moldova', '0');
INSERT INTO `country` VALUES ('341', 'MC', 'Monaco', '0');
INSERT INTO `country` VALUES ('342', 'MN', 'Mongolia', '0');
INSERT INTO `country` VALUES ('343', 'ME', 'Montenegro', '0');
INSERT INTO `country` VALUES ('344', 'MS', 'Montserrat', '0');
INSERT INTO `country` VALUES ('345', 'MA', 'Morocco', '0');
INSERT INTO `country` VALUES ('346', 'MZ', 'Mozambique', '0');
INSERT INTO `country` VALUES ('347', 'MM', 'Myanmar', '0');
INSERT INTO `country` VALUES ('348', 'NA', 'Namibia', '0');
INSERT INTO `country` VALUES ('349', 'NR', 'Nauru', '0');
INSERT INTO `country` VALUES ('350', 'NP', 'Nepal', '0');
INSERT INTO `country` VALUES ('351', 'NL', 'Netherlands', '1');
INSERT INTO `country` VALUES ('352', '', 'Netherlands Antilles', '0');
INSERT INTO `country` VALUES ('353', 'NC', 'New Caledonia (French)', '0');
INSERT INTO `country` VALUES ('354', 'NZ', 'New Zealand', '0');
INSERT INTO `country` VALUES ('355', 'NI', 'Nicaragua', '0');
INSERT INTO `country` VALUES ('356', 'NE', 'Niger', '0');
INSERT INTO `country` VALUES ('357', 'NG', 'Nigeria', '0');
INSERT INTO `country` VALUES ('358', 'NU', 'Niue', '0');
INSERT INTO `country` VALUES ('359', 'NF', 'Norfolk Island', '0');
INSERT INTO `country` VALUES ('360', 'KP', 'North Korea', '0');
INSERT INTO `country` VALUES ('361', 'MP', 'Northern Mariana Islands', '0');
INSERT INTO `country` VALUES ('362', 'NO', 'Norway', '0');
INSERT INTO `country` VALUES ('363', 'OM', 'Oman', '0');
INSERT INTO `country` VALUES ('364', 'PK', 'Pakistan', '0');
INSERT INTO `country` VALUES ('365', 'PW', 'Palau', '0');
INSERT INTO `country` VALUES ('366', 'PA', 'Panama', '0');
INSERT INTO `country` VALUES ('367', 'PG', 'Papua New Guinea', '0');
INSERT INTO `country` VALUES ('368', 'PY', 'Paraguay', '0');
INSERT INTO `country` VALUES ('369', 'PE', 'Peru', '0');
INSERT INTO `country` VALUES ('370', 'PH', 'Philippines', '0');
INSERT INTO `country` VALUES ('371', 'PN', 'Pitcairn Island', '0');
INSERT INTO `country` VALUES ('372', 'PL', 'Poland', '1');
INSERT INTO `country` VALUES ('373', '', 'Polynesia (French)', '0');
INSERT INTO `country` VALUES ('374', 'PT', 'Portugal', '1');
INSERT INTO `country` VALUES ('375', 'PR', 'Puerto Rico', '0');
INSERT INTO `country` VALUES ('376', 'QA', 'Qatar', '0');
INSERT INTO `country` VALUES ('377', 'RE', 'Reunion', '0');
INSERT INTO `country` VALUES ('378', 'RO', 'Romania', '1');
INSERT INTO `country` VALUES ('379', 'RU', 'Russia', '0');
INSERT INTO `country` VALUES ('380', 'RW', 'Rwanda', '0');
INSERT INTO `country` VALUES ('381', 'SH', 'Saint Helena', '0');
INSERT INTO `country` VALUES ('382', 'KN', 'Saint Kitts and Nevis', '0');
INSERT INTO `country` VALUES ('383', 'LC', 'Saint Lucia', '0');
INSERT INTO `country` VALUES ('384', 'PM', 'Saint Pierre and Miquelon', '0');
INSERT INTO `country` VALUES ('385', 'VC', 'Saint Vincent and Grenadines', '0');
INSERT INTO `country` VALUES ('386', 'WS', 'Samoa', '0');
INSERT INTO `country` VALUES ('387', 'SM', 'San Marino', '0');
INSERT INTO `country` VALUES ('388', 'ST', 'Sao Tome and Principe', '0');
INSERT INTO `country` VALUES ('389', 'SA', 'Saudi Arabia', '0');
INSERT INTO `country` VALUES ('390', 'SN', 'Senegal', '0');
INSERT INTO `country` VALUES ('391', 'RS', 'Serbia', '0');
INSERT INTO `country` VALUES ('392', 'SC', 'Seychelles', '0');
INSERT INTO `country` VALUES ('393', 'SL', 'Sierra Leone', '0');
INSERT INTO `country` VALUES ('394', 'SG', 'Singapore', '0');
INSERT INTO `country` VALUES ('395', 'SK', 'Slovakia', '1');
INSERT INTO `country` VALUES ('396', 'SI', 'Slovenia', '1');
INSERT INTO `country` VALUES ('397', 'SB', 'Solomon Islands', '0');
INSERT INTO `country` VALUES ('398', 'SO', 'Somalia', '0');
INSERT INTO `country` VALUES ('399', 'ZA', 'South Africa', '0');
INSERT INTO `country` VALUES ('400', 'GS', 'South Georgia and South Sandwich Islands', '0');
INSERT INTO `country` VALUES ('401', 'KR', 'South Korea', '0');
INSERT INTO `country` VALUES ('402', 'SS', 'South Sudan', '0');
INSERT INTO `country` VALUES ('403', 'ES', 'Spain', '1');
INSERT INTO `country` VALUES ('404', 'LK', 'Sri Lanka', '0');
INSERT INTO `country` VALUES ('405', 'SD', 'Sudan', '0');
INSERT INTO `country` VALUES ('406', 'SR', 'Suriname', '0');
INSERT INTO `country` VALUES ('407', 'SJ', 'Svalbard and Jan Mayen Islands', '0');
INSERT INTO `country` VALUES ('408', 'SZ', 'Swaziland', '0');
INSERT INTO `country` VALUES ('409', 'SE', 'Sweden', '1');
INSERT INTO `country` VALUES ('410', 'CH', 'Switzerland', '0');
INSERT INTO `country` VALUES ('411', 'SY', 'Syria', '0');
INSERT INTO `country` VALUES ('412', 'TW', 'Taiwan', '0');
INSERT INTO `country` VALUES ('413', 'TJ', 'Tajikistan', '0');
INSERT INTO `country` VALUES ('414', 'TZ', 'Tanzania', '0');
INSERT INTO `country` VALUES ('415', 'TH', 'Thailand', '0');
INSERT INTO `country` VALUES ('416', 'TL', 'Timor-Leste (East Timor)', '0');
INSERT INTO `country` VALUES ('417', 'TG', 'Togo', '0');
INSERT INTO `country` VALUES ('418', 'TK', 'Tokelau', '0');
INSERT INTO `country` VALUES ('419', 'TO', 'Tonga', '0');
INSERT INTO `country` VALUES ('420', 'TT', 'Trinidad and Tobago', '0');
INSERT INTO `country` VALUES ('421', 'TN', 'Tunisia', '0');
INSERT INTO `country` VALUES ('422', 'TR', 'Turkey', '0');
INSERT INTO `country` VALUES ('423', 'TM', 'Turkmenistan', '0');
INSERT INTO `country` VALUES ('424', 'TC', 'Turks and Caicos Islands', '0');
INSERT INTO `country` VALUES ('425', 'TV', 'Tuvalu', '0');
INSERT INTO `country` VALUES ('426', 'UG', 'Uganda', '0');
INSERT INTO `country` VALUES ('427', 'UA', 'Ukraine', '0');
INSERT INTO `country` VALUES ('428', 'AE', 'United Arab Emirates', '0');
INSERT INTO `country` VALUES ('429', 'GB', 'United Kingdom', '1');
INSERT INTO `country` VALUES ('430', 'US', 'United States', '0');
INSERT INTO `country` VALUES ('431', 'UY', 'Uruguay', '0');
INSERT INTO `country` VALUES ('432', 'UZ', 'Uzbekistan', '0');
INSERT INTO `country` VALUES ('433', 'VU', 'Vanuatu', '0');
INSERT INTO `country` VALUES ('434', 'VE', 'Venezuela', '0');
INSERT INTO `country` VALUES ('435', 'VN', 'Vietnam', '0');
INSERT INTO `country` VALUES ('436', 'VG', 'Virgin Islands', '0');
INSERT INTO `country` VALUES ('437', 'WF', 'Wallis and Futuna Islands', '0');
INSERT INTO `country` VALUES ('438', 'YE', 'Yemen', '0');
INSERT INTO `country` VALUES ('439', 'ZM', 'Zambia', '0');
INSERT INTO `country` VALUES ('440', 'ZW', 'Zimbabwe', '0');
