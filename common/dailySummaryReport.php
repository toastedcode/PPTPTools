<?php

require_once 'jobInfo.php';
require_once 'partWasherEntry.php';
require_once 'partWeightEntry.php';
require_once 'userInfo.php';
require_once 'timeCardInfo.php';

abstract class DailySummaryReportTable
{
   const FIRST = 0;
   const DAILY_SUMMARY = DailySummaryReportTable::FIRST;
   const OPERATOR_SUMMARY = 1;
   const SHOP_SUMMARY = 2;
   const LAST = 3;
   const COUNT = DailySummaryReportTable::LAST - DailySummaryReportTable::FIRST;
}

abstract class ReportEntryStatus
{
   const FIRST = 0;
   const UNKNOWN = ReportEntryStatus::FIRST;
   const INCOMPLETE_TIME_CARD = 1;
   const UNAPPROVED_TIME_CARD = 2;
   const NO_WEIGHT_LOGS = 3;
   const NO_WASH_LOGS = 4;
   const INCONSISTENT_PAN_COUNTS = 5;
   const INCONSISTENT_PART_COUNTS = 6;
   const UNREASONABLE_EFFICIENCY = 7;
   const COMPLETE = 8;
   const LAST = 9;
   const COUNT = ReportEntryStatus::LAST - ReportEntryStatus::FIRST;
   
   public static function getLabel($reportStatus)
   {
      $labels = array("---", 
                      "Incomplete Time Card", 
                      "Unapproved Time Card",
                      "No Weight Logs",
                      "No Wash Logs",
                      "Inconsistent Pan Counts",
                      "Inconsistent Part Counts",
                      "Unreasonable Efficiency",
                      "Complete");
      
      return ($labels[$reportStatus]);
   }
   
   public static function getClass($inspectionStatus)
   {
      $class = "";
      
      switch ($inspectionStatus)
      {
         case ReportEntryStatus::COMPLETE:
         {
            $class = "report-entry-valid";
            break;
         }
         
         case ReportEntryStatus::NO_WASH_LOGS:
         case ReportEntryStatus::INCONSISTENT_PAN_COUNTS:
         case ReportEntryStatus::INCONSISTENT_PART_COUNTS:
         {
            $class = "report-entry-warning";
            break;
         }
         
         case ReportEntryStatus::INCOMPLETE_TIME_CARD:
         case ReportEntryStatus::UNAPPROVED_TIME_CARD:
         case ReportEntryStatus::NO_WEIGHT_LOGS:
         case ReportEntryStatus::UNREASONABLE_EFFICIENCY:
         {
            $class = "report-entry-error";
            break;
         }
         
         default:
         {
            break;
         }
      }
      
      return ($class);
   }
}

class ReportEntry
{
   // Any efficiency >= 90 will be flagged as unreasonable.
   const UNREASONABLE_EFFICIENCY = 90;
   
   public $timeCardInfo;
   public $userInfo;
   public $jobInfo;
   public $partWeightEntries;   
   public $partWasherEntries;
   //public $inspections;  TODO
   
   // Calculated values
   public $totalPanCount;
   public $totalPartWeight;
   public $averagePanWeight;
   public $partCountByWeightLog;
   public $partCountByWasherLog;
   public $partCountEstimate;
   public $grossPartsPerShift;
   public $efficiency;
   public $machineHoursMade;
   public $dataStatus;
   
   public function __construct()
   {
      $this->timeCardInfo = null;
      $this->userInfo = null;
      $this->jobInfo = null;      
      $this->partWasherEntries = array();
      $this->partWeightEntries = array();
      //$this->inspections = array();  TODO
      
      // Calculated values
      $this->totalPanCount = 0;
      $this->totalPartWeight = 0;
      $this->averagePanWeight = 0;
      $this->partCountByWeightLog = 0;
      $this->partCountByWasherLog = 0;
      $this->partCountEstimate = 0;
      $this->grossPartsPerShift = 0;
      $this->efficiency;
      $this->machineHoursMade = 0;
      $this->dataStatus = ReportEntryStatus::UNKNOWN;
   }
   
