<?php
require_once 'partInspection.php';
require_once 'userFieldType.php';
require_once 'userField.php';

class OasisReport
{
   public static function parseFile($path)
   {
      $oasisReport = new OasisReport();
      
      $file = file_get_contents($path);
      
      $lines = explode("\n", $file);
      
      foreach ($lines as $line)
      {
         // Clear out carriage returns.
         $line = preg_replace( "/\r/", "", $line);
         
         if (!$oasisReport->parse($line))
         {
            echo "Parse failure";
            $oasisReport = null;
            break;
         }
      }
      
      return ($oasisReport);
   }
   
   public function getUserField($fieldType)
   {
      $userField = null;
      
      if (isset($this->userFields[$fieldType]))
      {
         $userField = $this->userFields[$fieldType];
      }
      
      return ($userField);
   }
   
   public function setUserField($fieldType, $value)
   {
      $this->userFields[$fieldType] = $value;
   }
   
   public function getEmployeeNumber()
   {
      $employeeNumber = UserInfo::UNKNOWN_EMPLOYEE_NUMBER;
      
      if (isset($this->userFields[UserFieldType::EMPLOYEE_NUMBER]))
      {
         $str = $this->userFields[UserFieldType::EMPLOYEE_NUMBER]->getValue();

         if (is_numeric($str))
         {
            $employeeNumber = intval($str);
         }
      }
      
      return ($employeeNumber);
   }
   
   public function getPartCount()
   {
      $partCount = 0;
      
      if (isset($this->userFields[UserFieldType::PART_COUNT]))
      {
         $partCount = intval($this->userFields[UserFieldType::PART_COUNT]->getValue());
      }
      
      return ($partCount);
   }
   
   public function getSampleSize()
   {
      $sampleSize = 0;
      
      if (isset($this->userFields[UserFieldType::SAMPLE_SIZE]))
      {
         $sampleSize = intval($this->userFields[UserFieldType::SAMPLE_SIZE]->getValue());
      }
      
      return ($sampleSize);
   }
   
   public function getMachineNumber()
   {
      $wcNumber = JobInfo::UNKNOWN_WC_NUMBER;
      
      if (isset($this->userFields[UserFieldType::MACHINE_NUMBER]))
      {
         $str = $this->userFields[UserFieldType::MACHINE_NUMBER]->getValue();
         
         if (is_numeric($str))
         {
            $wcNumber = inval($str);
         }
      }
      
      return ($wcNumber);
   }
   
   public function getDate()
   {
      $date = null;
      
      $inspection = $this->inspections[0];
     
      // TODO
      
      return ($date);
   }
   
   public function getPartNumber()
   {
      $partNumber = "";
      
      if (isset($this->userFields[UserFieldType::PART_NUMBER]))
      {
         $str = $this->userFields[UserFieldType::PART_NUMBER]->getValue();
         
         // String generally looks like this:
         // M8206 Rev 10
         $partNumber = substr($str, 0, strpos($str, " "));
      }
      
      return ($partNumber);
   }
   
   public function getEfficiency()
   {
      $efficiency = 0.0;
      
      if (isset($this->userFields[UserFieldType::EFFICIENCY]))
      {
         $str = $this->userFields[UserFieldType::EFFICIENCY]->getValue();
         
         // String generally looks like this:
         // 77%
         $str = preg_replace("[^\\d.]", "", $str);
         
         $efficiency = doubleval($str);
      }
      
      return ($efficiency);
   }
   
   public function getFailureCount()
   {
      $failureCount = 0;
      
      foreach ($this->inspections as $inspection)
      {
         $failureCount += $inspection->getFailureCount();
      }
      
      return ($failureCount);
   }
   
   public function getComments()
   {
      $value = "";
      
      if (isset($this->userFields[UserFieldType::COMMENTS]))
      {
         $value = $this->userFields[UserFieldType::COMMENTS]->getValue();
      }
      
      return ($value);
   }
   
   public function getPartInspectionCount()
   {
      return (count($this->inspections));
   }
      
   public function getPartInspection($index)
   {
      $partInspection = null;
      
      if (isset($partInspection[$index]))
      {
         $partInspection = $this->inspections[$index];
      }
      
      return ($partInspection);
   }
   
   public function addPartInspection($partInspection)
   {
      $this->inspections[] = $partInspection;
   }
   
   public function toHtml()
   {
      $html = "";
      
      foreach (UserFieldType::$VALUES as $fieldType)
      {
         $value = "";
         $userField = $this->getUserField($fieldType);
         if ($userField)
         {
            $value = $userField->getValue();   
         }
         
         $html .= "<div>" . UserFieldType::getLabel($fieldType) . ": " . $value . "</div>";
      }
      
      for ($i = 0; $i < count($this->inspections); $i++)
      {
         $html .= "<h3>Sample " . $i . "</h3><br>";
         $html .= $this->inspections[$i]->toHtml();
      }
         
      return ($html);
   }
   
