<?php

require_once 'measurementType.php';
require_once 'measurementResult.php';

class PartMeasurement
{
   public static function parse($string)
   {
      $partMeasurement = new PartMeasurement();
      
      // Oasis measurement format:
      // DATA|thd lgh|0.6600|0.6620|0.6626|0.6980|0.7000|PASS
      
      $tokens = array();
      $token = strtok($string, "\\|");
      while ($token !== false)
      {
         $tokens[] = $token;
         $token = strtok("\\|");
      }
      
      if ($tokens[PartMeasurement::DATA_INDEX] == "DATA")
      {
         for ($i = PartMeasurement::NAME_INDEX; $i <= PartMeasurement::RESULT_INDEX; $i++)
         {
            if ($i == PartMeasurement::NAME_INDEX)
            {
               $partMeasurement->name = $tokens[$i];
            }
            else if ($i == PartMeasurement::RESULT_INDEX)
            {
               $partMeasurement->result = MeasurementResult::valueOf($tokens[$i]);
            }
            else
            {
               $dataIndex = ($i - PartMeasurement::FIRST_MEASUREMENT_INDEX);
               
               $value = doubleval($tokens[$i]);
               $partMeasurement->data[$dataIndex] = $value;
            }
         }
      }
      else
      {
         $partMeasurement = null;
      }
      
      return ($partMeasurement);
   }
   
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
      // Name
      $html = "<tr><td>{$this->name}</td><td>";
      
      // Measurements
      foreach (MeasurementType::$VALUES as $measurementType)
      {
         $html .= "<td>";
         
         if (isset($this->data[$measurementType]))
         {
            $html .= $this->data[$measurementType];
         }
            
         $html .= "</td>";
      }
      
      // Result
      $html .= "<td>" . MeasurementResult::getLabel($this->result) . "</td></tr>";
         
      return ($html);
   }
   
   const DATA_INDEX = 0;
   
   const NAME_INDEX = 1;
   
   const FIRST_MEASUREMENT_INDEX = 2;
   
   const RESULT_INDEX = 7;
   
   private $name;
   
   private $data = array();
   
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
$token = MeasurementResult::getLabel($partMeasurement->getResult());
echo "Result: {$token}<br>";
echo $partMeasurement->toHtml() . "<br>";

$partMeasurement->parse("DATA|thd lgh|0.6600|0.6620|0.6626|0.6980|0.7000|PASS");
echo $partMeasurement->toHtml();
*/
?>