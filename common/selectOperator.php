<?php

require_once 'database.php';

abstract class SelectOperator
{
   public function getHtml()
   {
      $html = "";
      
      $description = $this->description();
      
      $operators = $this->operators();
      
      $navBar = $this->navBar();
      
      $html =
<<<HEREDOC
      <form id="input-form" action="#" method="POST"></form>

      <div class="flex-vertical content">

         <div class="heading">Select a Machine Operator</div>

         $description

         <div class="flex-vertical inner-content"> 

            $operators  

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
   
   abstract protected function description();
   
   abstract protected function navBar();
   
   abstract protected function getEmployeeNumber();
   
   private function operators()
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
         $result = $database->getUsersByRole(Role::OPERATOR);
     
         // output data of each row
         while ($result && ($row = $result->fetch_assoc()))
         {
            $userInfo = UserInfo::load($row["employeeNumber"]);
            
            if ($userInfo)
            {
               $isChecked = ($selectedEmployeeNumber == $userInfo->employeeNumber);
               
               $html .= SelectOperator::operator($userInfo->employeeNumber, $userInfo->getFullName(), $isChecked);
            }
         }
      }
      
      $html .=
<<<HEREDOC
      </div>
HEREDOC;
      
      return ($html);
   }
   
   private static function operator($employeeNumber, $name, $isChecked)
   {
      $html = "";
      
      $checked = $isChecked ? "checked" : "";
      
      $id = "list-option-" . $employeeNumber;
      
      $html =
<<<HEREDOC
      <input type="radio" form="input-form" id="$id" class="invisible-radio-button" name="operator" value="$employeeNumber" $checked/>
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