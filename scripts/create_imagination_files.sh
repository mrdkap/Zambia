#!/bin/sh

# Usage string
usage () {
    echo "
Usage: `basename "$0"` conid

This program collects all the appropriate files into a working
directory to use imagination to create your con-instance's Photo
Lounge DVD, provided you have the imagination software on your system
( http://imagination.sourceforge.net/ ), there is an appropriate
directory of photo lounge submissions within your conid, and you have
selected some of them to be included in said DVD.
"
}

# Make sure we are fed a conid
[ "$#" -lt 1 ] && usage && exit -1

# Set the conid and the subdirectories
TOPDIR="../Local"
WORKDIR=`basename $1`
PLS="Photo_Lounge_Submissions"
PLA="Photo_Lounge_Accepted"

# Error out if there are issues
! [ -d "$TOPDIR/$WORKDIR" ] && echo Error, conid not found \"$WORKDIR\"  && usage && exit -1
! [ -d "$TOPDIR/$WORKDIR/$PLS" ] && echo Error, no photo lounge submissiions directory \"$WORKDIR/$PLS\"  && usage && exit -1

# Create the Accepted directory if it doesn't exist
[ -d $TOPDIR/$WORKDIR/$PLA ] || mkdir $TOPDIR/$WORKDIR/$PLA

# Clean the Accepted directory of all files
[ "$(ls -A $TOPDIR/$WORKDIR/$PLA)" ] && rm $TOPDIR/$WORKDIR/$PLA/*

DBHOSTNAME=`cat $TOPDIR/db_name.php | awk -F'"' '/DBHOSTNAME/ {print $4}'`
DATABASE=`cat $TOPDIR/db_name.php | awk -F'"' '/DBDB/ {print $4}'`
DBUSERNAME=`cat $TOPDIR/db_name.php | awk -F'"' '/DBUSERID/ {print $4}'`
DBPASSWORD=`cat $TOPDIR/db_name.php | awk -F'"' '/DBPASSWORD/ {print $4}'`

for i in `echo 'SELECT photofile FROM PhotoLoungePix WHERE includestatus="a";' | /usr/bin/mysql -h$DBHOSTNAME -u$DBUSERNAME -p$DBPASSWORD $DATABASE -N`
  do
    echo $i
    cp $TOPDIR/$WORKDIR/$PLS/$i $TOPDIR/$WORKDIR/$PLA/$i
  done
