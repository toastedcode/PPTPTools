<?php

require_once 'database.php';

abstract class SelectOperator
{
   public function getHtml()
   {
      $html = "";
      
      $operators = $this->operators();
      
      $navBar = $this->navBar();
      
      $html =
<<<HEREDOC
      <form id="input-form" action="#" method="POST"></form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">Select Operator</div>
         <div class="flex-horizontal content-div" style="flex-wrap: wrap; align-items: flex-start;">
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
   
   abstract protected function navBar();
   
   abstract protected function getEmployeeNumber();
   
   private function operators()
   {
      $html = "";
      
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
      
      return ($html);
   }
   
   private static function operator($employeeNumber, $name, $isChecked)
   {
      $html = "";
      
      $checked = $isChecked ? "checked" : "";
      
      $id = "list-option-" + $employeeNumber;
      
      $html =
<<<HEREDOC
      <input type="radio" form="input-form" id="$id" class="operator-input" name="operator" value="$employeeNumber" $checked/>
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