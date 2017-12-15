<?php

function footer($homeAction, $backAction, $nextAction, $checkAction)
{
   echo "<div id=\"footer\">";
   
   $enableClass = ($homeAction) ? "" : "disabled";
   echo "<img class=\"navButton $enableClass\" src=\"home.png\" onclick=\"$homeAction\"/>";
   
   $enableClass = ($backAction) ? "" : "disabled";
   echo "<img class=\"navButton $enableClass\" src=\"back.png\" onclick=\"$backAction\"/>";
   
   $enableClass = ($nextAction) ? "" : "disabled";
   echo "<img class=\"navButton $enableClass\" src=\"next.png\" onclick=\"$nextAction\"/>";
   
   echo "</div>";
}

?>