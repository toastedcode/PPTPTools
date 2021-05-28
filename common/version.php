<?php
$VERSION = "1.0B";

function versionQuery()
{
   global $VERSION;
   return ("?version=$VERSION");
}
?>