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
         var jobValidator = new IntValidator("jobNumber-input", 5, 1, 10000, false);
         var setupTimeHourValidator = new IntValidator("setupTimeHour-input", 2, 0, 10, false);
         var setupTimeMinuteValidator = new IntValidator("setupTimeMinute-input", 2, 0, 59, false);
         var runTimeHourValidator = new IntValidator("runTimeHour-input", 2, 0, 10, false);
         var runTimeMinuteValidator = new IntValidator("runTimeMinute-input", 2, 0, 59, false);
         var panCountValidator = new IntValidator("panCount-input", 1, 1, 4, false);
         var partsCountValidator = new IntValidator("partsCount-input", 6, 0, 100000, true);
         var scrapCountValidator = new IntValidator("scrapCount-input", 6, 0, 100000, true);

         jobValidator.init();
         setupTimeHourValidator.init();
         setupTimeMinuteValidator.init();
         runTimeHourValidator.init();
         runTimeMinuteValidator.init();
         panCountValidator.init();
         partsCountValidator.init();
         scrapCountValidator.init();
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
      $disabled = ($readOnly) ? "disabled" : "";
      
      $html =
<<<HEREDOC
      <div class="flex-vertical time-card-table-col">
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Date</h3></div>
            <input type="date" class="medium-text-input" form="panTicketForm" name="date" style="width:180px;" value="$panTicketInfo->date" $disabled />
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
            <input id="jobNumber-input" type="number" class="medium-text-input" form="panTicketForm" name="jobNumber" style="width:150px;" oninput="jobValidator.validate()" value="$panTicketInfo->jobNumber" disabled />
         </div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Work center #</h3></div>
            <input type="text" class="medium-text-input" style="width:150px;" value="$panTicketInfo->wcNumber" disabled />
         </div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Part #</h3></div>
            <input type="text" class="medium-text-input" style="width:150px;" value="$panTicketInfo->partNumber" $disabled />
         </div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Heat #</h3></div>
            <input type="text" class="medium-text-input" style="width:150px;" value="$panTicketInfo->materialNumber" $disabled />
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
         
         $navBar->cancelButton("submitForm('panTicketForm', 'panTicket.php', 'view_pan_tickets', 'cancel_time_card')");
         $navBar->backButton("submitForm('panTicketForm', 'panTicket.php', 'enter_comments', 'update_pan_ticket_info');");
         $navBar->highlightNavButton("Save", "if (validateCard()){submitForm('panTicketForm', 'panTicket.php', 'view_pan_tickets', 'save_time_card');};", false);
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
         $navBar->highlightNavButton("Save", "if (validateCard()){submitForm('panTicketForm', 'panTicket.php', 'view_pan_tickets', 'save_pan_ticket');};", false);
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