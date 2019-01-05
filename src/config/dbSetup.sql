/*
 * User managemant and login
 */
CREATE TABLE `users`(
    `id` int(4) UNSIGNED NOT NULL auto_increment,
    `username` varchar(65) NOT NULL default '',
    `fullname` varchar(65) NOT NULL default '',
    `country` varchar(5) NOT NULL default '',
    `region` varchar(65) NOT NULL default '',
    `city` varchar(65) NOT NULL default '',
    `address` varchar(255) NOT NULL default '',
    `phone` varchar(65) NOT NULL default '',
    `email` varchar(65) NOT NULL default '',
    `password` varchar(255) NOT NULL default '',
    `rsakey` text NOT NULL default '',
    PRIMARY KEY (`id`)
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `login_history`(
    `id` int(4) UNSIGNED NOT NULL auto_increment,
    `user` int(4) UNSIGNED NOT NULL default 1,
    `date` timestamp NOT NULL default current_timestamp,
    `ip` varchar(45) NOT NULL default '0.0.0.0',
    `auth_token` varchar(65) NOT NULL default '',
    `user_agent` varchar(500) NOT NULL default '',
    `success` tinyint(1) NOT NULL default 0,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user`) REFERENCES users(`id`) ON DELETE CASCADE
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `login_remember`(
    `id` int(4) UNSIGNED NOT NULL auto_increment,
    `user` int(4) UNSIGNED NOT NULL default 0,
    `remember_token` varchar(65) NOT NULL default '',
    `until` timestamp NOT NULL default current_timestamp,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user`) REFERENCES users(`id`) ON DELETE CASCADE
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `login_bans`(
    `id` int(4) UNSIGNED NOT NULL auto_increment,
    `ip` varchar(45) NOT NULL default '0.0.0.0',
    `until` timestamp NOT NULL default current_timestamp,
    PRIMARY KEY (`id`)
) CHARACTER SET utf8 COLLATE utf8_general_ci;



/*
 * Groups
 */
CREATE TABLE `groups`(
    `id` varchar(65) NOT NULL default '',
    `description` text NOT NULL default '',
    PRIMARY KEY(`id`)
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `group_members`(
    `id` int(4) UNSIGNED NOT NULL auto_increment,
    `user` int(4) UNSIGNED NOT NULL default 0,
    `group` varchar(65) NOT NULL default '',
    `primary` tinyint(1) UNSIGNED NOT NULL default 0,
    PRIMARY KEY(`id`),
    FOREIGN KEY(`user`) REFERENCES users(`id`) ON DELETE CASCADE,
    FOREIGN KEY(`group`) REFERENCES groups(`id`) ON DELETE CASCADE
) CHARACTER SET utf8 COLLATE utf8_general_ci;



/*
 * News
 */
CREATE TABLE `news`(
    `id` int(4) UNSIGNED NOT NULL auto_increment,
    `title` varchar(120) NOT NULL default '',
    `content` text NOT NULL default '',
    `publish` timestamp NOT NULL default current_timestamp,
    `user` int(4) UNSIGNED NOT NULL default 0,
    PRIMARY KEY(`id`),
    FOREIGN KEY(`user`) REFERENCES users(`id`)
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `news_target`(
    `id` int(4) UNSIGNED NOT NULL auto_increment,
    `news` int(4) UNSIGNED NOT NULL default 0,
    `group` varchar(65) NOT NULL default '',
    PRIMARY KEY(`id`),
    FOREIGN KEY(`news`) REFERENCES news(`id`) ON DELETE CASCADE,
    FOREIGN KEY(`group`) REFERENCES groups(`id`) ON DELETE CASCADE
) CHARACTER SET utf8 COLLATE utf8_general_ci;



/*
 * Files
 */
CREATE TABLE `files`(
    `id` int(4) UNSIGNED NOT NULL auto_increment,
    `name` varchar(65) NOT NULL default '',
    `ext` varchar(20) NOT NULL default '',
    `size` int(4) UNSIGNED NOT NULL default 0, /* in bytes */
    `token` varchar(64) NOT NULL default '',
    PRIMARY KEY(`id`),
    UNIQUE KEY(`token`)
) CHARACTER SET utf8 COLLATE utf8_general_ci;



/*
 * Exams
 */
CREATE TABLE `exams`(
    `id` int(4) UNSIGNED NOT NULL auto_increment,
    `name` varchar(65) NOT NULL default '',
    `description` text NOT NULL default '',
    `objectives` text NOT NULL default '',
    `specifications` text NOT NULL default '',
    `needed_points` int(4) UNSIGNED NOT NULL default 0,
    `timelimit` int(4) UNSIGNED NOT NULL default 0, /* in seconds; 0=no time limit */
    `stage` tinyint(1) UNSIGNED NOT NULL default 0, /* 0:draft; 1:waiting admission; 2:active; 3:retired */
    PRIMARY KEY(`id`)
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `exam_tasks`(
    `id` int(4) UNSIGNED NOT NULL auto_increment,
    `name` varchar(65) NOT NULL default '',
    `description` text NOT NULL default '',
    `points` int(4) UNSIGNED NOT NULL default 1,
    `exam` int(4) UNSIGNED NOT NULL default 0,
    PRIMARY KEY(`id`),
    FOREIGN KEY(`exam`) REFERENCES exams(`id`) ON DELETE CASCADE
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `exam_task_variants`(
    `id` int(4) UNSIGNED NOT NULL auto_increment,
    `task` int(4) UNSIGNED NOT NULL default 0,
    `instructions` text NOT NULL default '',
    `file` varchar(64) default NULL,
    `correct` text NOT NULL default '',
    `correct_file` varchar(64) default NULL,
    PRIMARY KEY(`id`),
    FOREIGN KEY(`task`) REFERENCES exam_tasks(`id`) ON DELETE CASCADE,
    FOREIGN KEY(`file`) REFERENCES files(`token`) ON DELETE SET NULL,
    FOREIGN KEY(`correct_file`) REFERENCES files(`token`) ON DELETE SET NULL
) CHARACTER SET utf8 COLLATE utf8_general_ci;



/*
 * Organizations
 */
CREATE TABLE `organizations`(
    `id` int(4) UNSIGNED NOT NULL auto_increment,
    `name` varchar(255) NOT NULL default '',
    `country` varchar(5) NOT NULL default '',
    `region` varchar(65) NOT NULL default '',
    `city` varchar(65) NOT NULL default '',
    `address` varchar(255) NOT NULL default '',
    `phone` varchar(65) NOT NULL default '',
    `email` varchar(65) NOT NULL default '',
    `bio` text NOT NULL default '',
    `rsakey` text NOT NULL default '',
    `reputation` tinyint(1) NOT NULL default 10, /* from 0 to 100; 50 is neutral, 0 is untrusted, 100 is fully trusted */
    PRIMARY KEY(`id`)
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `organization_members`(
    `id` int(4) UNSIGNED NOT NULL auto_increment,
    `user` int(4) UNSIGNED NOT NULL default 0,
    `org` int(4) UNSIGNED NOT NULL default 0,
    `role` tinyint(1) UNSIGNED NOT NULL default 0, /* 0:exam invigilator; 1:manager; 2:administrator */
    PRIMARY KEY (`id`),
    FOREIGN KEY(`user`) REFERENCES users(`id`) ON DELETE CASCADE,
    FOREIGN KEY(`org`) REFERENCES organizations(`id`) ON DELETE CASCADE
) CHARACTER SET utf8 COLLATE utf8_general_ci;



/*
 * Examinations
 */
CREATE TABLE `examinations`(
    `id` int(4) UNSIGNED NOT NULL auto_increment,
    `org` int(4) UNSIGNED NOT NULL default 0,
    `exam` int(4) UNSIGNED NOT NULL default 0,
    `scheduled` timestamp NOT NULL default current_timestamp,
    `start_date` timestamp default current_timestamp,
    `invigilator` int(4) UNSIGNED NOT NULL default 0,
    PRIMARY KEY(`id`),
    FOREIGN KEY(`org`) REFERENCES organizations(`id`),
    FOREIGN KEY(`exam`) REFERENCES exams(`id`),
    FOREIGN KEY(`invigilator`) REFERENCES users(`id`)
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `examinees`(
    `id` int(4) UNSIGNED NOT NULL auto_increment,
    `examination` int(4) UNSIGNED NOT NULL default 0,
    `name` varchar(65) NOT NULL default '',
    `country` varchar(5) NOT NULL default '',
    `region` varchar(65) NOT NULL default '',
    `city` varchar(65) NOT NULL default '',
    `address` varchar(255) NOT NULL default '',
    `phone` varchar(65) NOT NULL default '',
    `email` varchar(65) NOT NULL default '',
    `token` varchar(16) NOT NULL default '',
    PRIMARY KEY(`id`),
    FOREIGN KEY(`examination`) REFERENCES examinations(`id`) ON DELETE RESTRICT,
    UNIQUE KEY(`token`)
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `responses`(
    `id` int(4) UNSIGNED NOT NULL auto_increment,
    `examination` int(4) UNSIGNED NOT NULL default 0,
    `examinee` int(4) UNSIGNED NOT NULL default 0,
    `task` int(4) UNSIGNED NOT NULL default 0,
    `answer` text NOT NULL default '',
    `answer_file` varchar(64) default NULL,
    `spenttime` int(4) UNSIGNED NOT NULL default 0,
    `correct` tinyint(1) UNSIGNED NOT NULL default 0, /* 0:incorrect; 1:correct */
    PRIMARY KEY(`id`),
    FOREIGN KEY(`examination`) REFERENCES examinations(`id`) ON DELETE CASCADE,
    FOREIGN KEY(`examinee`) REFERENCES examinees(`id`) ON DELETE CASCADE,
    FOREIGN KEY(`task`) REFERENCES exam_task_variants(`id`) ON DELETE RESTRICT
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `examination_results`(
    `id` int(4) UNSIGNED NOT NULL auto_increment,
    `examination` int(4) UNSIGNED NOT NULL default 0,
    `examinee` int(4) UNSIGNED NOT NULL default 0,
    `points` int(4) UNSIGNED NOT NULL default 0,
    `spenttime` int(4) UNSIGNED NOT NULL default 0,
    `succeeded` tinyint(1) UNSIGNED NOT NULL default 0, /* 0:failed; 1:succeeded */
    PRIMARY KEY(`id`),
    FOREIGN KEY(`examination`) REFERENCES examinations(`id`) ON DELETE RESTRICT,
    FOREIGN KEY(`examinee`) REFERENCES examinees(`id`) ON DELETE RESTRICT
) CHARACTER SET utf8 COLLATE utf8_general_ci;



/*
 * Certifications
 */
CREATE TABLE `certificates`(
    `id` int(4) UNSIGNED NOT NULL auto_increment,
    `examination` int(4) UNSIGNED NOT NULL default 0,
    `examinee` int(4) UNSIGNED NOT NULL default 0,
    `org` int(4) UNSIGNED NOT NULL default 0,
    `invigilator` int(4) UNSIGNED NOT NULL default 0,
    `result` int(4) UNSIGNED NOT NULL default 0,
    `token` varchar(64) NOT NULL default '',
    `sign_inviligator` text default NULL,
    `sign_org` text default NULL,
    `sign_issuer` text default NULL,
    `revoked` tinyint(1) NOT NULL default 0,
    PRIMARY KEY(`id`),
    FOREIGN KEY(`examination`) REFERENCES examinations(`id`) ON DELETE RESTRICT,
    FOREIGN KEY(`examinee`) REFERENCES examinees(`id`) ON DELETE RESTRICT,
    FOREIGN KEY(`org`) REFERENCES organizations(`id`) ON DELETE RESTRICT,
    FOREIGN KEY(`invigilator`) REFERENCES users(`id`) ON DELETE RESTRICT,
    FOREIGN KEY(`result`) REFERENCES examination_results(`id`) ON DELETE RESTRICT
) CHARACTER SET utf8 COLLATE utf8_general_ci;



/*
 * Default records
 */
INSERT INTO users(id, fullname) VALUES (1, 'nouser');
INSERT INTO groups(id) VALUES ('guest'),('admin'),('manager'),('exam_editor'),('variant_editor'),('evaluator'),('inspector');
/* default admin credintials: admin-admin */
INSERT INTO users(username, fullname, password) VALUES ('admin', 'Admin', 'sha1:64000:18:9nTO2bcEmz9Bg3fP79pIatpUbG9/3SYb:HoLjQZoWNtiBaDQuY0O9kOAU');
INSERT INTO group_members(`group`, user) VALUES ('admin', 2);