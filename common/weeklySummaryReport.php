<?php

require_once 'dailySummaryReport.php';

abstract class WeeklySummaryReportTable
{
   const FIRST = 0;
   const WEEKLY_SUMMARY = WeeklySummaryReportTable::FIRST;
   const BONUS = 1;
   const LAST = 2;
   const COUNT = WeeklySummaryReportTable::LAST - WeeklySummaryReportTable::FIRST;
}

abstract class WorkDay
{
   const UNKNOWN = 0;
   const FIRST = 1;
   const SUNDAY = WorkDay::FIRST;
   const MONDAY = 2;
   const TUESDAY = 3;
   const WEDNESDAY = 4;
   const THURSDAY = 5;
   const FRIDAY = 6;
   const SATURDAY = 7;
   const LAST = 8;
   const COUNT = (WorkDay::LAST - WorkDay::FIRST);
   
   public static function getLabel($workDay)
   {
      $labels = array("---",
                      "Sunday",
                      "Monday",
                      "Tuesday",
                      "Wednesday",
                      "Thursday",
                      "Friday",
                      "Saturday");
      
      return ($labels[$workDay]);
   }
   
   public static function getDates($dateTime)
   {
      $dates = array();
      
      $dt = new DateTime($dateTime, new DateTimeZone('America/New_York'));
      
      for ($workDay = WorkDay::FIRST; $workDay < WorkDay::LAST; $workDay++)
      {
         $index = ($workDay - WorkDay::FIRST);
         
         $evalDt = clone $dt->setISODate($dt->format("Y"), Time::weekNumber($dateTime), $index);
         
         $dates[$workDay] = $evalDt->format("Y-m-d H:i:s");
      }
      
      return ($dates);
   }
}

abstract class Bonus
{
   const UNKNOWN = 0;
   const FIRST = 1;
   const TIER1 = Bonus::FIRST;
   const MIN_EFFICIENCY_TIER = Bonus::TIER1;
   const TIER2 = 2;
   const TIER3 = 3;
   const TIER4 = 4;
   const TIER5 = 5;
   const TIER6 = 6;
   const MAX_EFFICIENCY_TIER = Bonus::TIER6;
   const ADDITIONAL_MACHINE_BONUS = 7; 
   const LAST = 8;
   const COUNT = (Bonus::LAST - Bonus::FIRST);
   
   public static function getTier($efficiency)
   {
      $tier = Bonus::UNKNOWN;
      
      for ($tempTier = Bonus::MAX_EFFICIENCY_TIER; $tempTier >= Bonus::MIN_EFFICIENCY_TIER; $tempTier--)
      {
         if ($efficiency >= Bonus::getEfficiencyRequirement($tempTier))
         {
            $tier = $tempTier;
            break;
         }
      }
      
      return ($tier);
   }
   
   public static function getBonusRate($tier)
   {
      $rates = array(0, 0.25, 0.50, 1.00, 1.50, 2.00, 3.00, 4.00);
      
      $bonusRate = 0.0;
      
      if (($tier >= Bonus::FIRST) && ($tier < Bonus::LAST))
      {
         $bonusRate = $rates[$tier];
      }
      
      return ($bonusRate);
   }
   
   public static function getEfficiencyRequirement($tier)
   {
      $efficiencies = array(0, 75, 80, 85, 90, 95, 100, 0);
      
      $efficiencyRequirement = 0;
      
      if (($tier >= Bonus::FIRST) && ($tier < Bonus::LAST))
      {
         $efficiencyRequirement = $efficiencies[$tier];
      }
      
      return ($efficiencyRequirement);
   }
   
   public static function calculateBonus($tier, $hours)
   {
      $bonus = 0.0;

      if (($tier >= Bonus::FIRST) && ($tier < Bonus::LAST))
      {
         $bonus = (($hours / 2) * Bonus::getBonusRate($tier));
      }
      
      return ($bonus);
   }
}

class WeeklySummaryReport
{
   
   public $dates;
   public $dailySummaryReports;
   
   public function __construct()
   {
      $this->dates = array();
      $this->dailySummaryReports = array();
   }
   
   public static function load($dateTime)
   {
      $weeklySummaryReport = new WeeklySummaryReport();
      
      $weeklySummaryReport->dates = WorkDay::getDates($dateTime);
      
      for ($workDay = WorkDay::FIRST; $workDay < WorkDay::LAST; $workDay++)
      {
         $weeklySummaryReport->dailySummaryReports[$workDay] = 
            DailySummaryReport::load(UserInfo::UNKNOWN_EMPLOYEE_NUMBER, $weeklySummaryReport->dates[$workDay]);
      }
      
      return ($weeklySummaryReport);
   }
   
   public function getReportData($table)
   {
      $reportData = array();
      
      switch ($table)
      {
         case WeeklySummaryReportTable::WEEKLY_SUMMARY:
         {
            $reportData = $this->getWeeklySummaryData();
            break;
         }
         
         case WeeklySummaryReportTable::BONUS:
         {
            $reportData = $this->getBonusData();
            break;
         }
            
         default:
         {
            break;
         }
      }
      
      return ($reportData);
   }
   
