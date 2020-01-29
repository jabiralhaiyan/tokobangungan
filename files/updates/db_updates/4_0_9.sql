ALTER TABLE `tec_settings` ADD `local_printers` TINYINT(1) NULL;
UPDATE `tec_settings` SET `version` = '4.0.9' WHERE `setting_id` = 1;
