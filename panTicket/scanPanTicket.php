<?php

require_once '../common/authentication.php';
require_once '../common/database.php';
require_once '../common/header.php';
require_once '../common/navigation.php';
require_once '../common/panTicket.php';
require_once '../common/params.php';
require_once '../common/timeCardInfo.php';

function getNavBar()
{
   $navBar = new Navigation();
   
   $navBar->start();
   $navBar->mainMenuButton();
   $navBar->end();
   
   return ($navBar->getHtml());
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
      function onScanResult(result)
      {
         if (result != null)
         {
         console.log("onScanResult: " + result);

         let panTicketId = parseInt(result);

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
                     location.href = "viewPanTicket.php?panTicketId=" + json.timeCardId;     
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

      import QrScanner from "./qr-scanner.min.js";
      QrScanner.WORKER_PATH = './qr-scanner-worker.min.js';
   
      const video = document.getElementById('camera');
   
      const scanner = new QrScanner(video, onScanResult());
      scanner.start();
   </script>

</body>

</html>