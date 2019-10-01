<?php
require_once '../common/selectTimeCard.php';

class SelectTimeCard_PartWeight extends SelectTimeCard
{
   protected function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->cancelButton("submitForm('input-form', 'partWeightLog.php', 'view_part_weight_log', 'cancel_part_weight_entry')");
      $navBar->backButton("submitForm('input-form', 'partWeightLog.php', 'select_operator', 'update_part_weight_entry')");
      $navBar->nextButton("if (validateTimeCardId()){submitForm('input-form', 'partWeightLog.php', 'enter_weight', 'update_part_weight_entry');}");
      $navBar->end();
      
      return ($navBar->getHtml());
   }
}
?>