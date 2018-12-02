<?php

require_once '../common/selectOperator.php';

class SelectOperator_PartWasher extends SelectOperator
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
      $navBar->cancelButton("submitForm('input-form', 'partWasherLog.php', 'view_part_washer_log', 'cancel_part_washer_entry')");
      $navBar->backButton("submitForm('input-form', 'partWasherLog.php', 'select_job', 'update_part_washer_entry');");
      $navBar->nextButton("if (validateJob()){submitForm('input-form', 'partWasherLog.php', 'enter_part_count', 'update_part_washer_entry');};");
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   protected function getEmployeeNumber()
   {
      $employeeNumber = null;
      
      if (isset($_SESSION['partWasherEntry']))
      {
         $employeeNumber = $_SESSION['partWasherEntry']->operator;
      }
      
      return ($employeeNumber);
   }
}
?>