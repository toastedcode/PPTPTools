<?php
require_once 'database.php';

abstract class SelectWorkCenter
{
   public function getHtml()
   {
      $html = "";
      
      $description = $this->description();
      
      $workCenters = $this->workCenters();
      
      $navBar = $this->navBar();
      
      $html =
<<<HEREDOC
      <form id="input-form" action="#" method="POST"></form>

      <div class="flex-vertical content">

         <div class="heading">Select a Work Center</div>

         $description

         <div class="flex-vertical inner-content">

            $workCenters
            
         </div>
         
         $navBar
         
      </div>
HEREDOC;
      
      return ($html);
   }
   
   public function render()
   {
      echo ($this->getHtml());
   }
   
   private function workCenters()
   {
      $html =
<<<HEREDOC
      <div class="flex-horizontal selection-container">
HEREDOC;
      
      $selectedWorkCenter = $this->getWorkCenter();
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getActiveWorkCenters();
         
         // output data of each row
         while ($result && ($row = $result->fetch_assoc()))
         {
            $wcNumber = $row["wcNumber"];
            
            $isChecked = ($selectedWorkCenter == $wcNumber);
            
            $html .= SelectWorkCenter::workCenter($wcNumber, $isChecked);
         }
      }
      
      $html .=
<<<HEREDOC
      </div>
HEREDOC;
      
      return ($html);
   }
   
   private static function workCenter($wcNumber, $isChecked)
   {
      $html = "";
      
      $checked = $isChecked ? "checked" : "";
      
      $id = "list-option-" + $wcNumber;
      
      $html =
<<<HEREDOC
         <input type="radio" form="input-form" id="$id" class="invisible-radio-button" name="wcNumber" value="$wcNumber" $checked/>
         <label for="$id">
            <div type="button" class="select-button wc-select-button">
               <i class="material-icons button-icon">build</i>
               <div>$wcNumber</div>
            </div>
         </label>
HEREDOC;
      
      return ($html);
   }
   
   abstract protected function description();
   
   abstract protected function navBar();
   
   abstract protected function getWorkCenter();
}
?>