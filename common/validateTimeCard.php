<?php

require_once 'jobInfo.php';
require_once 'timeCardInfo.php';
require_once 'partWasherEntry.php';
require_once 'partWeightEntry.php';
require_once 'userInfo.php';

class TimeCardThumbnail
{
   public function __construct($timeCardInfo)
   {
      $this->timeCardInfo = $timeCardInfo;
      
      if ($timeCardInfo)
      {
         $this->jobInfo = JobInfo::load($this->timeCardInfo->jobNumber);
         $this->partWeightEntry = PartWeightEntry::getPartWeightEntryForTimeCard($this->timeCardInfo->timeCardId);
         $this->partWasherEntry = PartWasherEntry::getPartWasherEntryForTimeCard($this->timeCardInfo->timeCardId);
      }
   }
   
   public function getHtml()
   {
      $html = "";
      
      if (($this->timeCardInfo) &&
          ($this->jobInfo))
      {
         $user = UserInfo::getUser($this->timeCardInfo->employeeNumber);
         
         $username = "unknown";
         if ($user)
         {
            $username = $user->username;
         }
         
         $weight = "-----";
         if ($this->partWeightEntry)
         {
            $weight = $this->partWeightEntry->weight;
         }
         
         $partCount = "-----";
         if ($this->partWasherEntry)
         {
            $partCount = $this->partWasherEntry->partCount;
         }
         
         $html = 
<<<HEREDOC
         <div class="flex-vertical">
            <div>{$this->timeCardInfo->dateTime}<div>
            <div>{$this->jobInfo->jobNumber}<div>
            <div>$username<div>
            <div>Weight: $weight<div>
            <div>Count: $partCount<div>
         </div>
HEREDOC;
      }
      
      return ($html);
   }
   
   public function render()
   {
      echo ($this->getHtml());
   }

   private $timeCardInfo;
   
   private $jobInfo;
   
   private $partWeightEntry;
   
   private $partWasherEntry;
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
      $html = addslashes($html);
      $html = str_replace(array("   ", "\n", "\t", "\r"), '', $html);
      
      echo "{\"isValidTimeCard\":true, \"timeCardDiv\":\"$html\"}";
   }
   else
   {
      echo "{\"isValidTimeCard\":false}";
   }
}
?>