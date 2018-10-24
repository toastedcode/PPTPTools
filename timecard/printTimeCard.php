<?php
require_once '../common/authentication.php';
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
      $commentsDiv = PrintTimeCard::commentsDiv($timeCardInfo, true);
      $commentCodesDiv = PrintTimeCard::commentCodesDiv($timeCardInfo, true);
      $qrDiv = ViewTimeCard::qrDiv($timeCardInfo);
      
      $html =
<<<HEREDOC
         <div class="pptp-form" style="height:500px;">
            <div class="form-row">
               $titleDiv
            </div>
            <div class="form-row">
               <div class="form-col">
                  $dateDiv
                  $operatorDiv
                  $jobDiv
               </div>
               <div class="form-col">
                  $timeDiv
                  $partsDiv
               </div>
               <div class="form-col" style="justify-content:space-around;">
                  $commentCodesDiv
                  $qrDiv
               </div>
            </div>
            $commentsDiv
         </div>
HEREDOC;
      
      return ($html);
   }
   
   public static function render($readOnly)
   {
      echo (PrintTimeCard::getHtml($readOnly));
   }
   
   protected static function commentsDiv($timeCardInfo, $readOnly)
   {
      $disabled = ($readOnly) ? "disabled" : "";
      
      $html =
<<<HEREDOC
      <div class="form-col" style="align-self:center;">
         <div class="form-section-header">Comments</div>
         <div class="form-item">
            <textarea form="input-form" class="comments-input" type="text" form="input-form" name="comments" rows="2" maxlength="256" style="width:500px" $disabled>$timeCardInfo->comments</textarea>
         </div>
      </div>
HEREDOC;
      
      return ($html);
   }
   
   protected static function commentCodesDiv($timeCardInfo, $readOnly)
   {
      $disabled = ($readOnly) ? "disabled" : "";
      
      $commentCodes = CommentCode::getCommentCodes();
      
      $codes = "";

      foreach($commentCodes as $commentCode)
      {
         if ($timeCardInfo->hasCommentCode($commentCode->code))
         {
            $id = "code-" . $commentCode->code . "-input";
            $name = "code-" . $commentCode->code;
            $description = $commentCode->description;
         
            $codes .=
<<< HEREDOC
               <div class="form-item">
                  <label for="$id" class="comment-code-text">$description</label>
               </div>
HEREDOC;
         }
      }
      
      $html =
<<<HEREDOC
      <div class="form-col">
         <div class="form-section-header">Codes</div>
         $codes
      </div>
HEREDOC;
      
      return ($html);
   }
}
?>

<!-- ********************************** BEGIN ********************************************* -->

<html>
<head>
<link rel="stylesheet" type="text/css" href="flex.css"/>
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>
<link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-blue.min.css"/>
<link rel="stylesheet" type="text/css" href="../common/form.css"/>
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