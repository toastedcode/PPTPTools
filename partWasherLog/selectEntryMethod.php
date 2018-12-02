<?php
require_once '../common/database.php';
require_once '../common/keypad.php';

class SelectEntryMethod
{
   public function getHtml()
   {
      $html =
<<<HEREDOC
      <form id="input-form" action="#" method="POST"></form>

      <div class="flex-vertical content">

         <div class="heading">Select Your Entry Method</div>

         <div class="description">Using the code from a Time Card is the quickest and most error-proof way to go.  But if you don't have a Pan Ticket handy, select Manual Entry instead.</div>

         <div class="flex-horizontal inner-content">
                 
            <button class="nav-button" onclick="submitForm('input-form', 'partWasherLog.php', 'select_time_card', 'no_action')">
               Time<br/>Card
            </button>
   
            <button class="nav-button" onclick="submitForm('input-form', 'partWasherLog.php', 'select_work_center', 'no_action')">
               Manual<br/>Entry
            </button>

         </div>

      </div>
HEREDOC;
      
      return ($html);
   }
   
   public function render()
   {
      echo (SelectEntryMethod::getHtml());
   }
}
?>