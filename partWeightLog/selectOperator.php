<?php

require_once '../common/selectOperator.php';

class SelectOperator_PartWeight extends SelectOperator
{
   protected function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->cancelButton("submitForm('input-form', 'partWeightLog.php', 'view_part_weight_log', 'cancel_part_weight_entry')");
      $navBar->backButton("submitForm('input-form', 'partWeightLog.php', 'select_job', 'update_part_weight_entry');");
      $navBar->nextButton("if (validateJob()){submitForm('input-form', 'partWeightLog.php', 'enter_weight', 'update_part_weight_entry');};");
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   protected function getEmployeeNumber()
   {
      $employeeNumber = null;
      
      if (isset($_SESSION['partWeightEntry']))
      {
         $employeeNumber = $_SESSION['partWeightEntry']->operator;
      }
      
      return ($employeeNumber);
   }
}
?>