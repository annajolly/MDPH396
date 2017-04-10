CREATE TABLE IF NOT EXISTS questionnaire (
  ser_num int(11) unsigned NOT NULL AUTO_INCREMENT,
  name_en varchar(128) NOT NULL,
  name_fr varchar(128) NOT NULL,
  private boolean NOT NULL,
  publish boolean NOT NULL,
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  created_by int(11) unsigned,
  last_updated_by int(11) unsigned,
  PRIMARY KEY (ser_num)
);

CREATE TABLE IF NOT EXISTS questiongroup (
  ser_num bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  name_en varchar(128) NOT NULL,
  name_fr varchar(128) NOT NULL,
  category_en varchar(128) NOT NULL,
  category_fr varchar(128) NOT NULL,
  private boolean NOT NULL,
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  created_by int(11) unsigned,
  last_updated_by int(11) unsigned,
  PRIMARY KEY (ser_num)
);

CREATE TABLE IF NOT EXISTS questionnaire_questiongroup (
  questionnaire_ser_num int(11) unsigned,
  questiongroup_ser_num bigint(20) unsigned,
  position int(11) unsigned,
  optional boolean NOT NULL,
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  created_by int(11) unsigned,
  last_updated_by int(11) unsigned,
  PRIMARY KEY (questionnaire_ser_num, questiongroup_ser_num),
  FOREIGN KEY (questionnaire_ser_num) REFERENCES questionnaire(ser_num),
  FOREIGN KEY (questiongroup_ser_num) REFERENCES questiongroup(ser_num)
);

CREATE TABLE IF NOT EXISTS library (
  ser_num int(11) unsigned NOT NULL AUTO_INCREMENT,
  name_en varchar(128) NOT NULL,
  name_fr varchar(128) NOT NULL,
  private boolean NOT NULL,
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  created_by int(11) unsigned,
  last_updated_by int(11) unsigned,
  PRIMARY KEY (ser_num)
);

CREATE TABLE IF NOT EXISTS questiongroup_library (
  questiongroup_ser_num bigint(20) unsigned NOT NULL,
  library_ser_num int(11) unsigned NOT NULL,
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  created_by int(11) unsigned,
  last_updated_by int(11) unsigned,
  PRIMARY KEY (questiongroup_ser_num, library_ser_num),
  FOREIGN KEY (questiongroup_ser_num) REFERENCES questiongroup(ser_num),
  FOREIGN KEY (library_ser_num) REFERENCES library(ser_num)
);

CREATE TABLE IF NOT EXISTS answertype (
  ser_num int(11) unsigned NOT NULL AUTO_INCREMENT,
  name_en varchar(256),
  name_fr varchar(256),
  category_en varchar(128) NOT NULL,
  category_fr varchar(128) NOT NULL,
  private boolean NOT NULL,
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  created_by int(11) unsigned,
  last_updated_by int(11) unsigned,
  PRIMARY KEY (ser_num)
);

CREATE TABLE IF NOT EXISTS question (
  ser_num bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  text_en varchar(1024) NOT NULL,
  text_fr varchar(1024) NOT NULL,
  questiongroup_ser_num bigint(20) unsigned,
  answertype_ser_num int(11) unsigned,
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  created_by int(11) unsigned,
  last_updated_by int(11) unsigned,
  PRIMARY KEY (ser_num),
  FOREIGN KEY (questiongroup_ser_num) REFERENCES questiongroup(ser_num),
  FOREIGN KEY (answertype_ser_num) REFERENCES answertype(ser_num)
);

CREATE TABLE IF NOT EXISTS answeroption (
  ser_num bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  text_en varchar(256) NOT NULL,
  text_fr varchar(256) NOT NULL,
  caption_en varchar(256),
  caption_fr varchar(256),
  answertype_ser_num int(11) unsigned,
  position int(4) unsigned NOT NULL,
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  created_by int(11) unsigned,
  last_updated_by int(11) unsigned,
  PRIMARY KEY (ser_num),
  FOREIGN KEY (answertype_ser_num) REFERENCES answertype(ser_num)
);

CREATE TABLE IF NOT EXISTS questionnaire_user (
  questionnaire_ser_num int(11) unsigned,
  /*user_ser_num REFERENCES user(ser_num),*/
  user_ser_num int(11) unsigned NOT NULL,
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  created_by int(11) unsigned,
  last_updated_by int(11) unsigned,
  PRIMARY KEY (questionnaire_ser_num, user_ser_num),
  FOREIGN KEY (questionnaire_ser_num) REFERENCES questionnaire(ser_num)
);

CREATE TABLE IF NOT EXISTS questionnaire_patient (
  ser_num bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  questionnaire_ser_num int(11) unsigned,
  patient_ser_num int(11) unsigned NOT NULL,
  /*patient_ser_num REFERENCES patient(ser_num),*/
  user_ser_num int(11) unsigned,
  /*FOREIGN KEY (user_ser_num) REFERENCES user(ser_num),*/
  submitted timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (ser_num),
  FOREIGN KEY (questionnaire_ser_num) REFERENCES questionnaire(ser_num)
);

CREATE TABLE IF NOT EXISTS answer (
  ser_num bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  question_ser_num bigint(20) unsigned,
  answeroption_ser_num bigint(20) unsigned,
  questionnaire_patient_ser_num int(11) unsigned,
  /* remove answered? */
  answered timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (ser_num),
  FOREIGN KEY (questionnaire_patient_ser_num) REFERENCES questionnaire_patient(ser_num),
  FOREIGN KEY (question_ser_num) REFERENCES question(ser_num),
  FOREIGN KEY (answeroption_ser_num) REFERENCES answeroption(ser_num)
);

CREATE TABLE IF NOT EXISTS answertext (
  ser_num bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  answer_ser_num bigint(20) unsigned,
  answer_text text NOT NULL,
  answered timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (ser_num),
  FOREIGN KEY (answer_ser_num) REFERENCES answer(ser_num)
);

CREATE TABLE IF NOT EXISTS tag (
  ser_num int(11) unsigned NOT NULL AUTO_INCREMENT,
  level smallint unsigned NOT NULL,
  ref_tag_ser_num int(11) unsigned,
  name_en varchar(256) NOT NULL,
  name_fr varchar(256) NOT NULL,
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  created_by int(11) unsigned,
  last_updated_by int(11) unsigned,
  PRIMARY KEY (ser_num),
  FOREIGN KEY (ref_tag_ser_num) REFERENCES tag(ser_num)
);

CREATE TABLE IF NOT EXISTS questionnaire_tag (
  questionnaire_ser_num int(11) unsigned,
  tag_ser_num int(11) unsigned,
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  created_by int(11) unsigned,
  last_updated_by int(11) unsigned,
  PRIMARY KEY (questionnaire_ser_num, tag_ser_num),
  FOREIGN KEY (questionnaire_ser_num) REFERENCES questionnaire(ser_num),
  FOREIGN KEY (tag_ser_num) REFERENCES tag(ser_num)
);

CREATE TABLE IF NOT EXISTS questiongroup_tag (
  questiongroup_ser_num bigint(20) unsigned,
  tag_ser_num int(11) unsigned,
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  created_by int(11) unsigned,
  last_updated_by int(11) unsigned,
  PRIMARY KEY (questiongroup_ser_num, tag_ser_num),
  FOREIGN KEY (questiongroup_ser_num) REFERENCES questiongroup(ser_num),
  FOREIGN KEY (tag_ser_num) REFERENCES tag(ser_num)
);