   private function parse($line)
   {
      $success = true;
      
      $tokens = explode("|", $line);
      
      if (count($tokens) == 0)
      {
         // Ignore empty lines.
      }
      else if (($tokens[0] == "") || ($tokens[0] == "\n"))
      {
         // Ignore blank lines.
      }
      else
      {
         $lineType = ReportLineType::valueOf($tokens[0]);      

         switch ($lineType)
         {
            case ReportLineType::PART_INSPECTION_START:
            {
               if ($this->partInspection != null)
               {
                  // Parse error!
                  $success = false;
               }
               else
               {
                  $this->partInspection = new PartInspection();
                  $success = $this->partInspection->parse($line);
               }
               break;
            }
               
            case ReportLineType::PART_INSPECTION_DATA:
            {
               if ($this->partInspection == null)
               {
                  // Parse error!
                  $success = false;
               }
               else
               {
                  $success = $this->partInspection->parse($line);
               }
               break;
            }
               
            case ReportLineType::PART_INSPECTION_END:
            {
               if ($this->partInspection == null)
               {
                  // Parse error!
                  $success = false;
               }
               else if (!$this->partInspection->parse($line))
               {
                  // Parse error!
                  $success = false;
               }
               else
               {
                  $this->inspections[] = $this->partInspection;
                  $this->partInspection = null;
               }
               break;
            }
               
            case ReportLineType::USER_FIELD_LABEL:
            case ReportLineType::USER_FIELD_VALUE:
            {
               $userFieldType = UserFieldType::valueOf($tokens[0]);
               
               if ($userFieldType == UserFieldType::UNKNOWN)
               {
                  // Parse error!
                  $success = false;
               }
               else
               {
                  if (!isset($this->userFields[$userFieldType]))
                  {
                     $this->userFields[$userFieldType] = new UserField();
                  }
                  
                  $success = $this->userFields[$userFieldType]->parse($line);
               }
               break;
            }
               
            default:
            {
               // Parse error.
               $success = false;
            }
         }
      }
      
      return ($success);
   }
   
   private $userFields = array();
   
   private $inspections = array();
   
   // A temporary object used for building part inspections.
   private $partInspection = null;
}

/*
$partInspection = new PartInspection();
$partInspection->setDataFile("report.rpt");
$partInspection->setDate(new DateTime());

$partInspection->addMeasurement(PartMeasurement::parse("DATA|OAL|0.8070|0.8075|0.8139|0.8165|0.8170|PASS"));
$partInspection->addMeasurement(PartMeasurement::parse("DATA|hex lgh|0.4900|0.4910|0.5065|0.5090|0.5100|PASS"));
$partInspection->addMeasurement(PartMeasurement::parse("DATA|chm lgh|0.0200|0.0210|0.0272|0.0390|0.0400|PASS"));
$partInspection->addMeasurement(PartMeasurement::parse("DATA|front|1.0300|1.0308|1.0390|1.0442|1.0450|PASS"));
$partInspection->addMeasurement(PartMeasurement::parse("DATA|back|1.0300|1.0308|1.0384|1.0442|1.0450|PASS"));

$oasisReport = new OasisReport();
$oasisReport->setUserField(UserFieldType::EMPLOYEE_NUMBER, 1975);
$oasisReport->setUserField(UserFieldType::PART_COUNT, 5000);
$oasisReport->setUserField(UserFieldType::SAMPLE_SIZE, 12);
$oasisReport->setUserField(UserFieldType::MACHINE_NUMBER, 101);
$oasisReport->setUserField(UserFieldType::PART_NUMBER, "M8206 Rev 10");
$oasisReport->setUserField(UserFieldType::EFFICIENCY, "77%");
$oasisReport->setUserField(UserFieldType::COMMENTS, "I messed up big time.");
$oasisReport->addPartInspection($partInspection);

echo "Employee number: " . $oasisReport->getEmployeeNumber() . "<br>";
echo "Part count: "      . $oasisReport->getPartCount() . "<br>";
echo "Sample size: "     . $oasisReport->getSampleSize() . "<br>";
echo "Machine number: "  . $oasisReport->getMachineNumber() . "<br>";
echo "Part number: "     . $oasisReport->getPartNumber() . "<br>";
echo "Efficiency: "      . $oasisReport->getEfficiency() . "<br>";
echo "Comments: "        . $oasisReport->getComments() . "<br>";

echo $oasisReport->toHtml();

$oasisReport = OasisReport::parseFile("report.rpt");

if ($oasisReport)
{
   echo $oasisReport->toHtml(); 
}
*/
?>