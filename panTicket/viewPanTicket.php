<?php

require_once '../common/authentication.php';
require_once '../common/database.php';
require_once '../common/header.php';
require_once '../common/navigation.php';
require_once '../common/panTicket.php';
require_once '../common/params.php';
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
      $navBar->highlightNavButton("Edit Time Card", "location.href = '../timeCard/viewTimeCard.php?timeCardId=$timeCardId'", false);
   
      $navBar->highlightNavButton("Weigh Parts", "location.href = '../partWeightLog/partWeightLogEntry.php?timeCardId=$timeCardId'", false);
   
      $navBar->highlightNavButton("Wash Parts", "location.href = '../partWasherLog/partWasherLogEntry.php?timeCardId=$timeCardId'", false);
      
      $navBar->highlightNavButton("Print Copies", "location.href = 'printPanTicket.php?panTicketId=$timeCardId'", false);
   }
   else
   {
      $navBar->highlightNavButton("Ok", "location.href = '../home.php'", false);      
   }
   
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
   <link rel="stylesheet" type="text/css" href="../common/tooltip.css"/>
   
   <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
   <script src="http://www.labelwriter.com/software/dls/sdk/js/DYMO.Label.Framework.3.0.js" type="text/javascript" charset="UTF-8"></script>
   <script src="../common/panTicket.js"></script>

</head>

<body>

   <?php Header::render("PPTP Tools"); ?>
   
   <div class="flex-horizontal main">
     
     <div class="flex-horizontal sidebar hide-on-tablet"></div> 
   
     <div class="flex-vertical content">

        <div class="heading">Pan Ticket</div>

        <div class="description">Select the action you want to take with this pan ticket.  All the relevant data will be automatically entered for you.</div>

        <div class="flex-vertical inner-content" style="align-items:center; width:100%;">
 
           <img id="pan-ticket-image" src="" style="display:none;" alt="pan ticket"/>
       
        </div>
        
        <?php echo getNavBar(); ?>
         
     </div>
     
   </div>
   
   <script>
      dymo.label.framework.init(function() {
         var label = new PanTicket(<?php echo getPanTicketId(); ?>, "pan-ticket-image", );
      });
   </script>

</body>

</html>