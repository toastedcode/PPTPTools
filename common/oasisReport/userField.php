<?php

class UserField
{
   public function __construct()
   {
      $this->label = "";
      $this->value = "";
   }
   
   public function parse($line)
   {
      $success = true;
      
      $tokens = explode("|", $line);
      
      $tokenCount = count($tokens);
      
      if ($tokenCount> 0)
      {
         $lineType = ReportLineType::valueOf($tokens[0]);
         
         switch ($lineType)
         {
            case ReportLineType::USER_FIELD_LABEL:
            {
               if ($tokenCount == 1)
               {
                  // Allow for empty values.
                  $this->label = "";
               }
               else
               {
                  $this->label = $tokens[1];
               }
               break;
            }
               
            case ReportLineType::USER_FIELD_VALUE:
            {
               if ($tokenCount == 1)
               {
                  // Allow for empty values.
                  $this->value = "";
               }
               else
               {
                  $this->value = $tokens[1];
               }
               break;
            }
               
            default:
            {
               // Parse error!
               $success = false;
               break;
            }
         }
      }
      
      return ($success);
   }
   
   public function isValid()
   {
      return ($this->label && $this->value);
   }
   
   public function getLabel()
   {
      return ($this->label);
   }
   
   public function setLabel($label)
   {
      $this->label = $label;
   }
   
   public function getValue()
   {
      return ($this->value);
   }
   
   public function setValue($value)
   {
      $this->value = $value;
   }
   
   private $label;
   
   private $value;
}

/*
$userField = new UserField();
$userField->setLabel("efficiency");
$userField->setValue("100%");
echo "Label: {$userField->getLabel()}<br>";
echo "Value: {$userField->getValue()}<br>";
*/

?>