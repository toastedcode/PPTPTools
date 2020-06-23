<?php
function tidyHtml($html)
{
   //$dom = new DOMDocument();
   //$dom->preserveWhiteSpace = false;
   //$dom->loadHTML($html,LIBXML_HTML_NOIMPLIED);
   //$dom->formatOutput = true;
   
   //return ($dom->saveXML($dom->documentElement));
   return $html;
}
?>