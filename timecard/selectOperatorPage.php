<?php
require_once '../database.php';

class SelectOperator
{
   public static function getHtml()
   {
      $html = "";
      
      $operators = SelectOperator::operators();
      
      $navBar = SelectOperator::navBar();
      
      $html =
<<<HEREDOC
      <form id="timeCardForm" action="timeCard.php" method="POST"></form>
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
   
   public static function render()
   {
      echo (SelectOperator::getHtml());
   }
   
   private static function operators()
   {
      $html = "";
      
      $selectedEmployeeNumber = SelectOperator::getEmployeeNumber();
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getOperators();
     
         // output data of each row
         while ($row = $result->fetch_assoc())
         {
            $name = $row["FirstName"] . " " . $row["LastName"];
            
            $employeeNumber = $row["EmployeeNumber"];
            
            $isChecked = ($selectedEmployeeNumber == $employeeNumber);
            
            $html .= SelectOperator::operator($employeeNumber, $name, $isChecked);
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
      <input type="radio" form="timeCardForm" id="$id" class="operator-input" name="employeeNumber" value="$employeeNumber" $checked/>
      <label for="$id">
         <div type="button" class="select-button operator-select-button">
            <i class="material-icons button-icon">person</i>
            <div>$name</div>
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
      $navBar->nextButton("if (validateOperator()) {submitForm('timeCardForm', 'timeCard.php', 'select_work_center', 'update_time_card_info');};");
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private static function getEmployeeNumber()
   {
      $employeeNumber = null;
      
      if (isset($_SESSION['timeCardInfo']))
      {
         $employeeNumber = $_SESSION['timeCardInfo']->employeeNumber;
      }
      
      return ($employeeNumber);
   }
}
?>