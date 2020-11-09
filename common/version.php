<?php
$VERSION = "1.03";

function versionQuery()
{
   global $VERSION;
   return ("?version=$VERSION");
}
?>