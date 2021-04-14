<?php
$VERSION = "1.09";

function versionQuery()
{
   global $VERSION;
   return ("?version=$VERSION");
}
?>