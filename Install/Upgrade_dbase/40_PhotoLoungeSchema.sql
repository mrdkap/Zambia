CREATE TABLE PhotoLoungePix (
  `photoid` INT(11) NOT NULL auto_increment, 
  `conid` INT(11) NOT NULL,
  `badgeid` varchar(15) NOT NULL,
  `genconsent` enum('Yes','No') NOT NULL default 'Yes',
  `dvdconsent` enum('Yes','No') NOT NULL default 'Yes',
  `photofile` varchar(60) default NULL,
  `phototitle` varchar(60) default NULL,
  `photoartist` varchar(60) default NULL,
  `photomodel` varchar(60) default NULL,
  `photoloc` varchar(60) default NULL,
  `photonotes` text,
  PRIMARY KEY (`photoid`),
  KEY (`conid`),
  CONSTRAINT `PhotoLoungePix_ibfk_1` FOREIGN KEY (`conid`) REFERENCES `ConInfo` (`conid`),
  CONSTRAINT `PhotoLoungePix_ibfk_2` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

