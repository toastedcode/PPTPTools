<?php
require_once '../userInfo.php';

class SelectOperator
{
   public static function getHtml()
   {
      $html = "";
      
      $operators = SelectOperator::operators();
      
      $navBar = SelectOperator::navBar();
      
      $html =
<<<HEREDOC
      <form id="panTicketForm" action="panTicket.php" method="POST"></form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">Select Operator</div>
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
      
      $operators = UserInfo::getUsers(Permissions::OPERATOR);
      
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
      <input type="radio" form="panTicketForm" id="$id" class="operator-input" name="employeeNumber" value="$employeeNumber" $checked/>
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
      $navBar->cancelButton("submitForm('panTicketForm', 'panTicket.php', 'view_pan_tickets', 'cancel_pan_ticket')");
      $navBar->nextButton("if (validateOperator()) {submitForm('panTicketForm', 'panTicket.php', 'select_time_card', 'update_pan_ticket_info');};");
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private static function getEmployeeNumber()
   {
      $employeeNumber = null;
      
      if (isset($_SESSION['panTicketInfo']))
      {
         $employeeNumber = $_SESSION['panTicketInfo']->employeeNumber;
      }
      
      return ($employeeNumber);
   }
}
?>