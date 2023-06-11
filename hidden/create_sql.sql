CREATE TABLE `kolpingjugend` (
  `kolpingjugend_id` int(10) NOT NULL AUTO_INCREMENT,
  `kolpingjugend_name` varchar(255) NOT NULL,
  `kolpingjugend_ort` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`kolpingjugend_id`)
);

CREATE TABLE `users` (
  `user_id` int(10) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nachname` varchar(255) NOT NULL,
  `vorname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `kolpingjugend_id` int(10),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `perm_login` tinyint(1) NOT NULL DEFAULT 0,
  `perm_admin` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`),
  FOREIGN KEY (`kolpingjugend_id`) REFERENCES `kolpingjugend` (`kolpingjugend_id`)
);

CREATE TABLE `securitytokens` (
  `securitytoken_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `identifier` varchar(255) NOT NULL,
  `securitytoken` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`securitytoken_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
);

CREATE TABLE `houses` (
  `house_id` int(10) NOT NULL AUTO_INCREMENT,
  `kolpingjugend_id` int(10) NOT NULL,
  `house_name` varchar(255) NOT NULL,
  `house_address` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`house_id`),
  FOREIGN KEY (`kolpingjugend_id`) REFERENCES `kolpingjugend` (`kolpingjugend_id`)
);

CREATE TABLE `rooms` (
  `room_id` int(10) NOT NULL AUTO_INCREMENT,
  `house_id` int(10) nOT NULL,
  `room_name` varchar(255),
  `room_desc` mediumtext,
  `room_done` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`room_id`),
  FOREIGN KEY (`house_id`) REFERENCES `houses` (`house_id`)
);

CREATE TABLE `solution_pics` (
  `solution_pic_id` int(10) NOT NULL AUTO_INCREMENT,
  `room_id` int(10) NOT NULL,
  `solution_pic_path` varchar(255) NOT NULL,
  PRIMARY KEY (`solution_pic_id`),
  FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`)
);

INSERT INTO `kolpingjugend` (`kolpingjugend_id`, `kolpingjugend_name`, `kolpingjugend_ort`) VALUES
(0, 'DVRS', 'Stuttart');

INSERT INTO `users` (`user_id`, `login`, `password`, `nachname`, `vorname`, `email`, `kolpingjugend_id`, `created_at`, `perm_login`, `perm_admin`) VALUES
(0, 'Admin', '$2y$10$LcuHyznyyzyznSuO.2nSuO.2znSyznSyznSuO.2uO.2uO.2yznSyznSuO.2yznSuOyznSuO.2.2uO.2yznSuOyznSuO.2.2SyznSuyznSuO.2O.2yznSuO.2uO.2UyznSyznSyznSuO.yznSuO.22uO.2uO.27yznSuO.2cUyznSuO.2', 'Admin', 'Admin', 'admin@kj-dvrs.de', 0, '2023-04-30 20:15:23', 0, 0);

INSERT INTO `houses` (`house_id`, `kolpingjugend_id`, `house_name`, `house_address`) VALUES
(0, 0, 'DVRS', 'Heusteigstraße 66, 70180 Stuttgart');

INSERT INTO `rooms` (`room_id`, `house_id`, `room_name`, `room_done`) VALUES
(1, 0, 'Kreativer Raum', 0),
(2, 0, 'Partykeller', 0),
(3, 0, 'Wohnzimmer & Heimkino', 0),
(4, 0, 'Essküche', 0),
(5, 0, 'Schlafzimmer', 0),
(6, 0, 'Atelier', 0),
(7, 0, 'Garten', 0),
(8, 0, 'Werkstatt', 0),
(9, 0, 'Fitnessraum', 0),
(10, 0, 'Spielzimmer', 0),
(11, 0, 'Musikzimmer', 0),
(12, 0, 'Garderobe', 0),
(13, 0, 'Boulderwand', 0),
(14, 0, 'Bad', 0),
(15, 0, 'Arbeitszimmer', 0),
(16, 0, 'Dachkapelle', 0);