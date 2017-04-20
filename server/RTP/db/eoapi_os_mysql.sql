DROP TABLE IF EXISTS `eo_api`;
CREATE TABLE `eo_api` (
  `apiID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `apiName` varchar(255) COLLATE utf8_bin NOT NULL,
  `apiURI` varchar(255) COLLATE utf8_bin NOT NULL,
  `apiProtocol` tinyint(1) unsigned NOT NULL,
  `apiFailureMock` text COLLATE utf8_bin,
  `apiSuccessMock` text COLLATE utf8_bin,
  `apiRequestType` tinyint(1) unsigned NOT NULL,
  `apiSuccessMockType` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `apiFailureMockType` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `apiStatus` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `apiUpdateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `groupID` int(11) unsigned NOT NULL,
  `projectID` int(11) unsigned NOT NULL,
  `starred` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `removed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `removeTime` timestamp NULL DEFAULT NULL,
  `apiNoteType` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `apiNoteRaw` text COLLATE utf8_bin,
  `apiNote` text COLLATE utf8_bin,
  `apiRequestParamType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `apiRequestRaw` text COLLATE utf8_bin,
  PRIMARY KEY (`apiID`,`groupID`,`apiURI`),
  KEY `groupID` (`groupID`),
  KEY `apiID` (`apiID`),
  KEY `projectID` (`projectID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
DROP TABLE IF EXISTS `eo_api_cache`;
CREATE TABLE `eo_api_cache` (
  `cacheID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `projectID` int(10) unsigned NOT NULL,
  `groupID` int(10) unsigned NOT NULL,
  `apiID` int(10) unsigned NOT NULL,
  `apiJson` longtext NOT NULL,
  `starred` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`cacheID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `eo_api_group`;
CREATE TABLE `eo_api_group` (
  `groupID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `groupName` varchar(30) COLLATE utf8_bin NOT NULL,
  `projectID` int(11) unsigned NOT NULL,
  `parentGroupID` int(10) unsigned NOT NULL DEFAULT '0',
  `isChild` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`groupID`,`projectID`),
  KEY `groupID` (`groupID`),
  KEY `projectID` (`projectID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
DROP TABLE IF EXISTS `eo_api_header`;
CREATE TABLE `eo_api_header` (
  `headerID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `headerName` varchar(255) NOT NULL,
  `headerValue` tinytext NOT NULL,
  `apiID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`headerID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `eo_api_mock`;
CREATE TABLE `eo_api_mock` (
  `mockID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mockCode` varchar(32) NOT NULL,
  `apiID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`mockID`,`mockCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `eo_api_request_param`;
CREATE TABLE `eo_api_request_param` (
  `paramID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `paramName` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `paramKey` varchar(255) COLLATE utf8_bin NOT NULL,
  `paramValue` text COLLATE utf8_bin NOT NULL,
  `paramType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `paramLimit` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `apiID` int(10) unsigned NOT NULL,
  `paramNotNull` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`paramID`),
  KEY `apiID` (`apiID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
DROP TABLE IF EXISTS `eo_api_request_value`;
CREATE TABLE `eo_api_request_value` (
  `valueID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `value` varchar(255) DEFAULT NULL,
  `valueDescription` varchar(255) DEFAULT NULL,
  `paramID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`valueID`),
  KEY `paramID` (`paramID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `eo_api_result_param`;
CREATE TABLE `eo_api_result_param` (
  `paramID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `paramName` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `paramKey` varchar(255) COLLATE utf8_bin NOT NULL,
  `apiID` int(11) unsigned NOT NULL,
  `paramNotNull` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`paramID`),
  KEY `apiID` (`apiID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
DROP TABLE IF EXISTS `eo_api_result_value`;
CREATE TABLE `eo_api_result_value` (
  `valueID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `value` text COLLATE utf8_bin NOT NULL,
  `valueDescription` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `paramID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`valueID`),
  KEY `resultParamID` (`paramID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
DROP TABLE IF EXISTS `eo_api_test_history`;
CREATE TABLE `eo_api_test_history` (
  `testID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `apiID` int(10) unsigned NOT NULL,
  `requestInfo` longtext,
  `resultInfo` longtext,
  `testTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `projectID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`testID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `eo_conn_database`;
CREATE TABLE `eo_conn_database` (
  `connID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dbID` int(10) unsigned NOT NULL,
  `userID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`connID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `eo_conn_project`;
CREATE TABLE `eo_conn_project` (
  `connID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `projectID` int(11) unsigned NOT NULL,
  `userID` int(11) unsigned NOT NULL,
  `userType` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`connID`,`projectID`,`userID`),
  KEY `projectID` (`projectID`),
  KEY `eo_conn_ibfk_2` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
DROP TABLE IF EXISTS `eo_database`;
CREATE TABLE `eo_database` (
  `dbID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dbName` varchar(255) NOT NULL,
  `dbVersion` float unsigned NOT NULL,
  `dbUpdateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`dbID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `eo_database_table`;
CREATE TABLE `eo_database_table` (
  `dbID` int(10) unsigned NOT NULL,
  `tableID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tableName` varchar(255) NOT NULL,
  `tableDescription` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`tableID`,`dbID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `eo_database_table_field`;
CREATE TABLE `eo_database_table_field` (
  `fieldID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fieldName` varchar(255) NOT NULL,
  `fieldType` varchar(10) NOT NULL,
  `fieldLength` varchar(10) NOT NULL,
  `isNotNull` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `isPrimaryKey` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fieldDescription` varchar(255) DEFAULT NULL,
  `tableID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`fieldID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `eo_message`;
CREATE TABLE `eo_message` (
  `msgID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `toUserID` int(10) unsigned NOT NULL,
  `fromUserID` int(10) unsigned NOT NULL,
  `msgSendTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `msgType` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) DEFAULT NULL,
  `msg` text NOT NULL,
  `isRead` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `otherMsg` text,
  PRIMARY KEY (`msgID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `eo_project`;
CREATE TABLE `eo_project` (
  `projectID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `projectType` tinyint(1) unsigned NOT NULL,
  `projectName` varchar(30) COLLATE utf8_bin NOT NULL,
  `projectUpdateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `projectVersion` varchar(6) COLLATE utf8_bin NOT NULL DEFAULT '1.0',
  PRIMARY KEY (`projectID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
DROP TABLE IF EXISTS `eo_project_environment`;
CREATE TABLE `eo_project_environment` (
  `envID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `envName` varchar(255) NOT NULL,
  `envURI` varchar(255) NOT NULL,
  `projectID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`envID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `eo_project_status_code`;
CREATE TABLE `eo_project_status_code` (
  `codeID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `codeDescription` varchar(255) NOT NULL,
  `groupID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`codeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `eo_project_status_code_group`;
CREATE TABLE `eo_project_status_code_group` (
  `groupID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `projectID` int(10) unsigned NOT NULL,
  `groupName` varchar(255) NOT NULL,
  `parentGroupID` int(10) unsigned NOT NULL DEFAULT '0',
  `isChild` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`groupID`,`projectID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `eo_user`;
CREATE TABLE `eo_user` (
  `userID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userName` varchar(60) NOT NULL,
  `userPassword` varchar(60) NOT NULL,
  `userNickName` varchar(16) NOT NULL DEFAULT '',
  PRIMARY KEY (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;