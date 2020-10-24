<?php
$VERSION = "1.00";

function versionQuery()
{
   global $VERSION;
   return ("?version=$VERSION");
}
?>