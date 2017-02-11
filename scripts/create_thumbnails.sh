#!/bin/bash

# Usage string
usage () {
    echo "
Usage: `basename "$0"` conid

This program creates all the appropriate thumbnails for your
con-instance's Photo Lounge, provided you have convert
(/usr/bin/convert) on your system, and there is an appropriate
directory of photo lounge submissions within your conid.
"
}

# Make sure we are fed a conid
[ "$#" -lt 1 ] && usage && exit -1

# Set the conid and the subdirectories
topdir="../Local"
workdir=`basename $1`
PLS="Photo_Lounge_Submissions"

# Error out if there are issues
! [ -d "$topdir/$workdir" ] && echo Error, conid not found \"$workdir\"  && usage && exit -1
! [ -d "$topdir/$workdir/$PLS" ] && echo Error, no photo lounge submissiions directory \"$workdir/$PLS\"  && usage && exit -1

# Create the thumbnail directory if it doesn't exist
[ -d $topdir/$workdir/$PLS/.thmb ] || mkdir $topdir/$workdir/$PLS/.thmb

# Create the thubmnails 
for i in `find $topdir/$workdir/$PLS -type f -exec basename {} \; | sort | uniq -c | grep -v "      2" | cut -c"9-"`
  do 
    echo $i
    /usr/bin/convert $topdir/$workdir/$PLS/$i -resize 300x300 -density 450 $topdir/$workdir/$PLS/.thmb/$i 
  done
