-- this script can be run to wipe any con specific data out of a dbase
-- it is used to build the script for building an empty dbase
-- drop table save;
-- drop table Roomtemp;
truncate table EmailQueue;
truncate table UserHasPermissionRole;
truncate table ParticipantAvailability;
truncate table ParticipantAvailabilityTimes;
truncate table ParticipantAvailabilityDays;
truncate table ParticipantHasRole;
truncate table ParticipantInterests;
truncate table ParticipantOnSession;
truncate table ParticipantSessionInterest;
truncate table ParticipantSuggestions;
truncate table NotesOnParticipants;
truncate table TaskList;
truncate table PersonalFlow;
truncate table SessionEditHistory;
truncate table TimeCard;
truncate table Participants;
truncate table CongoDump;
truncate table Schedule;
truncate table Feedback;
truncate table CommentsOnProgramming;
truncate table CommentsOnParticipants;
truncate table CommentsOnSessions;
truncate table SessionHasFeature;
truncate table SessionHasService;
truncate table SessionHasPubChar;
truncate table Sessions;
truncate table PreconHours;
truncate table Rooms;
truncate table AgendaList;
-- you don't want to disable constraints. Really! 
-- you want to run down the real problem instead  
-- because you missed zeroing a table and you need to zero it or eek!
--
-- Now you want to pull a backup using:
--    mysqldump -u username -p dbasename > EmptyDbase.dump
-- 
-- you might very well want to pull in the Initial_Users.sql file now.


