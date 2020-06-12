<?php

require_once '../common/authentication.php';
require_once '../common/database.php';
require_once '../common/header.php';
require_once '../common/navigation.php';
require_once '../common/panTicket.php';
require_once '../common/params.php';
require_once '../common/timeCardInfo.php';

abstract class ScanToFunction
{
   const FIRST = 0;
   const UNKNOWN = ScanToFunction::FIRST;
   const PART_WEIGHT_LOG = 1;
   const PART_WASHER_LOG = 2;
   const LAST = 3;
   const COUNT = ScanToFunction::LAST - ScanToFunction::FIRST;
}

function getNavBar()
{
   $navBar = new Navigation();
   
   $navBar->start();
   $navBar->mainMenuButton();
   $navBar->end();
   
   return ($navBar->getHtml());
}

function getScanToFunction()
{
   $scanTo = ScanToFunction::UNKNOWN;
   
   $params = Params::parse();
   
   if ($params->keyExists("scanTo"))
   {
      $scanTo = $params->getInt("scanTo");
   }
   
   return ($scanTo);
}

// *********************************** BEGIN ***********************************

Time::init();

session_start();

if (!Authentication::isAuthenticated())
{
   header('Location: ../pptpTools.php');
   exit;
}
?>

<!DOCTYPE html>
<html>

<head>

   <meta name="viewport" content="width=device-width, initial-scale=1">
   
   <link rel="stylesheet" type="text/css" href="../common/flex.css"/>
   <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>
   <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-blue.min.css"/>
   <link rel="stylesheet" type="text/css" href="../common/common.css"/>
   
   <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
   <script src="../common/common.js"></script>

</head>

<body>

   <?php Header::render("PPTP Tools"); ?>
   
   <div class="flex-horizontal main">
     
     <div class="flex-horizontal sidebar hide-on-tablet"></div> 
   
     <div class="flex-vertical content">

        <div class="heading">Pan Ticket Scanner</div>

        <div class="description">Scan a pan ticket as the first step in weighing, washing, or tracking parts.</div>

        <div class="flex-vertical inner-content" style="align-items:center; width:100%;">
        
           <video muted playsinline id="camera" width="500"></video>
       
        </div>
        
        <?php echo getNavBar(); ?>
         
     </div>
     
   </div>
   
   <script  type="module">
      preserveSession();

      function onScanResult(result)
      {
         if (result != null)
         {
            console.log("onScanResult: " + result);

            // Extract the pan ticket ID from the URL.
            let panTicketId = 0;
            let startPos = (result.lastIndexOf("panTicketId=") + 1);
            if (startPos > 0) 
            {
               panTicketId = parseInt(result.substring(startPos));
            }

            // AJAX call to retrieve gross parts per hour by selected job.
            let requestUrl = "../api/timeCardInfo/?timeCardId=" + panTicketId;
      
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function()
            {
               if (this.readyState == 4 && this.status == 200)
               {
                  try
                  {
                     var json = JSON.parse(this.responseText);
             
                     if (json.success == true)
                     {
                        if (scanTo == <?php echo (ScanToFunction::PART_WASHER_LOG); ?>)
                        {
                           location.href = "../partWeightLog/partWeightLogEntry.php?timeCardId=" + panTicketId;  
                        }
                        else if (scanTo == <?php echo (ScanToFunction::PART_WEIGHT_LOG); ?>)
                        {
                           location.href = "../partWasherLog/partWasherLogEntry.php?timeCardId=" + panTicketId;
                        }
                        else
                        {
                           location.href = result;
                        }
                     }
                     else
                     {
                        alert("Sorry. This pan ticket is no longer valid.");
                     }
                  }
                  catch (expection)
                  {
                     console.log("JSON syntax error");
                     console.log(this.responseText);
                  }
               }
            };
            xhttp.open("GET", requestUrl, true);
            xhttp.send();
         }
      }

      var scanTo = <?php echo (getScanToFunction()); ?>;

      import QrScanner from "./qr-scanner.min.js";
      QrScanner.WORKER_PATH = './qr-scanner-worker.min.js';
   
      const video = document.getElementById('camera');
   
      const scanner = new QrScanner(video, onScanResult());
      scanner.start();
   </script>

</body>

</html>