   public function getTotalRunTime($employeeNumber, $useApprovedRunTime = false)
   {
      $totalRunTime = 0;
      
      foreach ($this->dailySummaryReports as $dailySummaryReport)
      {
         $totalRunTime += $dailySummaryReport->getTotalRunTime($employeeNumber, $useApprovedRunTime);
      }
      
      return ($totalRunTime);
   }
   
   private function getWeeklySummaryData()
   {
      $reportData = array();
      
      $employeeNumbers = $this->getEmployeeNumbers();
      
      foreach ($employeeNumbers as $employeeNumber)
      {
         $userInfo = UserInfo::load($employeeNumber);
         
         $row = new stdClass();
         
         $row->operator = $userInfo->getFullName();
         $row->employeeNumber = $userInfo->employeeNumber;
         $row->sunday = $this->getDailyStats(WorkDay::SUNDAY, $employeeNumber);
         $row->monday = $this->getDailyStats(WorkDay::MONDAY, $employeeNumber);
         $row->tuesday = $this->getDailyStats(WorkDay::TUESDAY, $employeeNumber);
         $row->wednesday = $this->getDailyStats(WorkDay::WEDNESDAY, $employeeNumber);
         $row->thursday = $this->getDailyStats(WorkDay::THURSDAY, $employeeNumber);
         $row->friday = $this->getDailyStats(WorkDay::FRIDAY, $employeeNumber);
         $row->saturday = $this->getDailyStats(WorkDay::SATURDAY, $employeeNumber);
         
         $reportData[] = $row;
      }
      
      return ($reportData);
   }
   
   private function getBonusData()
   {
      $reportData = array();
      
      $employeeNumbers = $this->getEmployeeNumbers();
      
      foreach ($employeeNumbers as $employeeNumber)
      {
         $userInfo = UserInfo::load($employeeNumber);
         
         $row = new stdClass();
         
         $row->operator = $userInfo->getFullName();
         $row->employeeNumber = $userInfo->employeeNumber;
         $row->totalRunTime = ($this->getTotalRunTime($employeeNumber, true) / TimeCardInfo::MINUTES_PER_HOUR);         
         $row->tier1 = Bonus::calculateBonus(Bonus::TIER1, ($this->getTotalRunTime($employeeNumber) / TimeCardInfo::MINUTES_PER_HOUR));
         $row->tier2 = Bonus::calculateBonus(Bonus::TIER2, ($this->getTotalRunTime($employeeNumber) / TimeCardInfo::MINUTES_PER_HOUR));
         $row->tier3 = Bonus::calculateBonus(Bonus::TIER3, ($this->getTotalRunTime($employeeNumber) / TimeCardInfo::MINUTES_PER_HOUR));
         $row->tier4 = Bonus::calculateBonus(Bonus::TIER4, ($this->getTotalRunTime($employeeNumber) / TimeCardInfo::MINUTES_PER_HOUR));
         $row->tier5 = Bonus::calculateBonus(Bonus::TIER5, ($this->getTotalRunTime($employeeNumber) / TimeCardInfo::MINUTES_PER_HOUR));
         $row->tier6 = Bonus::calculateBonus(Bonus::TIER6, ($this->getTotalRunTime($employeeNumber) / TimeCardInfo::MINUTES_PER_HOUR));
         $row->additionalMachineBonus = 0.0;
         
         $reportData[] = $row;
      }
      
      return ($reportData);
   }
   
   private function getEmployeeNumbers()
   {
      $employeeNumbers = array();
      
      for ($workDay = WorkDay::FIRST; $workDay < WorkDay::LAST; $workDay++)
      {
         $employeeNumbers += $this->dailySummaryReports[$workDay]->getEmployeeNumbers();
      }
      
      return ($employeeNumbers);
   }
   
   private function getDailyStats($workDay, $employeeNumber)
   {
      $stats = new stdClass();
      
      $stats->day = WorkDay::getLabel($workDay);
      $stats->date = $this->dates[$workDay];
      $stats->runTime = ($this->dailySummaryReports[$workDay]->getTotalRunTime($employeeNumber, true) / TimeCardInfo::MINUTES_PER_HOUR);
      $stats->efficiency = $this->dailySummaryReports[$workDay]->getAverageEfficiency($employeeNumber);
      $stats->machineHoursMade = $this->dailySummaryReports[$workDay]->getTotalMachineHoursMade($employeeNumber);
      $stats->ratio = $this->dailySummaryReports[$workDay]->getRatio($employeeNumber);
      
      return ($stats);
   }
}

/*
$dateTime = new DateTime("10/29/2020");
$dtString = $dateTime->format("Y-m-d H:i:s");
$weeklySummaryReport = new WeeklySummaryReport();
$weeklySummaryReport->load($dtString);
echo var_dump($weeklySummaryReport->getReportData(WeeklySummaryReportTable::WEEKLY_SUMMARY));
*/