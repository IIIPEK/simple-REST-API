# Privileges for `api_user`@`localhost`
CREATE USER 'api_user'@'localhost' IDENTIFIED VIA mysql_native_password USING '*C7DC137E3E42F97376A6E887C75BB9C306C15530';
GRANT USAGE ON *.* TO `api_user`@`localhost` IDENTIFIED BY PASSWORD '*C7DC137E3E42F97376A6E887C75BB9C306C15530';

GRANT SELECT, INSERT, UPDATE, DELETE, CREATE VIEW, SHOW VIEW ON `api_db`.* TO `api_user`@`localhost`;