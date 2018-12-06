<?php

require_once '../common/selectOperator.php';

class SelectOperator_PartWeight extends SelectOperator
{
   protected function description()
   {
      $html =
      <<<HEREDOC
      <div class="description">Select the machine operator responsible for the creation of the parts being washed.  You can find the operator on the accompanying Pan Ticket.</div>
HEREDOC;
      
      return ($html);
   }
   
   protected function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->cancelButton("submitForm('input-form', 'partWeightLog.php', 'view_part_weight_log', 'cancel_part_weight_entry')");
      $navBar->backButton("submitForm('input-form', 'partWeightLog.php', 'select_job', 'update_part_weight_entry');");
      $navBar->nextButton("if (validateOperator()){submitForm('input-form', 'partWeightLog.php', 'enter_pan_count', 'update_part_weight_entry');};");
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