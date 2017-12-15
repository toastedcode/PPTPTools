<?php
require_once 'navigation.php';

class CommentsPage
{
   public static function getHtml()
   {
      $comments = "Test";
      
      $html =  
<<<HEREDOC
   <div class="flex-vertical card-div">
      <div class="card-header-div">Add Comments</div>
      <div class="flex-horizontal content-div" style="height:400px;">
      
         <form id="timeCardForm" action="timeCard.php" method="POST">
         
            <textarea class="comments-input" type="text" name="comments" rows="10" placeholder="Enter comments ..." form-id="timeCardForm" value="$comments"></textarea>
     
         </form>
   
      </div>
      <?php
      Navigation::start();
      Navigation::cancelButton("submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'cancel_time_card')");
      Navigation::backButton("if (validatePartCount()){submitForm('timeCardForm', 'timeCard.php', 'enter_parts_count', 'update_time_card_info');};");
      Navigation::nextButton("submitForm('timeCardForm', 'timeCard.php', 'edit_time_card', 'update_time_card_info');");
      Navigation::end();
      ?>   
   </div>
HEREDOC;

      return ($html);
   }
   
   public static function render()
   {
      echo (CommentsPage::getHtml());
   }
   
   private static function getComments()
   {
      $comments = null;
      
      if (isset($_SESSION['timeCardInfo']))
      {
         $comments = $_SESSION['timeCardInfo']->comments;
      }
      
      return ($comments);
   }
}
?>