<?php

class Filter
{
   public $employeeNumber = 0;
   public $startDate;
   public $endDate;
   
   function __construct()
   {
      $this->startDate = date('Y-m-d');
      $this->endDate = date('Y-m-d');
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
   
   $_SESSION['filter'] = $filter;
   
   return ($filter);
}

function viewTimeCardsPage()
{
   $filter = getFilter();
   
   $database = new PPTPDatabase("localhost", "root", "", "pptp");
   
   $database->connect();
   
   if ($database->isConnected())
   {
      $result = $database->getOperators();
      
      $selected = ($filter->employeeNumber == 0) ? "selected" : "";
      $options = "<option $selected value=0>All</option>";
      while($row = $result->fetch_assoc())
      {
         $selected = ($row["EmployeeNumber"] == $filter->employeeNumber) ? "selected" : "";
         $options .= "<option $selected value=\"" . $row["EmployeeNumber"] . "\">" . $row["FirstName"] . " " . $row["LastName"] . "</option>";
      }
      
      echo
<<<HEREDOC
      <script src="viewTimeCardsPage.js"></script>
      <form id="timeCardForm" action="timeCard.php" method="POST">
         <input type="hidden" name="view" value="view_time_cards"/>
         Employee:
         <select id="employeeNumberInput" name="employeeNumber">$options</select>
         Start Date:
         <input type="date" id="startDateInput" name="startDate" value="$filter->startDate">
         End Date:
         <input type="date" id="endDateInput" name="endDate" value="$filter->endDate">
         <button class="mdl-button mdl-js-button mdl-button--raised">Filter</button>
      </form>
HEREDOC;
      
      $result = $database->getTimeCards($filter->employeeNumber, $filter->startDate, $filter->endDate);
      
      // Table header
      echo
<<<HEREDOC
      <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">
      <tr>
         <th class="mdl-data-table__cell--non-numeric">Date</th>
         <th class="mdl-data-table__cell--non-numeric">Name</th>
         <th class="mdl-data-table__cell--non-numeric">Employee #</th>
         <th class="mdl-data-table__cell--non-numeric">Work Center #</th>
         <th class="mdl-data-table__cell--non-numeric">Job #</th>
         <th class="mdl-data-table__cell--non-numeric">Setup Time</th>
         <th class="mdl-data-table__cell--non-numeric">Run Time</th>
         <th class="mdl-data-table__cell--non-numeric">Pan Count</th>
         <th class="mdl-data-table__cell--non-numeric">Parts Count</th>
         <th class="mdl-data-table__cell--non-numeric">Scrap Count</th>
      </tr>
HEREDOC;
      
      // output data of each row
      while($row = $result->fetch_assoc())
      {
         $timeCardId = $row['TimeCard_ID'];
         
         $operator = $database->getOperator($row['EmployeeNumber']);
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
            <td>$setupTime</td>
            <td>$runTime</td>
            <td>{$row['PanCount']}</td>
            <td>{$row['PartsCount']}</td>
            <td>{$row['ScrapCount']}</td>
            <td>
               <!--button class="mdl-button mdl-js-button mdl-button--fab mdl-button--colored" onclick="onEdit($timeCardId)"-->
                  <i class="material-icons" onclick="onEdit($timeCardId)">mode_edit</i>
               </button>
            </td>
            <td>
               <i class="material-icons" onclick="onDelete($timeCardId)">delete</i>
            </td>
         </tr>
HEREDOC;
      }
      
      echo
<<<HEREDOC
      </table>
      <!--input type="button" value="Menu" onclick="location.href='../pptptools.php'"/-->
      <button class="mdl-button mdl-js-button mdl-button--fab mdl-button--colored" onclick="location.href='../pptptools.php'">
         <i class="material-icons">home</i>
      </button>
      <button class="mdl-button mdl-js-button mdl-button--fab mdl-button--colored" onclick="onNewTimeCard()">
         <i class="material-icons">add</i>
      </button>
      <!--echo "<input type=\"button\" value=\"New Timecard\" onclick=\"onNewTimeCard()\"/-->
HEREDOC;
   }
}
?>