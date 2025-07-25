CREATE TABLE `weekdays` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `abrv` varchar(3) COMMENT 'short name',
  `name` varchar(16)
);

CREATE TABLE `roomtype` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `acronym` varchar(8) COMMENT 'lab, class ...',
  `name` varchar(32)
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
  `capacity` integer unsigned DEFAULT null,
  `status_id` integer unsigned DEFAULT "1" COMMENT 'dept status',
  `comment` varchar(48) DEFAULT null
);

CREATE TABLE `classsegment` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `day` integer unsigned,
  `start` integer unsigned COMMENT '7<start<22',
  `length` integer unsigned COMMENT '1..4',
  `room_id` integer unsigned,
  `class_id` integer unsigned,
  `prof_id` integer unsigned,
  `status_id` integer unsigned DEFAULT "1" COMMENT 'dept status'
);

CREATE TABLE `class` (
  `id` integer unsigned PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(3) COMMENT 'A , B , U',
  `agreg` tinyint(1) DEFAULT "0" COMMENT 'if this is the common one (theory)',
  `partof` integer unsigned DEFAULT null COMMENT 'if this is the lab associated with...',
  `sem_id` integer unsigned,
  `discipline_id` integer unsigned,
  `status_id` integer unsigned DEFAULT "1" COMMENT 'dept class status',
  `scenery` tinyint(1) DEFAULT "0" COMMENT '0 normal, 1 part of a scenery',
  `comment` varchar(48) DEFAULT null
);

CREATE TABLE `unit` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `acronym` varchar(8) UNIQUE COMMENT 'short name',
  `code` varchar(5) UNIQUE COMMENT 'ENG03, MAT01, ENG10 ...',
  `name` varchar(64) UNIQUE,
  `contactname` varchar(64) DEFAULT null,
  `contactemail` varchar(64) DEFAULT null,
  `contactphone` varchar(16) DEFAULT null,
  `iscourse` tinyint(1) COMMENT 'is it a course?',
  `isdept` tinyint(1) COMMENT 'is it a dept?',
  `mark` tinyint(1) COMMENT 'data import hack'
);

CREATE TABLE `coursedept` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `course_id` integer unsigned COMMENT 'in case of course disciplines',
  `dept_id` integer unsigned COMMENT 'associated departemts (prof)'
);

CREATE TABLE `discipline` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `dept_id` integer unsigned,
  `code` varchar(10) UNIQUE COMMENT 'ENGxxyyy',
  `name` varchar(128),
  `Lcred` integer unsigned DEFAULT 0 COMMENT 'number of hours/week in lab',
  `Tcred` integer unsigned DEFAULT 0 COMMENT 'number of hours/week in class',
  `status_id` integer unsigned DEFAULT "1" COMMENT 'dept status',
  `comment` varchar(48) DEFAULT null
);

