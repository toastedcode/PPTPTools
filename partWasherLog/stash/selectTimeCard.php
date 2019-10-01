<?php
require_once '../common/selectTimeCard.php';

class SelectTimeCard_PartWasher extends SelectTimeCard
{  
   protected function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->cancelButton("submitForm('input-form', 'partWasherLog.php', 'view_part_washer_log', 'cancel_part_washer_entry')");
      $navBar->backButton("submitForm('input-form', 'partWasherLog.php', 'select_entry_method', 'update_time_card_info')");
      $navBar->nextButton("if (validateTimeCardId()){submitForm('input-form', 'partWasherLog.php', 'enter_part_count', 'update_part_washer_entry');}");
      $navBar->end();
      
      return ($navBar->getHtml());
   }
}
?>