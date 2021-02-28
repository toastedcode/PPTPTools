<?php
$VERSION = "1.04";

function versionQuery()
{
   global $VERSION;
   return ("?version=$VERSION");
}
?>