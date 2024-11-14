/*
 Navicat Premium Data Transfer


 Date: 14/11/2024 09:55:31
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for requests
-- ----------------------------
DROP TABLE IF EXISTS `requests`;
CREATE TABLE `requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `requestID` varchar(255) NOT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `requestID` (`requestID`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of requests
-- ----------------------------
BEGIN;
INSERT INTO `requests` (`id`, `requestID`, `timestamp`) VALUES (1, 'req-1731491882251-i7e8efqibge', '2024-11-13 17:58:02');
INSERT INTO `requests` (`id`, `requestID`, `timestamp`) VALUES (2, 'req-1731491882251-ddi7e8efqibge', '2024-11-13 17:58:18');
INSERT INTO `requests` (`id`, `requestID`, `timestamp`) VALUES (3, 'c1b25e5355144b891bf26a9786d498454fa46878a5b32b5114dd3661f9e2d2ca', '2024-11-13 18:06:39');
INSERT INTO `requests` (`id`, `requestID`, `timestamp`) VALUES (4, '84eca016bdf30025222bf0e100ab5ba4e4d764c62325195c8529f45d223c58e6', '2024-11-13 18:06:47');
INSERT INTO `requests` (`id`, `requestID`, `timestamp`) VALUES (5, '2ee9c5f28763a90a01171d07e5dc5101df0b01bd6588b9237332d9a3d4504bc0', '2024-11-13 18:06:49');
COMMIT;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of users
-- ----------------------------
BEGIN;
INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`) VALUES (2, 'admin', 'admin@example.com', 'e10adc3949ba59abbe56e057f20f883e', '2024-11-11 17:24:10');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
