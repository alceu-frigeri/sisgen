CREATE TABLE `weekdays` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` varchar(3) COMMENT 'short name',
  `longname` varchar(16)
);

CREATE TABLE `roomtype` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `type` varchar(8) COMMENT 'lab, class ...',
  `longname` varchar(32)
);

CREATE TABLE `building` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `acronym` varchar(16) UNIQUE COMMENT 'short designation. data import hack',
  `name` varchar(32) UNIQUE COMMENT 'eletro, mecanica, 12009, ...',
  `location` varchar(32) COMMENT 'vale, centro, ...',
  `description` varchar(64) COMMENT 'just an extra bit of info',
  `mark` tinyint(1) COMMENT 'data import hack'
);

CREATE TABLE `room` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `acronym` varchar(6),
  `name` varchar(32) NOT NULL,
  `building_id` integer unsigned DEFAULT "1" COMMENT '1:centre',
  `roomtype_id` integer unsigned DEFAULT "1" COMMENT '1 theory',
  `capacity` tinyint(1),
  `commentgroup_id` integer unsigned DEFAULT null,
  `roomstatus` integer unsigned DEFAULT "1" COMMENT 'dept status'
);

CREATE TABLE `classsegment` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `day` integer unsigned,
  `start` integer unsigned COMMENT '7<start<22',
  `length` integer unsigned COMMENT '1..4',
  `room_id` integer unsigned,
  `class_id` integer unsigned,
  `prof_id` integer unsigned,
  `commentgroup_id` integer unsigned DEFAULT null COMMENT 'anyone can add a comment',
  `segmentstatus` integer unsigned DEFAULT "1" COMMENT 'dept status'
);

CREATE TABLE `class` (
  `id` integer unsigned PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(3) COMMENT 'A , B , U',
  `agreg` tinyint(1) DEFAULT "0" COMMENT 'if this is the common one (theory)',
  `partof` integer unsigned DEFAULT null COMMENT 'if this is the lab associated with...',
  `sem_id` integer unsigned,
  `discipline_id` integer unsigned,
  `commentgroup_id` integer unsigned DEFAULT null COMMENT 'anyone can add a comment',
  `classstatus` integer unsigned DEFAULT "1" COMMENT 'dept class status'
);

CREATE TABLE `unit` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `acronym` varchar(8) UNIQUE COMMENT 'short name',
  `code` varchar(5) UNIQUE COMMENT 'ENG03, MAT01, ENG10 ...',
  `longname` varchar(64) UNIQUE,
  `iscourse` tinyint(1) COMMENT 'is it a course?',
  `isdept` tinyint(1) COMMENT 'is it a dept?',
  `mark` tinyint(1) COMMENT 'data import hack'
);

CREATE TABLE `discipline` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `dept_id` integer unsigned,
  `code` varchar(10) UNIQUE COMMENT 'ENGxxyyy',
  `long_name` varchar(128),
  `Lcred` integer unsigned COMMENT 'number of hours/week in lab',
  `Tcred` integer unsigned COMMENT 'number of hours/week in class',
  `commentgroup_id` integer unsigned DEFAULT NULL COMMENT 'anyone can add a comment',
  `disciplinestatus` integer unsigned DEFAULT "1" COMMENT 'dept status'
);

CREATE TABLE `semester` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` varchar(10) UNIQUE COMMENT '20xx/I'
);

