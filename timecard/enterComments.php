<?php

require_once '../common/navigation.php';

class CommentsPage
{
   public static function getHtml()
   {
      $timeCardInfo = CommentsPage::getTimeCardInfo();
      
      $commentsDiv = CommentsPage::commentsDiv($timeCardInfo);
      
      $commentCodesDiv = CommentsPage::commentCodesDiv($timeCardInfo);
      
      $navBar = CommentsPage::navBar();
      
      $html =
<<<HEREDOC
      <form id="input-form" action="timeCard.php" method="POST"></form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">Add Comments</div>
         <div class="flex-vertical content-div" style="height:400px;">
            
            $commentCodesDiv

            $commentsDiv
      
         </div>
   
         $navBar
    
      </div>
HEREDOC;

      return ($html);
   }
   
   public static function render()
   {
      echo (CommentsPage::getHtml());
   }
   
   public static function getCommentCodes()
   {
      $commentCodes = array();
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getCommentCodes();
         
         while ($result && ($row = $result->fetch_assoc()))
         {
            $code = new stdClass();
            $code->description = $row["description"];
            $code->code = intval($row["code"]);
            
            $commentCodes[] = $code;
         }
      }
      
      return ($commentCodes);
   }
   
   protected static function commentsDiv($timeCardInfo)
   {
      $html = 
<<< HEREDOC
         <textarea form="input-form" class="comments-input" type="text" name="comments" rows="10" maxlength="256" placeholder="Enter comments ...">$timeCardInfo->comments</textarea>
HEREDOC;

      return ($html);
   }
   
   protected static function commentCodesDiv($timeCardInfo)
   {
      $commentCodes = CommentsPage::getCommentCodes();
      
      $leftColumn = "";
      $rightColumn = "";
      $index = 0;
      foreach($commentCodes as $commentCode)
      {
         $id = "code-" . $commentCode->code . "-input";
         $name = "code-" . $commentCode->code;
         $checked = ($timeCardInfo->hasCommentCode($commentCode->code) ? "checked" : "");
         $description = $commentCode->description;
         
         $codeDiv =
<<< HEREDOC
            <div class="flex-horizontal">
               <input id="$id" type="checkbox" form="input-form" name="$name" $checked/>
               <label for="$id">$description</label>
            </div>
HEREDOC;
         
         if (($index % 2) == 0)
         {
            $leftColumn .= $codeDiv;
         }
         else
         {
            $rightColumn .= $codeDiv;
         }
         
         $index++;
      }
      
      $html =
<<<HEREDOC
         <input type="hidden" form="input-form" name="commentCodes" value="true"/>
         <div class="flex-horizontal">
            <div class="flex-vertical">
               $leftColumn
            </div>
            <div class="flex-vertical">
               $rightColumn
            </div>
         </div>
HEREDOC;
      
      return ($html);
   }
   
   private static function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->cancelButton("submitForm('input-form', 'timeCard.php', 'view_time_cards', 'cancel_time_card')");
      $navBar->backButton("submitForm('input-form', 'timeCard.php', 'enter_part_count', 'update_time_card_info');");
      $navBar->nextButton("submitForm('input-form', 'timeCard.php', 'edit_time_card', 'update_time_card_info');");
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   protected static function getTimeCardInfo()
   {
      $timeCardInfo = new TimeCardInfo();
      
      if (isset($_SESSION['timeCardInfo']))
      {
         $timeCardInfo = $_SESSION['timeCardInfo'];
      }
      
      return ($timeCardInfo);
   }  
}
?>