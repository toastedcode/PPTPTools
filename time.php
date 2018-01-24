<?php
class Time
{
   static public function init()
   {
      date_default_timezone_set('America/New_York');
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
}

/*
Time::init();
$dateTimeString = "2018-01-23 05:50:13";
$toMySql = Time::toMySqlDate($dateTimeString);
$fromMySql = Time::fromMySqlDate($toMySql, "Y-m-d h:i:s");
echo "DateTime: $dateTimeString";
echo "<br/>";
echo "toMySql: $toMySql";
echo "<br/>";
echo "fromMySql: $fromMySql";
*/
?>