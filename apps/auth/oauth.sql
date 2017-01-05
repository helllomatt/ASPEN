CREATE TABLE `users` (`username` varchar(255) NOT NULL, `password` varchar(2000) DEFAULT NULL, `first_name` varchar(255) DEFAULT NULL, `last_name` varchar(255) DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `users` ADD PRIMARY KEY (`username`);
