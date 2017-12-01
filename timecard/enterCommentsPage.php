<?php
require_once 'navigation.php';

class CommentsPage
{
   public static function getHtml()
   {
      $comments = CommentsPage::getComments();
      
      $navBar = CommentsPage::navBar();
      
      $html =
<<<HEREDOC
      <form id="timeCardForm" action="timeCard.php" method="POST"></form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">Add Comments</div>
         <div class="flex-horizontal content-div" style="height:400px;">
            
            <textarea form="timeCardForm" class="comments-input" type="text" name="comments" rows="10" placeholder="Enter comments ...">$comments</textarea>
      
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
   
   private static function getComments()
   {
      $comments = null;
      
      if (isset($_SESSION['timeCardInfo']))
      {
         $comments = $_SESSION['timeCardInfo']->comments;
      }
      
      return ($comments);
   }
   
   private static function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->cancelButton("submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'cancel_time_card')");
      $navBar->backButton("submitForm('timeCardForm', 'timeCard.php', 'enter_part_count', 'update_time_card_info');");
      $navBar->nextButton("submitForm('timeCardForm', 'timeCard.php', 'edit_time_card', 'update_time_card_info');");
      $navBar->end();
      
      return ($navBar->getHtml());
   }
}
?>