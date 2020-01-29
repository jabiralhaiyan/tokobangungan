CREATE TABLE IF NOT EXISTS `tec_printers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(55) NOT NULL,
  `type` varchar(25) NOT NULL,
  `profile` varchar(25) NOT NULL,
  `char_per_line` TINYINT UNSIGNED NULL,
  `path` varchar(255) DEFAULT NULL,
  `ip_address` varbinary(45) DEFAULT NULL,
  `port` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `tec_sales` ADD `hold_ref` VARCHAR(255) NULL;

ALTER TABLE `tec_stores` ADD `receipt_header` TEXT NULL;

ALTER TABLE `tec_settings` ADD `remote_printing` TINYINT(1) NULL DEFAULT '1',
ADD `printer` INT(11) NULL,
ADD `order_printers` VARCHAR(55) NULL,
ADD `auto_print` TINYINT(1) NULL DEFAULT '0';

UPDATE `tec_settings` SET `version` = '4.0.7' WHERE `setting_id` = 1;
