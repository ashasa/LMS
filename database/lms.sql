/*
SQLyog Ultimate v11.33 (32 bit)
MySQL - 10.1.31-MariaDB : Database - leave_mgmt
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `leaves` */

DROP TABLE IF EXISTS `leaves`;

CREATE TABLE `leaves` (
  `pk_leave_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `reason` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_backup_user_id` bigint(20) unsigned NOT NULL,
  `fk_user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`pk_leave_id`),
  KEY `fk_leaves_1` (`fk_backup_user_id`),
  KEY `fk_leaves_2` (`fk_user_id`),
  CONSTRAINT `fk_leaves_1` FOREIGN KEY (`fk_backup_user_id`) REFERENCES `users` (`pk_user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_leaves_2` FOREIGN KEY (`fk_user_id`) REFERENCES `users` (`pk_user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `leaves` */

/*Table structure for table `m_designations` */

DROP TABLE IF EXISTS `m_designations`;

CREATE TABLE `m_designations` (
  `pk_desig_id` int(11) NOT NULL,
  `designation_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`pk_desig_id`),
  UNIQUE KEY `designation_name_UNIQUE` (`designation_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `m_designations` */

insert  into `m_designations`(`pk_desig_id`,`designation_name`) values (1,'Developer'),(5,'HR Manager'),(4,'Project Manager'),(2,'Senior Developer'),(3,'Team Lead');

/*Table structure for table `m_menus` */

DROP TABLE IF EXISTS `m_menus`;

CREATE TABLE `m_menus` (
  `pk_menu_id` int(11) NOT NULL,
  `menu_label` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `menu_url` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_role_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`pk_menu_id`),
  KEY `fk_m_menu_1_idx` (`fk_role_id`),
  CONSTRAINT `fk_m_menu_1` FOREIGN KEY (`fk_role_id`) REFERENCES `m_roles` (`pk_role_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `m_menus` */

insert  into `m_menus`(`pk_menu_id`,`menu_label`,`menu_url`,`fk_role_id`) values (1,'Add Employee','addemp',1),(2,'List Employees','listemp',1),(3,'Apply For Leave','applyleave',3),(4,'Leave Applications','listleaveappl',2);

/*Table structure for table `m_roles` */

DROP TABLE IF EXISTS `m_roles`;

CREATE TABLE `m_roles` (
  `pk_role_id` int(11) NOT NULL,
  `role_name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`pk_role_id`),
  UNIQUE KEY `role_name_UNIQUE` (`role_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `m_roles` */

insert  into `m_roles`(`pk_role_id`,`role_name`) values (2,'Admin'),(3,'Employee'),(1,'Super admin');

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `migrations` */

insert  into `migrations`(`id`,`migration`,`batch`) values (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_resets_table',1);

/*Table structure for table `password_resets` */

DROP TABLE IF EXISTS `password_resets`;

CREATE TABLE `password_resets` (
  `email` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `password_resets` */

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `pk_user_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `employee_code` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_designation_id` int(11) DEFAULT NULL,
  `date_of_joining` date DEFAULT NULL,
  `mobile_number` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gender` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_role_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`pk_user_id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `employee_code_UNIQUE` (`employee_code`),
  KEY `fk_users_1_idx` (`fk_designation_id`),
  KEY `fk_users_2_idx` (`fk_role_id`),
  CONSTRAINT `fk_users_1` FOREIGN KEY (`fk_designation_id`) REFERENCES `m_designations` (`pk_desig_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_2` FOREIGN KEY (`fk_role_id`) REFERENCES `m_roles` (`pk_role_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`pk_user_id`,`name`,`email`,`email_verified_at`,`password`,`remember_token`,`created_at`,`updated_at`,`employee_code`,`fk_designation_id`,`date_of_joining`,`mobile_number`,`gender`,`address`,`fk_role_id`) values (1,'Super Administrator','sa@gmail.com','2011-01-01 00:00:00','$2y$10$fxKUTt3JczvhG9tYmS7pIuPjhx1lDJxXHKPCrF6w403ZeW2WREHCu','oQ5Vi8YLDKhlzoImf8t8mXLsafWaI65UVL0jWxEohy99UEcZg7jdV7th7PBE',NULL,NULL,NULL,5,'2018-01-01','1234567890','Male',NULL,1);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
