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

CREATE TABLE `room_templates` (
  `room_template_id` int(10) NOT NULL AUTO_INCREMENT,
  `room_name` varchar(255) NOT NULL,
  `room_desc` mediumtext NOT NULL,
  `room_unlocked` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`room_template_id`)
);

CREATE TABLE `rooms` (
  `room_id` int(10) NOT NULL AUTO_INCREMENT,
  `room_template_id` int(10) NOT NULL,
  `room_name_overwrite` varchar(255),
  `room_desc_overwrite` mediumtext,
  `room_done` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`room_id`),
  FOREIGN KEY (`room_template_id`) REFERENCES `room_templates` (`room_template_id`)
);

CREATE TABLE `solution_pics` (
  `solution_pic_id` int(10) NOT NULL AUTO_INCREMENT,
  `solution_pic_path` varchar(255) NOT NULL,
  PRIMARY KEY (`solution_pic_id`)
);

CREATE TABLE `room_solutions` (
  `room_solutions_id` int(10) NOT NULL AUTO_INCREMENT,
  `room_id` int(10) NOT NULL,
  `solution_pic_id` int(10) NOT NULL,
  PRIMARY KEY (`room_solutions_id`),
  FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`),
  FOREIGN KEY (`solution_pic_id`) REFERENCES `solution_pics` (`solution_pic_id`)
);

CREATE TABLE `houses` (
  `house_id` int(10) NOT NULL AUTO_INCREMENT,
  `kolpingjugend_id` int(10) NOT NULL,
  `house_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`house_id`),
  FOREIGN KEY (`kolpingjugend_id`) REFERENCES `kolpingjugend` (`kolpingjugend_id`)
);

CREATE TABLE `house_room` (
  `house_room_id` int(10) NOT NULL AUTO_INCREMENT,
  `house_id` int(10) NOT NULL,
  `room_id` int(10) NOT NULL,
  PRIMARY KEY (`house_room_id`),
  FOREIGN KEY (`house_id`) REFERENCES `houses` (`house_id`),
  FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`)
);