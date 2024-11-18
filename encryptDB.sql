/*
 Navicat Premium Data Transfer

 Date: 18/11/2024 16:56:43
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
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of requests
-- ----------------------------
BEGIN;
INSERT INTO `requests` (`id`, `requestID`, `timestamp`) VALUES (1, '927f3f727f26f77bfa121d528ba06c048f156ea50a2152a39d11aff9a3439a4b', '2024-11-14 10:01:56');
INSERT INTO `requests` (`id`, `requestID`, `timestamp`) VALUES (2, 'cd7b9dd5153259ee19e19281e06014d858399f88ca8cd9bbb74ebe24685617a2', '2024-11-14 15:04:46');
INSERT INTO `requests` (`id`, `requestID`, `timestamp`) VALUES (3, '42d296234e2cd68a573f17a1ed85c8825c2625c6db1735be0564ecfb13363403', '2024-11-14 15:04:51');
INSERT INTO `requests` (`id`, `requestID`, `timestamp`) VALUES (4, 'd14cc4cef41a1f2b3d612ac0b4d0bee04a3865e4ec8d17b6b7e3b2b1c369df40', '2024-11-14 15:13:29');
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
