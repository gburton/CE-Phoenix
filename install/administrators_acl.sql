-- ----------------------------
-- Table structure for administrators_acl
-- ----------------------------
DROP TABLE IF EXISTS `administrators_acl`;
CREATE TABLE `administrators_acl` (
  `aID` varchar(255) DEFAULT '',
  `menu_heading` varchar(255) DEFAULT NULL,
  `page_name` varchar(255) DEFAULT NULL,
  `blocked_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
