<?php
$VERSION = "1.06";

function versionQuery()
{
   global $VERSION;
   return ("?version=$VERSION");
}
?>