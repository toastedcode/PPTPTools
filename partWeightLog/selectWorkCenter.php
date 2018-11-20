<?php
require_once '../common/selectWorkCenter.php';

class SelectWorkCenter_PartWeight extends SelectWorkCenter
{
   protected function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->cancelButton("submitForm('input-form', 'partWeightLog.php', 'view_part_weight_log', 'cancel_part_weight_entry')");
      $navBar->backButton("submitForm('input-form', 'partWeightLog.php', 'select_entry_method', 'update_part_washer_entry');");
      $navBar->nextButton("if (validateWorkCenter()){submitForm('input-form', 'partWeightLog.php', 'select_job', 'update_part_washer_entry');};");
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   protected function getWorkCenter()
   {
      $wcNumber = null;
      
      if (isset($_SESSION['wcNumber']))
      {
         $wcNumber = $_SESSION['wcNumber'];
      }
      
      return ($wcNumber);
   }
}
?>