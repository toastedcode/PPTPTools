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
      <form id="partWasherForm" action="#" method="POST"></form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">Select Part Washer</div>
         <div class="flex-horizontal content-div" style="flex-wrap: wrap; align-items: flex-start; align-content:center;">
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
      
      $operators = User::getUsers(Permissions::PART_WASHER);
      
      foreach ($operators as $operator)
      {
         $name = $operator->getFullName();
         
         $employeeNumber = $operator->employeeNumber;
         
         $isChecked = ($selectedEmployeeNumber == $employeeNumber);
         
         $html .= SelectOperator::operator($employeeNumber, $name, $isChecked);
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
      <input type="radio" form="partWasherForm" id="$id" class="operator-input" name="employeeNumber" value="$employeeNumber" $checked/>
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
      $navBar->cancelButton("submitForm('partWasherForm', 'partWasherLog.php', 'view_part_washer_log', 'cancel_part_washer_entry')");
      $navBar->nextButton("if (validateOperator()) {submitForm('partWasherForm', 'partWasherLog.php', 'select_pan_ticket', 'update_part_washer_entry');};");
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private static function getEmployeeNumber()
   {
      $employeeNumber = null;
      
      if (isset($_SESSION['partWasherEntry']))
      {
         $employeeNumber = $_SESSION['partWasherEntry']->employeeNumber;
      }
      
      return ($employeeNumber);
   }
}
?>