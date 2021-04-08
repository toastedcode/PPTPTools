<?php
$VERSION = "1.08";

function versionQuery()
{
   global $VERSION;
   return ("?version=$VERSION");
}
?>