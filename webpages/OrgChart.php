<?php
require_once('PostingCommonCode.php');
global $link;
$title="Org Chart";
$description="Print the Org Chart with the assistance of Google.";

if ((!empty($_GET['conid'])) AND (is_numeric($_GET['conid']))) {
  $conid=$_GET['conid'];
}

// Test for conid being passed in
if ($conid == "") {
  $conid=$_SESSION['conid'];
}

// Another attempt to generate content, directly from the database
$startstring=<<<EOF
{
    "cols": [
	{"id":"","label":"Name","pattern":"","type":"string"},
	{"id":"","label":"Manager","pattern":"","type":"string"},
	{"id":"","label":"ToolTip","pattern":"","type":"string"}
      ],
    "rows": [
EOF;

$query=<<<EOF
SELECT
    concat('{"c":[{"v":"',HR.conrolenotes,'"},{"v":"',CR.conrolenotes,'"},{"v":""}]}') AS reportmap
  FROM
      HasReports
    JOIN ConRoles CR USING (conroleid)
    JOIN ConRoles HR ON (HR.conroleid=hasreport)
  WHERE
    conid=$conid;
EOF;

//Build the string
list($rows,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);
for ($i=1; $i<=$rows; $i++) { $mapping[]=$element_array[$i]['reportmap']; }
$workstring=implode(",\n\t",$mapping);

$endstring="
    ]
}
";

$string="$startstring $workstring $endstring";
echo "$string";
exit();
?>