   public static function load($timeCardId)
   {
      $entry = new ReportEntry();
      
      $entry->timeCardInfo = TimeCardInfo::load($timeCardId);
      
      $entry->userInfo = UserInfo::load($entry->timeCardInfo->employeeNumber);
      
      if ($entry->timeCardInfo)
      {
         $entry->jobInfo = JobInfo::load($entry->timeCardInfo->jobId);
      }
      
      $database = PPTPDatabase::getInstance();
      
      if ($database && $database->isConnected())
      {
         $result = $database->getPartWasherEntriesByTimeCard($timeCardId);
         
         while ($result && $row = $result->fetch_assoc())
         {
            $entry->partWasherEntries[] = PartWasherEntry::load(intval($row["partWasherEntryId"]));
         }
         
         $result = $database->getPartWeightEntriesByTimeCard($timeCardId);
         
         while ($result && $row = $result->fetch_assoc())
         {
            $entry->partWeightEntries[] = PartWeightEntry::load(intval($row["partWeightEntryId"]));
         }
      }
      
      // Calculated values.
      $entry->recalculate();
      
      return ($entry);
   }
   
   function recalculate()
   {
      $this->totalPanCount = $this->getPanCountByWeightLog();
      
      $this->totalPartWeight = $this->getTotalPartWeight();
      
      $this->averagePanWeight = $this->getAveragePanWeight();
      
      $this->partCountByWeightLog = $this->getPartCountByWeightLog();
      
      $this->partCountByWasherLog = $this->getPartCountByWasherLog();
      
      $this->partCountEstimate =
         ReportEntry::getPartCountEstimate(
            $this->timeCardInfo->partCount,
            $this->partCountByWeightLog,
            $this->partCountByWasherLog);
      
      $this->grossPartsPerShift = $this->getGrossPartsPerShift();
      
      $this->efficiency = TimeCardInfo::calculateEfficiency(
         $this->timeCardInfo->runTime,
         $this->jobInfo->grossPartsPerHour,
         $this->partCountEstimate);
      
      $this->machineHoursMade = $this->getMachineHoursMade();
      
      $this->dataStatus = $this->getReportEntryStatus();  // Note: Must be called after other calculations.
   }
   
   private function getPanCountByWeightLog()
   {
      $panCount = 0;
      
      foreach ($this->partWeightEntries as $partWeightEntry)
      {
         $panCount += $partWeightEntry->panCount;
      }
      
      return ($panCount);
   }
   
   private function getTotalPartWeight()
   {
      $partWeight = 0.0;
      
      foreach ($this->partWeightEntries as $partWeightEntry)
      {
         $partWeight += $partWeightEntry->weight;
      }
      
      return ($partWeight);
   }
   
   private function getAveragePanWeight()
   {
      $averageWeight = 0;
      
      $totalWeight = $this->getTotalPartWeight();      
      
      $panCount = $this->getPanCountByWeightLog();
            
      if ($panCount > 0)
      {
         $averageWeight = round((($totalWeight - PartWeightEntry::STANDARD_PALLET_WEIGHT) / $panCount), 2);
      }
      
      return ($averageWeight);
   }

   private function getPartCountByWeightLog()
   {
      $partCount = 0;
      
      foreach ($this->partWeightEntries as $partWeightEntry)
      {
         $partCount += $partWeightEntry->calculatePartCount();
      }
      
      return ($partCount);
   }
   
   private function getPanCountByWasherLog()
   {
      $panCount = 0;
      
      foreach ($this->partWasherEntries as $partWasherEntry)
      {
         $panCount += $partWasherEntry->panCount;
      }
      
      return ($panCount);
   }
   
   private function getPartCountByWasherLog()
   {
      $partCount = 0;
      
      foreach ($this->partWasherEntries as $partWasherEntry)
      {
         $partCount += $partWasherEntry->partCount;
      }
      
      return ($partCount);
   }   
   
   private static function getPartCountEstimate($timeCardCount, $partWeightCount, $partWasherCount)
   {
      $partCount = 0;
      
      if ($partWeightCount > 0)
      {
         $partCount = $partWeightCount;
      }
      else if ($partWasherCount > 0)
      {
         $partCount = $partWasherCount;
      }
      else
      {
         $partCount = $timeCardCount;
      }
      
      return ($partCount);
   }
   
   private function getGrossPartsPerShift()
   {
      $grossPartsPerHour = $this->jobInfo->grossPartsPerHour;
      
      $grossParts = round(($grossPartsPerHour * ($this->timeCardInfo->getApprovedRunTime() / TimeCardInfo::MINUTES_PER_HOUR)), 2);
      
      return ($grossParts);
   }
   
   private function getMachineHoursMade()
   {      
      $machineHours = 0;
      
      $netPartsPerHour = $this->jobInfo->netPartsPerHour;
      
      if ($netPartsPerHour != 0)
      {
         $machineHours = round(($this->partCountEstimate / $netPartsPerHour), 2);
      }
      
      return ($machineHours);
   }
   
