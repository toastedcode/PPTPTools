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
   
   public $ordinal;
   
   public $token;
   
   public static function getValues()
   {
      if (MeasurementResult::$values == null)
      {
         MeasurementResult::$values =
            array(new MeasurementResult(MeasurementResult::PASS,      "PASS"),
                  new MeasurementResult(MeasurementResult::WARN_LOW,  "WARN LOW"),
                  new MeasurementResult(MeasurementResult::WARN_HIGH, "WARN HIGH"),
                  new MeasurementResult(MeasurementResult::FAIL_LOW,  "FAIL LOW"),
                  new MeasurementResult(MeasurementResult::FAIL_HIGH, "FAIL HIGH"),
                  new MeasurementResult(MeasurementResult::FAIL_NULL, "FAIL NULL"));
      }
      
      return (MeasurementResult::$values);
   }
   
   public static function getValue($ordinal)
   {
      $value = new MeasurementResult(MeasurementResult::UNKNOWN, "");
      
      if (($ordinal >= MeasurementResult::FIRST) && ($ordinal <= MeasurementResult::LAST))
      {
         $value = MeasurementResult::getValues()[$ordinal - MeasurementResult::FIRST];
      }
      
      return ($value);
   }
   
   public static function valueOf($token)
   {
      $value = new MeasurementResult(MeasurementResult::UNKNOWN, "");
      
      foreach (MeasurementResult::getValues() as $measurementResult)
      {
         if ($measurementResult->token == $token)
         {
            $value = $measurementResult;
            break;
         }
      }
      
      return ($value);
   }
   
   public function isPassed()
   {
      return ($this->ordinal == MeasurementResult::PASS);
   }
   
   public function isFailed()
   {
      return (($this->ordinal == MeasurementResult::FAIL_LOW) || 
             ($this->ordinal == MeasurementResult::FAIL_HIGH) || 
             ($this->ordinal == MeasurementResult::FAIL_NULL));
      
   }
   
   public function isWarning()
   {
      return (($this->ordinal == MeasurementResult::WARN_LOW) || 
              ($this->ordinal == MeasurementResult::WARN_HIGH));
   }
   
   private static $values = null;
   
   private function __construct($ordinal, $token)
   {
      $this->ordinal = $ordinal;
      $this->token = $token;
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