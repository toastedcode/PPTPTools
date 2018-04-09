<?php

require_once '../common/authentication.php';
require_once '../common/database.php';
require_once '../common/navigation.php';

class Filter
{
   public $employeeNumber = 0;
   public $startDate;
   public $endDate;
   public $page = -1;
   public $itemsPerPage = 0;
   
   function __construct()
   {
      $this->startDate = Time::now("Y-m-d");
      $this->endDate = Time::now("Y-m-d");
   }
}

class ViewPanTickets
{

   public static function getHtml()
   {
      $filter = ViewPanTickets::getFilter();
      $filter->itemsPerPage = 5;
      
      $filterDiv = ViewPanTickets::filterDiv($filter);
      
      $panTicketsDiv = ViewPanTickets::panTicketsDiv($filter);
      
      $navBar = ViewPanTickets::navBar();
      
      $html = 
<<<HEREDOC
      <script src="viewPanTicketsPage.js"></script>
   
      <div class="flex-vertical card-div">
         <div class="card-header-div">View Pan Tickets</div>
         <div class="flex-vertical content-div" style="justify-content: flex-start; height:400px;">
   
               $filterDiv
   
               $panTicketsDiv
         
         </div>

         $navBar;

      </div>
HEREDOC;
      
      return ($html);
   }
   
   public static function getPanTicketDiv($panTicketId, $isEditable)
   {
      $html = "";

      $panTicketInfo = getPanTicketInfo($panTicketId);

      if ($panTicketInfo)
      {
         $html = ViewPanTickets::panTicketDiv($panTicketInfo, $isEditable);
      }
      
      return ($html);
   }
   
   public static function render()
   {
      echo (ViewPanTickets::getHtml());
   }
      
   private static function filterDiv($filter)
   {
      $operators = User::getUsers(Permissions::OPERATOR);
      
      $selected = ($filter->employeeNumber == 0) ? "selected" : "";
      
      $options = "<option $selected value=0>All</option>";
      
      foreach ($operators as $operator)
      {
         $selected = ($operator->employeeNumber == $filter->employeeNumber) ? "selected" : "";
         $options .= "<option $selected value=\"" . $operator->employeeNumber . "\">" . $operator->getFullName() . "</option>";
      }
      
      $html = 
<<<HEREDOC
      <div>
      <form id="panTicketForm" action="panTicket.php" method="POST">
         <input type="hidden" name="view" value="view_pan_tickets"/>
         Employee:
         <select id="employeeNumberInput" name="employeeNumber">$options</select>
         &nbsp
         Start Date:
         <input type="date" id="startDateInput" name="startDate" value="$filter->startDate">
         &nbsp
         End Date:
         <input type="date" id="endDateInput" name="endDate" value="$filter->endDate">
         &nbsp
         <button class="mdl-button mdl-js-button mdl-button--raised">Filter</button>
         &nbsp | &nbsp 
         <button class="mdl-button mdl-js-button mdl-button--raised" onclick="filterToday()">Today</button>
         &nbsp
         <button class="mdl-button mdl-js-button mdl-button--raised" onclick="filterYesterday()">Yesterday</button>
         &nbsp
         <button class="mdl-button mdl-js-button mdl-button--raised" onclick="filterThisWeek()">This Week</button>
      </form>
      </div>
HEREDOC;
      
      return ($html);
   }
   
   private static function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->mainMenuButton();
      $navBar->highlightNavButton("New Pan Ticket", "onNewPanTicket()", true);
      $navBar->highlightNavButton("Enter Weight", "onEnterWeight()", true);
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private static function panTicketsDiv($filter)
   {
      $html = 
<<<HEREDOC
         <div class="pan-tickets-div">
HEREDOC;
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         // Start date.
         $startDate = new DateTime($filter->startDate, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
         $startDateString = $startDate->format("Y-m-d");
         
         // End date.
         // Increment the end date by a day to make it inclusive.
         $endDate = new DateTime($filter->endDate, new DateTimeZone('America/New_York'));
         $endDate->modify('+1 day');
         $endDateString = $endDate->format("Y-m-d");
         
         $result = $database->getPanTickets($filter->employeeNumber, $startDateString, $endDateString);
         
         if ($result)
         {
            while ($row = $result->fetch_assoc())
            {
               $panTicketInfo = getPanTicketInfo($row["panTicketId"]);
               
               if ($panTicketInfo)
               {
                  $html .= ViewPanTickets::panTicketDiv($panTicketInfo, true);  // isEditable = true
               }
            }
         }
      }
      
      $html .=
<<<HEREDOC
         </div>
HEREDOC;
      
      return ($html);
   }
   