CREATE TABLE `semester` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` varchar(10) UNIQUE COMMENT '20xx/I',
  `readonly` tinyint(1) DEFAULT 0 COMMENT 'to block changes',
  `imported` tinyint(1) DEFAULT 0 COMMENT 'if it was imported',
);

CREATE TABLE `profkind` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `acronym` varchar(6) COMMENT 'DE,20h,40h,subs',
  `name` varchar(32)
);

CREATE TABLE `prof` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `nickname` varchar(16) COMMENT 'should be unique, but...',
  `name` varchar(128) UNIQUE,
  `dept_id` integer unsigned DEFAULT 1,
  `profkind_id` integer unsigned DEFAULT 1,
  `comment` varchar(48) DEFAULT null
);

CREATE TABLE `term` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `code` varchar(8) UNIQUE,
  `name` varchar(32) UNIQUE
);

CREATE TABLE `disciplinekind` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `code` varchar(2) UNIQUE COMMENT 'ob el al',
  `name` varchar(32)
);

CREATE TABLE `coursedisciplines` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `course_id` integer unsigned DEFAULT null,
  `term_id` integer unsigned DEFAULT null,
  `discipline_id` integer unsigned DEFAULT null,
  `disciplinekind_id` integer unsigned DEFAULT 1
);

CREATE TABLE `vacancies` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `class_id` integer unsigned,
  `course_id` integer unsigned,
  `askednum` integer unsigned DEFAULT 0 COMMENT 'by a course',
  `reservnum` integer unsigned DEFAULT 0 COMMENT 'reserved',
  `askedstatus_id` integer unsigned DEFAULT "1" COMMENT 'course vacancy review status',
  `givennum` integer unsigned DEFAULT 0 COMMENT 'by the dept',
  `givenreservnum` integer unsigned DEFAULT 0 COMMENT 'reserved by dept',
  `givenstatus_id` integer unsigned DEFAULT "1" COMMENT 'depto vacancy review status',
  `usednum` integer unsigned DEFAULT 0 COMMENT 'effectively used ones by course',
  `usedreservnum` integer unsigned DEFAULT 0 COMMENT 'effectively used ones by course',
  `comment` varchar(48) DEFAULT null
);

CREATE TABLE `role` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `rolename` varchar(32) UNIQUE COMMENT 'admin comgrad depto',
  `description` varchar(128) DEFAULT null,
  `isadmin` tinyint(1) DEFAULT "0" COMMENT 'is an admin?',
  `can_edit` tinyint(1) DEFAULT "0" COMMENT 'can edit data?',
  `can_dupsem` tinyint(1) DEFAULT "0",
  `can_class` tinyint(1) DEFAULT "0" COMMENT 'can edit classes',
  `can_addclass` tinyint(1) DEFAULT "0" COMMENT 'can add classes',
  `can_scenery` tinyint(1) DEFAULT "0" COMMENT 'can edit sceneries list',
  `can_vacancies` tinyint(1) DEFAULT "0" COMMENT 'comgrad, can change vacancies',
  `can_disciplines` tinyint(1) DEFAULT "0",
  `can_coursedisciplines` tinyint(1) DEFAULT "0",
  `can_prof` tinyint(1) DEFAULT "0",
  `can_room` tinyint(1) DEFAULT "0",
  `can_viewlog` tinyint(1) DEFAULT "0",
  `unit_id` integer unsigned NOT NULL
);

CREATE TABLE `account` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `email` varchar(64) UNIQUE NOT NULL COMMENT 'login and email',
  `password` varchar(32) NOT NULL COMMENT 'plain text!',
  `chgpasswd` tinyint(1) DEFAULT "0" COMMENT 'should user change password next time?',
  `valhash` varchar(64) NOT NULL DEFAULT 0 COMMENT 'validation hash',
  `sessionhash` varchar(64) NOT NULL DEFAULT 0,
  `activ` tinyint(1) NOT NULL DEFAULT "0" COMMENT 'activ account',
  `name` varchar(128) DEFAULT null COMMENT 'should be unique, but..',
  `displayname` varchar(16) DEFAULT null COMMENT 'should be unique, but..'
);

CREATE TABLE `accrole` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `account_id` integer unsigned,
  `role_id` integer unsigned
);

CREATE TABLE `log` (
  `seq` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `date` timestamp,
  `loglevel_id` integer unsigned,
  `user_id` integer unsigned,
  `browserIP` varchar(64) DEFAULT null,
  `browseragent` varchar(128) DEFAULT null,
  `callerA` varchar(64) DEFAULT null,
  `callerB` varchar(64) DEFAULT null,
  `callerC` varchar(64) DEFAULT null,
  `action` varchar(64) DEFAULT null,
  `logline` varchar(256) DEFAULT null,
  `logxtra` varchar(128) DEFAULT null,
  `dataorg` varchar(256) DEFAULT null,
  `datanew` varchar(256) DEFAULT null
);

CREATE TABLE `loglevel` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `level` varchar(16) NOT NULL COMMENT 'info, warning, DBerror, debug, user, secretary, dept, comgrad, admin',
  `str` varchar(32) NOT NULL COMMENT 'short desc',
  `description` varchar(64) NOT NULL COMMENT 'long description'
);

CREATE TABLE `status` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `status` varchar(16) DEFAULT null COMMENT 'tbd, working on, in review, attention, ERR, OK, checked',
  `desc` varchar(32) DEFAULT null COMMENT 'more of the same',
  `color` varchar(16) DEFAULT null COMMENT 'html, display color'
);

CREATE TABLE `scenery` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT null COMMENT 'scenery short name',
  `desc` varchar(64) DEFAULT null COMMENT 'more of the same',
  `hide` tinyint(1) DEFAULT "0" COMMENT 'scenery hidden from others'
);

CREATE TABLE `sceneryacc` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `scenery_id` integer unsigned,
  `account_id` integer unsigned COMMENT 'who can edit it'
);

CREATE TABLE `sceneryrole` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `scenery_id` integer unsigned,
  `role_id` integer unsigned COMMENT 'who can edit it'
);

CREATE TABLE `sceneryclass` (
  `id` integer unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `class_id` integer unsigned COMMENT 'a class can be part of many sceneries',
  `scenery_id` integer unsigned COMMENT 'a scenery can have many classes'
);

CREATE UNIQUE INDEX `building_room` ON `room` (`building_id`, `name`);

CREATE UNIQUE INDEX `class_day_start` ON `classsegment` (`class_id`, `day`, `start`);

CREATE UNIQUE INDEX `sem_disc_class` ON `class` (`sem_id`, `discipline_id`, `name`);

CREATE UNIQUE INDEX `course_dept` ON `coursedept` (`course_id`, `dept_id`);

CREATE UNIQUE INDEX `course_disc` ON `coursedisciplines` (`course_id`, `discipline_id`);

CREATE UNIQUE INDEX `course_class` ON `vacancies` (`course_id`, `class_id`);

CREATE UNIQUE INDEX `acc_role` ON `accrole` (`account_id`, `role_id`);

CREATE UNIQUE INDEX `scenery_acc` ON `sceneryacc` (`account_id`, `scenery_id`);

CREATE UNIQUE INDEX `scenery_role` ON `sceneryrole` (`role_id`, `scenery_id`);

CREATE UNIQUE INDEX `scenery_class` ON `sceneryclass` (`class_id`, `scenery_id`);

ALTER TABLE `room` ADD FOREIGN KEY (`building_id`) REFERENCES `building` (`id`) ON DELETE CASCADE;

ALTER TABLE `room` ADD FOREIGN KEY (`roomtype_id`) REFERENCES `roomtype` (`id`) ON DELETE RESTRICT;

ALTER TABLE `room` ADD FOREIGN KEY (`status_id`) REFERENCES `status` (`id`) ON DELETE RESTRICT;

ALTER TABLE `classsegment` ADD FOREIGN KEY (`day`) REFERENCES `weekdays` (`id`) ON DELETE RESTRICT;

ALTER TABLE `classsegment` ADD FOREIGN KEY (`room_id`) REFERENCES `room` (`id`) ON DELETE SET NULL;

ALTER TABLE `classsegment` ADD FOREIGN KEY (`prof_id`) REFERENCES `prof` (`id`) ON DELETE SET NULL;

ALTER TABLE `classsegment` ADD FOREIGN KEY (`class_id`) REFERENCES `class` (`id`) ON DELETE CASCADE;

ALTER TABLE `classsegment` ADD FOREIGN KEY (`status_id`) REFERENCES `status` (`id`) ON DELETE RESTRICT;

ALTER TABLE `class` ADD FOREIGN KEY (`sem_id`) REFERENCES `semester` (`id`) ON DELETE CASCADE;

ALTER TABLE `class` ADD FOREIGN KEY (`discipline_id`) REFERENCES `discipline` (`id`) ON DELETE CASCADE;

ALTER TABLE `class` ADD FOREIGN KEY (`partof`) REFERENCES `class` (`id`) ON DELETE SET NULL;

ALTER TABLE `class` ADD FOREIGN KEY (`status_id`) REFERENCES `status` (`id`) ON DELETE RESTRICT;

ALTER TABLE `coursedept` ADD FOREIGN KEY (`course_id`) REFERENCES `unit` (`id`) ON DELETE CASCADE;

ALTER TABLE `coursedept` ADD FOREIGN KEY (`dept_id`) REFERENCES `unit` (`id`) ON DELETE CASCADE;

ALTER TABLE `discipline` ADD FOREIGN KEY (`dept_id`) REFERENCES `unit` (`id`) ON DELETE CASCADE;

ALTER TABLE `discipline` ADD FOREIGN KEY (`status_id`) REFERENCES `status` (`id`) ON DELETE RESTRICT;

ALTER TABLE `prof` ADD FOREIGN KEY (`dept_id`) REFERENCES `unit` (`id`) ON DELETE RESTRICT;

ALTER TABLE `prof` ADD FOREIGN KEY (`profkind_id`) REFERENCES `profkind` (`id`) ON DELETE RESTRICT;

ALTER TABLE `coursedisciplines` ADD FOREIGN KEY (`course_id`) REFERENCES `unit` (`id`) ON DELETE CASCADE;

ALTER TABLE `coursedisciplines` ADD FOREIGN KEY (`term_id`) REFERENCES `term` (`id`) ON DELETE CASCADE;

ALTER TABLE `coursedisciplines` ADD FOREIGN KEY (`discipline_id`) REFERENCES `discipline` (`id`) ON DELETE CASCADE;

ALTER TABLE `coursedisciplines` ADD FOREIGN KEY (`disciplinekind_id`) REFERENCES `disciplinekind` (`id`) ON DELETE RESTRICT;

ALTER TABLE `vacancies` ADD FOREIGN KEY (`course_id`) REFERENCES `unit` (`id`) ON DELETE CASCADE;

ALTER TABLE `vacancies` ADD FOREIGN KEY (`class_id`) REFERENCES `class` (`id`) ON DELETE CASCADE;

ALTER TABLE `vacancies` ADD FOREIGN KEY (`askedstatus_id`) REFERENCES `status` (`id`) ON DELETE RESTRICT;

ALTER TABLE `vacancies` ADD FOREIGN KEY (`givenstatus_id`) REFERENCES `status` (`id`) ON DELETE RESTRICT;

ALTER TABLE `role` ADD FOREIGN KEY (`unit_id`) REFERENCES `unit` (`id`) ON DELETE CASCADE;

ALTER TABLE `accrole` ADD FOREIGN KEY (`account_id`) REFERENCES `account` (`id`) ON DELETE CASCADE;

ALTER TABLE `accrole` ADD FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE;

ALTER TABLE `log` ADD FOREIGN KEY (`loglevel_id`) REFERENCES `loglevel` (`id`) ON DELETE SET NULL;

ALTER TABLE `log` ADD FOREIGN KEY (`user_id`) REFERENCES `account` (`id`) ON DELETE CASCADE;

ALTER TABLE `sceneryacc` ADD FOREIGN KEY (`scenery_id`) REFERENCES `scenery` (`id`) ON DELETE CASCADE;

ALTER TABLE `sceneryacc` ADD FOREIGN KEY (`account_id`) REFERENCES `account` (`id`) ON DELETE CASCADE;

ALTER TABLE `sceneryrole` ADD FOREIGN KEY (`scenery_id`) REFERENCES `scenery` (`id`) ON DELETE CASCADE;

ALTER TABLE `sceneryrole` ADD FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE;

ALTER TABLE `sceneryclass` ADD FOREIGN KEY (`class_id`) REFERENCES `class` (`id`) ON DELETE CASCADE;

ALTER TABLE `sceneryclass` ADD FOREIGN KEY (`scenery_id`) REFERENCES `scenery` (`id`) ON DELETE CASCADE;


ALTER TABLE `account` ADD `last_hashcheck` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'time since last login or check' AFTER `sessionhash`;