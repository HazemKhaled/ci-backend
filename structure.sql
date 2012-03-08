CREATE TABLE `modrators` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`user` VARCHAR( 255 ) NOT NULL ,
`pass` VARCHAR( 255 ) NOT NULL ,
`mail` VARCHAR( 255 ) NOT NULL ,
`lastlogin` TIMESTAMP NOT NULL
) ENGINE = MYISAM ;

CREATE TABLE `rules` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`mod` INT NOT NULL ,
`method` VARCHAR( 255 ) NOT NULL ,
`view` ENUM( '0', '1' ) NOT NULL ,
`add` ENUM( '0', '1' ) NOT NULL ,
`edit` ENUM( '0', '1' ) NOT NULL ,
`delete` ENUM( '0', '1' ) NOT NULL ,
`active` ENUM( '0', '1' ) NOT NULL ,
`search` ENUM( '0', '1' ) NOT NULL ,
`hits` INT NOT NULL
) ENGINE = MYISAM ;

CREATE TABLE `log` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`modrator` INT NOT NULL ,
`action` VARCHAR( 255 ) NOT NULL ,
`time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
`ref_table` VARCHAR( 255 ) NOT NULL ,
`ref_pk` INT NOT NULL ,
INDEX ( `modrator` , `time` )
) ENGINE = MYISAM ;