CREATE TABLE `profkind` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` varchar(6) COMMENT 'DE,20h,40h,subs',
  `longname` varchar(32)
);

CREATE TABLE `prof` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `shortname` varchar(16) COMMENT 'should be unique, but...',
  `name` varchar(128) UNIQUE,
  `dept_id` integer unsigned,
  `profkind_id` integer unsigned,
  `commentgroup_id` integer unsigned DEFAULT null COMMENT 'anyone can add a comment'
);

CREATE TABLE `term` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `code` varchar(8) UNIQUE,
  `name` varchar(32) UNIQUE
);

CREATE TABLE `disciplinekind` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `code` varchar(2) UNIQUE COMMENT 'ob el al',
  `longname` varchar(32)
);

CREATE TABLE `coursedisciplines` (
  `course_id` integer unsigned,
  `term_id` integer unsigned,
  `discipline_id` integer unsigned,
  `disciplinekind_id` integer unsigned,
  `commentgroup_id` integer unsigned DEFAULT null COMMENT 'anyone can add a comment',
  PRIMARY KEY (`course_id`, `discipline_id`)
);

CREATE TABLE `vacancies` (
  `class_id` integer unsigned,
  `course_id` integer unsigned,
  `askednum` integer unsigned COMMENT 'by a course',
  `askedstatus` integer unsigned DEFAULT "1" COMMENT 'course vacancy review status',
  `courseclassstatus` integer unsigned DEFAULT "1" COMMENT 'overall class status',
  `givennum` integer unsigned COMMENT 'by the dept',
  `givenstatus` integer unsigned DEFAULT "1" COMMENT 'depto vacancy review status',
  `deptclassstatus` integer unsigned DEFAULT "1" COMMENT 'overall class status',
  `commentgroup_id` integer unsigned DEFAULT null COMMENT 'anyone can add a comment',
  PRIMARY KEY (`class_id`, `course_id`)
);

CREATE TABLE `role` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `rolename` varchar(32) COMMENT 'admin comgrad depto',
  `description` varchar(128),
  `isadmin` tinyint(1) DEFAULT "0" COMMENT 'is an admin?',
  `can_edit` tinyint(1) DEFAULT "0" COMMENT 'can edit data?',
  `can_dupsem` tinyint(1) DEFAULT "0",
  `chg_vacancies` tinyint(1) DEFAULT "0" COMMENT 'comgrad, can change vacancies',
  `chg_class` tinyint(1) DEFAULT "0" COMMENT 'can change/add a classspan',
  `can_viewlog` tinyint(1) DEFAULT "0",
  `chg_disciplines` tinyint(1) DEFAULT "0",
  `chg_coursedisciplines` tinyint(1) DEFAULT "0",
  `unit_id` integer unsigned NOT NULL
);

CREATE TABLE `account` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `email` varchar(64) UNIQUE NOT NULL COMMENT 'login and email',
  `password` varchar(32) NOT NULL COMMENT 'plain text!',
  `valhash` varchar(64) NOT NULL COMMENT 'validation hash',
  `sessionhash` varchar(64) NOT NULL,
  `activ` tinyint(1) NOT NULL DEFAULT "0" COMMENT 'activ account',
  `hashdate` date NOT NULL COMMENT 'hash creation date',
  `last_login` datetime NOT NULL,
  `name` varchar(128) UNIQUE,
  `displayname` varchar(16) UNIQUE
);

CREATE TABLE `accrole` (
  `account_id` integer unsigned,
  `role_id` integer unsigned,
  PRIMARY KEY (`account_id`, `role_id`)
);

CREATE TABLE `log` (
  `seq` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `date` timestamp,
  `loglevel_id` integer unsigned,
  `user_id` integer unsigned,
  `browserIP` varchar(64) NOT NULL,
  `browseragent` varchar(128) NOT NULL,
  `callersfunction` varchar(64) NOT NULL,
  `action` varchar(64) NOT NULL,
  `logline` varchar(256),
  `logxtra` varchar(128)
);

CREATE TABLE `loglevel` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `level` varchar(16) NOT NULL COMMENT 'info, warning, DBerror, debug, user, secretary, dept, comgrad, admin',
  `str` varchar(32) NOT NULL COMMENT 'short desc',
  `description` varchar(64) NOT NULL COMMENT 'long description'
);

CREATE TABLE `commentgroup` (
  `id` integer unsigned PRIMARY KEY AUTO_INCREMENT COMMENT 'comment group id, to be sure it is unique',
  `inuse` tinyint(1) DEFAULT "0"
);

CREATE TABLE `comment` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT COMMENT 'really unique comment ID',
  `commentgroup_id` integer unsigned COMMENT 'group ref.',
  `account_id` integer unsigned COMMENT 'who did it',
  `cdate` datetime,
  `comment` varchar(512) COMMENT 'comment itself'
);

CREATE TABLE `status` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `status` varchar(16) COMMENT 'tbd, working on, in review, attention, ERR, OK, checked',
  `desc` varchar(32) COMMENT 'more of the same',
  `color` varchar(16) COMMENT 'html, display color'
);

CREATE UNIQUE INDEX `room_index_0` ON `room` (`name`, `building_id`);

CREATE UNIQUE INDEX `class_index_1` ON `class` (`name`, `discipline_id`, `sem_id`);

ALTER TABLE `room` ADD FOREIGN KEY (`building_id`) REFERENCES `building` (`id`);

ALTER TABLE `room` ADD FOREIGN KEY (`roomtype_id`) REFERENCES `roomtype` (`id`);

ALTER TABLE `room` ADD FOREIGN KEY (`roomstatus`) REFERENCES `status` (`id`);

ALTER TABLE `room` ADD FOREIGN KEY (`commentgroup_id`) REFERENCES `commentgroup` (`id`) ON DELETE SET NULL;

ALTER TABLE `classsegment` ADD FOREIGN KEY (`day`) REFERENCES `weekdays` (`id`);

ALTER TABLE `classsegment` ADD FOREIGN KEY (`room_id`) REFERENCES `room` (`id`);

ALTER TABLE `classsegment` ADD FOREIGN KEY (`prof_id`) REFERENCES `prof` (`id`);

ALTER TABLE `classsegment` ADD FOREIGN KEY (`segmentstatus`) REFERENCES `status` (`id`);

ALTER TABLE `classsegment` ADD FOREIGN KEY (`class_id`) REFERENCES `class` (`id`) ON DELETE CASCADE;

ALTER TABLE `classsegment` ADD FOREIGN KEY (`commentgroup_id`) REFERENCES `commentgroup` (`id`) ON DELETE SET NULL;

ALTER TABLE `class` ADD FOREIGN KEY (`classstatus`) REFERENCES `status` (`id`);

ALTER TABLE `class` ADD FOREIGN KEY (`sem_id`) REFERENCES `semester` (`id`) ON DELETE CASCADE;

ALTER TABLE `class` ADD FOREIGN KEY (`discipline_id`) REFERENCES `discipline` (`id`) ON DELETE CASCADE;

ALTER TABLE `class` ADD FOREIGN KEY (`partof`) REFERENCES `class` (`id`) ON DELETE CASCADE;

ALTER TABLE `class` ADD FOREIGN KEY (`commentgroup_id`) REFERENCES `commentgroup` (`id`) ON DELETE SET NULL;

ALTER TABLE `discipline` ADD FOREIGN KEY (`dept_id`) REFERENCES `unit` (`id`);

ALTER TABLE `discipline` ADD FOREIGN KEY (`disciplinestatus`) REFERENCES `status` (`id`);

ALTER TABLE `discipline` ADD FOREIGN KEY (`commentgroup_id`) REFERENCES `commentgroup` (`id`) ON DELETE SET NULL;

ALTER TABLE `prof` ADD FOREIGN KEY (`dept_id`) REFERENCES `unit` (`id`);

ALTER TABLE `prof` ADD FOREIGN KEY (`profkind_id`) REFERENCES `profkind` (`id`);

ALTER TABLE `prof` ADD FOREIGN KEY (`commentgroup_id`) REFERENCES `commentgroup` (`id`) ON DELETE SET NULL;

ALTER TABLE `coursedisciplines` ADD FOREIGN KEY (`course_id`) REFERENCES `unit` (`id`);

ALTER TABLE `coursedisciplines` ADD FOREIGN KEY (`term_id`) REFERENCES `term` (`id`);

ALTER TABLE `coursedisciplines` ADD FOREIGN KEY (`disciplinekind_id`) REFERENCES `disciplinekind` (`id`);

ALTER TABLE `coursedisciplines` ADD FOREIGN KEY (`discipline_id`) REFERENCES `discipline` (`id`) ON DELETE CASCADE;

ALTER TABLE `coursedisciplines` ADD FOREIGN KEY (`commentgroup_id`) REFERENCES `commentgroup` (`id`) ON DELETE SET NULL;

ALTER TABLE `vacancies` ADD FOREIGN KEY (`course_id`) REFERENCES `unit` (`id`);

ALTER TABLE `vacancies` ADD FOREIGN KEY (`askedstatus`) REFERENCES `status` (`id`);

ALTER TABLE `vacancies` ADD FOREIGN KEY (`courseclassstatus`) REFERENCES `status` (`id`);

ALTER TABLE `vacancies` ADD FOREIGN KEY (`givenstatus`) REFERENCES `status` (`id`);

ALTER TABLE `vacancies` ADD FOREIGN KEY (`deptclassstatus`) REFERENCES `status` (`id`);

ALTER TABLE `vacancies` ADD FOREIGN KEY (`class_id`) REFERENCES `class` (`id`) ON DELETE CASCADE;

ALTER TABLE `vacancies` ADD FOREIGN KEY (`commentgroup_id`) REFERENCES `commentgroup` (`id`) ON DELETE SET NULL;

ALTER TABLE `role` ADD FOREIGN KEY (`unit_id`) REFERENCES `unit` (`id`);

ALTER TABLE `accrole` ADD FOREIGN KEY (`account_id`) REFERENCES `account` (`id`);

ALTER TABLE `accrole` ADD FOREIGN KEY (`role_id`) REFERENCES `role` (`id`);

ALTER TABLE `log` ADD FOREIGN KEY (`loglevel_id`) REFERENCES `loglevel` (`id`);

ALTER TABLE `log` ADD FOREIGN KEY (`user_id`) REFERENCES `account` (`id`);

ALTER TABLE `comment` ADD FOREIGN KEY (`account_id`) REFERENCES `account` (`id`) ON DELETE CASCADE;

ALTER TABLE `comment` ADD FOREIGN KEY (`commentgroup_id`) REFERENCES `commentgroup` (`id`) ON DELETE CASCADE;
