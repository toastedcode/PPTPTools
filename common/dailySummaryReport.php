<?php

require_once 'calculations.php';
require_once 'jobInfo.php';
require_once 'partWasherEntry.php';
require_once 'panTicket.php';
require_once 'partWeightEntry.php';
require_once 'userInfo.php';
require_once 'timeCardInfo.php';

// TODO: Find a better place for this?
function array_copy($arr)
{
   $newArray = array();
   
   foreach($arr as $key => $value)
   {
      if (is_array($value))
      {
         $newArray[$key] = array_copy($value);
      }
      else if (is_object($value))
      {
         $newArray[$key] = clone $value;
      }
      else
      {
         $newArray[$key] = $value;
      }
   }
   
   return $newArray;
}

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
   // Error conditions
   const INCOMPLETE_TIME_CARD = 1;
   const UNAPPROVED_TIME_CARD = 2;
   const NO_WEIGHT_LOGS = 3;
   const UNREASONABLE_PART_COUNT_BY_WEIGHT_LOG = 4;
   const UNREASONABLE_EFFICIENCY = 5;
   // Warning conditions
   const NO_WASH_LOGS = 6;
   const UNREASONABLE_PART_COUNT_BY_TIME_CARD = 7;
   const UNREASONABLE_PART_COUNT_BY_WASHER_LOG = 8;
   const INCONSISTENT_PAN_COUNTS = 9;
   const INCONSISTENT_PART_COUNTS = 10;
   // No errors or warnings
   const COMPLETE = 11;
   const LAST = 12;
   const COUNT = ReportEntryStatus::LAST - ReportEntryStatus::FIRST;
   
   public static function getLabel($reportStatus)
   {
      $labels = array("---", 
                      "Incomplete Time Card", 
                      "Unapproved Time Card",
                      "No Weight Logs",
                      "Unreasonable Part Count",
                      "Unreasonable Efficiency",
                      "No Wash Logs",
                      "Unreasonable Part Count",
                      "Unreasonable Part Count",
                      "Inconsistent Pan Counts",
                      "Inconsistent Part Counts",
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
         case ReportEntryStatus::UNREASONABLE_PART_COUNT_BY_TIME_CARD:
         case ReportEntryStatus::UNREASONABLE_PART_COUNT_BY_WASHER_LOG:
            
         {
            $class = "report-entry-warning";
            break;
         }
         
         case ReportEntryStatus::INCOMPLETE_TIME_CARD:
         case ReportEntryStatus::UNAPPROVED_TIME_CARD:
         case ReportEntryStatus::NO_WEIGHT_LOGS:
         case ReportEntryStatus::UNREASONABLE_EFFICIENCY:
         case ReportEntryStatus::UNREASONABLE_PART_COUNT_BY_WEIGHT_LOG:
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
   
   public static function getOverallStatus($reportStatuses)
   {
      $reportStatus = ReportEntryStatus::COMPLETE;
      
      if (count($reportStatuses) > 0)
      {
         sort($reportStatuses);
         
         $reportStatus = $reportStatuses[0];
      }
      
      return ($reportStatus);
   }
}

class ReportEntry
{
   // Any efficiency >= 100% will be flagged as unreasonable.
   const UNREASONABLE_EFFICIENCY = 1.00;

   // Source data.
   public $timeCardInfo;
   public $userInfo;
   public $jobInfo;
   public $partWeightEntries;   
   public $partWasherEntries;
   
   // Derrived values.
   public $panCount;    // from all weight logs
   public $partWeight;  // from all weight logs
   public $runTime;     // approved hours
   public $partCountByWeightLog;  // compiled
   public $partCountByWasherLog;  // compiled
   public $partCount;   // estimated
   public $grossParts;  // for run time
   public $statusFlags;
      
   // Calculated values.
   public $averagePanWeight;  // using part weight logs
   public $efficiency;        // part count / gross parts
   public $machineHoursMade;  // part count / net parts-per-hour
   public $reportStatus;      // "worst" of the status flags
   
   public function __construct()
   {
      // Source data.
      $this->timeCardInfo = null;
      $this->userInfo = null;
      $this->jobInfo = null;      
      $this->partWasherEntries = array();
      $this->partWeightEntries = array();
      
      // Derrived values.
      $this->panCount = 0;
      $this->partWeight = 0;
      $this->runTime = 0;  // hours
      $this->partCount;
      $this->partCountByWeightLog = 0;
      $this->partCountByWasherLog = 0;
      
      $this->grossParts;
      $this->statusFlags = array();
      
      // Calculated values.
      $this->averagePanWeight = 0;
      $this->efficiency;
      $this->machineHoursMade = 0;
      $this->pcOverG = 0;
      $this->reportStatus = ReportEntryStatus::UNKNOWN;
   }
   
   public static function load($timeCardId)
   {
      $entry = new ReportEntry();
      
      //
      // Load source data.
      //
      
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
      
      // 
      // Copy derrived values.
      //
      
      $entry->panCount = $entry->getPanCountByWeightLog();
      
      $entry->partWeight = $entry->getTotalPartWeight();
      
      $entry->runTime = $entry->timeCardInfo->getApprovedRunTime();  // hours
      
      $entry->grossParts = Calculations::calculateGrossParts($entry->runTime, $entry->jobInfo->grossPartsPerHour);
      
      $entry->partCountByWeightLog = $entry->getPartCountByWeightLog();
      
      $entry->partCountByWasherLog = $entry->getPartCountByWasherLog();
      
      $entry->partCount = Calculations::estimatePartCount($entry->getPartCountByTimeCard(), 
                                                          $entry->getPartCountByWeightLog(), 
                                                          $entry->getPartCountByWasherLog(),
                                                          $entry->grossParts);
      
      //
      // Validate data
      //
      
      $entry->validate();
      
      //
      // Compute calculated values.
      //

      $entry->recalculate();
      
      return ($entry);
   }
   
   public function validate()
   {
      if (!$this->timeCardInfo->isPlaceHolder())
      {
         if (!$this->timeCardInfo->isComplete())
         {
            $this->statusFlags[] = ReportEntryStatus::INCOMPLETE_TIME_CARD;
         }
         
         if (!$this->timeCardInfo->isApproved())
         {
            $this->statusFlags[] = ReportEntryStatus::UNAPPROVED_TIME_CARD;
         }
         
         if ($this->getPartCountByWeightLog() == 0)
         {
            $this->statusFlags[] = ReportEntryStatus::NO_WEIGHT_LOGS;
         }
         
         if ($this->getPartCountByWasherLog() == 0)
         {
            $this->statusFlags[] = ReportEntryStatus::NO_WASH_LOGS;
         }
         
         if (!Calculations::isReasonablePartCount($this->getPartCountByTimeCard(), $this->grossParts))
         {
            $this->statusFlags[] = ReportEntryStatus::UNREASONABLE_PART_COUNT_BY_TIME_CARD;
         }
         
         if (!Calculations::isReasonablePartCount($this->getPartCountByWeightLog(), $this->grossParts))
         {
            $this->statusFlags[] = ReportEntryStatus::UNREASONABLE_PART_COUNT_BY_WEIGHT_LOG;
         }
         
         if (!Calculations::isReasonablePartCount($this->getPartCountByWasherLog(), $this->grossParts))
         {
            $this->statusFlags[] = ReportEntryStatus::UNREASONABLE_PART_COUNT_BY_WASHER_LOG;
         }
         
         if (false)  // TODO
         {
            $this->statusFlags[] = ReportEntryStatus::INCONSISTENT_PART_COUNTS;
         }
         
         if (($this->timeCardInfo->panCount != $this->getPanCountByWeightLog()) ||
             ($this->getPanCountByWeightLog() != $this->getPanCountByWasherLog()))
         {
            $this->statusFlags[] = ReportEntryStatus::INCONSISTENT_PAN_COUNTS;
         }
         
         if ($this->efficiency >= ReportEntry::UNREASONABLE_EFFICIENCY)
         {
            $this->statusFlags[] = ReportEntryStatus::UNREASONABLE_EFFICIENCY;
         }
      }
   }
   
   public function recalculate()
   {
      $this->averagePanWeight = Calculations::calculateAveragePanWeight($this->getTotalPartWeight(), $this->getPanCountByWeightLog());
      
      $this->grossParts = Calculations::calculateGrossParts($this->runTime, $this->jobInfo->grossPartsPerHour);
      
      $this->efficiency = Calculations::calculateEfficiency($this->partCount, $this->grossParts);
      
      $this->machineHoursMade = Calculations::calculateMachineHoursMade($this->partCount, $this->jobInfo->netPartsPerHour);
      
      $this->pcOverG = Calculations::calculatePCOverG($this->partCount, $this->jobInfo->grossPartsPerHour);
      
      $this->reportStatus = ReportEntryStatus::getOverallStatus($this->statusFlags);
   }
   
   public function checkStatusFlag($statusFlag)
   {
      return (in_array($statusFlag, $this->statusFlags));
   }
   
   // **************************************************************************
   
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
      
   private function getPartCountByTimeCard()
   {
      $partCount = $this->timeCardInfo->partCount;
      
      return ($partCount);
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
}

class OperatorSummary
{
   const TARGET_EFFICIENCY = 0.75;
   
   public $topEntryCount;
   
   public $reportEntries;
   
   public $adjustedTopEntries;
   
   public $runTime;
   
   public $shiftTime;
      
   public $efficiency;
   
   public $topEfficiency;
   
   public $adjustedTopEfficiency;
   
   public $adjustedTopRunTime;
      
   public $machineHoursMade;
   
   public $pcOverG;
   
   public $ratio;
   
   public function __construct($reportEntries, $topEntryCount)
   {
      $this->topEntryCount = $topEntryCount;
      
      $this->reportEntries = $reportEntries;
      
      $this->topEntries = OperatorSummary::getTopReportEntries($reportEntries, $topEntryCount);
      
      $this->bottomEntries = OperatorSummary::getBottomReportEntries($reportEntries, $topEntryCount);
      
      $this->adjustedEntries = OperatorSummary::getAdjustedEntries($this->topEntries, $this->bottomEntries, OperatorSummary::TARGET_EFFICIENCY);
      
      $this->runTime = OperatorSummary::calculateRunTime($reportEntries);  // Approved run times
      
      $this->shiftTime = OperatorSummary::calculateShiftTime($reportEntries);  // Expected same for all report entries
      
      $this->efficiency = OperatorSummary::calculateAverageEfficiency($reportEntries);
      
      $this->topEfficiency = OperatorSummary::calculateAverageEfficiency($this->topEntries);

      $this->adjustedTopEfficiency = OperatorSummary::calculateAverageEfficiency($this->adjustedEntries->topEntries);
      
      $this->adjustedTopRunTime = OperatorSummary::calculateRunTime($this->adjustedEntries->topEntries);
      
      $this->machineHoursMade = OperatorSummary::calculateMachineHoursMade($this->adjustedEntries->topEntries);
      
      $this->adjustedBottomPCOverG = OperatorSummary::calculatePcOverG($this->adjustedEntries->bottomEntries);
      
      $this->ratio = Calculations::calculateRatio($this->machineHoursMade, $this->shiftTime);
   }
   
   // **************************************************************************
   
   private static function calculateShiftTime($reportEntries)  // hours
   {
      $shiftTime = 0;
      
      foreach ($reportEntries as $reportEntry)
      {
         // Shift hours should be the same across all time cards.
         // If they're not, go with the greatest value.
         if ($reportEntry->timeCardInfo->getShiftTimeInHours() > $shiftTime)
         {
            $shiftTime = $reportEntry->timeCardInfo->getShiftTimeInHours();
         }
      }
      
      return ($shiftTime);
   }
   
   private static function calculateRunTime($reportEntries)  // hours
   {
      $runTime = 0;
      
      foreach ($reportEntries as $reportEntry)
      {
         $runTime += $reportEntry->timeCardInfo->getApprovedRunTime();
      }
      
      return ($runTime);
   }
   
   private function getTopReportEntries()
   {
      $topReportEntries = $this->reportEntries;
      
      usort($topReportEntries, function ($left, $right) {
         return (($left->efficiency < $right->efficiency) ? 1 : (($left->efficiency > $right->efficiency) ? -1 : 0));
      });
         
      // Take the top entries.
      $topReportEntries = array_slice($topReportEntries, 0, $this->topEntryCount);
      
      return ($topReportEntries);
   }
   
   private function getBottomReportEntries()
   {
      $bottomReportEntries = $this->reportEntries;
      
      if (count($bottomReportEntries) <= $this->topEntryCount)
      {
         $bottomReportEntries = array();
      }
      else
      {
         usort($bottomReportEntries, function ($left, $right) {
            return (($left->efficiency < $right->efficiency) ? 1 : (($left->efficiency > $right->efficiency) ? -1 : 0));
         });
            
         // Take the bottom entries.
         $bottomReportEntries = array_slice($bottomReportEntries, $this->topEntryCount);
      }
      
      return ($bottomReportEntries);
   }
   
   private static function getAdjustedEntries($topEntries, $bottomEntries, $targetEfficiency)
   {
      // Algorithm:
      // - Get top report entries.
      // - Get remaining (bottom) report entries.
      // - For each of the top entries ...
      //    - If the efficiency is less that the target
      //       - Create an additional "top" entry that pulls parts (and gross parts) from the bottom entries,
      //         and hours from the top entries, such that the new efficiency of the entry equals the target efficiency.
      
      $adjustedEntries = new stdClass();
      
      $adjustedEntries->topEntries = array_copy($topEntries);
      $adjustedEntries->bottomEntries = array_copy($bottomEntries);
      
      // Check if there are any machines to "borrow" from.
      if (count($adjustedEntries->bottomEntries) > 0)
      {
         // Create a new report entry, starting with the first "bottom" entry.
         $additionalReportEntry = clone $adjustedEntries->bottomEntries[0];
         $additionalReportEntry->runTime = 0;
         $additionalReportEntry->partCount = 0;
         
         foreach ($adjustedEntries->topEntries  as $reportEntry)
         {
            if ($reportEntry->efficiency < $targetEfficiency)
            {
               // Calculate the number of hours to "borrow".
               $adjustedHours = OperatorSummary::getAdjustedHours($reportEntry, $targetEfficiency);
               
               // Move hours from top entry to new entry.
               $reportEntry->runTime -= $adjustedHours;
               $additionalReportEntry->runTime += $adjustedHours;
               
               // Calculate the number of parts to "borrow".
               $adjustedPartCount = OperatorSummary::getAdjustedPartCount($additionalReportEntry, $adjustedEntries->bottomEntries[0], $targetEfficiency);
               
               // Move part counts from bottom entry to new entry.
               $adjustedEntries->bottomEntries[0]->partCount -= $adjustedPartCount;
               $additionalReportEntry->partCount += $adjustedPartCount;
               
               // Recalculate.
               $reportEntry->recalculate();
               $adjustedEntries->bottomEntries[0]->recalculate();
               $additionalReportEntry->recalculate();
            }
         }
         
         // Add the new report entry.
         $adjustedEntries->topEntries[] = $additionalReportEntry;
      }
      
      return ($adjustedEntries);
   }
   
   private static function getAdjustedHours($reportEntry, $targetEfficiency)
   {
      $adjustedHours = 0;
      
      if ($reportEntry->jobInfo->grossPartsPerHour > 0)
      {
         if (true) //($reportEntry->efficiency > $targetEfficiency) TODO
         {
            $adjustedHours = $reportEntry->runTime - ($reportEntry->partCount / ($reportEntry->jobInfo->grossPartsPerHour * $targetEfficiency));
            $adjustedHours = Calculations::roundUpToNearestQuarter($adjustedHours);
         }
         else
         {
            // TODO:
            $adjustedHours = Calculations::roundUpToNearestQuarter($reportEntry->runTime / 2);
         }
      }
      
      return ($adjustedHours);
   }
   
   private static function getAdjustedPartCount($topReportEntry, $bottomReportEntry, $targetEfficiency)
   {
      $adjustedPartCount = ceil($targetEfficiency * ($topReportEntry->runTime * $topReportEntry->jobInfo->grossPartsPerHour));
      
      if ($adjustedPartCount > $bottomReportEntry->partCount)
      {
         $adjustedPartCount = $bottomReportEntry->partCount;
      }
      
      return ($adjustedPartCount);
   }
   
   // **************************************************************************
   
   private static function calculateTotalPartCount($reportEntries)
   {
      $totalPartCount = 0;
      
      foreach ($reportEntries as $entry)
      {
         $totalPartCount += $entry->partCount;
      }
      
      return ($totalPartCount);
   }
   
   private static function calculateAverageEfficiency($reportEntries)
   {
      // Note: This calculation computes a *weighted* average of efficiencies, by run time.
      $averageEfficiency = 0;
      
      $totalEfficiency = 0;
      $totalRunTime = 0;
      
      foreach ($reportEntries as $entry)
      {
         $totalRunTime += $entry->runTime;
         $totalEfficiency += ($entry->efficiency * $entry->runTime);  // Weight using run time.
      }
      
      if ($totalRunTime > 0)
      {
         $averageEfficiency = ($totalEfficiency / $totalRunTime);
      }
      
      return ($averageEfficiency);
   }
   
   private static function calculateMachineHoursMade($reportEntries)
   {
      $machineHours = 0;
      
      foreach ($reportEntries as $entry)
      {
         $machineHours += $entry->machineHoursMade;
      }
      
      return ($machineHours);
   }
   
   private static function calculatePcOverG($reportEntries)
   {
      $pcOverG = 0;
      
      foreach ($reportEntries as $entry)
      {
         $pcOverG += $entry->pcOverG;
      }
      
      return ($pcOverG);
   }
}

class ShopSummary
{
   public $shiftTime;  // hours
   public $runTime;    // hours
   public $efficiency;
   public $machineHoursMade;
   public $ratio;
   
   public function __construct($operatorSummaries)
   {      
      $this->shiftTime = ShopSummary::calculateShiftTime($operatorSummaries);
      
      $this->runTime = ShopSummary::calculateRunTime($operatorSummaries);
      
      $this->efficiency = ShopSummary::calculateAverageEfficiency($operatorSummaries);
      
      $this->machineHoursMade = ShopSummary::calculateMachineHoursMade($operatorSummaries);
      
      $this->ratio = Calculations::calculateRatio($this->machineHoursMade, $this->shiftTime);
   }
   
   private static function calculateShiftTime($operatorSummaries)
   {
      $shiftTime = 0;
      
      foreach ($operatorSummaries as $operatorSummary)
      {
         $shiftTime += $operatorSummary->shiftTime;
      }
      
      return ($shiftTime);
   }
   
   private static function calculateRunTime($operatorSummaries)
   {
      $runTime = 0;
      
      foreach ($operatorSummaries as $operatorSummary)
      {
         $runTime += $operatorSummary->runTime;   
      }
      
      return ($runTime);
   }
   
   private static function calculateAverageEfficiency($operatorSummaries)
   {
      // Note: This calculation computes a *weighted* average of efficiencies, by run time.
      $averageEfficiency = 0;
      
      $totalEfficiency = 0;
      $totalRunTime = 0;
      
      foreach ($operatorSummaries as $operatorSummary)
      {
         $totalRunTime += $operatorSummary->adjustedTopRunTime;
         $totalEfficiency += ($operatorSummary->adjustedTopEfficiency * $operatorSummary->adjustedTopRunTime);  // Weight using run time.
      }
      
      if ($totalRunTime > 0)
      {
         $averageEfficiency = ($totalEfficiency / $totalRunTime);
      }
      
      return ($averageEfficiency);
   }
   
   private static function calculateMachineHoursMade($operatorSummaries)
   {
      $machineHours = 0;
      
      foreach ($operatorSummaries as $operatorSummary)
      {
         $machineHours += $operatorSummary->machineHoursMade;
      }
      
      return ($machineHours);
   }
}

class DailySummaryReport
{
   const TOP_ENTRY_COUNT = 2;
   
   public $dateTime;
   public $reportEntries;
   public $operatorSummaries;
      
   public function __construct()
   {
      $this->dateTime = null;
      $this->reportEntries = array();       // 2D array, indexed by [employee number][]
      $this->operatorSummaries = array();  // indexed by [employee number]
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
            
            $index = intval($row["employeeNumber"]);
            
            if (!isset($report->reportEntries[$index]))
            {
               $report->reportEntries[$index] = array();
            }
            
            $report->reportEntries[$index][] = ReportEntry::load($timeCardId);
         }
         
         // Compile operator summaries.
         $report->compileOperatorSummaries();
         
         // Compile shop summary.
         $report->shopSummary = new ShopSummary($report->operatorSummaries);
      }
      
      return ($report);
   }
   
   public function getEmployeeNumbers()
   {
      $employeeNumbers = array_keys($this->reportEntries);
      
      return ($employeeNumbers);
   }
   
   public function isWorkDay($employeeNumber)
   {
      return ($this->shiftTime > 0);
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
   
   private function getDailySummaryData()
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
      
      foreach ($this->reportEntries as $operatorEntries)
      {
         foreach ($operatorEntries as $reportEntry)
         {
            $row = new stdClass();
            
            // Source data.
            $row->timeCardId =           $reportEntry->timeCardInfo->timeCardId;
            $row->panTicketCode =        PanTicket::getPanTicketCode($reportEntry->timeCardInfo->timeCardId);
            $row->manufactureDate =      $reportEntry->timeCardInfo->dateTime;
            $row->operator =             $reportEntry->userInfo->getFullName();
            $row->employeeNumber =       $reportEntry->userInfo->employeeNumber;
            $row->jobNumber =            $reportEntry->jobInfo->jobNumber;
            $row->wcNumber =             $reportEntry->jobInfo->wcNumber;
            $row->materialNumber =       $reportEntry->timeCardInfo->materialNumber;
            $row->shiftTime =            $reportEntry->timeCardInfo->shiftTime;
            $row->runTime =              $reportEntry->timeCardInfo->runTime;
            $row->setupTime =            $reportEntry->timeCardInfo->setupTime;
            $row->panCount =             $reportEntry->timeCardInfo->panCount;
            $row->sampleWeight =         $reportEntry->jobInfo->sampleWeight;
            $row->partWeight =           $reportEntry->partWeight;
            $row->grossPartsPerHour =    $reportEntry->jobInfo->grossPartsPerHour;
            $row->netPartsPerHour =      $reportEntry->jobInfo->netPartsPerHour;
            $row->partCountByTimeCard =  $reportEntry->timeCardInfo->partCount;
            $row->partCountByWeightLog = $reportEntry->partCountByWeightLog;
            $row->partCountByWasherLog = $reportEntry->partCountByWasherLog;
            $row->partCount =            $reportEntry->partCount;
            $row->scrapCount =           $reportEntry->timeCardInfo->scrapCount;
   
            // Data validation.
            $row->incompleteShiftTime =              $reportEntry->timeCardInfo->incompleteShiftTime();
            $row->unapprovedRunTime =                !$reportEntry->timeCardInfo->isRunTimeApproved();
            $row->unapprovedSetupTime =              !$reportEntry->timeCardInfo->isSetupTimeApproved();
            $row->incompletePanCount =               $reportEntry->timeCardInfo->incompletePanCount();
            $row->unreasonablePartWeight =           $reportEntry->checkStatusFlag(ReportEntryStatus::UNREASONABLE_PART_COUNT_BY_WEIGHT_LOG);
            $row->unreasonablePartCountByTimeCard =  $reportEntry->checkStatusFlag(ReportEntryStatus::UNREASONABLE_PART_COUNT_BY_TIME_CARD);
            $row->unreasonablePartCountByWeightLog = $reportEntry->checkStatusFlag(ReportEntryStatus::UNREASONABLE_PART_COUNT_BY_WEIGHT_LOG);
            $row->unreasonablePartCountByWasherLog = $reportEntry->checkStatusFlag(ReportEntryStatus::UNREASONABLE_PART_COUNT_BY_WASHER_LOG);
            $row->incompletePartCount =              $reportEntry->timeCardInfo->incompletePartCount();
            $row->unreasonableEfficiency =           ($reportEntry->efficiency >= ReportEntry::UNREASONABLE_EFFICIENCY);
            $row->reportStatus =                     $reportEntry->reportStatus;
            $row->dataStatusLabel =                  ReportEntryStatus::getLabel($reportEntry->reportStatus);
            $row->dataStatusClass =                  ReportEntryStatus::getClass($reportEntry->reportStatus);
            
            // Calculated values.
            $row->averagePanWeight = $reportEntry->averagePanWeight;
            $row->grossParts =       $reportEntry->grossParts;
            $row->efficiency =       round(($reportEntry->efficiency * 100), 2);
            $row->machineHoursMade = round($reportEntry->machineHoursMade, 2);
            
            $reportData[] = $row;
         }
      }
      
      return ($reportData);
   }
   
   private function getOperatorSummaryData()
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
         
         $operatorSummary = $this->operatorSummaries[$employeeNumber];
         
         $row->operator = $userInfo->getFullName();
         $row->employeeNumber = $userInfo->employeeNumber;
         $row->runTime = $operatorSummary->runTime;  // use approved run time
         $row->efficiency = round($operatorSummary->efficiency * 100, 2);
         $row->topEfficiency = round($operatorSummary->topEfficiency * 100, 2);
         $row->adjustedTopEfficiency = round($operatorSummary->adjustedTopEfficiency * 100, 2);
         
         if (count($operatorSummary->adjustedEntries->topEntries) == 3)
         {
            $row->adjustedPartCount = $operatorSummary->adjustedEntries->topEntries[2]->partCount;
            $row->adjustedHours = $operatorSummary->adjustedEntries->topEntries[2]->runTime;
         }
         else 
         {
            $row->adjustedPartCount = 0;
            $row->adjustedHours = 0;
         }
         
         $row->adjustedBottomPCOverG = round($operatorSummary->adjustedBottomPCOverG, 2);
         $row->shiftTime = $operatorSummary->shiftTime;
         $row->machineHoursMade = round($operatorSummary->machineHoursMade, 2);
         $row->ratio = round($operatorSummary->ratio, 2);
         
         $reportData[] = $row;
      }
      
      return ($reportData);
   }
   
   private function getShopSummaryData()
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
      
      $row->shiftTime = $this->shopSummary->shiftTime;
      $row->runTime = $this->shopSummary->runTime;
      $row->efficiency = round($this->shopSummary->efficiency * 100, 2);
      $row->machineHoursMade = round($this->shopSummary->machineHoursMade, 2);
      $row->ratio = round($this->shopSummary->ratio, 2);
      
      $reportData[] = $row;
      
      return ($reportData);
   }  
   
   private function compileOperatorSummaries()
   {
      foreach ($this->getEmployeeNumbers() as $employeeNumber)
      {
         $this->operatorSummaries[$employeeNumber] = new OperatorSummary($this->reportEntries[$employeeNumber], DailySummaryReport::TOP_ENTRY_COUNT);         
      }
   }
   
   private function getReportEntries($employeeNumber)
   {
      return ($this->reportEntries[$employeeNumber]);
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