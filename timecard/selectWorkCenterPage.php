<?php
require_once '../database.php';

class SelectWorkCenter
{
   public static function getHtml()
   {
      $html = "";
      
      $workCenters = SelectWorkCenter::workCenters();
      
      $navBar = SelectWorkCenter::navBar();
      
      $html =
<<<HEREDOC
      <form id="timeCardForm" action="timeCard.php" method="POST"></form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">Select Work Center</div>
         <div class="flex-horizontal content-div" style="flex-wrap: wrap; align-items: flex-start;">
            $workCenters
         </div>
         
         $navBar
         
      </div>
HEREDOC;
      
      return ($html);
   }
   
   public static function render()
   {
      echo (SelectWorkCenter::getHtml());
   }
   
   private static function workCenters()
   {
      $html = "";
      
      $selectedWorkCenter = SelectWorkCenter::getWorkCenter();
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getWorkCenters();
         
         // output data of each row
         while ($row = $result->fetch_assoc())
         {
            $wcNumber = $row["WCNumber"];
            
            $isChecked = ($selectedWorkCenter == $wcNumber);
            
            $html .= SelectWorkCenter::workCenter($wcNumber, $isChecked);
         }
      }
      
      return ($html);
   }
   
   private static function workCenter($wcNumber, $isChecked)
   {
      $html = "";
      
      $checked = $isChecked ? "checked" : "";
      
      $id = "list-option-" + $wcNumber;
      
      $html =
<<<HEREDOC
         <input type="radio" form="timeCardForm" id="$id" class="operator-input" name="wcNumber" value="$wcNumber" $checked/>
         <label for="$wcNumber">
            <div type="button" class="select-button wc-select-button">
               <i class="material-icons button-icon">build</i>
               <div>$wcNumber</div>
            </div>
         </label>
HEREDOC;
      
      return ($html);
   }
   
   private static function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->cancelButton("submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'cancel_time_card')");
      $navBar->backButton("if (validateWorkCenter()){submitForm('timeCardForm', 'timeCard.php', 'select_operator', 'update_time_card_info');};");
      $navBar->nextButton("if (validateWorkCenter()) {submitForm('timeCardForm', 'timeCard.php', 'select_job', 'update_time_card_info');};");
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private static function getWorkCenter()
   {
      $wcNumber = null;
      
      if (isset($_SESSION['timeCardInfo']))
      {
         $wcNumber = $_SESSION['timeCardInfo']->wcNumber;
      }
      
      return ($wcNumber);
   }
}
?>