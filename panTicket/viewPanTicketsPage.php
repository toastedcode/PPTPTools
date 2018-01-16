<?php

require_once '../database.php';
require_once '../navigation.php';

class Filter
{
   public $employeeNumber = 0;
   public $startDate;
   public $endDate;
   public $page = -1;
   public $itemsPerPage = 0;
   
   function __construct()
   {
      $this->startDate = date('Y-m-d');
      $this->endDate = date('Y-m-d');
   }
}

/*
class TimeCardTable
{
   public function __construct($filter)
   {
      $this->filter = $filter;
      $this->startDate = date('Y-m-d');
      $this->endDate = date('Y-m-d');
   }
   
   public function getHtml()
   {
      $html = "";
      
      $html .= $this->tableStart();
      
      $html .= $this->tableHeader();
      
      $html .= $this->tableBody($this->filter);
      
      $html .= $this->tableEnd();
     
      $html .= $this->pageNav($this->filter);
      
      return ($html);
   }
   
   public function render()
   {
      echo ($this->getHtml());
   }
      
   public static function getTableData($filter)
   {
      $result = null;
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getTimeCards($filter->employeeNumber, $filter->startDate, $filter->endDate);
      }
      
      return ($result);
   }
   
   private static function getOperator($employeeNumber)
   {
      $operator = null;
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $operator = $database->getOperator($employeeNumber);
      }
      
      return ($operator);
   }
   
   private function tableStart()
   {
      $html =
<<<HEREDOC
      <div>
         <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">
HEREDOC;
      
      return ($html);
   }
   
   private function tableEnd()
   {
      $html =
<<<HEREDOC
         </table>
      </div>
HEREDOC;
      
      return ($html);
   }
   
   private function tableHeader()
   {
      $html =
<<<HEREDOC
      <thead>
      <tr>
         <th class="mdl-data-table__cell--non-numeric">Date</th>
         <th class="mdl-data-table__cell--non-numeric">Name</th>
         <th class="mdl-data-table__cell--non-numeric">Employee #</th>
         <th class="mdl-data-table__cell--non-numeric">Work Center #</th>
         <th class="mdl-data-table__cell--non-numeric">Job #</th>
         <th class="mdl-data-table__cell--non-numeric largeTableOnly">Setup Time</th>
         <th class="mdl-data-table__cell--non-numeric largeTableOnly">Run Time</th>
         <th class="mdl-data-table__cell--non-numeric largeTableOnly">Pan Count</th>
         <th class="mdl-data-table__cell--non-numeric largeTableOnly">Parts Count</th>
         <th class="mdl-data-table__cell--non-numeric largeTableOnly">Scrap Count</th>
         <th class="mdl-data-table__cell--non-numeric"></th>
         <th class="mdl-data-table__cell--non-numeric"></th>
      </tr>
      </thead>
HEREDOC;
      
      return ($html);
   }
      
   private function tableBody($filter)
   {
      $html = "";
      
      $tableData = TimeCardTable::getTableData($filter);
      
      $html .= "<tbody>";
      
      // Output data of each row
      $rowIndex = 0;
      while ($row = $tableData->fetch_assoc())
      {
         if (($filter->page == -1) || 
             (floor($rowIndex/ $filter->itemsPerPage) == $filter->page))
         {
            $timeCardId = $row['TimeCard_ID'];
            
            $operator = TimeCardTable::getOperator($row['EmployeeNumber']);
            $name = $operator["FirstName"] . " " . $operator["LastName"];
            $setupTime = round($row['SetupTime'] / 60) . ":" . sprintf("%02d", ($row['SetupTime'] % 60));
            $runTime = round($row['RunTime'] / 60) . ":" . sprintf("%02d", ($row['RunTime'] % 60));
            
            $viewEditIcon = "";
            $deleteIcon = "";
            if (Authentication::getPermissions() < Permissions::ADMIN)
            {
               $viewEditIcon = 
                  "<i class=\"material-icons table-function-button\" onclick=\"onViewTimeCard($timeCardId)\">visibility</i>";
            }
            else
            {
               $viewEditIcon =
                  "<i class=\"material-icons table-function-button\" onclick=\"onEditTimeCard($timeCardId)\">mode_edit</i>";
               
               $deleteIcon = 
                  "<i class=\"material-icons table-function-button\" onclick=\"onDeleteTimeCard($timeCardId)\">delete</i>";
            }
            
            $html .=
<<<HEREDOC
            <tr>
               <td>{$row['Date']}</td>
               <td>$name</td>
               <td>{$row['EmployeeNumber']}</td>
               <td>{$row['WCNumber']}</td>
               <td>{$row['JobNumber']}</td>
               <td class="largeTableOnly">$setupTime</td>
               <td class="largeTableOnly">$runTime</td>
               <td class="largeTableOnly">{$row['PanCount']}</td>
               <td class="largeTableOnly">{$row['PartsCount']}</td>
               <td class="largeTableOnly">{$row['ScrapCount']}</td>
               <td>
                  $viewEditIcon
               </td>
               <td>
                  $deleteIcon
               </td>
            </tr>
HEREDOC;
         }

         $rowIndex++;
      }
         
      $html .= "</tbody>";
      
      return ($html);
   }
   
   private function pageNav($filter)
   {
      $html = "";
      
      $tableData = TimeCardTable::getTableData($filter);
      
      $numPages = ceil(mysqli_num_rows($tableData) / $filter->itemsPerPage);
      
      $currentPage = $this->filter->page;
      
      $maxRenderedPages = 10;  // TODO $numberOfPages, $maxRenderedPages
      
      if ($numPages > 1)
      {
         $html .= "<div class=\"table-nav-div\">";
         if ($currentPage > 0)
         {
            $previousPage= $currentPage - 1;
            $html .= "<span class=\"table-nav-span\"><a href=\"#\" onclick=\"doPageNav($previousPage)\">Previous</a></span>";
         }
         
         $firstPage = ($currentPage < $maxRenderedPages / 2) ? 0 : ($currentPage -  ($maxRenderedPages / 2));
         $lastPage = ($numPages < $maxRenderedPages) ? ($firstPage + $numPages) : ($firstPage + $maxRenderedPages);
         
         for ($i = $firstPage; $i < $lastPage; $i++)
         {
            $isCurrentPage = ($i == $currentPage) ? "table-nav-selected-page" : "";
            $page = $i + 1;
            $html .= "<span class=\"table-nav-number-span\"><a class=\"$isCurrentPage\" href=\"#\" onclick=\"doPageNav($i)\">$page</a></span>";
         }
         
         if ($currentPage < ($numPages - 1))
         {
            $nextPage = $currentPage + 1;
            $html .= "<span class=\"table-nav-span\"><a href=\"#\" onclick=\"doPageNav($nextPage);\">Next</a></span>";
         }
         $html .= "</div>";
      }
      
      return ($html);
   }
}
*/

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
   
   public static function render()
   {
      echo (ViewPanTickets::getHtml());
   }
      
   private static function filterDiv($filter)
   {
      $operators = ViewPanTickets::getOperators();
      
      $selected = ($filter->employeeNumber == 0) ? "selected" : "";
      
      $options = "<option $selected value=0>All</option>";
      while ($row = $operators->fetch_assoc())
      {
         $selected = ($row["EmployeeNumber"] == $filter->employeeNumber) ? "selected" : "";
         $options .= "<option $selected value=\"" . $row["EmployeeNumber"] . "\">" . $row["FirstName"] . " " . $row["LastName"] . "</option>";
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
         // Increment the end date by a day to make it inclusive.
         $endDate = new DateTime($filter->endDate);
         $endDate->modify('+1 day');
         $endDateString = $endDate->format('Y-m-d');
         
         $result = $database->getPanTickets($filter->employeeNumber, $filter->startDate, $endDateString);
         
         if ($result)
         {
            while ($row = $result->fetch_assoc())
            {
               $panTicketInfo = getPanTicketInfo($row["panTicketId"]);
               
               $html .= ViewPanTickets::panTicketDiv($panTicketInfo);
            }
         }
      }
      
      $html .=
<<<HEREDOC
         </div>
HEREDOC;
      
      return ($html);
   }
   
   private static function panTicketDiv($panTicketInfo)
   {
      $operator = ViewPanTickets::getOperator($panTicketInfo->employeeNumber);
      
      $dateTime = new DateTime($panTicketInfo->date);
      
      $name = $operator["LastName"];
      $date = date_format(new DateTime($panTicketInfo->date), "m-d-Y");
      $time = date_format(new DateTime($panTicketInfo->date), "H:i");
      $jobNumber = $panTicketInfo->jobNumber;
      $wcNumber = $panTicketInfo->wcNumber;
      $weight = $panTicketInfo->weight ? $panTicketInfo->weight : "";
      $weightLabel = $panTicketInfo->weight ? "LBS" : "unweighed";
      
      $viewEditIcon = "";
      $deleteIcon = "";
      if (Authentication::getPermissions() < Permissions::ADMIN)
      {
         $viewEditIcon =
         "<i class=\"material-icons table-function-button\" onclick=\"onViewPanTicket($panTicketInfo->panTicketId)\">visibility</i>";
      }
      else
      {
         $viewEditIcon =
         "<i class=\"material-icons pan-ticket-function-button\" onclick=\"onEditPanTicket($panTicketInfo->panTicketId)\">mode_edit</i>";
         
         $deleteIcon =
         "<i class=\"material-icons pan-ticket-function-button\" onclick=\"onDeletePanTicket($panTicketInfo->panTicketId)\">delete</i>";
      }
      
      $html =
<<<HEREDOC
         <div class="flex-horizontal stretch pan-ticket-div">
            <div style="flex-grow: 1; display:flex; flex-direction:column; justify-content:space-around; align-items:flex-start;">
               <div class="pan-ticket-name">$name</div>
               <div>Job $jobNumber</div>
               <div>Part $jobNumber</div>
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
   
   private static function getOperators()
   {
      $operators = null;
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $operators= $database->getOperators();
      }
      
      return ($operators);
   }
   
   private static function getOperator($employeeNumber)
   {
      $operator = null;
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $operator = $database->getOperator($employeeNumber);
      }
      
      return ($operator);
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