   private static function panTicketDiv($panTicketInfo, $isEditable)
   {
      $operator = User::getUser($panTicketInfo->employeeNumber);
      
      $dateTime = new DateTime($panTicketInfo->date);
      
      $name = $operator->lastName;
      $date = date_format(new DateTime($panTicketInfo->date), "m-d-Y");
      $time = date_format(new DateTime($panTicketInfo->date), "H:i A");
      $jobNumber = $panTicketInfo->jobNumber;
      $partNumber = $panTicketInfo->partNumber;
      $weightLabel = $panTicketInfo->weight ? "LBS" : "unweighed";
      
      $weight = "";
      if ($panTicketInfo->weight)
      {
         $weight = $panTicketInfo->weight;
         
         // Display weight as integer if no decimal.
         if (floatval($weight) == round($weight, 2))
         {
            $weight = round($weight, 2);
         }
         else
         {
            
         }
      }
      
      $viewEditIcon = "";
      $deleteIcon = "";
      if ($isEditable)
      {
         if (Authentication::getPermissions() & (Permissions::ADMIN | Permissions::SUPER_USER))
         {
            $viewEditIcon =
            "<i class=\"material-icons pan-ticket-function-button\" onclick=\"onEditPanTicket($panTicketInfo->panTicketId)\">mode_edit</i>";
            
            $deleteIcon =
            "<i class=\"material-icons pan-ticket-function-button\" onclick=\"onDeletePanTicket($panTicketInfo->panTicketId)\">delete</i>";
         }
         else
         {
            $viewEditIcon =
            "<i class=\"material-icons table-function-button\" onclick=\"onViewPanTicket($panTicketInfo->panTicketId)\">visibility</i>";
         }
      }
      
      $html =
<<<HEREDOC
         <div class="flex-horizontal stretch pan-ticket-div">
            <div style="flex-grow: 1; display:flex; flex-direction:column; justify-content:space-around; align-items:flex-start;">
               <div class="pan-ticket-name">$name</div>
               <div>Job $jobNumber</div>
               <div>Part $partNumber</div>
            </div>
            <div class="flex-vertical" style="flex-grow: 1;">
               <div class="flex-horizontal">
                  <div class="pan-ticket-count">$panTicketInfo->partsCount</div>
                  <div class="pan-ticket-count-label">&nbsp CT</div>
               </div>
               <div class="count-weight-divider"></div>
               <div class="flex-horizontal">
                  <div class="pan-ticket-weight">$weight</div>
                  <div class="pan-ticket-weight-label">&nbsp $weightLabel</div>
               </div>
            </div>
            <div class="flex-vertical" style="flex-grow: 1; display:flex; flex-direction:column; justify-content:space-around; align-items:flex-end;">
               <div>$date</div>
               <div>$time</div>
               <div class="flex-horizontal">
                  $viewEditIcon
                  $deleteIcon
               </div>
            </div>
         </div>
HEREDOC;
      
      return ($html);
   }
   
   private static function getFilter()
   {
      $filter = null;
      
      if (isset($_SESSION['filter']))
      {
         $filter = $_SESSION['filter'];
      }
      else 
      {
         $filter = new Filter();
         $filter->startDate = date('Y-m-d', strtotime(' -1 day'));
      }
      
      if (isset($_POST['startDate']))
      {
         $filter->startDate = $_POST['startDate'];
      }
      
      if (isset($_POST['endDate']))
      {
         $filter->endDate = $_POST['endDate'];
      }
      
      if (isset($_POST["employeeNumber"]))
      {
         $filter->employeeNumber = $_POST['employeeNumber'];
      }
      
      if (isset($_POST["page"]))
      {
         $filter->page = $_POST["page"];
      }
      else
      {
         $filter->page = 0;
      }
      
      $_SESSION['filter'] = $filter;
      
      return ($filter);
   }
}
?>