   private function getReportEntryStatus()
   {
      $status = ReportEntryStatus::COMPLETE;
      
      if (!$this->timeCardInfo->isPlaceHolder())
      {
         if (!$this->timeCardInfo->isComplete())
         {
            $status = ReportEntryStatus::INCOMPLETE_TIME_CARD;
         }
         else if (!$this->timeCardInfo->isApproved())
         {
            $status = ReportEntryStatus::UNAPPROVED_TIME_CARD;
         }
         else if ($this->getPartCountByWeightLog() == 0)
         {
            $status = ReportEntryStatus::NO_WEIGHT_LOGS;         
         }
         else if ($this->getPartCountByWasherLog() == 0)
         {
            $status = ReportEntryStatus::NO_WASH_LOGS;
         }
         else if (($this->timeCardInfo->panCount != $this->getPanCountByWeightLog()) ||
                  ($this->getPanCountByWeightLog() != $this->getPanCountByWasherLog()))
         {
            $status = ReportEntryStatus::INCONSISTENT_PAN_COUNTS;
         }
         else if (false)  // TODO
         {
            $status = ReportEntryStatus::INCONSISTENT_PART_COUNTS;
         }
         else if ($this->efficiency >= ReportEntry::UNREASONABLE_EFFICIENCY)
         {
            $status = ReportEntryStatus::UNREASONABLE_EFFICIENCY;
         }
         else
         {
            $status = ReportEntryStatus::COMPLETE;
         }
      }
      
      return ($status);
   }
}

class DailySummaryReport
{
   public $dateTime;
   public $reportEntries;
      
   public function __construct()
   {
      $this->dateTime = null;
      $this->reportEntries = array();
   }
   
   public static function load($employeeNumber, $dateTime)
   {
      $report = new DailySummaryReport();
      
      $report->dateTime = $dateTime;
      
      $database = PPTPDatabase::getInstance();
      
      if ($database && $database->isConnected())
      {
         $result = $database->getTimeCards($employeeNumber, Time::startOfDay($dateTime), Time::endOfDay($dateTime), true);  // Use mfg date.
         
         while ($result && $row = $result->fetch_assoc())
         {
            $timeCardId = intval($row["timeCardId"]);
            
            $report->reportEntries[] = ReportEntry::load($timeCardId);
         }
      }
      
      return ($report);
   }
   
   public function getReportData($table)
   {
      $reportData = array();
      
      switch ($table)
      {
         case DailySummaryReportTable::DAILY_SUMMARY:
         {
            $reportData = $this->getDailySummaryData();
            break;
         }
         
         case DailySummaryReportTable::OPERATOR_SUMMARY:
         {
            $reportData = $this->getOperatorSummaryData();
            break;
         }
         
         case DailySummaryReportTable::SHOP_SUMMARY:
         {
            $reportData = $this->getShopSummaryData();
            break;
         }
         
         default:
         {
            break;
         }
      }
      
      return ($reportData);
   }
   
