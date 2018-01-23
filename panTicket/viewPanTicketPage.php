<?php
require_once '../database.php';
require_once 'panTicketInfo.php';
require_once '../navigation.php';

class ViewPanTicket
{
   public static function getHtml($readOnly)
   {
      $html = "";
      
      $panTicketInfo = ViewPanTicket::getPanTicketInfo();
      
      // Fill in some fields from the associated Time Card.
      $timeCardInfo = getTimeCardInfo($panTicketInfo->timeCardId);
      if ($timeCardInfo)
      {
         $panTicketInfo->jobNumber = $timeCardInfo->jobNumber;
         $panTicketInfo->wcNumber = $timeCardInfo->wcNumber;
      }
      
      $titleDiv = ViewPanTicket::titleDiv();
      $dateDiv = ViewPanTicket::dateDiv($panTicketInfo, $readOnly);
      $operatorDiv = ViewPanTicket::operatorDiv($panTicketInfo);
      $jobDiv = ViewPanTicket::jobDiv($panTicketInfo, $readOnly);
      
      $navBar = ViewPanTicket::navBar($panTicketInfo, $readOnly);
      
      $html =
<<<HEREDOC
      <form id="panTicketForm" action="panTicket.php" method="POST">
         <input type="hidden" name="panTicketId" value="$panTicketInfo->panTicketId"/>
      </form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">View Time Card</div>

         <div class="flex-vertical content-div">
         <div class="flex-vertical time-card-div">
            <div class="flex-horizontal">
               $titleDiv
               $dateDiv
            </div>
            <div class="flex-horizontal" style="align-items: flex-start;">
               $operatorDiv
               $jobDiv
            </div>
         </div>
         </div>
         
         $navBar
         
      </div>

      <script>
         var partNumberValidator = new IntValidator("partNumber-input", 5, 1, 10000, false);
         var materialNumberValidator = new IntValidator("materialNumber-input", 5, 1, 10000, false);

         partNumberValidator.init();
         materialNumberValidator.init();
      </script>
HEREDOC;
      
      return ($html);
   }
   
   public static function render($readOnly)
   {
      echo (ViewPanTicket::getHtml($readOnly));
   }
   
   protected static function titleDiv()
   {
      $html =
<<<HEREDOC
      <div class="flex-horizontal time-card-table-col">
         <h1>Pan Ticket</h1>
      </div>
HEREDOC;

      return ($html);
   }
   
   protected static function dateDiv($panTicketInfo, $readOnly)
   {
      $date = date_format(new DateTime($panTicketInfo->date), "Y-m-d");
      $time = date_format(new DateTime($panTicketInfo->date), "h:i");
      
      $html =
<<<HEREDOC
      <div class="flex-vertical time-card-table-col">
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Date</h3></div>
            <input type="date" class="medium-text-input" form="panTicketForm" name="date" style="width:180px;" value="$date" disabled />
         </div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Time</h3></div>
            <input type="time" class="medium-text-input" form="panTicketForm" name="time" style="width:180px;" value="$time" disabled />
         </div>
      </div>
HEREDOC;
      return ($html);
   }
   
   protected static function operatorDiv($panTicketInfo)
   {
      $name = ViewPanTicket::getOperatorName($panTicketInfo->employeeNumber);
      
      $html = 
<<<HEREDOC
      <div class="flex-vertical time-card-table-col">
         <div class="section-header-div"><h2>Operator</h2></div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Name</h3></div>
            <input type="text" class="medium-text-input" style="width:200px;" value="$name" disabled>
         </div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Employee #</h3></div>
            <input type="text" class="medium-text-input" style="width:100px;" value="$panTicketInfo->employeeNumber" disabled>
         </div>
      </div>
HEREDOC;
         
      return ($html);
   }
   
   protected static function jobDiv($panTicketInfo, $readOnly)
   {
      $disabled = ($readOnly) ? "disabled" : "";
      
      $html =
<<<HEREDOC
      <div class="flex-vertical time-card-table-col">
         <div class="section-header-div"><h2>Job</h2></div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Job #</h3></div>
            <input type="number" class="medium-text-input" style="width:150px;" oninput="jobValidator.validate()" value="$panTicketInfo->jobNumber" disabled />
         </div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Work center #</h3></div>
            <input type="text" class="medium-text-input" style="width:150px;" value="$panTicketInfo->wcNumber" disabled />
         </div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Part #</h3></div>
            <input id="partNumber-input" type="text" class="medium-text-input" form="panTicketForm" style="width:150px;" name="partNumber" value="$panTicketInfo->partNumber" $disabled />
         </div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Heat #</h3></div>
            <input id="materialNumber-input" type="text" class="medium-text-input" form="panTicketForm" style="width:150px;" name="materialNumber" value="$panTicketInfo->materialNumber" $disabled />
         </div>
      </div>
HEREDOC;
         
      return ($html);
   }
   
   protected static function navBar($panTicketInfo, $readOnly)
   {
      $navBar = new Navigation();
      
      $navBar->start();
      
      if ($panTicketInfo->panTicketId == 0)
      {
         // Case 1
         // Viewing as last step of creating a new time card.
         
         $navBar->cancelButton("submitForm('panTicketForm', 'panTicket.php', 'view_pan_tickets', 'cancel_pan_ticket')");
         $navBar->backButton("submitForm('panTicketForm', 'panTicket.php', 'enter_material_number', 'update_pan_ticket_info');");
         $navBar->highlightNavButton("Save", "if (validatePanTicket()){submitForm('panTicketForm', 'panTicket.php', 'view_pan_tickets', 'save_pan_ticket');};", false);
      }
      else if ($readOnly == true)
      {
         // Case 2
         // Viewing single time card selected from table of time cards.
         $navBar->printButton("onPrintPanTicket($panTicketInfo->panTicketId)");
         $navBar->highlightNavButton("Ok", "submitForm('panTicketForm', 'panTicket.php', 'view_pan_tickets', 'no_action')", false);
      }
      else 
      {   
         // Case 3
         // Editing a single time card selected from table of time cards.
         $navBar->cancelButton("submitForm('panTicketForm', 'panTicket.php', 'view_pan_tickets', 'cancel_time_card')");
         $navBar->printButton("onPrintPanTicket($panTicketInfo->panTicketId)");
         $navBar->highlightNavButton("Save", "if (validatePanTicket()){submitForm('panTicketForm', 'panTicket.php', 'view_pan_tickets', 'save_pan_ticket');};", false);
      }
      
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   protected static function getPanTicketInfo()
   {
      $panTicketInfo = new PanTicketInfo();
      
      if (isset($_POST['panTicketId']))
      {
         $panTicketInfo = getPanTicketInfo($_POST['panTicketId']);
      }
      else if (isset($_SESSION['panTicketInfo']))
      {
         $panTicketInfo = $_SESSION['panTicketInfo'];
      }
      
      return ($panTicketInfo);
   }
   
   protected static function getOperatorName($employeeNumber)
   {
      $name = "";
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         if ($operator = $database->getOperator($employeeNumber))
         {
            $name = $operator["FirstName"] . " " . $operator["LastName"];
         }
      }
      
      return ($name);
   }
}
?>