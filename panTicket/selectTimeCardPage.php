<?php
require_once '../database.php';
require_once '../timecard/timeCardInfo.php';

class SelectTimeCard
{
   public static function getHtml()
   {
      $html = "";
      
      $timeCardsDiv = SelectTimeCard::TimeCardsDiv();
      
      $navBar = SelectTimeCard::navBar();
      
      $html =
<<<HEREDOC
      <form id="panTicketForm" action="panTicket.php" method="POST"></form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">Select Time Card</div>
         <div class="flex-horizontal content-div" style="flex-wrap: wrap; align-items: flex-start;">
            $timeCardsDiv
         </div>
         $navBar         
      </div>
HEREDOC;
      
      return ($html);
   }
   
   public static function render()
   {
      echo (SelectTimeCard::getHtml());
   }
   
   private static function timeCardsDiv()
   {
      $html =
<<<HEREDOC
         <div class="pan-tickets-div">
HEREDOC;
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $employeeNumber = SelectTimeCard::getEmployeeNumber();
         
         $selectedTimeCardId = SelectTimeCard::getTimeCardId();
         
         $result = $database->getIncompleteTimeCards($employeeNumber);
         
         if ($result)
         {
            while ($row = $result->fetch_assoc())
            {
               $timeCardInfo = getTimeCardInfo($row["TimeCard_ID"]);
               
               $isChecked = ($selectedTimeCardId == $timeCardInfo->timeCardId);
               
               $html .= SelectTimeCard::timeCardDiv($timeCardInfo, $isChecked);
            }
         }
      }
      
      $html .=
<<<HEREDOC
         </div>
HEREDOC;
      
      return ($html);
   }
   
   private static function timeCardDiv($timeCardInfo, $isChecked)
   {
      $name = "";
      $operator = UserInfo::load($timeCardInfo->employeeNumber);
      if ($operator)
      {
         $name = $operator->lastName;
      }

      $date = date_format(new DateTime($timeCardInfo->dateTime), "m-d-Y");
      $jobNumber = $timeCardInfo->jobNumber;
      $wcNumber = $timeCardInfo->wcNumber;
      
      $id = "list-option-" + $timeCardInfo->timeCardId;
      
      $checked = $isChecked ? "checked" : "";
      
      $html =
      <<<HEREDOC
         <input type="radio" form="panTicketForm" id="$id" class="operator-input" name="timeCardId" value="$timeCardInfo->timeCardId" $checked/>
         <label for="$id">
            <div class="flex-horizontal stretch pan-ticket-div">
               <div style="flex-grow: 1; display:flex; flex-direction:column; justify-content:space-around; align-items:flex-start;">
                  <div class="pan-ticket-name">$name</div>
                  <div>Job $jobNumber</div>
                  <div>WC $wcNumber</div>
               </div>
               <div class="flex-vertical" style="flex-grow: 1;">
                  <div class="flex-horizontal">
                     <div class="pan-ticket-count">$timeCardInfo->partsCount</div>
                     <div class="pan-ticket-count-label">&nbsp CT</div>
                  </div>
               </div>
               <div class="flex-vertical" style="flex-grow: 1; display:flex; flex-direction:column; justify-content:space-around; align-items:flex-end;">
                  <div>$date</div>
               </div>
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
      $navBar->backButton("submitForm('panTicketForm', 'panTicket.php', 'select_operator', 'update_time_card_info')");
      $navBar->nextButton("submitForm('panTicketForm', 'panTicket.php', 'enter_part_number', 'update_pan_ticket_info')");
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private static function getTimeCardId()
   {
      $timeCardId = null;
      
      if (isset($_SESSION['panTicketInfo']))
      {
         $timeCardId = $_SESSION['panTicketInfo']->timeCardId;
      }
      
      return ($timeCardId);
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