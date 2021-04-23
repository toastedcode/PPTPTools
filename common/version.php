<?php
$VERSION = "1.0A";

function versionQuery()
{
   global $VERSION;
   return ("?version=$VERSION");
}
?>