   public function getDailySummaryData()
   {
      // Report columns
      /*
       * Ticket
       * Mfg. Date
       * Operator
       * Employee #
       * Job #
       * WC #
       * Heat #
       * Run time
       * Pan count
       * Sample weight
       * Total weight
       * Average pan weight
       * Part count (by time card)
       * Part count (by weight)
       * Part count (by wash log)
       * Part count (best estimate)
       */
      
      $reportData = array();
      
      foreach ($this->reportEntries as $entry)
      {     
         $row = new stdClass();
         
         $row->timeCardId = $entry->timeCardInfo->timeCardId;
         $row->panTicketCode = PanTicket::getPanTicketCode($entry->timeCardInfo->timeCardId);
         $row->dataStatus = $entry->dataStatus;
         $row->dataStatusLabel = ReportEntryStatus::getLabel($entry->dataStatus);
         $row->dataStatusClass = ReportEntryStatus::getClass($entry->dataStatus);
         $row->manufactureDate = $entry->timeCardInfo->dateTime;
         $row->operator = $entry->userInfo->getFullName();
         $row->employeeNumber = $entry->userInfo->employeeNumber;
         $row->jobNumber = $entry->jobInfo->jobNumber;
         $row->wcNumber = $entry->jobInfo->wcNumber;
         $row->materialNumber = $entry->timeCardInfo->materialNumber;
         $row->shiftTime = $entry->timeCardInfo->shiftTime;
         $row->incompleteShiftTime = $entry->timeCardInfo->incompleteShiftTime();
         $row->runTime = $entry->timeCardInfo->runTime;
         $row->unapprovedRunTime = !$entry->timeCardInfo->isRunTimeApproved();
         $row->setupTime = $entry->timeCardInfo->setupTime;
         $row->unapprovedSetupTime = !$entry->timeCardInfo->isSetupTimeApproved();         
         $row->panCount = $entry->timeCardInfo->panCount;
         $row->incompletePanCount = $entry->timeCardInfo->incompletePanCount();
         $row->sampleWeight = $entry->jobInfo->sampleWeight;
         $row->partWeight = $entry->totalPartWeight;
         $row->averagePanWeight = $entry->averagePanWeight;         
         $row->partCountByTimeCard = $entry->timeCardInfo->partCount;
         $row->incompletePartCount = $entry->timeCardInfo->incompletePartCount();
         $row->partCountByWeightLog = $entry->partCountByWeightLog;
         $row->partCountByWasherLog = $entry->partCountByWasherLog;
         $row->partCountEstimate = $entry->partCountEstimate;
         $row->grossPartsPerHour = $entry->jobInfo->grossPartsPerHour;
         $row->grossPartsPerShift = $entry->grossPartsPerShift;
         $row->efficiency = $entry->efficiency;
         $row->unreasonableEfficiency = ($entry->efficiency >= ReportEntry::UNREASONABLE_EFFICIENCY);
         $row->scrapCount = $entry->timeCardInfo->scrapCount;
         $row->netPartsPerHour = $entry->jobInfo->netPartsPerHour;
         $row->machineHoursMade = $entry->machineHoursMade;
         
         $reportData[] = $row;
      }
      
      return ($reportData);
   }
   
   public function getOperatorSummaryData()
   {
      // Report columns
      /*
       * Operator
       * Employee #
       * Total Run Time
       * Average Efficiency
       * Total Machine Hours Made
       * Ratio
       */
      
      $reportData = array();
      
      foreach ($this->getEmployeeNumbers() as $employeeNumber)
      {
         $row = new stdClass();
         
         $userInfo = UserInfo::load($employeeNumber);
         
         $row->operator = $userInfo->getFullName();
         $row->employeeNumber = $userInfo->employeeNumber;
         $row->runTime = ($this->getTotalRunTime($employeeNumber, true) / TimeCardInfo::MINUTES_PER_HOUR);  // use approved run time
         $row->efficiency = $this->getAverageEfficiency($employeeNumber);
         $row->shiftHours = $this->getTotalShiftHours($employeeNumber);
         $row->machineHoursMade = $this->getTotalMachineHoursMade($employeeNumber);
         $row->ratio = $this->getRatio($employeeNumber);
         
         $reportData[] = $row;
      }
      
      return ($reportData);
   }
   
   public function getShopSummaryData()
   {
      // Report columns
      /*
       * Hours
       * Efficiency
       * Machine Hours Made
       * Ratio
       */
      
      $reportData = array();

      $row = new stdClass();
      
      $row->hours = ($this->getTotalRunTime(UserInfo::UNKNOWN_EMPLOYEE_NUMBER, true) / TimeCardInfo::MINUTES_PER_HOUR);  // Use approved run time
      $row->efficiency = $this->getAverageEfficiency(UserInfo::UNKNOWN_EMPLOYEE_NUMBER);
      $row->shiftHours = $this->getTotalShiftHours(UserInfo::UNKNOWN_EMPLOYEE_NUMBER);
      $row->machineHoursMade = $this->getTotalMachineHoursMade(UserInfo::UNKNOWN_EMPLOYEE_NUMBER);
      $row->ratio = $this->getRatio(UserInfo::UNKNOWN_EMPLOYEE_NUMBER);
      
      $reportData[] = $row;
      
      return ($reportData);
   }
   
   public function getEmployeeNumbers()
   {
      $employeeNumbers = array();
      
      foreach ($this->reportEntries as $entry)
      {
         if (!in_array($entry->timeCardInfo->employeeNumber, $employeeNumbers))
         {
            $employeeNumbers[] = $entry->timeCardInfo->employeeNumber;
         }
      }
      
      return ($employeeNumbers);
   }
   
