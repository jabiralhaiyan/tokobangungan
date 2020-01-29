ALTER TABLE `tec_settings` ADD `rtl` TINYINT(1) NULL, ADD `print_img` TINYINT(1) NULL;
ALTER TABLE `tec_settings` CHANGE `version` `version` VARCHAR(10) NOT NULL DEFAULT '4.0.13';
UPDATE `tec_settings` SET `version` = '4.0.13' WHERE `setting_id` = 1;
