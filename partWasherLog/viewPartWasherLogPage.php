<?php

require_once '../database.php';
require_once '../navigation.php';
require_once '../panTicket/panTicketInfo.php';

class Filter
{
   public $employeeNumber = 0;
   public $startDate;
   public $endDate;
   
   function __construct()
   {
      $this->startDate = Time::now("Y-m-d");
      $this->endDate = Time::now("Y-m-d");
   }
}

class ViewPartWasherLog
{

   public static function getHtml()
   {
      $filter = ViewPartWasherLog::getFilter();
      
      $filterDiv = ViewPartWasherLog::filterDiv($filter);
      
      $partWasherLogDiv = ViewPartWasherLog::partWasherLogDiv($filter);
      
      $navBar = ViewPartWasherLog::navBar();
      
      $html = 
<<<HEREDOC
      <script src="partWasherLog.js"></script>
   
      <div class="flex-vertical card-div">
         <div class="card-header-div">View Part Washer Log</div>
         <div class="flex-vertical content-div" style="justify-content: flex-start; height:400px;">
   
               $filterDiv
   
               $partWasherLogDiv
         
         </div>

         $navBar;

      </div>
HEREDOC;
      
      return ($html);
   }
   
   public static function render()
   {
      echo (ViewPartWasherLog::getHtml());
   }
      
   private static function filterDiv($filter)
   {
      $operators = User::getUsers(Permissions::PART_WASHER);
      
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
      <form action="#" method="POST">
         <input type="hidden" name="view" value="view_part_washer_log"/>
         Washer:
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
      $navBar->highlightNavButton("New Log Entry", "onNewPartWasherEntry()", true);
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private static function partWasherLogDiv($filter)
   {
      $html = 
<<<HEREDOC
         <div class="part-washer-log-div">
            <table class="part-washer-log-table">
               <tr>
                  <th>Washer Name</th>
                  <th>Wash Date</th>
                  <th>Operator Name</th>
                  <th>Mfg. Date</th>
                  <th>Work Center #</th>
                  <th>Part #</th>
                  <th>Basket Count</th>
                  <th>Part Count</th>
                  <th></th>
               </tr>
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
         
         $result = $database->getPartWasherEntries($filter->employeeNumber, $startDateString, $endDateString);
        
         if ($result)
         {
            while ($row = $result->fetch_assoc())
            {
               $partWasherEntry = getPartWasherEntry($row["partWasherEntryId"]);
               
               if ($partWasherEntry)
               {
                  $panTicketInfo = getPanTicketInfo($partWasherEntry->panTicketId);
                  
                  if ($panTicketInfo)
                  {
                     $partWasherName = "unknown";
                     $operator = User::getUser($partWasherEntry->employeeNumber);
                     if ($operator)
                     {
                        $partWasherName= $operator->getFullName();
                     }
                     
                     $operatorName = "unknown";
                     $operator = User::getUser($panTicketInfo->employeeNumber);
                     if ($operator)
                     {
                        $operatorName = $operator->getFullName();
                     }
                     
                     $dateTime = new DateTime($partWasherEntry->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
                     $washDate = $dateTime->format("m-d-Y");
                     
                     $dateTime = new DateTime($panTicketInfo->date, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
                     $mfgDate = $dateTime->format("m-d-Y");
                     
                     $deleteIcon = "";
                     if (Authentication::getPermissions() & (Permissions::ADMIN | Permissions::SUPER_USER))
                     {
                        $deleteIcon =
                        "<i class=\"material-icons table-function-button\" onclick=\"onDeletePartWasherEntry($partWasherEntry->partWasherEntryId)\">delete</i>";
                     }
   
                     $html .=
<<<HEREDOC
                        <tr>
                           <td>$partWasherName</td>
                           <td>$washDate</td>
                           <td>$operatorName</td>
                           <td>$mfgDate</td>
                           <td>$panTicketInfo->wcNumber</td>
                           <td>$panTicketInfo->partNumber</td>
                           <td>$partWasherEntry->panCount</td>
                           <td>$partWasherEntry->partCount</td>
                           <td>$deleteIcon</td>
                        </tr>
HEREDOC;
                  }
               }
            }
         }
      }
      
      $html .=
<<<HEREDOC
            </table>
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