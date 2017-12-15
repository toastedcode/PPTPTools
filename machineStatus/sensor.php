<?php

require_once '../database.php';

function updatePartCount($sensorId, $partCount)
{
   $database = new PPTPDatabase();
   
   $database->connect();
   
   if ($database->isConnected())
   {
      $database->updatePartCount($sensorId, $partCount);
   }
}

function resetPartCount($sensorId)
{
   $database = new PPTPDatabase();
   
   $database->connect();
   
   if ($database->isConnected())
   {
      $database->resetPartCounter($sensorId);
   }
}

// *****************************************************************************
//                                   Begin

if (isset($_GET["sensorId"]) && (isset($_GET["action"])))
{
   $sensorId = $_GET["sensorId"];
   $action = $_GET["action"];
   
   switch ($action)
   {
      case "count":
      {
         if (isset($_GET["count"]))
         {
            $partCount = $_GET["count"];
            
            updatePartCount($sensorId, $partCount);
         }
         break;
      }
      
      case "ping":
      {
         updatePartCount($sensorId, 0);
         break;
      }
      
      case "reset":
      {
         resetPartCount($sensorId);
         break;
      }
      
      default:
      {
         break;
      }
   }
}