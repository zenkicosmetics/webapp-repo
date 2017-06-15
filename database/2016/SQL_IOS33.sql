/*
Navicat MySQL Data Transfer
Source Host     : localhost:3306
Source Database : clevvermail_dev
Target Host     : localhost:3306
Target Database : clevvermail_dev
Date: 2016-04-04 23:58:51
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for app_external
-- ----------------------------

-- --------------------------------------------------------

--
-- Table structure for table `app_external`
--

CREATE TABLE IF NOT EXISTS `app_external` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_code` varchar(20) DEFAULT NULL,
  `app_name` varchar(100) DEFAULT NULL,
  `app_key` varchar(250) DEFAULT NULL,
  `validate_key_flag` tinyint(4) DEFAULT NULL,
  `disable_flag` tinyint(4) DEFAULT NULL,
  `created_date` bigint(20) DEFAULT NULL,
  `deleted_date` bigint(20) DEFAULT NULL,
  `version` varchar(10) NOT NULL,
  `required_update_flag` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `app_external`
--

INSERT INTO `app_external` (`id`, `app_code`, `app_name`, `app_key`, `validate_key_flag`, `disable_flag`, `created_date`, `deleted_date`, `version`, `required_update_flag`) VALUES
(1, 'clevvermail_ios', 'clevvermail ios', '5a09797fd9907f382a87588a9bfe29a7', 1, 0, 1459731661, NULL, '1.10', 1),
(2, 'clevvermail_desktop', 'clevvermail desktop application', '4bfd0ae77f68fc3e1f0b2b61fed2dcf9', 1, 0, 1459731661, NULL, '1.00', 0),
(3, 'clevvermail_android', 'clevvermail android', '0dcd66d6878c92730776922df0b1571c', 1, 0, 1459731661, NULL, '1.00', 0),
(4, 'clevvermail_dev', 'clevvermail demo', '0dcd66d6878c92730776922df0b1579c', 1, 0, 1459731661, NULL, '1.00', 0);
