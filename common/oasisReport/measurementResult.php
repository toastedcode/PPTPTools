<?php

class MeasurementResult
{
   const UNKNOWN = 0;
   const FIRST = 1;
   const PASS = MeasurementResult::FIRST;
   const WARN_LOW = 2;
   const WARN_HIGH = 3;
   const FAIL_LOW = 4;
   const FAIL_HIGH = 5;
   const FAIL_NULL = 6;
   const LAST = MeasurementResult::FAIL_NULL;
   
   private static $LABELS = array(
      "UNKNOWN",
      "PASS",
      "WARN LOW",
      "WARN HIGH",
      "FAIL LOW",
      "FAIL HIGH",
      "FAIL NULL");
   
   public static $VALUES = array(
      MeasurementResult::PASS,
      MeasurementResult::WARN_LOW,
      MeasurementResult::WARN_HIGH,
      MeasurementResult::FAIL_LOW,
      MeasurementResult::FAIL_HIGH,
      MeasurementResult::FAIL_NULL);
   
   private static $CSS = array(
      "",
      "measurement-result-pass",
      "measurement-result-warn-low",
      "measurement-result-warn-high",
      "measurement-result-fail-low",
      "measurement-result-fail-high",
      "measurement-result-fail-null");
   
   public static function valueOf($token)
   {
      $ordinal = MeasurementResult::UNKNOWN;
      
      for ($i = 0; $i < count(MeasurementResult::$LABELS); $i++)
      {
         if ($token == MeasurementResult::$LABELS[$i])
         {
            $ordinal = $i;
            break;
         }
      }
      
      return ($ordinal);
   }
   
   public static function getLabel($measurementResult)
   {
      $label = MeasurementResult::$LABELS[MeasurementResult::UNKNOWN];
      
      if ($measurementResult <= MeasurementResult::LAST)
      {
         $label = MeasurementResult::$LABELS[$measurementResult];
      }
      
      return ($label);
   }
   
   public static function getCss($measurementResult)
   {
      $cssClass = MeasurementResult::$CSS[MeasurementResult::UNKNOWN];
      
      if ($measurementResult <= MeasurementResult::LAST)
      {
         $cssClass = MeasurementResult::$CSS[$measurementResult];
      }
      
      return ($cssClass);
   }
   
   public static function isPassed($measurementResult)
   {
      return ($measurementResult == MeasurementResult::PASS);
   }
   
   public static function isFailed($measurementResult)
   {
      return (($measurementResult == MeasurementResult::FAIL_LOW) || 
              ($measurementResult == MeasurementResult::FAIL_HIGH) || 
              ($measurementResult == MeasurementResult::FAIL_NULL));
      
   }
   
   public static function isWarning($measurementResult)
   {
      return (($measurementResult == MeasurementResult::WARN_LOW) || 
              ($measurementResult == MeasurementResult::WARN_HIGH));
   }
}

/*
$measurementResult = MeasurementResult::getValue(MeasurementResult::PASS);
echo "getValue(PASS) = {$measurementResult->token}<br>";
 
$measurementResult = MeasurementResult::valueOf("FAIL LOW");
echo "valueOf(\"FAIL LOW\") = {$measurementResult->token}<br>";
 
foreach (MeasurementResult::getValues() as $measurementResult)
{
   echo "[{$measurementResult->ordinal}] : {$measurementResult->token}<br>";
}
*/

?>