<?php

class UserFieldType
{
   const UNKNOWN = 0;
   const FIRST = 1;
   const INSPECTION_TYPE = UserFieldType::FIRST;
   const EMPLOYEE_NUMBER = 2;
   const COMMENTS = 3;
   const PART_COUNT = 4;
   const SAMPLE_SIZE = 5;
   const MACHINE_NUMBER = 6;
   const DATE = 7;
   const PART_NUMBER = 8;
   const EFFICIENCY = 9;
   const LAST = UserFieldType::EFFICIENCY;
   
   public $ordinal;
   
   public $label;
   
   public static function getValues()
   {
      if (UserFieldType::$values == null)
      {
         UserFieldType::$values =
            array(new UserFieldType(UserFieldType::INSPECTION_TYPE, "Inspection Type"),
                  new UserFieldType(UserFieldType::EMPLOYEE_NUMBER, "Employee #"),
                  new UserFieldType(UserFieldType::COMMENTS,        "Comments"),
                  new UserFieldType(UserFieldType::PART_COUNT,      "Part Count"),
                  new UserFieldType(UserFieldType::SAMPLE_SIZE,     "Sample Size"),
                  new UserFieldType(UserFieldType::MACHINE_NUMBER,  "Machine #"),
                  new UserFieldType(UserFieldType::DATE,            "Date"),
                  new UserFieldType(UserFieldType::PART_NUMBER,     "Part #"),
                  new UserFieldType(UserFieldType::PART_NUMBER,     "Efficiency"));
      }
      
      return (UserFieldType::$values);
   }
   
   public static function getValue($ordinal)
   {
      $value = new UserFieldType(UserFieldType::UNKNOWN, "");
      
      if (($ordinal >= UserFieldType::FIRST) && ($ordinal <= UserFieldType::LAST))
      {
         $value = UserFieldType::getValues()[$ordinal - UserFieldType::FIRST];
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
$userFieldType = UserFieldType::getValue(UserFieldType::EMPLOYEE_NUMBER);
echo "getValue(EMPLOYEE_NUMBER) = {$userFieldType->label}<br>";
 
foreach (UserFieldType::getValues() as $userFieldType)
{
   echo "[{$userFieldType->ordinal}] : {$userFieldType->label}<br>";
}
*/

?>