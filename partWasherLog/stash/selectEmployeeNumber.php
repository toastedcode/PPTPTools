<?php

class SelectEmployeeNumber
{
   public function getHtml()
   {
      $html = "";
      
      $description = $this->description();
      
      $washers = $this->washers();
      
      $navBar = $this->navBar();
      
      $html =
<<<HEREDOC
      <form id="input-form" action="#" method="POST"></form>
      
      <div class="flex-vertical content">
      
         <div class="heading">Select a Parts Washer</div>
         
         $description
         
         <div class="flex-vertical inner-content">
         
            $washers
            
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
   
   protected function description()
   {
      $html =
<<<HEREDOC
      <div class="description">Select the parts washer responsible for this log entry.</div>
HEREDOC;
      
      return ($html);
   }
   
   protected function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->cancelButton("submitForm('input-form', 'partWasherLog.php', 'view_part_washer_log', 'cancel_part_washer_entry')");
      $navBar->nextButton("if (validateEmployeeNumber()){submitForm('input-form', 'partWasherLog.php', 'select_entry_method', 'update_part_washer_entry');};");
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   protected function getEmployeeNumber()
   {
      $employeeNumber = null;
      
      if (isset($_SESSION['partWasherEntry']))
      {
         $employeeNumber = $_SESSION['partWasherEntry']->employeeNumber;
      }
      
      return ($employeeNumber);
   }
   
   private function washers()
   {
      $html =
<<<HEREDOC
      <div class="flex-horizontal selection-container">
HEREDOC;
      
      $selectedEmployeeNumber = $this->getEmployeeNumber();
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getUsersByRole(Role::PART_WASHER);
         
         // output data of each row
         while ($result && ($row = $result->fetch_assoc()))
         {
            $userInfo = UserInfo::load($row["employeeNumber"]);
            
            if ($userInfo)
            {
               $isChecked = ($selectedEmployeeNumber == $userInfo->employeeNumber);
               
               $html .= SelectEmployeeNumber::washer($userInfo->employeeNumber, $userInfo->getFullName(), $isChecked);
            }
         }
      }
      
      $html .=
<<<HEREDOC
      </div>
HEREDOC;
      
      return ($html);
   }
   
   private static function washer($employeeNumber, $name, $isChecked)
   {
      $html = "";
      
      $checked = $isChecked ? "checked" : "";
      
      $id = "list-option-" + $employeeNumber;
      
      $html =
<<<HEREDOC
      <input type="radio" form="input-form" id="$id" class="invisible-radio-button" name="employeeNumber" value="$employeeNumber" $checked/>
      <label for="$id">
         <div type="button" class="select-button operator-select-button">
            <i class="material-icons button-icon">person</i>
            <div>$name</div>
         </div>
      </label>
HEREDOC;
      
      return ($html);
   }
}
?>