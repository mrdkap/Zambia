CREATE TABLE Verbiage (
  `verbiageid` INT(11) NOT NULL auto_increment,
  `verbiageloc` varchar(40) NOT NULL,
  `conid` INT(11) NOT NULL,
  `badgeid` varchar(15) NOT NULL,
  `verbiagerevno` INT(11) NOT NULL auto_increment,
  `verbiagerevnotes` text,
  `verbiagetimestamp` timestamp NOT NULL default CURRENT_TIMESTAMP
  `verbiagecurrent` enum('Yes','No') NOT NULL default 'No',
  PRIMARY KEY (`verbiageid`),
  KEY (`conid`),
  CONSTRAINT `Verbiage_ibfk_1` FOREIGN KEY (`conid`) REFERENCES `ConInfo` (`conid`),
  CONSTRAINT `Verbiage_ibfk_2` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

