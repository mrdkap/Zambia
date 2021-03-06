<?php
require_once('PostingCommonCode.php');
require_once('SubmitCommentOn.php');
global $link;

// LOCALIZATIONS
$title="Comment On Participant";
$description="<P>Please add a comment about a presenter, below.</P>";

// Collaps the three choices into one
if ($_GET["partid"]!=0) {$partid=$_GET["partid"];}

// Submit the comment, if there was one, when this is called
if (isset($_POST["comment"])) {
  SubmitCommentOnParticipants();
 }

// Stop page here if individual has not been selected
if ((!isset($partid)) or ($partid==0)) {
  topofpagereport($title,"<P>Please, indicate which presenter you wish to comment on, properly.  Thank you.</P>",$additionalinfo,$message,$message_error);
  correct_footer();
  exit();
 }

// Start the page properly
topofpagereport($title,$description,$additionalinfo,$message,$message_error);

// Query to get the pubsname of the individuals in question.
$query="SELECT pubsname FROM Participants WHERE badgeid='$partid'";
list($participant,$header_array,$participant_array)=queryreport($query,$link,$title,$description,0);
$pubsname=$participant_array[1]['pubsname'];

?>
<BR>
<FORM name="partcommentform" method=POST action="CommentOnParticipants.php">
  <P>Comment on/for <?php echo htmlspecialchars($pubsname)?> for 
<?php echo $_SESSION['conname']; ?>:
<INPUT type="hidden" name="partid" value="<?php echo $partid; ?>">
<INPUT type="hidden" name="pubsname" value="<?php echo $pubsname; ?>">
<INPUT type="hidden" name="commenter" value="Anonymous">
<DIV class="titledtextarea">
  <LABEL for="comment">Comment:</LABEL>
  <TEXTAREA name="comment" rows=6 cols=72></TEXTAREA>
</DIV>
<BUTTON class="SubmitButton" type="submit" name="submit" >Submit Comment</BUTTON>
</FORM>
<?php
correct_footer();
?>
