<?php
require_once '../common/database.php';
require_once 'viewTimeCard.php';

class PrintTimeCard extends ViewTimeCard
{
   public static function getHtml($readOnly)
   {
      $html = "";
      
      $timeCardInfo = ViewTimeCard::getTimeCardInfo();
      
      $titleDiv = ViewTimeCard::titleDiv();
      $dateDiv = ViewTimeCard::dateDiv($timeCardInfo, true);
      $operatorDiv = ViewTimeCard::operatorDiv($timeCardInfo);
      $jobDiv = ViewTimeCard::jobDiv($timeCardInfo, true);
      $timeDiv = ViewTimeCard::timeDiv($timeCardInfo, true);
      $partsDiv = ViewTimeCard::partsDiv($timeCardInfo, true);
      $commentsDiv = ViewTimeCard::commentsDiv($timeCardInfo, true);
      $commentCodesDiv = ViewTimeCard::commentCodesDiv($timeCardInfo, true);
      $qrDiv = ViewTimeCard::qrDiv($timeCardInfo);
      
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
                  $timeDiv
               </div>
               <div class="flex-horizontal" style="align-items: flex-start;">
                  $jobDiv
                  $partsDiv
               </div>
               <div class="flex-horizontal" style="align-items: flex-start;">
                  $commentCodesDiv
                  $commentsDiv
               </div>
               <div class="flex-horizontal" style="align-items: flex-start;">
                  $qrDiv
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
<link rel="stylesheet" type="text/css" href="timeCard.css"/>

<script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
<script src="timeCard.js"></script>
</head>

<body>

<?php PrintTimeCard::render($readOnly = false); ?>

</body>

<script>
javascript:window.print()
</script>

</html>