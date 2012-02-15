DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE `system_settings` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `group` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Comment prefix, i.e. site, cms, blog, eblast',
  `key` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `input_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text' COMMENT 'text, textarea, checkbox',
  `editable` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`,`group`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `system_settings` (`id`, `group`, `key`, `value`, `title`, `description`, `input_type`, `editable`) VALUES
(1,	'Site',	'title',	'Your Site',	'',	'',	'',	1),
(2,	'Site',	'info_email',	'you@test.com',	'',	'',	'',	1),
(3,	'Site',	'admin_email',	'admin@test.com',	'',	'',	'',	1),
(4,	'Site',	'meta.robots',	'index, follow',	'',	'',	'',	1),
(5,	'Site',	'meta.keywords',	'Event, Keyword ...',	'',	'',	'textarea',	1),
(6,	'Site',	'meta.description',	'Description goes here...',	'',	'',	'textarea',	1),
(7,	'Site',	'facebook_account',	'',	'',	'',	'',	0),
(8,	'Site',	'twitter_account',	'',	'',	'',	'',	0),
(9,	'Site',	'analytics_uid',	'test-id-51515',	'',	'',	'text',	1),
(38,	'Blog',	'title',	'Cupcake\'s blog',	'Blog Title',	'Your blog title',	'text',	1),
(39,	'Blog',	'description',	'This is my cupcake blog... details go here',	'Blog Description',	'Details about the blog',	'textarea',	1),
(40,	'Blog',	'language',	'en-au',	'Blog Language',	'Default language for this blog (i.e. en-au, en-uk, en-us)',	'text',	1),
(41,	'Blog',	'comment_moderation',	'1',	'Comment Moderation',	'Tick this option if you want to screen comments',	'checkbox',	1),
(42,	'Blog',	'rss.max_items',	'2',	'Maximum number of feeds',	'Set the maximum number of feeds allowed for RSS feeds',	'text',	1);