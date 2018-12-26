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
   
   public $ordinal;
   
   public $label;
   
   public static function getValues()
   {
      if (MeasurementType::$values == null)
      {
         MeasurementType::$values =
            array(new MeasurementType(MeasurementType::LOW_LIMIT,  "Low Limit"),
                  new MeasurementType(MeasurementType::LOW_WARN,   "Low Warning"),
                  new MeasurementType(MeasurementType::MEASURED,   "Measured"),
                  new MeasurementType(MeasurementType::HIGH_WARN,  "High Warning"),
                  new MeasurementType(MeasurementType::HIGH_LIMIT, "High Limit"));
      }
      
      return (MeasurementType::$values);
   }
   
   public static function getValue($ordinal)
   {
      $value = new MeasurementType(MeasurementType::UNKNOWN, "");
      
      if (($ordinal >= MeasurementType::FIRST) && ($ordinal <= MeasurementType::LAST))
      {
         $value = MeasurementType::getValues()[$ordinal - MeasurementType::FIRST];
      }
      
      return ($value);
   }
   
   private static $values = null;
   
   private function __construct($ordinal, $label)
   {
      $this->ordinal = $ordinal;
      $this->label = $label;
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