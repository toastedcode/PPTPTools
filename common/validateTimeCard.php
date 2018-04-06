<?php

require_once 'timeCardInfo.php';

class TimeCardThumbnail
{
   public function __construct($timeCardInfo)
   {
      $this->timeCardInfo = $timeCardInfo;
   }
   
   public function getHtml()
   {
      $html = "";
      
      if ($this->timeCardInfo)
      {
         $html = "<div>$this->timeCardInfo->timeCardId</div>";
      }
      
      return ($html);
   }
   
   public function render()
   {
      echo ($this->getHtml());
   }
   
   private $timeCardInfo;
}

// *****************************************************************************
//                                   Begin

if (isset($_GET["timeCardId"]))
{
   $timeCardId = $_GET["timeCardId"];
   
   $timeCardInfo = TimeCardInfo::load($timeCardId);
   
   if ($timeCardInfo)
   {
      $timeCardThumbnail = new TimeCardThumbnail($timeCardInfo);
      $html = $timeCardThumbnail->getHtml();
      $html = str_replace(array("   ", "\n", "\t", "\r"), '', $html);
      
      echo "{\"isValidTimeCard\":true, \"timeCardDiv\":\"$html\"}";
   }
   else
   {
      echo "{\"isValidTimeCard\":false}";
   }
}
?>