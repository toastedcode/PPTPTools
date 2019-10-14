<?php
require_once '../common/selectJob.php';

class SelectJob_PartWeight extends SelectJob
{
   protected function description()
   {
      $html =
      <<<HEREDOC
      <div class="description">Select one of the following active jobs.  You can find the job number for this run of parts on the accompanying Pan Ticket.</div>
HEREDOC;
      
      return ($html);
   }
   
   protected function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->cancelButton("submitForm('input-form', 'partWeightLog.php', 'view_part_washer_log', 'cancel_part_weight_entry')");
      $navBar->backButton("submitForm('input-form', 'partWeightLog.php', 'select_work_center', 'update_part_weight_entry');");
      $navBar->nextButton("if (validateJob()){submitForm('input-form', 'partWeightLog.php', 'select_operator', 'update_part_weight_entry');};");
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   protected function getJobId()
   {
      $jobId = null;
      
      if (isset($_SESSION['partWeightEntry']))
      {
         $jobId= $_SESSION['partWeightEntry']->jobId;
      }
      
      return ($jobId);
   }
   
   protected function getWorkCenterNumber()
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