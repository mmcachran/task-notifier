# Task Notifier
Parses the Basecamp RSS feed to display old tasks and push recently updated/added tasks into Slack channels.

# Create the database and add an options table:

```
CREATE TABLE `options` (
  `option_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `option_name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `option_value` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`option_id`),
  UNIQUE KEY `option_name` (`option_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

# Create the options
```INSERT INTO `options` (`option_id`, `option_name`, `option_value`)
VALUES
	(1, 'bc_tokens', ''),
	(2, 'last_run', '');```
