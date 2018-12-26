<?php

class UserField
{
   public function __construct($label, $value)
   {
      $this->label = $label;
      $this->value = $value;
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
$userField = new UserField("efficiency", "100%");
echo "Label: {$userField->getLabel()}<br>";
echo "Value: {$userField->getValue()}<br>";
*/

?>