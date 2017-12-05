<?php
require_once '../database.php';
require_once 'viewTimeCardPage.php';

class PrintTimeCard extends ViewTimeCard
{
   public static function getHtml()
   {
      $html = "";
      
      $timeCardInfo = ViewTimeCard::getTimeCardInfo();
      
      $titleDiv = ViewTimeCard::titleDiv();
      $dateDiv = ViewTimeCard::dateDiv($timeCardInfo);
      $operatorDiv = ViewTimeCard::operatorDiv($timeCardInfo);
      $jobDiv = ViewTimeCard::jobDiv($timeCardInfo);
      $timeDiv = ViewTimeCard::timeDiv($timeCardInfo);
      $partsDiv = ViewTimeCard::partsDiv($timeCardInfo);
      $commentsDiv = ViewTimeCard::commentsDiv($timeCardInfo);
      
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
               $commentsDiv
            </div>
         </div>
HEREDOC;
      
      return ($html);
   }
   
   public static function render()
   {
      echo (PrintTimeCard::getHtml());
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

<?php PrintTimeCard::render(); ?>

</body>

<script>
javascript:window.print()
</script>

</html>