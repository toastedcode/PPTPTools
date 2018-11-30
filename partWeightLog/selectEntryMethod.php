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
      <div class="flex-vertical card-div">
         <div class="card-header-div">Select Entry Method</div>
         <div class="flex-horizontal content-div">
                    
            <button class="nav-button" onclick="submitForm('input-form', 'partWeightLog.php', 'select_time_card', 'no_action')">
               Time Card
            </button>
   
            <button class="nav-button" onclick="submitForm('input-form', 'partWeightLog.php', 'select_work_center', 'no_action')">
               Manual Entry
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