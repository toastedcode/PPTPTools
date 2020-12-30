<?php

abstract class Calculations
{
   public static function calculateAveragePanWeight($totalWeight, $panCount)
   {
      $averageWeight = 0;
      
      if ($panCount > 0)
      {
         $averageWeight = round((($totalWeight - PartWeightEntry::STANDARD_PALLET_WEIGHT) / $panCount), 2);
      }
      
      return ($averageWeight);
   }
   
   public static function estimatePartCount($timeCardCount, $partWeightCount, $partWasherCount, $grossParts)
   {
      $partCount = 0;
      
      if (($partWeightCount > 0) &&
            (Calculations::isReasonablePartCount($partWeightCount, $grossParts)))
      {
         $partCount = $partWeightCount;
      }
      else if (($partWasherCount > 0) &&
            (Calculations::isReasonablePartCount($partWasherCount, $grossParts)))
      {
         $partCount = $partWasherCount;
      }
      else
      {
         $partCount = $timeCardCount;
      }
      
      return ($partCount);
   }
   
   public static function calculateGrossParts($runTime, $grossPartsPerHour)
   {
      $grossParts = floor($grossPartsPerHour * $runTime);
      
      return ($grossParts);
   }
   
   public static function isReasonablePartCount($partCount, $grossParts)
   {
      $MAX_EFFICIENCY = 1.10;  // 110%
      
      $efficiency = Calculations::calculateEfficiency($partCount, $grossParts);
      
      return ($efficiency < $MAX_EFFICIENCY);
   }
   
   public static function calculateEfficiency($partCount, $grossParts)
   {
      $efficiency = 0;
      
      if ($grossParts != 0)
      {
         $efficiency = ($partCount / $grossParts);
      }
      
      return ($efficiency);
   }
   
   public static function calculateMachineHoursMade($partCount, $netPartsPerHour)
   {
      $machineHours = 0;
      
      if ($netPartsPerHour != 0)
      {
         $machineHours = ($partCount / $netPartsPerHour);
      }
      
      return ($machineHours);
   }
   
   public static function calculatePCOverG($partCount, $grossPartsPerHour)
   {
      $pcOverG = 0;
      
      if ($grossPartsPerHour != 0)
      {
         $pcOverG = ($partCount / $grossPartsPerHour);
      }
      
      return ($pcOverG);
   }
   
   public static function calculateRatio($machineHoursMade, $shiftTime)
   {
      $ratio = 0;
      
      if ($shiftTime > 0)
      {
         $ratio = ($machineHoursMade / $shiftTime);
      }
      
      return ($ratio);
   }
   
   public static function roundUpToNearestQuarter($value)
   {
      return (ceil($value * 4) / 4);
   }
}


?>