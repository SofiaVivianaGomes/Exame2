USE exame2;

DROP TABLE IF EXISTS `users`, `indexes`, `auth`, `logs`;

SET SQL_MODE='ALLOW_INVALID_DATES';

CREATE TABLE `users` 
(
	`id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL,
    `username` VARCHAR(50) NOT NULL,
    `password` VARCHAR(255) NOT NULL, # BCrypt generates an implementation-dependent 448-bit hash value,
    `register` TIMESTAMP NOT NULL DEFAULT  '0000-00-00 00:00:00',
    `login` TIMESTAMP NOT NULL DEFAULT  '0000-00-00 00:00:00',
    
    PRIMARY KEY (`id`, `username`)
);

ALTER TABLE `users` ADD INDEX `index_username` (`username`);

CREATE TABLE `indexes` 
(
	`id` INT(11) NOT NULL AUTO_INCREMENT,
    `symbol` VARCHAR(50) NOT NULL,
    `type` VARCHAR(50),
    `trading_hours` VARCHAR(255),
    `description` VARCHAR(255),
    `spread_target_standard` VARCHAR(50),
    
    PRIMARY KEY (`id`)
);

CREATE TABLE `logs` 
(	
	`id` INT(11) NOT NULL AUTO_INCREMENT,
    `username_id` INT(11) NOT NULL,
    `date` VARCHAR(50) NOT NULL,
    `request` VARCHAR(76) NOT NULL,
    `ip` VARCHAR(76) NOT NULL,
	
	PRIMARY KEY (`id`),
    FOREIGN KEY (`username_id`) REFERENCES `users`(`id`)
);

CREATE TABLE `auth` 
(	
	`id` INT(11) NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL, 
    `hash` CHAR(32) NOT NULL,    
	`register_date` TIMESTAMP NOT NULL DEFAULT  '0000-00-00 00:00:00',
    
	PRIMARY KEY (`id`, `username`)
);

