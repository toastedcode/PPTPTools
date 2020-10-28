<?php
$VERSION = "1.02";

function versionQuery()
{
   global $VERSION;
   return ("?version=$VERSION");
}
?>