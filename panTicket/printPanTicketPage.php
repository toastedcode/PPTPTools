<?php
require_once '../database.php';
require_once 'viewPanTicketPage.php';

class PrintTimeCard extends ViewPanTicket
{
   public static function getHtml($readOnly)
   {
      $html = "";
      
      $panTicketInfo = ViewPanTicket::getPanTicketInfo();
      
      $titleDiv = ViewPanTicket::titleDiv($panTicketInfo);
      $dateDiv = ViewPanTicket::dateDiv($panTicketInfo, false);
      $operatorDiv = ViewPanTicket::operatorDiv($panTicketInfo);
      $jobDiv = ViewPanTicket::jobDiv($panTicketInfo, false);
      $weightDiv = ViewPanTicket::weightDiv($panTicketInfo, false);
      $qrDiv = ViewPanTicket::qrDiv($panTicketInfo, false);
      
      $html =
<<<HEREDOC
         <div class="flex-horizontal" style="width:100%">
            <div class="flex-vertical time-card-div">
               <div class="flex-horizontal">
                  $titleDiv
                  $dateDiv
               </div>
               <div class="flex-horizontal" style="align-items: flex-start;">
                  $operatorDiv
                  $jobDiv
               </div>
               <div class="flex-horizontal" style="align-items: flex-start;">
                  $qrDiv
                  $weightDiv
               </div>
            </div>
         </div>
HEREDOC;
      
      return ($html);
   }
   
   public static function render($readOnly)
   {
      echo (PrintTimeCard::getHtml($readOnly));
   }
}
?>

<!-- ********************************** BEGIN ********************************************* -->

<html>
<head>
<link rel="stylesheet" type="text/css" href="flex.css"/>
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>
<link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-blue.min.css"/>
<link rel="stylesheet" type="text/css" href="panTicket.css"/>

<script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
<script src="panTicket.js"></script>
</head>

<body>

<?php PrintTimeCard::render($readOnly = false); ?>

</body>

<script>
javascript:window.print()
</script>

</html>