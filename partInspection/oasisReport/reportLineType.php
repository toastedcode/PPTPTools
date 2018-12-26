<?php

class ReportLineType
{
   const UNKNOWN = 0;
   const FIRST = 1;
   const PART_INSPECTION_START = ReportLineType::FIRST;
   const PART_INSPECTION_DATA = 2;
   const PART_INSPECTION_END = 3;
   const USER_FIELD_LABEL = 4;
   const USER_FIELD_VALUE = 5;
   const LAST = ReportLineType::USER_FIELD_VALUE;
   
   public $ordinal;
   
   public $token;
   
   public $tokenCount;
   
   public static function getValues()
   {
      if (ReportLineType::$values == null)
      {
         ReportLineType::$values =
            array(new ReportLineType(ReportLineType::PART_INSPECTION_START, "START",     2),
                  new ReportLineType(ReportLineType::PART_INSPECTION_DATA,  "DATA",      8),
                  new ReportLineType(ReportLineType::PART_INSPECTION_END,   "END",       3),
                  new ReportLineType(ReportLineType::USER_FIELD_LABEL,      "UserField", 2),
                  new ReportLineType(ReportLineType::USER_FIELD_VALUE,      "UserData",  2));
      }
      
      return (ReportLineType::$values);
   }
   
   public static function getValue($ordinal)
   {
      $value = new ReportLineType(ReportLineType::UNKNOWN, "", 0);
      
      if (($ordinal >= ReportLineType::FIRST) && ($ordinal <= ReportLineType::LAST))
      {
         $value = ReportLineType::getValues()[$ordinal - ReportLineType::FIRST];
      }
      
      return ($value);
   }
   
   public static function valueOf($token)
   {
      $value = new ReportLineType(ReportLineType::UNKNOWN, "", 0);
      
      foreach (ReportLineType::getValues() as $userFieldType)
      {
         if ($userFieldType->token == $token)
         {
            $value = $userFieldType;
            break;
         }
      }
      
      return ($value);
   }
   
   private static $values = null;
   
   private function __construct($ordinal, $token, $tokenCount)
   {
      $this->ordinal = $ordinal;
      $this->token = $token;
      $this->tokenCount = $tokenCount;
   }
}

/*
$userFieldType = ReportLineType::getValue(ReportLineType::PART_INSPECTION_DATA);
echo "getValue(PART_INSPECTION_DATA) = {$userFieldType->token}<br>";
 
$userFieldType = ReportLineType::valueOf("UserField");
echo "valueOf(\"UserField\") = {$userFieldType->token}<br>";
 
foreach (ReportLineType::getValues() as $userFieldType)
{
   echo "[{$userFieldType->ordinal}] : {$userFieldType->token}, {$userFieldType->tokenCount}<br>";
}
*/

?>