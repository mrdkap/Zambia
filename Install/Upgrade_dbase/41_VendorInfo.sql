CREATE TABLE VendorAnnualInfo (
  `conid` INT(11) NOT NULL,
  `badgeid` varchar(15) NOT NULL DEFAULT 0,
  `vendorwhenapplied` datetime NOT NULL,
  `vendorupdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `vendorpayadj` DECIMAL(8, 2) NOT NULL DEFAULT '0.00',
  `vendorpaid` DECIMAL(8, 2),
  `vendorselfcarry` enum('Y','N') NOT NULL DEFAULT 'N',
  `vendoracknowledgement` varchar(50),
  `vendornotes` text,
  `vendordenyreason` text,
  PRIMARY KEY (`conid`,`badgeid`),
  CONSTRAINT `VendorAnnualInfo_ibfk_1` FOREIGN KEY (`conid`) REFERENCES `ConInfo` (`conid`),
  CONSTRAINT `VendorAnnualInfo_ibfk_2` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
  
CREATE TABLE BaseDigitalAd (
  `basedigitaladid` INT(11) NOT NULL auto_increment,
  `basedigitaladname` varchar(50),
  `basedigitaladdesc` text,
  PRIMARY KEY (`basedigitaladid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE BaseSponsorLevel (
  `basesponsorlevelid` INT(11) NOT NULL auto_increment,
  `basesponsorlevelname` varchar(50),
  `basesponsorleveldesc` text,
  PRIMARY KEY (`basesponsorlevelid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE BasePrintAd (
  `baseprintadid` INT(11) NOT NULL auto_increment,
  `baseprintadname` varchar(50),
  `baseprintaddesc` text,
  PRIMARY KEY (`baseprintadid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE BaseVendorFeature (
  `basevendorfeatureid` INT(11) NOT NULL auto_increment,
  `basevendorfeaturename` varchar(50),
  `basevendorfeaturedesc` text,
  PRIMARY KEY (`basevendorfeatureid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE BaseVendorSpace (
  `basevendorspaceid` INT(11) NOT NULL auto_increment,
  `basevendorspacename` varchar(50),
  `basevendorspacedesc` text,
  PRIMARY KEY (`basevendorspaceid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE DigitalAd (
  `digitaladid` INT(11) NOT NULL auto_increment,
  `basedigitaladid` INT(11) NOT NULL,
  `conid` INT(11) NOT NULL,
  `digitaladprice` DECIMAL(8, 2),
  `digitaladnotes` text,
  `digitaladurl` varchar(150),
  `display_order` INT(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`digitaladid`),
  CONSTRAINT `DigitalAd_ibfk_1` FOREIGN KEY (`basedigitaladid`) REFERENCES `BaseDigitalAd` (`basedigitaladid`),
  CONSTRAINT `DigitalAd_ibfk_2` FOREIGN KEY (`conid`) REFERENCES `ConInfo` (`conid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE SponsorLevel (
  `sponsorlevelid` INT(11) NOT NULL auto_increment,
  `basesponsorlevelid` INT(11) NOT NULL,
  `conid` INT(11) NOT NULL,
  `sponsorlevelprice` DECIMAL(8, 2),
  `sponsorlevelnotes` text,
  `sponsorlevelurl` varchar(150),
  `display_order` INT(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sponsorlevelid`),
  CONSTRAINT `SponsorLevel_ibfk_1` FOREIGN KEY (`basesponsorlevelid`) REFERENCES `BaseSponsorLevel` (`basesponsorlevelid`),
  CONSTRAINT `SponsorLevel_ibfk_2` FOREIGN KEY (`conid`) REFERENCES `ConInfo` (`conid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE PrintAd (
  `printadid` INT(11) NOT NULL auto_increment,
  `baseprintadid` INT(11) NOT NULL,
  `conid` INT(11) NOT NULL,
  `printadprice` DECIMAL(8, 2),
  `printadnotes` text,
  `printadurl` varchar(150),
  `display_order` INT(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`printadid`),
  CONSTRAINT `PrintAd_ibfk_1` FOREIGN KEY (`baseprintadid`) REFERENCES `BasePrintAd` (`baseprintadid`),
  CONSTRAINT `PrintAd_ibfk_2` FOREIGN KEY (`conid`) REFERENCES `ConInfo` (`conid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE VendorFeature (
  `vendorfeatureid` INT(11) NOT NULL auto_increment,
  `basevendorfeatureid` INT(11) NOT NULL,
  `conid` INT(11) NOT NULL,
  `vendorfeatureprice` DECIMAL(8, 2),
  `vendorfeaturenotes` varchar(50),
  `display_order` INT(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`vendorfeatureid`),
  CONSTRAINT `VendorFeature_ibfk_1` FOREIGN KEY (`basevendorfeatureid`) REFERENCES `BaseVendorFeature` (`basevendorfeatureid`),
  CONSTRAINT `VendorFeature_ibfk_2` FOREIGN KEY (`conid`) REFERENCES `ConInfo` (`conid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE VendorSpace (
  `vendorspaceid` INT(11) NOT NULL auto_increment,
  `basevendorspaceid` INT(11) NOT NULL,
  `conid` INT(11) NOT NULL,
  `vendorspaceprice` DECIMAL(8, 2),
  `vendorspacenotes` varchar(50),
  `display_order` INT(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`vendorspaceid`),
  CONSTRAINT `VendorSpace_ibfk_1` FOREIGN KEY (`basevendorspaceid`) REFERENCES `BaseVendorSpace` (`basevendorspaceid`),
  CONSTRAINT `VendorSpace_ibfk_2` FOREIGN KEY (`conid`) REFERENCES `ConInfo` (`conid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE VendorHasDigitalAd (
  `badgeid` varchar(15) NOT NULL DEFAULT 0,
  `digitaladid` INT(11) NOT NULL DEFAULT 0,
  `digitaladcount` INT(2) NOT NULL DEFAULT 1,
  PRIMARY KEY (`badgeid`,`digitaladid`),
  KEY `badgeid` (`badgeid`),
  KEY `digitaladid` (`digitaladid`),
  CONSTRAINT `VendorHasDigitalAd_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`),
  CONSTRAINT `VendorHasDigitalAd_ibfk_2` FOREIGN KEY (`digitaladid`) REFERENCES `DigitalAd` (`digitaladid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE VendorHasSponsorLevel (
  `badgeid` varchar(15) NOT NULL DEFAULT 0,
  `sponsorlevelid` INT(11) NOT NULL DEFAULT 0,
  `sponsorlevelcount` INT(2) NOT NULL DEFAULT 1,
  PRIMARY KEY (`badgeid`,`sponsorlevelid`),
  KEY `badgeid` (`badgeid`),
  KEY `sponsorlevelid` (`sponsorlevelid`),
  CONSTRAINT `VendorHasSponsorLevel_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`),
  CONSTRAINT `VendorHasSponsorLevel_ibfk_2` FOREIGN KEY (`sponsorlevelid`) REFERENCES `SponsorLevel` (`sponsorlevelid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE VendorHasPrintAd (
  `badgeid` varchar(15) NOT NULL DEFAULT 0,
  `printadid` INT(11) NOT NULL DEFAULT 0,
  `printadcount` INT(2) NOT NULL DEFAULT 1,
  PRIMARY KEY (`badgeid`,`printadid`),
  KEY `badgeid` (`badgeid`),
  KEY `printadid` (`printadid`),
  CONSTRAINT `VendorHasPrintAd_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`),
  CONSTRAINT `VendorHasPrintAd_ibfk_2` FOREIGN KEY (`printadid`) REFERENCES `PrintAd` (`printadid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE VendorHasFeature (
  `badgeid` varchar(15) NOT NULL DEFAULT 0,
  `vendorfeatureid` INT(11) NOT NULL DEFAULT 0,
  `vendorfeaturecount` INT(2) NOT NULL DEFAULT 1,
  PRIMARY KEY (`badgeid`,`vendorfeatureid`),
  KEY `badgeid` (`badgeid`),
  KEY `vendorfeatureid` (`vendorfeatureid`),
  CONSTRAINT `VendorHasFeature_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`),
  CONSTRAINT `VendorHasFeature_ibfk_2` FOREIGN KEY (`vendorfeatureid`) REFERENCES `VendorFeature` (`vendorfeatureid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE VendorPrefSpace (
  `badgeid` varchar(15) NOT NULL DEFAULT 0,
  `vendorspaceid` INT(11) NOT NULL DEFAULT 0,
  `vendorprefspacerank` enum('1st','2nd','3rd') NOT NULL DEFAULT '1st',
  PRIMARY KEY (`badgeid`,`vendorspaceid`),
  KEY `badgeid` (`badgeid`),
  KEY `vendorspaceid` (`vendorspaceid`),
  CONSTRAINT `VendorPrefSpace_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`),
  CONSTRAINT `VendorPrefSpace_ibfk_2` FOREIGN KEY (`vendorspaceid`) REFERENCES `VendorSpace` (`vendorspaceid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE VendorHasSpace (
  `badgeid` varchar(15) NOT NULL DEFAULT 0,
  `vendorspaceid` INT(11) NOT NULL DEFAULT 0,
  `vendorspacecount` INT(2) NOT NULL DEFAULT 1,
  PRIMARY KEY (`badgeid`,`vendorspaceid`),
  KEY `badgeid` (`badgeid`),
  KEY `vendorspaceid` (`vendorspaceid`),
  CONSTRAINT `VendorHasSpace_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`),
  CONSTRAINT `VendorHasSpace_ibfk_2` FOREIGN KEY (`vendorspaceid`) REFERENCES `VendorSpace` (`vendorspaceid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE VendorTypes (
  `vendortypeid` INT(11) NOT NULL DEFAULT '0',
  `display_order` INT(3) NOT NULL DEFAULT '0',
  `vendortypename` varchar(100) DEFAULT NULL,
  `vendortypedesc` text,
  PRIMARY KEY (`vendortypeid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE VendorIs (
  `badgeid` varchar(15) NOT NULL DEFAULT 0,
  `vendortypeid` INT(11) NOT NULL,
  PRIMARY KEY (`badgeid`,`vendortypeid`),
  KEY `badgeid` (`badgeid`),
  KEY `vendortypeid` (`vendortypeid`),
  CONSTRAINT `VendorIs_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`),
  CONSTRAINT `VendorIs_ibfk_2` FOREIGN KEY (`vendortypeid`) REFERENCES `VendorTypes` (`vendortypeid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE VendorTimesAdj (
  `badgeid` varchar(15) NOT NULL DEFAULT 0,
  `vendortimesadj` INT(3) NOT NULL,
  PRIMARY KEY (`badgeid`, `vendortimesadj`),
  KEY `badgeid` (`badgeid`),
  KEY `vendortimesadj` (`vendortimesadj`),
  CONSTRAINT `VendorTimesAdj_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE VendorStatusTypes (
  `vendorstatustypeid` INT(11) NOT NULL DEFAULT '0',
  `display_order` INT(3) NOT NULL DEFAULT '0',
  `vendorstatustypename` varchar(100) DEFAULT NULL,
  `vendorstatustypedesc` text,
  PRIMARY KEY (`vendorstatustypeid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `VendorStatusTypes` VALUES (1,1,"Proposed","Vendor is proposed by someone other than the vendor themselves.  Possibly the vendor coordinator."), (2,2,"Applied","Vendor has applied to be in this con."), (3,3,"NELA Approval","NELA has approved this vendor to be allowed to apply to Warwic."), (4,4,"Warwick Invoiced","Vendor has been sent/set up with an invoice to be paid for the Warwick fees."), (5,5,"Pending Warwick Approval","Vendor has paid the Warwick Fees, and we have submitted them to Warwick for approval."), (6,6,"Warwick Approval","Approved by Warwick to be able to be at our con."), (7,7,"NELA Invoiced","Vendor has been sent/set up with an invoice for all the things they have asked for."), (8,8,"Paid","Vendor has paid their invoice."), (9,9,"Accepted","Vendor has been accepted, by all parties, and paid all fees."), (10,10,"NELA Denied","This vendor will not be at our show this year, due to a NELA decision."), (11,11,"Warwick Denied","This vendor will not be at our show this year due to a Warwick decision."), (12,12,"Banned","This vendor is banned from our event.  See the Banned Reason.");

CREATE TABLE VendorStatus (
  `conid` INT(11) DEFAULT NULL,
  `badgeid` varchar(15) DEFAULT NULL,
  `vendorstatustypeid` INT(11) DEFAULT NULL,
  PRIMARY KEY (`conid`,`badgeid`),
  KEY `conid` (`conid`),
  KEY `badgeid` (`badgeid`),
  CONSTRAINT `VendorStatus_ibfk_1` FOREIGN KEY (`conid`) REFERENCES `ConInfo` (`conid`),
  CONSTRAINT `VendorStatus_ibfk_2` FOREIGN KEY (`vendorstatustypeid`) REFERENCES `VendorStatusTypes` (`vendorstatustypeid`),
  CONSTRAINT `VendorStatus_ibfk_3` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE VendorEditCodes (
  `vendoreditcode` INT(11) NOT NULL AUTO_INCREMENT,
  `display_order` INT(11) NOT NULL DEFAULT '1',
  `vendoreditname` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`vendoreditcode`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `VendorEditCodes` VALUES (1,1,"Created in Proposed"),(2,2,"Created in Applied"),(3,3,"Changed Status"),(4,4,"Updated Bios"),(5,5,"Updated Fixed information."),(6,6,"Updated yearly informaion");

CREATE TABLE VendorEditHistory (
  `vbadgeid` varchar(15) NOT NULL DEFAULT '0',
  `conid` INT(11) NOT NULL DEFAULT '0',
  `badgeid` varchar(15) DEFAULT NULL,
  `vendoreditcode` INT(11) NOT NULL DEFAULT '0',
  `vendorstatustypeid` INT(11) NOT NULL DEFAULT '0',
  `vendorchangets` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vendordelta` text,
  PRIMARY KEY (`vbadgeid`,`vendorchangets`,`conid`),
  KEY `vbadgeid` (`vbadgeid`),
  KEY `conid` (`conid`),
  KEY `badgeid` (`badgeid`),
  KEY `vendoreditcode` (`vendoreditcode`),
  KEY `vendorstatustypeid` (`vendorstatustypeid`),
  CONSTRAINT `VendorEditHistory_ibfk_1` FOREIGN KEY (`vbadgeid`) REFERENCES `Participants` (`badgeid`),
  CONSTRAINT `VendorEditHistory_ibfk_2` FOREIGN KEY (`conid`) REFERENCES `ConInfo` (`conid`),
  CONSTRAINT `VendorEditHistory_ibfk_3` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`),
  CONSTRAINT `VendorEditHistory_ibfk_4` FOREIGN KEY (`vendoreditcode`) REFERENCES `VendorEditCodes` (`vendoreditcode`),
  CONSTRAINT `VendorEditHistory_ibfk_5` FOREIGN KEY (`vendorstatustypeid`) REFERENCES `VendorStatusTypes` (`vendorstatustypeid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `NotesOnVendors` (
  `noteid` int(11) NOT NULL AUTO_INCREMENT,
  `badgeid` varchar(15) NOT NULL DEFAULT '',
  `rbadgeid` varchar(15) NOT NULL DEFAULT '',
  `conid` int(11) NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `note` text,
  PRIMARY KEY (`noteid`),
  KEY `badgeid` (`badgeid`),
  KEY `rbadgeid` (`rbadgeid`),
  KEY `conid` (`conid`),
  CONSTRAINT `NotesOnVendors_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`),
  CONSTRAINT `NotesOnVendors_ibfk_2` FOREIGN KEY (`rbadgeid`) REFERENCES `Participants` (`badgeid`),
  CONSTRAINT `NotesOnVendors_ibfk_3` FOREIGN KEY (`conid`) REFERENCES `ConInfo` (`conid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