   public function getTotalShiftHours($employeeNumber)
   {
      $totalShiftHours = 0;
      
      // Build an array of shift hours, indexed by employee number.
      $shiftHoursByEmployee = array();
      
      foreach ($this->reportEntries as $entry)
      {
         if (($employeeNumber == UserInfo::UNKNOWN_EMPLOYEE_NUMBER) ||
             ($entry->timeCardInfo->employeeNumber == $employeeNumber))
         {
            $tempEmployeeNumber = $entry->timeCardInfo->employeeNumber;
            
            if (!isset($shiftHoursByEmployee[$tempEmployeeNumber]))
            {
               $shiftHoursByEmployee[$tempEmployeeNumber] = $entry->timeCardInfo->getShiftTimeInHours();
            }
            // Shift hours should be the same across all time cards.
            // If they're not, go with the greatest value.
            else if ($entry->timeCardInfo->getShiftTimeInHours() > $shiftHoursByEmployee[$tempEmployeeNumber])
            {
               $shiftHoursByEmployee[$tempEmployeeNumber] = $entry->timeCardInfo->getShiftTimeInHours();
            }
         }
      }
      
      foreach ($shiftHoursByEmployee as $shiftHours)
      {
         $totalShiftHours += $shiftHours;
      }
      
      return ($totalShiftHours);
   }
   
   public function getTotalRunTime($employeeNumber, $useApprovedRunTime = false)
   {
      $totalRunTime = 0;
      
      foreach ($this->reportEntries as $entry)
      {
         if (($employeeNumber == UserInfo::UNKNOWN_EMPLOYEE_NUMBER) ||
             ($entry->timeCardInfo->employeeNumber == $employeeNumber))
         {
            if ($useApprovedRunTime)
            {
               $totalRunTime += $entry->timeCardInfo->getApprovedRunTime();
            }
            else
            {
               $totalRunTime += $entry->timeCardInfo->runTime;
            }
         }
      }
      
      return ($totalRunTime);
   }
   
   public function getAverageEfficiency($employeeNumber)
   {
      $averageEfficiency = 0;
      
      $totalRunTime = $this->getTotalRunTime($employeeNumber, true);  // use approved run time
      
      $totalEfficiency = 0;
      
      foreach ($this->reportEntries as $entry)
      {
         if (($employeeNumber == UserInfo::UNKNOWN_EMPLOYEE_NUMBER) ||
             ($entry->timeCardInfo->employeeNumber == $employeeNumber))
         {
            $totalEfficiency += ($entry->efficiency * ($entry->timeCardInfo->getApprovedRunTime() / 60));
         }
      }
      
      if ($totalRunTime > 0)
      {
         $averageEfficiency = round(($totalEfficiency / ($totalRunTime / 60)), 2);
      }
      
      return ($averageEfficiency);
   }
   
   public function getTotalMachineHoursMade($employeeNumber)
   {
      $machineHours = 0;
      
      foreach ($this->reportEntries as $entry)
      {
         if (($employeeNumber == UserInfo::UNKNOWN_EMPLOYEE_NUMBER) ||
             ($entry->timeCardInfo->employeeNumber == $employeeNumber))
         {
            $machineHours += $entry->machineHoursMade;
         }
      }
      
      return ($machineHours);
   }
   
   public function getRatio($employeeNumber)
   {
      $ratio = 0;
      
      foreach ($this->reportEntries as $entry)
      {
         if (($employeeNumber == UserInfo::UNKNOWN_EMPLOYEE_NUMBER) ||
             ($entry->timeCardInfo->employeeNumber == $employeeNumber))
         {
            $totalMachineHoursMade = $this->getTotalMachineHoursMade($employeeNumber);
            
            $totalShiftHours = $this->getTotalShiftHours($employeeNumber);
            
            if ($totalShiftHours > 0)
            {
               $ratio = round(($totalMachineHoursMade / $totalShiftHours), 2);
            }
         }
      }
      
      return ($ratio);
   }
      
   private function getEntriesForOperator($employeeNumber)
   {
      $entries = array();
      
      foreach ($this->reportEntries as $entry)
      {
         if ($entry->timeCardInfo->employeeNumber == $employeeNumber)
         {
            $entries[] = $entry;
         }
      }
      
      return ($entries);
   }
}

/*
if (isset($_GET["employeeNumber"]) && isset($_GET["mfgDate"]))
{
   $employeeNumber = intval($_GET["employeeNumber"]);
   $mfgDate = $_GET["mfgDate"];
   
   $dailySummaryReport = DailySummaryReport::load($employeeNumber, $mfgDate);
   
   if ($dailySummaryReport)
   {
      $reportData = $dailySummaryReport->getReportData(DailySummaryReportTable::DAILY_SUMMARY);
      
      echo (json_encode($reportData));
   }
}
*/

?>