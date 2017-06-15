ALTER TABLE location
ADD shared_office_space_flag tinyint DEFAULT 0;

ALTER TABLE location
ADD shared_office_image_path varchar(255);

ALTER TABLE location
ADD booking_email_address varchar(100);


SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for location_office
-- ----------------------------
DROP TABLE IF EXISTS `location_office`;
CREATE TABLE `location_office` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `location_id` bigint(20) DEFAULT NULL,
  `business_concierge_flag` tinyint(4) DEFAULT NULL,
  `video_conference_flag` tinyint(4) DEFAULT NULL,
  `meeting_rooms_flag` tinyint(4) DEFAULT NULL,
  `created_date` bigint(20) DEFAULT NULL,
  `updated_date` bigint(20) DEFAULT NULL,
  `deleted_flag` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of location_office
-- ----------------------------

-- ----------------------------
-- Table structure for location_office_feature
-- ----------------------------
DROP TABLE IF EXISTS `location_office_feature`;
CREATE TABLE `location_office_feature` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `office_id` bigint(20) DEFAULT NULL,
  `feature_name` varchar(255) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of location_office_feature
-- ----------------------------


-- ----------------------------
-- Table structure for location_office_booking_request
-- ----------------------------
DROP TABLE IF EXISTS `location_office_booking_request`;
CREATE TABLE `location_office_booking_request` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) DEFAULT NULL,
  `location_id` bigint(20) DEFAULT NULL,
  `your_name` varchar(100) DEFAULT NULL,
  `your_email` varchar(100) DEFAULT NULL,
  `your_phone` varchar(30) DEFAULT NULL,
  `booking_request` text,
  `created_date` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;