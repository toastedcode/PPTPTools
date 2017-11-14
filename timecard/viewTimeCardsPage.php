<?php

require_once '../database.php';

class PageNav
{
   public static function render($numPages, $currentPage, $maxRenderedPages)
   {
      if ($numPages > 1)
      {
         echo "<div class=\"table-nav-div\">";
         if ($currentPage > 0)
         {
            $previousPage= $currentPage - 1;
            echo "<span class=\"table-nav-span\"><a href=\"#\" onclick=\"doPageNav($previousPage)\">Previous</a></span>";
         }
         
         $firstPage = ($currentPage < $maxRenderedPages / 2) ? 0 : ($currentPage -  ($maxRenderedPages / 2));
         $lastPage = ($numPages < $maxRenderedPages) ? ($firstPage + $numPages) : ($firstPage + $maxRenderedPages);
         
         for ($i = $firstPage; $i < $lastPage; $i++)
         {
            $page = $i + 1;
            echo "<span class=\"table-nav-number-span\"><a href=\"#\" onclick=\"doPageNav($i)\">$page</a></span>";
         }
         
         if ($currentPage < ($numPages - 1))
         {
            $nextPage = $currentPage + 1;
            echo "<span class=\"table-nav-span\"><a href=\"#\" onclick=\"doPageNav($nextPage);\">Next</a></span>";
         }
         echo "</div>";
      }
   }
}

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

class TimeCardTable
{
   public static function render($filter)
   {
      echo
<<<HEREDOC
      <div class="view-time-cards-table-container">
         <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">
HEREDOC;
      
      TimeCardTable::tableHeader();
      
      TimeCardTable::tableBody($filter);
      
      echo
<<<HEREDOC
         </table>
      </div>
HEREDOC;

      $tableData = TimeCardTable::getTableData($filter);
      $numPages = ceil(mysqli_num_rows($tableData) / $filter->itemsPerPage);
      
      PageNav::render($numPages, $filter->page, 10);  // TODO $numberOfPages, $maxRenderedPages
   }
      
   private static function getTableData($filter)
   {
      $result = null;
      
      $database = new PPTPDatabase("localhost", "root", "", "pptp");
      
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
      
      $database = new PPTPDatabase("localhost", "root", "", "pptp");
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $operator = $database->getOperator($employeeNumber);
      }
      
      return ($operator);
   }
   
   private static function tableHeader()
   {
      echo
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
   }
      
   private static function tableBody($filter)
   {
      $tableData = TimeCardTable::getTableData($filter);
      
      echo "<tbody>";
      
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
            $setupTime = round($row['SetupTime'] / 60) . ":" . ($row['SetupTime'] % 60);
            $runTime = round($row['RunTime'] / 60) . ":" . ($row['RunTime'] % 60);
            
            echo
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
                  <i class="material-icons" onclick="onEdit($timeCardId)">mode_edit</i>
               </td>
               <td>
                  <i class="material-icons" onclick="onDelete($timeCardId)">delete</i>
               </td>
            </tr>
HEREDOC;
         }

         $rowIndex++;
      }
         
      echo "</tbody>";
   }
}

function getFilter()
{
   $filter = isset($_SESSION['filter']) ? $_SESSION['filter'] : new Filter();
   
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

function getOperators()
{
   $operators = null;
   
   $database = new PPTPDatabase("localhost", "root", "", "pptp");
   
   $database->connect();
   
   if ($database->isConnected())
   {
      $operators= $database->getOperators();
   }
   
   return ($operators);
}

function viewTimeCardsPage()
{
   $filter = getFilter();
   $filter->itemsPerPage = 5;

   $operators = getOperators();
   
   $selected = ($filter->employeeNumber == 0) ? "selected" : "";
   
   $options = "<option $selected value=0>All</option>";
   while ($row = $operators->fetch_assoc())
   {
      $selected = ($row["EmployeeNumber"] == $filter->employeeNumber) ? "selected" : "";
      $options .= "<option $selected value=\"" . $row["EmployeeNumber"] . "\">" . $row["FirstName"] . " " . $row["LastName"] . "</option>";
   }
   
   echo
<<<HEREDOC
      <style>
         .view-time-cards-card {
            width: 80%;
            height: 625px;
            margin: auto;
         }
         
         .view-time-cards-table-container {
            margin: auto;
            padding: 20px;
         }
         
         .filter-container {
            margin: auto;
            display: table;
         }

         .table-nav-div {
            display: table;
            margin: auto;
         }

         .table-nav-span {
            padding: 20px;
         }

         .table-nav-number-span {
            padding: 5px;
         }

         a {
            color: #1E7EC8;
            text-decoration: none; // changed from text-decoration:underline
         }

         a:hover {
            text-decoration: underline;
         }

         .nav-div {
            margin: auto;
         }

         .wide-nav-button {
            width:250px;
         }

         .inner-div {
            margin: auto;
            padding: 20px 20px 20px 20px;
            display: table;
         }

         .largeTableOnly {
            display: none;
         }

         .mdl-card__title {
           height: 50px;
           background: #f4b942;
         }

      </style>

      <script src="viewTimeCardsPage.js"></script>

      <div class="mdl-card mdl-shadow--2dp view-time-cards-card">

      <div class="mdl-card__title">
         <h6 class="mdl-card__title-text">View Time Cards</h6>
      </div>

      <div class="inner-div">

      <div class="filter-container">
      <form id="timeCardForm" action="timeCard.php" method="POST">
         <input type="hidden" name="view" value="view_time_cards"/>
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
      </form>
      </div>

HEREDOC;

   TimeCardTable::render($filter);
   
   echo
<<<HEREDOC
      </div>

      <div class="nav-div">

      <button class="mdl-button mdl-js-button mdl-button--raised pptpNavButton wide-nav-button" onclick="location.href='../pptptools.php'">
         Main Menu
      </button>

      <button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored pptpNavButton wide-nav-button" onclick="onNewTimeCard()">
         New Time Card
      </button>

      </div>
      </div>
HEREDOC;
}
?>