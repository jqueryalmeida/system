DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE `system_settings` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `key` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `input_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text',
  `editable` tinyint(1) NOT NULL DEFAULT '1',
  `weight` int(11) DEFAULT NULL,
  `params` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `system_settings` (`id`, `key`, `value`, `title`, `description`, `input_type`, `editable`, `weight`, `params`) VALUES
(6,	'Site.title',	'Your Site',	'',	'',	'',	1,	1,	''),
(7,	'Site.info_email',	'you@test.com',	'',	'',	'',	1,	3,	''),
(8,	'Site.admin_email',	'admin@test.com',	'',	'',	'',	1,	3,	''),
(12,	'Meta.robots',	'index, follow',	'',	'',	'',	1,	6,	''),
(13,	'Meta.keywords',	'Event, Keyword ...',	'',	'',	'textarea',	1,	7,	''),
(14,	'Meta.description',	'Description goes here...',	'',	'',	'textarea',	1,	8,	''),
(20,	'Site.facebook_account',	'',	'',	'',	'',	0,	14,	''),
(21,	'Site.twitter_account',	'',	'',	'',	'',	0,	15,	''),
(33,	'Site.test_add',	'teatadf',	'',	'',	'text',	1,	NULL,	''),
(34,	'Site.analytics_uid',	'test-id-51515',	'',	'',	'text',	1,	NULL,	'');
