<?php

require_once 'partMeasurement.php';

class PartInspection
{
   public function isValid()
   {
      return ($this->isValid);
   }
   
   public function setValid($isValid)
   {
     $this->isValid = $isValid;
   }
   
   public function getDataFile()
   {
      return ($this->dataFile);
   }
   
   public function setDataFile($dataFile)
   {
      $this->dataFile = $dataFile;
   }
   
   public function getDate()
   {
      return ($this->date);
   }
   
  public function setDate($date)
  {
     $this->date = $date;
  }
  
  public function getMeasurements()
  {
     return ($this->measurements);
  }
  
  public function addMeasurement($measurement)
  {
     $this->measurements[] = $measurement;
  }
  
  public function getFailureCount()
  {
     $failureCount = 0;
     
     foreach ($this->measurements as $measurement)
     {
        if (MeasurementResult::getValue($measurement->getResult())->isFailed())
        {
           $failureCount++;
        }
     }
     
     return ($failureCount);
  }
   
   public function toHtml()
   {
      $html = "<table class=\"part-inspection-table\">";

      // Table heading.
      $html .= "<tr class=\"part-inspection-table-header\">";
      $html .= "<th>Name</th>";
      foreach (MeasurementType::getValues() as $measurmentType)
      {
         $html .= "<th>{$measurmentType->label}</th>";
      }
      $html .= "<th>Result</th>";
      $html .= "</tr>";
      
      foreach ($this->measurements as $measurement)
      {
         $html .= $measurement->toHtml();
      }
      
      $html .= "</table>";
        
      return ($html);
   }
   
   private $isValid = false;
   
   private $dataFile = "";
   
   private $date;
   
   private $measurements = array();
}

/*
$partMeasurement = new PartMeasurement();
$partMeasurement->setName("Measurement 1");
$partMeasurement->addValue(MeasurementType::LOW_WARN, 2.5);
$partMeasurement->addValue(MeasurementType::MEASURED, 7.1);
$partMeasurement->addValue(MeasurementType::HIGH_WARN, 1.0);
$partMeasurement->addValue(MeasurementType::HIGH_LIMIT, 6.9);
$partMeasurement->setResult(MeasurementResult::FAIL_LOW);

$partInspection = new PartInspection();
$partInspection->setValid(true);
$partInspection->setDataFile("report.rpt");
$partInspection->setDate(new DateTime());
$partInspection->addMeasurement($partMeasurement);

$validString = $partInspection->isValid() ? "true" : "false";
echo "isValid: $validString<br>";
echo "dataFile: {$partInspection->getDataFile()}<br>";
echo "date: {$partInspection->getDate()->format("Y-m-d")}<br>";
echo "failureCount: {$partInspection->getFailureCount()}<br>";

echo $partInspection->toHtml();
*/

?>