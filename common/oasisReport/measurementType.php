<?php

class MeasurementType
{
   const UNKNOWN = 0;
   const FIRST = 1;
   const LOW_LIMIT = MeasurementType::FIRST;
   const LOW_WARN = 2;
   const MEASURED = 3;
   const HIGH_WARN = 4;
   const HIGH_LIMIT = 5;
   const LAST = MeasurementType::HIGH_LIMIT;
   
   private static $LABELS = array(
      "UNKNOWN",
      "FIRST",
      "LOW_LIMIT",
      "LOW_WARN",
      "MEASURED",
      "HIGH_WARN",
      "HIGH_LIMIT");
   
   public static $VALUES = array(
      MeasurementType::LOW_LIMIT,
      MeasurementType::LOW_WARN,
      MeasurementType::MEASURED,
      MeasurementType::LOW_WARN,
      MeasurementType::HIGH_WARN,
      MeasurementType::HIGH_LIMIT);
   
   public static function valueOf($token)
   {
      $ordinal = MeasurementType::UNKNOWN;
      
      for ($i = 0; $i < count(MeasurementType::$LABELS); $i++)
      {
         if ($token == MeasurementType::$LABELS[$i])
         {
            $ordinal = $i;
            break;
         }
      }
      
      return ($ordinal);
   }
   
   public static function getLabel($measurementType)
   {
      $label = MeasurementType::$LABELS[MeasurementType::UNKNOWN];
      
      if ($measurementType <= MeasurementType::LAST)
      {
         $label = MeasurementType::$LABELS[$measurementType];
      }
      
      return ($label);
   }
}

/*
$measurementType = MeasurementType::getValue(MeasurementType::LOW_WARN);
echo "getValue(LOW_WARN) = {$measurementType->label}<br>";
 
foreach (MeasurementType::getValues() as $measurementType)
{
   echo "[{$measurementType->ordinal}] : {$measurementType->label}<br>";
}
*/

?>