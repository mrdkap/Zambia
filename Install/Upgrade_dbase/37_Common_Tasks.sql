CREATE TABLE `CommonLiaisonTasks` (
  `comliactivityid` int(11) NOT NULL auto_increment,
  `conid` int(11) NOT NULL default '0',
  `comliactivity` text,
  `comliactivitynotes` text,
  `comliactivitystart` date,
  `comlitargettime` date,
  PRIMARY KEY (`comliactivityid`),
  KEY (`conid`),
  CONSTRAINT `CommonLiaisonTasks_ibfk_1` FOREIGN KEY (`conid`) REFERENCES `ConInfo` (`conid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
