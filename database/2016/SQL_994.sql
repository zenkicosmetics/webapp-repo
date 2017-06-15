CREATE TABLE `group_users` (
 `id` INT(11) NOT NULL AUTO_INCREMENT,
 `group_id` INT(11) NOT NULL,
 `user_id` INT(11) NOT NULL,
 PRIMARY KEY (`id`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
;


insert into group_users
select 0, group_id, id from users;
