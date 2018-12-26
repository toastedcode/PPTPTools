<?php

require_once 'measurementType.php';
require_once 'measurementResult.php';

class PartMeasurement
{
   public function getName()
   {
      return ($this->name);
   }
   
   public function setName($name)
   {
      $this->name = $name;
   }
   
   public function addValue($measurementType, $value)
   {
      $this->data[$measurementType] = $value;
   }
   
   public function getValue($measurementType)
   {
      $value = 0.0;
      
      if (isset($this->data[$measurementType]))
      {
         $value = $this->data[$measurementType];
      }
      
      return ($value);
   }
   
   public function getResult()
   {
      return ($this->result);
   }
   
   public function setResult($measurementResult)
   {
      $this->result = $measurementResult;
   }
   
   public function toHtml()
   {
      $html = "<tr><td>{$this->name}</td><td>";
      
      foreach (MeasurementType::getValues() as $measurementType)
      {
         $html .= "<td>";
         
         if (isset($this->data[$measurementType->ordinal]))
         {
            $html .= $this->data[$measurementType->ordinal];
         }
            
         $html .= "</td>";
      }
      
      $html .= "</tr>";
         
      return ($html);
   }
   
   private $name;
   
   private $data;
   
   private $result;
}

/*
$partMeasurement = new PartMeasurement();
$partMeasurement->setName("Measurement 1");
$partMeasurement->addValue(MeasurementType::LOW_WARN, 2.5);
$partMeasurement->addValue(MeasurementType::MEASURED, 7.1);
$partMeasurement->addValue(MeasurementType::HIGH_WARN, 1.0);
$partMeasurement->addValue(MeasurementType::HIGH_LIMIT, 6.9);
$partMeasurement->setResult(MeasurementResult::FAIL_LOW);

echo "Name: {$partMeasurement->getName()}<br>";
echo "Value[HIGH_WARN]: {$partMeasurement->getValue(MeasurementType::HIGH_WARN)}<br>";
$token = MeasurementResult::getValue($partMeasurement->getResult())->token;
echo "Result: {$token}<br>";
echo $partMeasurement->toHtml();
*/
?>