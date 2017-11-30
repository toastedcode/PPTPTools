<?php

require_once '../database.php';

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
            $setupTime = round($row['SetupTime'] / 60) . ":" . ($row['SetupTime'] % 60);
            $runTime = round($row['RunTime'] / 60) . ":" . ($row['RunTime'] % 60);
            
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
            $page = $i + 1;
            $html .= "<span class=\"table-nav-number-span\"><a href=\"#\" onclick=\"doPageNav($i)\">$page</a></span>";
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

class ViewTimeCards
{

   public static function getHtml()
   {
      $filter = ViewTimeCards::getFilter();
      $filter->itemsPerPage = 5;
      
      $filterDiv = ViewTimeCards::filterDiv($filter);
      
      $table = new TimeCardTable($filter);
      $timeCardsTable = $table->getHtml();
      
      $navBar = ViewTimeCards::navBar();
      
      $html = 
<<<HEREDOC
      <script src="viewTimeCardsPage.js"></script>
   
      <div class="flex-vertical card-div">
         <div class="card-header-div">View Time Cards</div>
         <div class="flex-vertical content-div" style="justify-content: flex-start; height:400px;">
   
               $filterDiv
   
               $timeCardsTable
         
         </div>

         $navBar;

      </div>
HEREDOC;
      
      return ($html);
   }
   
   public static function render()
   {
      echo (ViewTimeCards::getHtml());
   }
      
   private static function filterDiv($filter)
   {
      $operators = ViewTimeCards::getOperators();
      
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
      
      return ($html);
   }
   
   private static function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->mainMenuButton();
      $navBar->highlightNavButton("New Time Card", "onNewTimeCard()", true);
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private static function getOperators()
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
   
   private static function getFilter()
   {
      $filter = isset($_SESSION['filter']) ? $_SESSION['filter'] : new Filter();
      
      if (isset($_POST['startDate']))
      {
         $filter->startDate = $_POST['startDate'];
      }
      else
      {
         $filter->startDate = date('Y-m-d', strtotime(' -1 day'));
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