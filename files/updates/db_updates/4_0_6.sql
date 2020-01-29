ALTER TABLE `categories` ENGINE = InnoDB,
ADD `code` varchar(20) NOT NULL,
ADD `image` varchar(100) DEFAULT 'no_image.png';

CREATE TABLE IF NOT EXISTS `tec_combo_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `item_code` varchar(20) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `price` decimal(25,2) DEFAULT NULL,
  `cost` decimal(25,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `customers` ADD `store_id` INT NULL;
ALTER TABLE `customers` ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `tec_expenses` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `reference` varchar(50) NOT NULL,
    `amount` decimal(25,2) NOT NULL,
    `note` varchar(1000) DEFAULT NULL,
    `created_by` varchar(55) NOT NULL,
    `attachment` varchar(55) DEFAULT NULL,
    `store_id` INT NOT NULL DEFAULT '1',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tec_gift_cards` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `card_no` varchar(20) NOT NULL,
    `value` decimal(25,2) NOT NULL,
    `customer_id` int(11) DEFAULT NULL,
    `balance` decimal(25,2) NOT NULL,
    `expiry` date DEFAULT NULL,
    `created_by` int(11) DEFAULT NULL,
    `store_id` INT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `card_no` (`card_no`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `groups` ENGINE = InnoDB;
ALTER TABLE `login_attempts` ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `tec_payments` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `sale_id` int(11) DEFAULT NULL,
    `customer_id` int(11) DEFAULT NULL,
    `transaction_id` varchar(50) DEFAULT NULL,
    `paid_by` varchar(20) NOT NULL,
    `cheque_no` varchar(20) DEFAULT NULL,
    `cc_no` varchar(20) DEFAULT NULL,
    `cc_holder` varchar(25) DEFAULT NULL,
    `cc_month` varchar(2) DEFAULT NULL,
    `cc_year` varchar(4) DEFAULT NULL,
    `cc_type` varchar(20) DEFAULT NULL,
    `amount` decimal(25,2) NOT NULL,
    `currency` varchar(3) DEFAULT NULL,
    `created_by` int(11) NOT NULL,
    `attachment` varchar(55) DEFAULT NULL,
    `note` varchar(1000) DEFAULT NULL,
    `pos_paid` decimal(25,2) DEFAULT '0.0000',
    `pos_balance` decimal(25,2) DEFAULT '0.0000',
    `gc_no` varchar(20) DEFAULT NULL,
    `reference` varchar(50) DEFAULT NULL,
    `updated_by` int(11) DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    `store_id` INT NOT NULL DEFAULT '1',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tec_product_store_qty` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `product_id` int(11) NOT NULL,
    `store_id` int(11) NOT NULL,
    `quantity` decimal(12,2) NOT NULL DEFAULT '0',
    `price` decimal(25,2) NULL,
    PRIMARY KEY (`id`),
    KEY `product_id` (`product_id`),
    KEY `store_id` (`store_id`) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `products` ENGINE = InnoDB,
ADD `tax_method` tinyint(1) DEFAULT '1',
ADD `quantity` decimal(15,2) DEFAULT '0.00',
ADD `barcode_symbology` varchar(20) NOT NULL DEFAULT 'code39',
ADD `type` varchar(20) NOT NULL DEFAULT 'standard',
ADD `details` text,
ADD `alert_quantity` decimal(10,2) DEFAULT '0.00';

CREATE TABLE IF NOT EXISTS `tec_purchases` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `reference` varchar(55) NOT NULL,
    `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `note` varchar(1000) NOT NULL,
    `total` decimal(25,2) NOT NULL,
    `attachment` varchar(255) DEFAULT NULL,
    `supplier_id` int(11) DEFAULT NULL,
    `received` tinyint(1) DEFAULT NULL,
    `created_by` INT NOT NULL,
    `store_id` INT NOT NULL DEFAULT '1',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tec_purchase_items` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `purchase_id` int(11) NOT NULL,
    `product_id` int(11) NOT NULL,
    `quantity` decimal(15,2) NOT NULL,
    `cost` decimal(25,2) NOT NULL,
    `subtotal` decimal(25,2) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tec_registers` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `user_id` int(11) NOT NULL,
    `cash_in_hand` decimal(25,2) NOT NULL,
    `status` varchar(10) NOT NULL,
    `total_cash` decimal(25,2) DEFAULT NULL,
    `total_cheques` int(11) DEFAULT NULL,
    `total_cc_slips` int(11) DEFAULT NULL,
    `total_cash_submitted` decimal(25,2) NOT NULL,
    `total_cheques_submitted` int(11) NOT NULL,
    `total_cc_slips_submitted` int(11) NOT NULL,
    `note` text,
    `closed_at` timestamp NULL DEFAULT NULL,
    `transfer_opened_bills` varchar(50) DEFAULT NULL,
    `closed_by` int(11) DEFAULT NULL,
    `store_id` INT NOT NULL DEFAULT '1',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `sales` ENGINE = InnoDB,
CHANGE `total` `grand_total` decimal(25,2) NOT NULL,
CHANGE `inv_total` `total` decimal(25,2) NOT NULL,
CHANGE `tax` `order_tax_id` varchar(20) DEFAULT NULL,
CHANGE `discount` `order_discount_id` varchar(20) DEFAULT NULL,
ADD `product_discount` decimal(25,2) DEFAULT NULL,
ADD `order_discount` decimal(25,2) DEFAULT NULL,
ADD `product_tax` decimal(25,2) DEFAULT NULL,
ADD `order_tax` decimal(25,2) DEFAULT NULL,
ADD `total_quantity` decimal(15,2) DEFAULT NULL,
ADD `created_by` int(11) DEFAULT NULL,
ADD `updated_by` int(11) DEFAULT NULL,
ADD `updated_at` datetime DEFAULT NULL,
ADD `note` varchar(1000) DEFAULT NULL,
ADD `status` varchar(20) DEFAULT NULL,
ADD `rounding` decimal(8,2) DEFAULT NULL,
ADD `store_id` INT NOT NULL DEFAULT '1';

ALTER TABLE `sale_items` ENGINE = InnoDB,
CHANGE `gross_total` `subtotal` decimal(25,2) NOT NULL,
CHANGE `quantity` `quantity` decimal(15,2) NOT NULL,
ADD `unit_price` decimal(25,2) NOT NULL,
ADD `net_unit_price` decimal(25,2) NOT NULL,
ADD `discount` varchar(20) DEFAULT NULL,
ADD `item_discount` decimal(25,2) DEFAULT NULL,
ADD `tax` int(20) DEFAULT NULL,
ADD `item_tax` decimal(25,2) DEFAULT NULL,
ADD `real_unit_price` decimal(25,2) DEFAULT NULL,
ADD `cost` DECIMAL(25,2) NULL DEFAULT '0',
ADD `comment` VARCHAR(255) NULL;

CREATE TABLE IF NOT EXISTS `tec_sessions` (
  `id` varchar(40) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `data` blob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `settings` ENGINE = InnoDB,
CHANGE `dateformat` `dateformat` varchar(20) DEFAULT NULL,
ADD `timeformat` varchar(20) DEFAULT NULL,
ADD `default_email` varchar(100) NOT NULL,
ADD `version` varchar(10) NOT NULL DEFAULT '1.0',
ADD `theme` varchar(20) NOT NULL,
ADD `timezone` varchar(255) NOT NULL DEFAULT '0',
ADD `protocol` varchar(20) NOT NULL DEFAULT 'mail',
ADD `smtp_host` varchar(255) DEFAULT NULL,
ADD `smtp_user` varchar(100) DEFAULT NULL,
ADD `smtp_pass` varchar(255) DEFAULT NULL,
ADD `smtp_port` varchar(10) DEFAULT '25',
ADD `smtp_crypto` varchar(5) DEFAULT NULL,
ADD `mmode` tinyint(1) NOT NULL,
ADD `captcha` tinyint(1) NOT NULL DEFAULT '1',
ADD `mailpath` varchar(55) DEFAULT NULL,
ADD `item_addition` tinyint(1) NOT NULL,
ADD `decimals` tinyint(1) NOT NULL DEFAULT '2',
ADD `thousands_sep` varchar(2) NOT NULL DEFAULT ',',
ADD `decimals_sep` varchar(2) NOT NULL DEFAULT '.',
ADD `focus_add_item` varchar(55) DEFAULT NULL,
ADD `add_customer` varchar(55) DEFAULT NULL,
ADD `toggle_category_slider` varchar(55) DEFAULT NULL,
ADD `cancel_sale` varchar(55) DEFAULT NULL,
ADD `suspend_sale` varchar(55) DEFAULT NULL,
ADD `print_order` varchar(55) DEFAULT NULL,
ADD `print_bill` varchar(55) DEFAULT NULL,
ADD `finalize_sale` varchar(55) DEFAULT NULL,
ADD `today_sale` varchar(55) DEFAULT NULL,
ADD `open_hold_bills` varchar(55) DEFAULT NULL,
ADD `close_register` varchar(55) DEFAULT NULL,
ADD `java_applet` tinyint(1) NOT NULL,
ADD `receipt_printer` varchar(55) DEFAULT NULL,
ADD `pos_printers` varchar(255) DEFAULT NULL,
ADD `cash_drawer_codes` varchar(55) DEFAULT NULL,
ADD `char_per_line` tinyint(4) DEFAULT '42',
ADD `rounding` tinyint(1) DEFAULT '0',
ADD `pro_limit` tinyint(4) NOT NULL,
ADD `pin_code` varchar(20) DEFAULT NULL,
ADD `stripe` tinyint(1) DEFAULT NULL,
ADD `stripe_secret_key` varchar(100) DEFAULT NULL,
ADD `stripe_publishable_key` varchar(100) DEFAULT NULL,
ADD `purchase_code` varchar(100) DEFAULT NULL,
ADD `envato_username` varchar(50) DEFAULT NULL,
ADD `theme_style` VARCHAR(25) NULL DEFAULT 'green', 
ADD `after_sale_page` TINYINT(1) NULL,
ADD `overselling` TINYINT(1) NULL DEFAULT '1',
ADD `multi_store` TINYINT(1) NULL,
ADD `qty_decimals` TINYINT(1) NULL DEFAULT '2',
ADD `symbol` VARCHAR(55) NULL,
ADD `sac` TINYINT(1) NULL DEFAULT '0',
ADD `display_symbol` TINYINT(1) NULL;

UPDATE `settings` SET `dateformat`='D j M Y', `timeformat`='h:i A', `default_email`='noreply@spos.tecdiary.my', `theme`='default', `protocol`='mail', `mmode`=0, `captcha`=0, `bsty`=3, `item_addition`=1, `decimals`=2, `thousands_sep`=',', `decimals_sep`='.', `focus_add_item`='ALT+F1', `add_customer`='ALT+F2', `toggle_category_slider`='ALT+F10', `cancel_sale`='ALT+F5', `suspend_sale`='ALT+F6', `print_order`='ALT+F11', `print_bill`='ALT+F12', `finalize_sale`='ALT+F8', `today_sale`='Ctrl+F1', `open_hold_bills`='Ctrl+F2', `close_register`='ALT+F7', `java_applet`=0 WHERE `setting_id` = 1;

CREATE TABLE IF NOT EXISTS `tec_stores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `code` varchar(20) NOT NULL,
  `logo` varchar(40) DEFAULT NULL,
  `email` varchar(20) DEFAULT NULL,
  `phone` varchar(15) NOT NULL,
  `address1` varchar(50) DEFAULT NULL,
  `address2` varchar(50) DEFAULT NULL,
  `city` varchar(20) DEFAULT NULL,
  `state` varchar(20) DEFAULT NULL,
  `postal_code` varchar(8) DEFAULT NULL,
  `country` varchar(25) DEFAULT NULL,
  `currency_code` varchar(3) NOT NULL,
  `receipt_footer` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `tec_stores` (`name`, `code`, `logo`, `email`, `phone`, `address1`, `address2`, `city`, `state`, `postal_code`, `country`, `currency_code`, `receipt_footer`) VALUES ('SimplePOS', 'POS', 'logo.png', 'store@tecdiary.com', '012345678', 'Address Line 1', '', 'Petaling Jaya', 'Selangor', '46000', 'Malaysia', 'MYR', 'This is receipt footer for store');

CREATE TABLE IF NOT EXISTS `tec_suppliers` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(55) NOT NULL,
    `cf1` varchar(255) NOT NULL,
    `cf2` varchar(255) NOT NULL,
    `phone` varchar(20) NOT NULL,
    `email` varchar(100) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT INTO `tec_suppliers` (`id`, `name`, `cf1`, `cf2`, `phone`, `email`) VALUES
(1, 'Test Supplier', '1', '2', '0123456789', 'supplier@tecdairy.com');

CREATE TABLE IF NOT EXISTS `tec_suspended_items` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `suspend_id` int(11) NOT NULL,
    `product_id` int(11) NOT NULL,
    `quantity` decimal(15,2) NOT NULL,
    `unit_price` decimal(25,2) NOT NULL,
    `net_unit_price` decimal(25,2) NOT NULL,
    `discount` varchar(20) DEFAULT NULL,
    `item_discount` decimal(25,2) DEFAULT NULL,
    `tax` int(20) DEFAULT NULL,
    `item_tax` decimal(25,2) DEFAULT NULL,
    `subtotal` decimal(25,2) NOT NULL,
    `real_unit_price` decimal(25,2) DEFAULT NULL,
    `product_code` VARCHAR(50) NULL, 
    `product_name` VARCHAR(50) NULL,
    `comment` VARCHAR(255) NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tec_suspended_sales` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `date` datetime NOT NULL,
    `customer_id` int(11) NOT NULL,
    `customer_name` varchar(55) NOT NULL,
    `total` decimal(25,2) NOT NULL,
    `product_discount` decimal(25,2) DEFAULT NULL,
    `order_discount_id` varchar(20) DEFAULT NULL,
    `order_discount` decimal(25,2) DEFAULT NULL,
    `total_discount` decimal(25,2) DEFAULT NULL,
    `product_tax` decimal(25,2) DEFAULT NULL,
    `order_tax_id` varchar(20) DEFAULT NULL,
    `order_tax` decimal(25,2) DEFAULT NULL,
    `total_tax` decimal(25,2) DEFAULT NULL,
    `grand_total` decimal(25,2) NOT NULL,
    `total_items` int(11) DEFAULT NULL,
    `total_quantity` decimal(15,2) DEFAULT NULL,
    `paid` decimal(25,2) DEFAULT NULL,
    `created_by` int(11) DEFAULT NULL,
    `updated_by` int(11) DEFAULT NULL,
    `updated_at` datetime DEFAULT NULL,
    `note` varchar(1000) DEFAULT NULL,
    `hold_ref` varchar(255) DEFAULT NULL,
    `store_id` INT NOT NULL DEFAULT '1',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `users` ENGINE = InnoDB,
CHANGE `ip_address` `ip_address` varbinary(45) DEFAULT NULL,
ADD `last_ip_address` varbinary(45) DEFAULT NULL,
ADD `avatar` varchar(55) DEFAULT NULL,
ADD `gender` varchar(20) NULL DEFAULT 'male',
ADD `group_id` int(11) unsigned NOT NULL DEFAULT '1',
ADD `store_id` INT NULL;

CREATE TABLE IF NOT EXISTS `tec_user_logins` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `company_id` int(11) DEFAULT NULL,
    `ip_address` varbinary(16) NOT NULL,
    `login` varchar(100) NOT NULL,
    `time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

RENAME TABLE `categories` TO `tec_categories`,
`customers` TO `tec_customers`,
`groups` TO `tec_groups`,
`login_attempts` TO `tec_login_attempts`,
`products` TO `tec_products`,
`sales` TO `tec_sales`,
`sale_items` TO `tec_sale_items`,
`settings` TO `tec_settings`,
`users` TO `tec_users`,
`users_groups` TO `tec_users_groups`;