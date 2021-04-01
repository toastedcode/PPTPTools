<?php
$VERSION = "1.07";

function versionQuery()
{
   global $VERSION;
   return ("?version=$VERSION");
}
?>