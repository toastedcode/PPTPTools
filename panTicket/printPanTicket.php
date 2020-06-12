<?php

require_once '../common/authentication.php';
require_once '../common/database.php';
require_once '../common/header.php';
require_once '../common/navigation.php';
require_once '../common/panTicket.php';
require_once '../common/params.php';
require_once '../common/printerInfo.php';
require_once '../common/timeCardInfo.php';

function getParams()
{
   static $params = null;
   
   if (!$params)
   {
      $params = Params::parse();
   }
   
   return ($params);
}

function getPanTicketId()
{
   $panTicketId = PanTicket::UNKNOWN_PAN_TICKET_ID;
   
   $params = getParams();
   
   if ($params->keyExists("panTicketId"))
   {
      $panTicketId = $params->getInt("panTicketId");
   }
   
   return ($panTicketId);
}

function getPanTicket()
{
   $panTicket = null;
   
   $panTicketId = getPanTicketId();
   
   if ($panTicketId != PanTicket::UNKNOWN_PAN_TICKET_ID)
   {
      $panTicket = new PanTicket($panTicketId);
   }
   
   return ($panTicket);
}

function getNavBar()
{
   // Time card ids are synonomous with pan ticket ids.
   $timeCardId = getPanTicketId();
   
   $navBar = new Navigation();
   
   $navBar->start();
   
   if ($timeCardId != TimeCardInfo::UNKNOWN_TIME_CARD_ID)
   {
      $navBar->cancelButton("window.history.back();", false);
      $navBar->highlightNavButton("Print", "printPanTicket()", false);
   }
   else
   {
      $navBar->highlightNavButton("Ok", "location.href = '../home.php'", false);
   }
   
   $navBar->end();
   
   return ($navBar->getHtml());
}

function getPrinterOptions()
{
   $options = "";
   
   $database = PPTPDatabase::getInstance();
   
   if ($database && $database->isConnected())
   {
      $result = $database->getPrinters();
      
      while ($result && ($row = $result->fetch_assoc()))
      {
         $printerInfo = PrinterInfo::load($row["printerName"]);

         if ($printerInfo && $printerInfo->isCurrent())
         {
            $displayName = $printerInfo->printerName;
            if (strrpos($printerInfo->printerName, "\\") !== false)
            {
               $displayName = substr($printerInfo->printerName, strrpos($printerInfo->printerName, "\\") + 1);
            }            
            
            $disabled = "";
            $selected = "";
            if (!$printerInfo->isConnected)
            {
               $displayName .= " (offline)";
               $disabled = "disabled";
               
               if (isset($_SESSION["preferredPrinter"]) &&
                   ($printerInfo->printerName == $_SESSION["preferredPrinter"]))
               {
                 $selected = "selected"; 
               }
            }
            
            $options .= "<option value=\"$printerInfo->printerName\" $selected $disabled>$displayName</option>";
         }
      }      
   }
   
   return ($options);
}

// *********************************** BEGIN ***********************************

Time::init();

session_start();

if (!Authentication::isAuthenticated())
{
   header('Location: ../home.php');
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
   <link rel="stylesheet" type="text/css" href="../common/form.css"/>
   <link rel="stylesheet" type="text/css" href="../common/panTicket.css"/>
   <link rel="stylesheet" type="text/css" href="../common/tooltip.css"/>
   
   <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
   <script src = "../thirdParty/dymo/DYMO.Label.Framework.3.0.js" type="text/javascript" charset="UTF-8"></script>
   <script src="../common/common.js"></script>
   <script src="../common/panTicket.js"></script>

</head>

<body>

   <?php Header::render("PPTP Tools"); ?>
   
   <div class="flex-horizontal main">
     
     <div class="flex-horizontal sidebar hide-on-tablet"></div> 
     
     <form id="input-form" action="" method="POST">
        <input type="hidden" name="panTicketId" value="<?php echo getPanTicketId(); ?>">
      </form>
   
     <div class="flex-vertical content">

        <div class="heading">Print Pan Tickets</div>

        <div class="description">ISO standards require that all manufactured parts be tracked with a pan ticket.</div>

        <div class="flex-horizontal inner-content" style="align-items:center; width:100%;">
 
           <!-- img id="pan-ticket-image" src="" width="25%" style="display:none; margin-right:50px;" alt="pan ticket"/-->
           <div style="margin-right: 50px;">
              <?php $panTicket = new PanTicket(getPanTicketId()); $panTicket->render(); ?>
           </div>
           
            <div class="pptp-form">

                  <div class="form-item">
                     <div class="form-label">Printer</div>
                     <select id="printer-input" class="form-input-medium" type="text" name="printerName" form="input-form">
                        <?php echo getPrinterOptions(); ?>
                     </select>
                  </div>

                  <div class="form-item">
                     <div class="form-label">Copies</div>
                     <input id="copies-input" class="form-input-medium" type="number" name="copies" form="input-form" style="width:50px;" value="1">
                  </div>

            </div>

        </div>
        
        <?php echo getNavBar(); ?>
         
     </div>
     
   </div>
   
   <script>
      preserveSession();
   
      /*
      dymo.label.framework.init(function() {
         var label = new PanTicket(<!--php echo getPanTicketId(); ?-->, "pan-ticket-image", );
      });
      */

      function printPanTicket()
      {
         var form = document.querySelector('#input-form');
         
         var xhttp = new XMLHttpRequest();
      
         // Bind the form data.
         var formData = new FormData(form);
      
         // Define what happens on successful data submission.
         xhttp.addEventListener("load", function(event) {
            try
            {
               var json = JSON.parse(event.target.responseText);
      
               if (json.success == true)
               {
                  alert("Print job was successfully queued.");
               }
               else
               {
                  alert(json.error);
               }
            }
            catch (expection)
            {
               console.log("JSON syntax error");
               console.log(this.responseText);
            }
         });
      
         // Define what happens on successful data submission.
         xhttp.addEventListener("error", function(event) {
           alert('Oops! Something went wrong.');
         });
      
         // Set up our request
         requestUrl = "../api/printPanTicket/"
         xhttp.open("POST", requestUrl);
      
         // The data sent is what the user provided in the form
         xhttp.send(formData);         
      }
   </script>

</body>

</html>