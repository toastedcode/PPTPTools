<?php
$VERSION = "1.01";

function versionQuery()
{
   global $VERSION;
   return ("?version=$VERSION");
}
?>