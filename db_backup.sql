/*
SQLyog Community v13.1.6 (64 bit)
MySQL - 10.4.11-MariaDB : Database - klipspruit_db
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`klipspruit_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

USE `klipspruit_db`;

/*Table structure for table `activity_logs` */

DROP TABLE IF EXISTS `activity_logs`;

CREATE TABLE `activity_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `object_id` int(10) unsigned DEFAULT NULL,
  `project_id` int(10) unsigned DEFAULT 0,
  `action` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `raw_data` longtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by_id` int(10) unsigned DEFAULT NULL,
  `is_private` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `is_hidden` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `target_source` (`target_source_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `activity_logs` */

insert  into `activity_logs`(`id`,`model`,`object_id`,`project_id`,`action`,`raw_data`,`created_at`,`created_by_id`,`is_private`,`is_hidden`,`target_source_id`) values 
(1,'Projects',1,1,'add','YToyOntzOjU6InRpdGxlIjtzOjIyOiI5NjIgLSBKYWNvIExsb3lkIFBsYW5zIjtzOjc6Im1lc3NhZ2UiO3M6MTc6ImNyZWF0ZWQgYSBwcm9qZWN0Ijt9','2021-04-15 11:40:11',1,1,0,1),
(2,'ProjectTaskLists',1,1,'add','YToyOntzOjU6InRpdGxlIjtzOjQ6InRlc3QiO3M6NzoibWVzc2FnZSI7czoxOToiY3JlYXRlZCBhIHRhc2sgbGlzdCI7fQ==','2021-04-22 14:32:11',1,1,0,1),
(3,'Projects',2,2,'add','YToyOntzOjU6InRpdGxlIjtzOjE6ImEiO3M6NzoibWVzc2FnZSI7czoxNzoiY3JlYXRlZCBhIHByb2plY3QiO30=','2021-04-23 15:08:32',4,1,0,1);

/*Table structure for table `announcements` */

DROP TABLE IF EXISTS `announcements`;

CREATE TABLE `announcements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `start_date` date NOT NULL DEFAULT '0000-00-00',
  `end_date` date NOT NULL DEFAULT '0000-00-00',
  `share_with` enum('members','clients','all') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'all',
  `created_by_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `start_date` (`start_date`),
  KEY `target_source` (`target_source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `announcements` */

/*Table structure for table `companies` */

DROP TABLE IF EXISTS `companies`;

CREATE TABLE `companies` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` smallint(5) unsigned DEFAULT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vat_no` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone_number` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `logo_file` varchar(44) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by_id` int(10) unsigned DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `is_trashed` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `trashed_by_id` int(10) unsigned NOT NULL DEFAULT 0,
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `created_at` (`created_at`),
  KEY `parent_id` (`parent_id`),
  KEY `target_source` (`target_source_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `companies` */

insert  into `companies`(`id`,`parent_id`,`name`,`address`,`vat_no`,`phone_number`,`logo_file`,`created_at`,`created_by_id`,`updated_at`,`is_active`,`is_trashed`,`trashed_by_id`,`target_source_id`) values 
(1,0,'Quintin de Bruin',NULL,NULL,NULL,NULL,'2021-04-15 08:49:19',1,'2021-04-15 08:49:19',1,0,0,1),
(2,0,'Spectiv Mechanical Drawings',NULL,NULL,NULL,NULL,'2021-04-15 11:35:33',2,'2021-04-15 11:35:33',1,0,0,2),
(3,1,'Hot Point Investments','Plot 579 Naauwpoort',NULL,'+27 82 891 7616',NULL,'2021-04-15 11:38:50',1,'2021-04-15 11:38:50',1,0,0,1),
(4,0,'123',NULL,NULL,NULL,NULL,'2021-04-22 13:58:51',5,'2021-04-22 13:58:51',1,0,0,3);

/*Table structure for table `configurations` */

DROP TABLE IF EXISTS `configurations`;

CREATE TABLE `configurations` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `value` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `category_name` (`category_name`),
  KEY `target_source` (`target_source_id`),
  KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `configurations` */

insert  into `configurations`(`id`,`category_name`,`name`,`value`,`target_source_id`) values 
(1,'mailing','smtp_server','server@example.com',1),
(2,'mailing','smtp_port','101',1),
(3,'mailing','smtp_authenticate','1',1),
(4,'mailing','smtp_username','username',1),
(5,'mailing','smtp_password','password',1),
(6,'mailing','smtp_secure_connection','no',1),
(7,'mailing','smtp_reply_from_email','noreply@example.com',1),
(8,'mailing','smtp_from_name','PROMS',1),
(9,'mailing','smtp_from_email','support@example.com',1),
(10,'system','site_name','Klipspruit Collaboration',1),
(11,'system','contact_email','system@de-bruin.co.za',1),
(12,'system','default_currency','R',1),
(13,'system','items_per_page','10',1),
(14,'system','calendar_google_api_key',NULL,1),
(15,'system','calendar_google_event_address',NULL,1),
(16,'system','paypal_email','paypal_sandbox@example.com',1),
(17,'system','paypal_sandbox','yes',1),
(18,'system','paypal_currency_code','USD',1),
(19,'system','stripe_secret_key','sk_live_lV5nITIlIlzvSZmkfunl5bH3',1),
(20,'system','stripe_publishable_key','pk_live_ogI79OxdoS6YUGWLkkAVwbC3',1),
(21,'system','stripe_currency_code','USD',1),
(22,'system','offline_bank_name','Sample Bank',1),
(23,'system','offline_bank_account','XXXXXX00000000',1),
(24,'system','invoice_color','#00A65A',1),
(25,'system','send_due_date_invoice_reminder_before_days','1',1),
(26,'system','send_due_date_invoice_reminder_after_days','1',1),
(27,'system','logo_text','Q de Bruin',1),
(28,'mailing','smtp_server','server@example.com',2),
(29,'mailing','smtp_port','101',2),
(30,'mailing','smtp_authenticate','1',2),
(31,'mailing','smtp_username','username',2),
(32,'mailing','smtp_password','password',2),
(33,'mailing','smtp_secure_connection','no',2),
(34,'mailing','smtp_reply_from_email','noreply@example.com',2),
(35,'mailing','smtp_from_name','PROMS',2),
(36,'mailing','smtp_from_email','support@example.com',2),
(37,'system','site_name','Kilpspruit Collaboration',2),
(38,'system','contact_email','contact@example.com',2),
(39,'system','default_currency','$',2),
(40,'system','items_per_page','10',2),
(41,'system','calendar_google_api_key',NULL,2),
(42,'system','calendar_google_event_address',NULL,2),
(43,'system','paypal_email','paypal_sandbox@example.com',2),
(44,'system','paypal_sandbox','yes',2),
(45,'system','paypal_currency_code','USD',2),
(46,'system','stripe_secret_key','sk_live_lV5nITIlIlzvSZmkfunl5bH3',2),
(47,'system','stripe_publishable_key','pk_live_ogI79OxdoS6YUGWLkkAVwbC3',2),
(48,'system','stripe_currency_code','USD',2),
(49,'system','offline_bank_name','Sample Bank',2),
(50,'system','offline_bank_account','XXXXXX00000000',2),
(51,'system','invoice_color','#00A65A',2),
(52,'system','send_due_date_invoice_reminder_before_days','1',2),
(53,'system','send_due_date_invoice_reminder_after_days','1',2),
(54,'system','logo_text','PROMS',2),
(55,'mailing','smtp_server','server@example.com',3),
(56,'mailing','smtp_port','101',3),
(57,'mailing','smtp_authenticate','1',3),
(58,'mailing','smtp_username','username',3),
(59,'mailing','smtp_password','password',3),
(60,'mailing','smtp_secure_connection','no',3),
(61,'mailing','smtp_reply_from_email','noreply@example.com',3),
(62,'mailing','smtp_from_name','PROMS',3),
(63,'mailing','smtp_from_email','support@example.com',3),
(64,'system','site_name','Project Management System',3),
(65,'system','contact_email','contact@example.com',3),
(66,'system','default_currency','$',3),
(67,'system','items_per_page','10',3),
(68,'system','calendar_google_api_key',NULL,3),
(69,'system','calendar_google_event_address',NULL,3),
(70,'system','paypal_email','paypal_sandbox@example.com',3),
(71,'system','paypal_sandbox','yes',3),
(72,'system','paypal_currency_code','USD',3),
(73,'system','stripe_secret_key','sk_live_lV5nITIlIlzvSZmkfunl5bH3',3),
(74,'system','stripe_publishable_key','pk_live_ogI79OxdoS6YUGWLkkAVwbC3',3),
(75,'system','stripe_currency_code','USD',3),
(76,'system','offline_bank_name','Sample Bank',3),
(77,'system','offline_bank_account','XXXXXX00000000',3),
(78,'system','invoice_color','#00A65A',3),
(79,'system','send_due_date_invoice_reminder_before_days','1',3),
(80,'system','send_due_date_invoice_reminder_after_days','1',3),
(81,'system','logo_text','PROMS',3);

/*Table structure for table `estimate_items` */

DROP TABLE IF EXISTS `estimate_items`;

CREATE TABLE `estimate_items` (
  `u_key` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `o_key` smallint(5) unsigned NOT NULL DEFAULT 0,
  `estimate_id` int(11) unsigned NOT NULL,
  `quantity` decimal(10,2) unsigned NOT NULL,
  `amount` decimal(10,2) unsigned NOT NULL,
  `description` varchar(255) NOT NULL,
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`u_key`),
  KEY `o_key` (`o_key`),
  KEY `target_source` (`target_source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `estimate_items` */

insert  into `estimate_items`(`u_key`,`o_key`,`estimate_id`,`quantity`,`amount`,`description`,`target_source_id`) values 
('47d4c0b8c82e2a693f32b3c9119e6c452ed62997',1,1,1.00,15000.00,'Architectural Plans for council submission',1);

/*Table structure for table `estimates` */

DROP TABLE IF EXISTS `estimates`;

CREATE TABLE `estimates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `estimate_no` varchar(100) NOT NULL,
  `client_id` int(11) unsigned NOT NULL DEFAULT 0,
  `project_id` int(11) unsigned NOT NULL DEFAULT 0,
  `company_id` int(10) unsigned NOT NULL DEFAULT 0,
  `company_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_address` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `subject` varchar(255) NOT NULL,
  `due_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tax` varchar(255) NOT NULL,
  `tax_rate` decimal(10,2) NOT NULL,
  `tax2` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `tax_rate2` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
  `discount_amount_type` enum('percentage','fixed') CHARACTER SET utf32 COLLATE utf32_unicode_ci NOT NULL,
  `note` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `private_note` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by_id` int(11) unsigned NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access_key` varchar(40) NOT NULL,
  `is_online_payment_disabled` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `is_trashed` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `trashed_by_id` int(10) unsigned NOT NULL DEFAULT 0,
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `target_source` (`target_source_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `estimates` */

insert  into `estimates`(`id`,`estimate_no`,`client_id`,`project_id`,`company_id`,`company_name`,`company_address`,`status`,`subject`,`due_date`,`tax`,`tax_rate`,`tax2`,`tax_rate2`,`discount_amount`,`discount_amount_type`,`note`,`private_note`,`total_amount`,`created_at`,`created_by_id`,`updated_at`,`access_key`,`is_online_payment_disabled`,`is_trashed`,`trashed_by_id`,`target_source_id`) values 
(1,'EST-000001',3,1,3,'Hot Point Investments','Plot 579 Naauwpoort',1,'Update Architectural Plans','2021-04-29 00:00:00','',0.00,'',0.00,0.00,'percentage','Update plans with pool, walls, carports and outside building','',15000.00,'2021-04-15 11:45:32',1,'2021-04-15 11:45:32','be996cccc5618b7d380baeefbd9d17e8519f51cf',1,0,0,1);

/*Table structure for table `event_users` */

DROP TABLE IF EXISTS `event_users`;

CREATE TABLE `event_users` (
  `event_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`event_id`,`user_id`),
  KEY `target_source` (`target_source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `event_users` */

/*Table structure for table `events` */

DROP TABLE IF EXISTS `events`;

CREATE TABLE `events` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT '',
  `description` text DEFAULT NULL,
  `start` datetime DEFAULT '0000-00-00 00:00:00',
  `end` datetime DEFAULT '0000-00-00 00:00:00',
  `classname` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by_id` int(10) unsigned DEFAULT NULL,
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `target_source` (`target_source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `events` */

/*Table structure for table `global_labels` */

DROP TABLE IF EXISTS `global_labels`;

CREATE TABLE `global_labels` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `bg_color_hex` varchar(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `target_source` (`target_source_id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8;

/*Data for the table `global_labels` */

insert  into `global_labels`(`id`,`type`,`name`,`bg_color_hex`,`is_default`,`is_active`,`target_source_id`) values 
(1,'PROJECT','NEW','FFEB3B',1,1,1),
(2,'PROJECT','CANCELED','FF5722',0,1,1),
(3,'PROJECT','INPROGRESS','8BC34A',0,1,1),
(4,'PROJECT','PAUSED','FF5722',0,1,1),
(5,'TASK','NEW','F5BA42',1,1,1),
(6,'TASK','CONFIRMED','B276D8',0,1,1),
(7,'TASK','DUPLICATE','31353C',0,1,1),
(8,'TASK','WONT FIX','7277D5',0,1,1),
(9,'TASK','ASSIGNED','D9434E',0,1,1),
(10,'TASK','BLOCKED','E3643E',0,1,1),
(11,'TASK','IN PROGRESS','A5ADB8',0,1,1),
(12,'TASK','FIXED','F59B43',0,1,1),
(13,'TASK','REOPENED','4B8CDC',0,1,1),
(14,'TASK','VERIFIED','B1C252',0,1,1),
(15,'TICKET','NEW','FFEB3B',1,1,1),
(16,'TICKET','INPROGRESS','8BC34A',0,1,1),
(17,'TICKET','PAUSED','FF5722',0,1,1),
(18,'TICKET','DONE','B276D8',0,1,1),
(19,'PROJECT','NEW','FFEB3B',1,1,2),
(20,'PROJECT','CANCELED','FF5722',0,1,2),
(21,'PROJECT','INPROGRESS','8BC34A',0,1,2),
(22,'PROJECT','PAUSED','FF5722',0,1,2),
(23,'TASK','NEW','F5BA42',1,1,2),
(24,'TASK','CONFIRMED','B276D8',0,1,2),
(25,'TASK','DUPLICATE','31353C',0,1,2),
(26,'TASK','WONT FIX','7277D5',0,1,2),
(27,'TASK','ASSIGNED','D9434E',0,1,2),
(28,'TASK','BLOCKED','E3643E',0,1,2),
(29,'TASK','IN PROGRESS','A5ADB8',0,1,2),
(30,'TASK','FIXED','F59B43',0,1,2),
(31,'TASK','REOPENED','4B8CDC',0,1,2),
(32,'TASK','VERIFIED','B1C252',0,1,2),
(33,'TICKET','NEW','FFEB3B',1,1,2),
(34,'TICKET','INPROGRESS','8BC34A',0,1,2),
(35,'TICKET','PAUSED','FF5722',0,1,2),
(36,'TICKET','DONE','B276D8',0,1,2),
(37,'PROJECT','NEW','FFEB3B',1,1,3),
(38,'PROJECT','CANCELED','FF5722',0,1,3),
(39,'PROJECT','INPROGRESS','8BC34A',0,1,3),
(40,'PROJECT','PAUSED','FF5722',0,1,3),
(41,'TASK','NEW','F5BA42',1,1,3),
(42,'TASK','CONFIRMED','B276D8',0,1,3),
(43,'TASK','DUPLICATE','31353C',0,1,3),
(44,'TASK','WONT FIX','7277D5',0,1,3),
(45,'TASK','ASSIGNED','D9434E',0,1,3),
(46,'TASK','BLOCKED','E3643E',0,1,3),
(47,'TASK','IN PROGRESS','A5ADB8',0,1,3),
(48,'TASK','FIXED','F59B43',0,1,3),
(49,'TASK','REOPENED','4B8CDC',0,1,3),
(50,'TASK','VERIFIED','B1C252',0,1,3),
(51,'TICKET','NEW','FFEB3B',1,1,3),
(52,'TICKET','INPROGRESS','8BC34A',0,1,3),
(53,'TICKET','PAUSED','FF5722',0,1,3),
(54,'TICKET','DONE','B276D8',0,1,3);

/*Table structure for table `iconfigurations` */

DROP TABLE IF EXISTS `iconfigurations`;

CREATE TABLE `iconfigurations` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `value` text COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `category_name` (`category_name`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `iconfigurations` */

insert  into `iconfigurations`(`id`,`category_name`,`name`,`value`) values 
(1,'mailing','smtp_server','smtpout.example.com'),
(2,'mailing','smtp_port','25'),
(3,'mailing','smtp_authenticate','1'),
(4,'mailing','smtp_username','username@example.com'),
(5,'mailing','smtp_password','password'),
(6,'mailing','smtp_secure_connection','no'),
(7,'mailing','smtp_reply_from_email','no-reply@example.com'),
(8,'mailing','smtp_from_name','PROMS'),
(9,'mailing','smtp_from_email','hello@example.com'),
(10,'system','paypal_account','paypal@example.com'),
(11,'system','site_name','Project Management System'),
(12,'system','logo_text','Klipspruit');

/*Table structure for table `invoice_item_project_timelogs` */

DROP TABLE IF EXISTS `invoice_item_project_timelogs`;

CREATE TABLE `invoice_item_project_timelogs` (
  `invoice_u_key` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `project_timelog_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`invoice_u_key`,`project_timelog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `invoice_item_project_timelogs` */

/*Table structure for table `invoice_items` */

DROP TABLE IF EXISTS `invoice_items`;

CREATE TABLE `invoice_items` (
  `u_key` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `o_key` smallint(5) unsigned NOT NULL DEFAULT 0,
  `invoice_id` int(11) unsigned NOT NULL,
  `quantity` decimal(10,2) unsigned NOT NULL,
  `amount` decimal(10,2) unsigned NOT NULL,
  `description` varchar(255) NOT NULL,
  `project_timelog_id` int(10) unsigned NOT NULL DEFAULT 0,
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`u_key`),
  KEY `o_key` (`o_key`),
  KEY `target_source` (`target_source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `invoice_items` */

/*Table structure for table `invoices` */

DROP TABLE IF EXISTS `invoices`;

CREATE TABLE `invoices` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_no` varchar(100) NOT NULL,
  `reference` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `client_id` int(11) unsigned NOT NULL DEFAULT 0,
  `project_id` int(11) unsigned NOT NULL DEFAULT 0,
  `company_id` int(10) unsigned NOT NULL DEFAULT 0,
  `company_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_address` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `subject` varchar(255) NOT NULL,
  `due_after_days` int(10) unsigned NOT NULL DEFAULT 0,
  `issue_date` date NOT NULL DEFAULT '0000-00-00',
  `due_date` date NOT NULL DEFAULT '0000-00-00',
  `tax` varchar(255) NOT NULL,
  `tax_rate` decimal(10,2) NOT NULL,
  `tax2` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `tax_rate2` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
  `discount_amount_type` enum('percentage','fixed') CHARACTER SET utf32 COLLATE utf32_unicode_ci NOT NULL,
  `note` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `private_note` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_recurring` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `recurring_start_date` date NOT NULL DEFAULT '0000-00-00',
  `recurring_invoice_id` int(10) unsigned NOT NULL DEFAULT 0,
  `recurring_value` int(10) unsigned NOT NULL DEFAULT 0,
  `recurring_type` enum('days','weeks','months','years') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `no_of_cycles` int(10) unsigned DEFAULT NULL,
  `no_of_cycles_completed` int(10) unsigned NOT NULL DEFAULT 0,
  `due_reminder_date` date NOT NULL DEFAULT '0000-00-00',
  `recurring_reminder_date` date NOT NULL DEFAULT '0000-00-00',
  `next_recurring_date` date NOT NULL DEFAULT '0000-00-00',
  `discount` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `paid_amount` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by_id` int(11) unsigned NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access_key` varchar(40) NOT NULL,
  `is_online_payment_disabled` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `is_cancelled` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `is_trashed` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `trashed_by_id` int(10) unsigned NOT NULL DEFAULT 0,
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `access_key` (`access_key`),
  KEY `target_source` (`target_source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `invoices` */

/*Table structure for table `ipackages` */

DROP TABLE IF EXISTS `ipackages`;

CREATE TABLE `ipackages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `price_per_month` int(10) unsigned NOT NULL,
  `max_storage` int(10) unsigned NOT NULL,
  `max_users` int(10) unsigned NOT NULL,
  `max_projects` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `ipackages` */

insert  into `ipackages`(`id`,`name`,`price_per_month`,`max_storage`,`max_users`,`max_projects`,`created_at`,`updated_at`) values 
(1,'Free',0,2,10,5,'2020-02-16 11:17:29','2020-02-24 07:35:52'),
(2,'Pro',15,10,20,10,'2020-02-16 11:17:29','2020-02-16 11:17:29'),
(3,'Enterprise',29,25,50,40,'2020-02-16 11:17:29','2020-02-16 11:17:29');

/*Table structure for table `ipayment_orders` */

DROP TABLE IF EXISTS `ipayment_orders`;

CREATE TABLE `ipayment_orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payment_method` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `is_verified` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `payer_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `payer_email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `payment_status` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `receiver_email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `send_amount` decimal(10,2) NOT NULL,
  `payment_fee` decimal(10,2) NOT NULL,
  `tax` decimal(10,2) unsigned NOT NULL,
  `txn_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `currency` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `raw_data` text COLLATE utf8_unicode_ci NOT NULL,
  `order_message` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by_id` int(10) unsigned NOT NULL,
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `payment_method` (`payment_method`),
  KEY `created_by_id` (`created_by_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `ipayment_orders` */

/*Table structure for table `ipn_logs` */

DROP TABLE IF EXISTS `ipn_logs`;

CREATE TABLE `ipn_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `data` text COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `is_payment_processed` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `target_source` (`target_source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `ipn_logs` */

/*Table structure for table `iusers` */

DROP TABLE IF EXISTS `iusers`;

CREATE TABLE `iusers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `salt` varchar(13) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `address` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone_number` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by_id` int(10) unsigned DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_login` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_visit` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_activity` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `is_super` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `last_visit` (`last_visit`),
  KEY `last_login` (`last_login`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `iusers` */

insert  into `iusers`(`id`,`name`,`email`,`token`,`salt`,`address`,`phone_number`,`created_at`,`created_by_id`,`updated_at`,`last_login`,`last_visit`,`last_activity`,`is_active`,`is_super`) values 
(1,'Quintin de Bruin','quintin@spectiv.co.za','bddeb7e13348ed87598ee66b7c9f77ca2bb5a8ab','f6656e8d59b38',NULL,NULL,'2021-04-14 18:32:33',NULL,'2021-04-14 18:32:33','2021-04-22 14:30:03','2021-04-22 14:42:21','2021-04-22 14:30:07',1,1),
(2,'Shendi','danevhome01@gmail.com','bddeb7e13348ed87598ee66b7c9f77ca2bb5a8ab','f6656e8d59b38',NULL,NULL,'2021-04-14 18:32:33',NULL,'2021-04-14 18:32:33','2021-04-23 14:53:29','2021-04-22 14:29:43','2021-04-23 14:39:00',1,1);

/*Table structure for table `iwidgets` */

DROP TABLE IF EXISTS `iwidgets`;

CREATE TABLE `iwidgets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4;

/*Data for the table `iwidgets` */

insert  into `iwidgets`(`id`,`title`,`description`,`photo`,`created_at`,`updated_at`) values 
(18,'Hello, everyone','This impressive paella is a perfect party dish and a fun meal to cook together with your guests. Add 1 cup of frozen peas along with the mussels, if you like.','8a02ed8de0077361cf24032752d4b24480d61dbc.png','2021-04-23 15:15:26','2021-04-23 14:35:17'),
(19,'Another widget','This impressive paella is a perfect party dish and a fun meal to cook together with your guests. Add 1 cup of frozen peas along with the mussels, if you like.','ba1ea7a35a0a9bec8502fab0ff895da5fcc34fe0.png','2021-04-23 14:35:42','2021-04-23 14:35:42'),
(20,'New Widget 123','This impressive paella is a perfect party dish and a fun meal to cook together with your guests. Add 1 cup of frozen peas along with the mussels, if you like.','244473b8b98fb8bfa48580b176e492c7ab32fb8a.png','2021-04-23 14:36:44','2021-04-23 14:36:44'),
(21,'Hello, test widget','This impressive paella is a perfect party dish and a fun meal to cook together with your guests. Add 1 cup of frozen peas along with the mussels, if you like.','98e9d5bfd3ffc2074ec9188090798f7b431f781b.png','2021-04-23 14:37:11','2021-04-23 14:37:11'),
(22,'Test Widget 77','This impressive paella is a perfect party dish and a fun meal to cook together with your guests. Add 1 cup of frozen peas along with the mussels, if you like.','ca057c1d64073ac48c79c898a149f216ede54a77.png','2021-04-23 14:37:34','2021-04-23 14:37:34'),
(23,'Good widget','This impressive paella is a perfect party dish and a fun meal to cook together with your guests. Add 1 cup of frozen peas along with the mussels, if you like.					','80d2ec1d97479095411aa7f5e30d02a89934bea4.png','2021-04-23 14:38:05','2021-04-23 14:38:05');

/*Table structure for table `lead_form_element_values` */

DROP TABLE IF EXISTS `lead_form_element_values`;

CREATE TABLE `lead_form_element_values` (
  `element_id` int(10) unsigned NOT NULL,
  `lead_id` int(10) unsigned NOT NULL,
  `form_id` int(10) unsigned NOT NULL,
  `element_value` text COLLATE utf8_unicode_ci NOT NULL,
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  UNIQUE KEY `element_id` (`element_id`,`lead_id`),
  KEY `target_source` (`target_source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `lead_form_element_values` */

/*Table structure for table `lead_form_elements` */

DROP TABLE IF EXISTS `lead_form_elements`;

CREATE TABLE `lead_form_elements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `field_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `field_category` int(10) unsigned NOT NULL,
  `field_data` text COLLATE utf8_unicode_ci NOT NULL,
  `is_required` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `help_text` text COLLATE utf8_unicode_ci NOT NULL,
  `form_id` int(10) unsigned NOT NULL,
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `target_source` (`target_source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `lead_form_elements` */

/*Table structure for table `lead_forms` */

DROP TABLE IF EXISTS `lead_forms`;

CREATE TABLE `lead_forms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `assigned_id` int(10) unsigned NOT NULL DEFAULT 0,
  `default_status_id` int(10) unsigned NOT NULL DEFAULT 0,
  `default_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  `is_collect_userinfo` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `access_key` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `created_by_id` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `access_key` (`access_key`),
  UNIQUE KEY `access_key_2` (`access_key`),
  KEY `target_source` (`target_source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `lead_forms` */

/*Table structure for table `leads` */

DROP TABLE IF EXISTS `leads`;

CREATE TABLE `leads` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `city` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `state` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `postcode` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `country` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `phone_number` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `notes` text COLLATE utf8_unicode_ci NOT NULL,
  `project_id` int(10) unsigned NOT NULL DEFAULT 0,
  `client_id` int(10) unsigned NOT NULL DEFAULT 0,
  `assigned_id` int(10) unsigned NOT NULL DEFAULT 0,
  `status_id` int(10) unsigned NOT NULL DEFAULT 0,
  `source_id` int(10) unsigned NOT NULL DEFAULT 0,
  `form_id` int(10) unsigned NOT NULL DEFAULT 0,
  `is_import_lead` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `ip_address` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `target_source` (`target_source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `leads` */

/*Table structure for table `leads_sources` */

DROP TABLE IF EXISTS `leads_sources`;

CREATE TABLE `leads_sources` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `target_source` (`target_source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `leads_sources` */

/*Table structure for table `leads_statuses` */

DROP TABLE IF EXISTS `leads_statuses`;

CREATE TABLE `leads_statuses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `target_source` (`target_source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `leads_statuses` */

/*Table structure for table `project_comments` */

DROP TABLE IF EXISTS `project_comments`;

CREATE TABLE `project_comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `text` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `project_id` int(10) unsigned DEFAULT NULL,
  `parent_type` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent_id` int(10) unsigned NOT NULL DEFAULT 0,
  `is_private` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `is_trashed` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `trashed_by_id` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by_id` int(10) unsigned DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `parent_type` (`parent_type`,`parent_id`) USING BTREE,
  KEY `created_at` (`created_at`) USING BTREE,
  KEY `target_source` (`target_source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `project_comments` */

/*Table structure for table `project_companies` */

DROP TABLE IF EXISTS `project_companies`;

CREATE TABLE `project_companies` (
  `project_id` int(10) unsigned NOT NULL DEFAULT 0,
  `company_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`project_id`,`company_id`),
  KEY `target_source` (`target_source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `project_companies` */

/*Table structure for table `project_discussions` */

DROP TABLE IF EXISTS `project_discussions`;

CREATE TABLE `project_discussions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `text` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_private` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `is_sticky` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by_id` int(10) unsigned DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_trashed` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `trashed_by_id` int(10) unsigned NOT NULL DEFAULT 0,
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `created_at` (`created_at`),
  KEY `is_sticky` (`is_sticky`),
  KEY `target_source` (`target_source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `project_discussions` */

/*Table structure for table `project_files` */

DROP TABLE IF EXISTS `project_files`;

CREATE TABLE `project_files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `access_key` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `project_id` int(10) unsigned DEFAULT NULL,
  `parent_type` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent_id` int(10) unsigned NOT NULL DEFAULT 0,
  `is_private` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `can_download` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `file_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `file_repository_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `file_extension` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `file_type_string` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `file_size` int(10) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by_id` int(10) unsigned NOT NULL DEFAULT 0,
  `is_trashed` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `trashed_by_id` int(10) unsigned NOT NULL DEFAULT 0,
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `access_key` (`access_key`),
  KEY `parent_type` (`parent_type`,`parent_id`),
  KEY `created_at` (`created_at`),
  KEY `target_source` (`target_source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `project_files` */

/*Table structure for table `project_notes` */

DROP TABLE IF EXISTS `project_notes`;

CREATE TABLE `project_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `project_id` int(11) NOT NULL DEFAULT 0,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `target_source` (`target_source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `project_notes` */

/*Table structure for table `project_task_lists` */

DROP TABLE IF EXISTS `project_task_lists`;

CREATE TABLE `project_task_lists` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_private` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `is_high_priority` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `sort_order` smallint(5) unsigned NOT NULL DEFAULT 1,
  `start_date` date DEFAULT '0000-00-00',
  `due_date` date DEFAULT '0000-00-00',
  `created_at` datetime DEFAULT NULL,
  `created_by_id` int(10) unsigned NOT NULL DEFAULT 0,
  `updated_at` datetime DEFAULT NULL,
  `is_trashed` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `trashed_by_id` int(10) unsigned NOT NULL DEFAULT 0,
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `created_at` (`created_at`),
  KEY `is_high_priority` (`sort_order`),
  KEY `target_source` (`target_source_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `project_task_lists` */

insert  into `project_task_lists`(`id`,`project_id`,`name`,`description`,`is_private`,`is_high_priority`,`sort_order`,`start_date`,`due_date`,`created_at`,`created_by_id`,`updated_at`,`is_trashed`,`trashed_by_id`,`target_source_id`) values 
(1,1,'test','test',1,1,1,'2021-04-02','2021-04-08','2021-04-22 14:32:11',1,'2021-04-22 14:32:11',0,0,1);

/*Table structure for table `project_tasks` */

DROP TABLE IF EXISTS `project_tasks`;

CREATE TABLE `project_tasks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `project_id` int(10) unsigned DEFAULT NULL,
  `task_list_id` int(10) unsigned DEFAULT NULL,
  `assignee_id` int(10) unsigned DEFAULT NULL,
  `label_id` int(10) unsigned NOT NULL DEFAULT 0,
  `start_date` date NOT NULL DEFAULT '0000-00-00',
  `due_date` date NOT NULL DEFAULT '0000-00-00',
  `is_high_priority` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `sort_order` smallint(5) unsigned NOT NULL DEFAULT 1,
  `completed_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `completed_by_id` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by_id` int(10) unsigned DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_trashed` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `trashed_by_id` int(10) unsigned NOT NULL DEFAULT 0,
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `task_list_id` (`task_list_id`),
  KEY `completed_at` (`completed_at`),
  KEY `created_at` (`created_at`),
  KEY `target_source` (`target_source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `project_tasks` */

/*Table structure for table `project_timelogs` */

DROP TABLE IF EXISTS `project_timelogs`;

CREATE TABLE `project_timelogs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `memo` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `project_id` int(10) unsigned NOT NULL,
  `task_id` int(10) unsigned NOT NULL DEFAULT 0,
  `member_id` int(10) unsigned NOT NULL,
  `start_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `total_hours` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
  `hourly_rate` decimal(10,2) unsigned NOT NULL DEFAULT 10.00,
  `is_approved` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `is_timer` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `is_timer_started` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `is_billable` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `created_by_id` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_trashed` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `trashed_by_id` int(10) unsigned NOT NULL DEFAULT 0,
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `target_source` (`target_source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `project_timelogs` */

/*Table structure for table `project_users` */

DROP TABLE IF EXISTS `project_users`;

CREATE TABLE `project_users` (
  `project_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`project_id`,`user_id`),
  KEY `target_source` (`target_source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `project_users` */

insert  into `project_users`(`project_id`,`user_id`,`target_source_id`) values 
(2,4,1);

/*Table structure for table `projects` */

DROP TABLE IF EXISTS `projects`;

CREATE TABLE `projects` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `label_id` int(10) unsigned DEFAULT 0,
  `description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_featured` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `is_enable_timelog` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `is_timelog_visible` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `start_date` date NOT NULL DEFAULT '0000-00-00',
  `due_date` date NOT NULL DEFAULT '0000-00-00',
  `completed_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `completed_by_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by_id` int(10) unsigned DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_trashed` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `completed_at` (`completed_at`),
  KEY `target_source` (`target_source_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `projects` */

insert  into `projects`(`id`,`name`,`company_id`,`label_id`,`description`,`is_featured`,`is_enable_timelog`,`is_timelog_visible`,`start_date`,`due_date`,`completed_at`,`completed_by_id`,`created_at`,`created_by_id`,`updated_at`,`is_trashed`,`target_source_id`) values 
(1,'962 - Jaco Lloyd Plans',3,1,'Update plans with pool, walls, carports and building at back',0,0,0,'2021-04-15','2021-04-29','0000-00-00 00:00:00',NULL,'2021-04-15 11:40:11',1,'2021-04-15 11:40:11',0,1),
(2,'a',3,3,'a',0,0,0,'2021-04-02','2021-04-15','0000-00-00 00:00:00',NULL,'2021-04-23 15:08:32',4,'2021-04-23 15:08:32',0,1);

/*Table structure for table `searchable_objects` */

DROP TABLE IF EXISTS `searchable_objects`;

CREATE TABLE `searchable_objects` (
  `model` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `object_id` int(10) unsigned NOT NULL DEFAULT 0,
  `field_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `field_content` text COLLATE utf8_unicode_ci NOT NULL,
  `project_id` int(10) unsigned NOT NULL DEFAULT 0,
  `is_private` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`model`,`object_id`,`field_name`) USING BTREE,
  KEY `project_id` (`project_id`),
  FULLTEXT KEY `field_content` (`field_content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `searchable_objects` */

insert  into `searchable_objects`(`model`,`object_id`,`field_name`,`field_content`,`project_id`,`is_private`,`created_at`,`target_source_id`) values 
('Projects',1,'name','962 - Jaco Lloyd Plans',1,0,'2021-04-15 11:40:11',1),
('Projects',1,'description','Update plans with pool, walls, carports and building at back',1,0,'2021-04-15 11:40:11',1),
('Estimates',1,'subject','Update Architectural Plans',1,0,'2021-04-15 11:45:32',1),
('ProjectTaskLists',1,'name','test',1,1,'2021-04-22 14:32:11',1),
('ProjectTaskLists',1,'description','test',1,1,'2021-04-22 14:32:11',1),
('Projects',2,'name','a',2,0,'2021-04-23 15:08:32',1),
('Projects',2,'description','a',2,0,'2021-04-23 15:08:32',1);

/*Table structure for table `subscriptions` */

DROP TABLE IF EXISTS `subscriptions`;

CREATE TABLE `subscriptions` (
  `parent_type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `code` varchar(44) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`parent_type`,`parent_id`,`user_id`),
  UNIQUE KEY `code` (`code`),
  KEY `target_source` (`target_source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `subscriptions` */

/*Table structure for table `target_sources` */

DROP TABLE IF EXISTS `target_sources`;

CREATE TABLE `target_sources` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `subscription_id` smallint(5) unsigned NOT NULL,
  `created_by_id` int(10) unsigned NOT NULL,
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `expire_date` date NOT NULL DEFAULT '0000-00-00',
  `storage_used` bigint(20) unsigned NOT NULL DEFAULT 0,
  `storage_limit` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
  `projects_created` int(11) unsigned NOT NULL DEFAULT 0,
  `projects_limit` int(10) unsigned NOT NULL,
  `users_created` int(10) unsigned NOT NULL DEFAULT 1,
  `users_limit` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `target_sources` */

insert  into `target_sources`(`id`,`name`,`subscription_id`,`created_by_id`,`is_active`,`expire_date`,`storage_used`,`storage_limit`,`projects_created`,`projects_limit`,`users_created`,`users_limit`,`created_at`,`updated_at`) values 
(1,'Arqtek',3,1,1,'2021-05-15',0,25.00,2,40,3,50,'2021-04-15 10:49:19','2021-04-23 15:08:32'),
(2,'Spectiv',3,2,1,'2021-05-15',0,25.00,0,40,1,50,'2021-04-15 13:35:33','2021-04-15 13:35:33'),
(3,'123',3,5,1,'2021-05-22',0,25.00,0,40,1,50,'2021-04-22 12:58:50','2021-04-22 12:58:50');

/*Table structure for table `ticket_types` */

DROP TABLE IF EXISTS `ticket_types`;

CREATE TABLE `ticket_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`,`target_source_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `ticket_types` */

insert  into `ticket_types`(`id`,`name`,`is_active`,`target_source_id`) values 
(1,'General Support',1,1),
(2,'General Support',1,2),
(3,'General Support',1,3);

/*Table structure for table `tickets` */

DROP TABLE IF EXISTS `tickets`;

CREATE TABLE `tickets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_no` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `access_key` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `subject` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `project_id` int(10) unsigned NOT NULL DEFAULT 0,
  `type_id` int(10) unsigned NOT NULL DEFAULT 0,
  `label_id` int(10) unsigned NOT NULL DEFAULT 0,
  `assignee_id` int(10) unsigned DEFAULT NULL,
  `priority` enum('low','medium','high','urget') COLLATE utf8_unicode_ci DEFAULT 'low',
  `is_open` tinyint(1) NOT NULL DEFAULT 1,
  `created_by_id` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_trashed` tinyint(1) NOT NULL DEFAULT 0,
  `trashed_by_id` int(10) unsigned NOT NULL DEFAULT 0,
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `access_key` (`access_key`),
  KEY `target_source` (`target_source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `tickets` */

/*Table structure for table `transaction_logs` */

DROP TABLE IF EXISTS `transaction_logs`;

CREATE TABLE `transaction_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `amount` decimal(10,2) unsigned NOT NULL,
  `transaction_type` enum('expense','payment') COLLATE utf8_unicode_ci NOT NULL,
  `reference_id` int(10) unsigned NOT NULL DEFAULT 0,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `credit_account_id` int(10) unsigned NOT NULL,
  `debit_account_id` int(10) unsigned NOT NULL,
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `debit_account_id` (`debit_account_id`),
  KEY `credit_account_id` (`credit_account_id`),
  KEY `transaction_type` (`transaction_type`),
  KEY `transaction_reference` (`transaction_type`,`reference_id`) USING BTREE,
  KEY `target_source` (`target_source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `transaction_logs` */

/*Table structure for table `user_notifications` */

DROP TABLE IF EXISTS `user_notifications`;

CREATE TABLE `user_notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `is_read` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by_id` int(10) unsigned NOT NULL,
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `is_read` (`is_read`),
  KEY `created_by_id` (`created_by_id`),
  KEY `target_source` (`target_source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `user_notifications` */

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `salt` varchar(13) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `address` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone_number` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hourly_rate` decimal(10,2) unsigned NOT NULL DEFAULT 15.00,
  `can_access_invoices_estimates` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `notes` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `avatar_file` varchar(44) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by_id` int(10) unsigned DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_login` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_visit` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_activity` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `is_admin` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `is_trashed` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `trashed_by_id` int(10) unsigned NOT NULL DEFAULT 0,
  `is_group_trashed` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `target_source_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `last_visit` (`last_visit`),
  KEY `company_id` (`company_id`),
  KEY `last_login` (`last_login`),
  KEY `target_source` (`target_source_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`company_id`,`name`,`email`,`token`,`salt`,`address`,`phone_number`,`hourly_rate`,`can_access_invoices_estimates`,`notes`,`avatar_file`,`created_at`,`created_by_id`,`updated_at`,`last_login`,`last_visit`,`last_activity`,`is_active`,`is_admin`,`is_trashed`,`trashed_by_id`,`is_group_trashed`,`target_source_id`) values 
(1,1,'Quintin de Bruin','quintin@de-bruin.co.za','9ec85c820819ad5ed13cf51ba19861ccd966560f','473ced583771c',NULL,NULL,15.00,0,NULL,'aa79ecebdc9b030da8733b8620578a4e9738cf28.png','2021-04-15 08:49:19',NULL,'2021-04-17 06:51:16','2021-04-22 14:30:25','2021-04-22 13:47:52','2021-04-22 14:31:27',1,1,0,0,0,1),
(2,2,'Quintin de Bruin','quintin@spectiv.co.za','6ad6a42401788b357e2025c0257afff36a0141d9','39834e58772f6',NULL,NULL,15.00,0,NULL,NULL,'2021-04-15 11:35:33',NULL,'2021-04-15 11:35:33','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00',1,0,0,0,0,2),
(3,3,'Jaco Lloyd','jacolloyd@hotmail.com','49ba368a9f1ac56e15d343a039aab3a6cfb02ac4','011198cc28dbb','Plot 579 Naauwpoort','+27 82 891 7616',0.00,0,'962',NULL,'2021-04-15 11:39:24',1,'2021-04-15 11:39:24','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00',1,0,0,0,0,1),
(4,1,'Jarun','danevhome01@gmail.com','d994ca3edae7a87f160f9a4b49f40537631fa08f','8ccd8abff18d8','','',0.00,0,'','e11be7ad8b8a3f413bfcc97e3fab286375805589.png','2021-04-17 06:48:13',1,'2021-04-22 14:31:57','2021-04-23 15:04:46','2021-04-22 14:30:14','2021-04-23 15:04:48',1,1,0,0,0,1),
(5,4,'test user','admin@ps.com','98a43ab4a9e1125819de1e35113369aff7dedeeb','19beef73bdc95',NULL,NULL,15.00,0,NULL,NULL,'2021-04-22 13:58:51',NULL,'2021-04-22 13:58:51','2021-04-22 13:59:02','2021-04-22 13:59:02','2021-04-22 14:10:05',1,0,0,0,0,3);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
