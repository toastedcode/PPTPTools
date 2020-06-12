<?php
class Time
{
   // Date format required for initializing date inputs.
   static public $javascriptDateFormat = "Y-m-d";
   
   // Date format required for initializing time inputs.
   static public $javascriptTimeFormat = "H:m";
   
   static public function init()
   {
      date_default_timezone_set('America/New_York');
   }
   
   static public function dateTimeObject($dateTimeString)
   {
      return (new DateTime($dateTimeString, new DateTimeZone('America/New_York')));
   }
   
   static public function now($format)
   {
      $dateTime = new DateTime();
      $dateTime->setTimezone(new DateTimeZone('America/New_York'));
      
      return ($dateTime->format($format));
   }
   
   static public function toMySqlDate($dateString)
   {
      $dateTime = new DateTime($dateString, new DateTimeZone('America/New_York'));
      $dateTime->setTimezone(new DateTimeZone('UTC'));
      
      return ($dateTime->format("Y-m-d H:i:s"));
   }
   
   static public function fromMySqlDate($dateString, $format)
   {
      $dateTime= new DateTime($dateString, new DateTimeZone('UTC'));
      $dateTime->setTimezone(new DateTimeZone('America/New_York'));
      
      return ($dateTime->format($format));
   }
   
   static public function toJavascriptDate($dateString)
   {
      $dateTime = new DateTime($dateString, new DateTimeZone('America/New_York'));
      
      return ($dateTime->format("Y-m-d"));
   }
   
   static public function startOfDay($dateTime)
   {
      $startDateTime = new DateTime($dateTime, new DateTimeZone('America/New_York'));
      
      return ($startDateTime->format("Y-m-d 00:00:00"));
   }
   
   static public function endOfDay($dateTime)
   {
      $endDateTime = new DateTime($dateTime, new DateTimeZone('America/New_York'));
      
      return ($endDateTime->format("Y-m-d 23:59:59"));
   }
   
   static public function differenceSeconds($startTime, $endTime)
   {
      $startDateTime = new DateTime($startTime);
      $endDateTime = new DateTime($endTime);
      
      $diff = $startDateTime->diff($endDateTime);
      
      // Convert to seconds.
      $seconds = (($diff->d * 12 * 60 * 60) + ($diff->h * 60 * 60) + ($diff->i * 60) + $diff->s);
      
      return ($seconds);
   }
}

/*
$now = Time::now("Y-m-d H:i:s");
$toMySql = Time::toMySqlDate($now);
$fromMySql = Time::fromMySqlDate($toMySql, "Y-m-d H:i:s");
echo "now: $now";
echo "<br/>";
echo "toMySql: $toMySql";
echo "<br/>";
echo "fromMySql: $fromMySql";
*/
?>