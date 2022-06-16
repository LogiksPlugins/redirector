CREATE TABLE `do_redirector` ( 
	`id` int(11) NOT NULL AUTO_INCREMENT, 
	`guid` varchar(100) DEFAULT NULL, 
	`redirection_type` int(11) NOT NULL DEFAULT '302', 
	`source_uri` varchar(255) DEFAULT NULL, 
	`target_uri` varchar(255) DEFAULT NULL, 
	`blocked` enum('false','true') NOT NULL DEFAULT 'false', 
	`created_on` datetime NOT NULL, 
	`created_by` varchar(255) NOT NULL, 
	`edited_on` datetime NOT NULL, 
	`edited_by` varchar(255) NOT NULL, 
	PRIMARY KEY (`id`) 
) ENGINE=MyISAM DEFAULT CHARSET=utf8;