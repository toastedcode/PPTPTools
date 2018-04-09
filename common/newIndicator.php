<?php

class NewIndicator
{
   public function __construct($dateTime, $newThresholdMinutes)
   {
      $this->isNew = NewIndicator::isNew($dateTime, $newThresholdMinutes);
   }
   
   public function getHtml()
   {
	   $html = "";
	   
	   if ($this->isNew)
	   {
	      $html = "<span class=\"new-indicator\">new</span>";
	   }
	   
      return ($html);
   }
   
   public function render()
   {
      echo (NewIndicator::getHtml());
   }
   
   public static function isNew($dateTime, $newThresholdMinutes)
   {
      $now = new DateTime("now", new DateTimeZone('America/New_York'));
      
      // Determine the interval between the supplied date and the current time.
      $interval = $dateTime->diff($now);
      
      // Convert to minutes.
      $minutes = (($interval->h * 60) + ($interval->i));
      if ($interval->days)
      {
         $minutes = (($interval->days * 24 * 60) + $minutes); 
      }
      
      // Check if it's below the threshold.
      $isNew = $minutes <= $newThresholdMinutes;
   
      return ($isNew);
   }
   
   private $isNew;
}

?>
