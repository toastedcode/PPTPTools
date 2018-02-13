<?php

require_once '../database.php';
require_once '../navigation.php';

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
      $operators = ViewPartWasherLog::getOperators();
      
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
      <form action="#" method="POST">
         <input type="hidden" name="view" value="view_part_washer_log"/>
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
      $navBar->highlightNavButton("New Log Entry", "onNewLogEntry()", true);
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
                  <th>Basket Count</th>
                  <th>Part Count</th>
               </tr>
HEREDOC;
      
      /*
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
         
         $result = $database->getPartInspections($filter->employeeNumber, $startDateString, $endDateString);
         
         if ($result)
         {
            while ($row = $result->fetch_assoc())
            {
               $partInspectionInfo = getPartInspectionInfo($row["partInspectionId"]);
               
               if ($partInspectionInfo)
               {
                  $operatorName = "unknown";
                  $operator = ViewPartInspections::getOperator($partInspectionInfo->employeeNumber);
                  if ($operator)
                  {
                     $operatorName = $operator['FirstName'] . " " . $operator['LastName'];
                  }
                  
                  $dateTime = new DateTime($partInspectionInfo->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
                  $date = $dateTime->format("m-d-Y");
                  $time = $dateTime->format("h:i a");
                  
                  $inspectionClass = ($partInspectionInfo->failures > 0) ? "failed-inspection" : "good-inspection"; 
                  
                  $workCenter = ($partInspectionInfo->wcNumber == 0) ? "unknown" : $partInspectionInfo->wcNumber;

                  $html .=
<<<HEREDOC
                     <tr class="$inspectionClass">
                        <td>$operatorName</td>
                        <td>$date</td>
                        <td>$time</td>
                        <td>$workCenter</td>
                        <td>$partInspectionInfo->partNumber</td>
                        <td>$partInspectionInfo->partCount</td>
                        <td>$partInspectionInfo->failures</td>
                        <td>$partInspectionInfo->efficiency</td>
                     </tr>
HEREDOC;
               }
            }
         }
      }
      */
      
      $html .=
<<<HEREDOC
            </table>
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