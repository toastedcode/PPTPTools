<?php

require_once '../database.php';
require_once '../user.php';
require_once '../common/filter.php';
require_once '../common/jobInfo.php';

/*
class TimeCardTable
{
   public function __construct($filter)
   {
      $this->filter = $filter;
      $this->startDate = Time::now('Y-m-d');
      $this->endDate = Time::now('Y-m-d');
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
         // Start date.
         $startDate = new DateTime($filter->startDate, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
         $startDateString = $startDate->format("Y-m-d");
         
         // End date.
         // Increment the end date by a day to make it inclusive.
         $endDate = new DateTime($filter->endDate, new DateTimeZone('America/New_York'));
         $endDate->modify('+1 day');
         $endDateString = $endDate->format("Y-m-d");
         
         $result = $database->getTimeCards($filter->employeeNumber, $startDateString, $endDateString);
      }
      
      return ($result);
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
            
            $date = Time::fromMySqlDate($row['Date'], "m-d-Y");
            
            $name = "";
            $operator = User::getUser($row['EmployeeNumber']);
            if ($operator)
            {
               $name = $operator->getFullName();
            }

            $setupTime = round($row['SetupTime'] / 60) . ":" . sprintf("%02d", ($row['SetupTime'] % 60));
            $runTime = round($row['RunTime'] / 60) . ":" . sprintf("%02d", ($row['RunTime'] % 60));
            
            $viewEditIcon = "";
            $deleteIcon = "";
            if (Authentication::getPermissions() & (Permissions::ADMIN | Permissions::SUPER_USER))
            {
               $viewEditIcon =
               "<i class=\"material-icons table-function-button\" onclick=\"onEditTimeCard($timeCardId)\">mode_edit</i>";
               
               $deleteIcon =
               "<i class=\"material-icons table-function-button\" onclick=\"onDeleteTimeCard($timeCardId)\">delete</i>";
            }
            else
            {
               $viewEditIcon = 
                  "<i class=\"material-icons table-function-button\" onclick=\"onViewTimeCard($timeCardId)\">visibility</i>";
            }
            
            $html .=
<<<HEREDOC
            <tr>
               <td>$date</td>
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

class ViewTimeCards
{
   private $filter;
   
   public function __construct()
   {
      $this->filter = new Filter();
      
      $operators = User::getUsers(Permissions::OPERATOR);
      
      $this->filter->addByName('date', new DateFilterComponent());
      $this->filter->addByName("operator", new UserFilterComponent("Operator", $operators, "All"));
      $this->filter->add(new FilterButton());
      $this->filter->add(new FilterDivider());
      $this->filter->add(new TodayButton());
      $this->filter->add(new YesterdayButton());
      $this->filter->add(new ThisWeekButton());
      
      $this->filter->load();
   }
   
   public function getHtml()
   {
      $filterDiv = ViewTimeCards::filterDiv();
      
      $timeCardsDiv = ViewTimeCards::timeCardsDiv();
      
      $navBar = ViewTimeCards::navBar();
      
      $html =
      <<<HEREDOC
      <script src="jobs.js"></script>
      
      <div class="flex-vertical card-div">
         <div class="card-header-div">View Time Cards</div>
         <div class="flex-vertical content-div" style="justify-content: flex-start; height:400px;">
         
               $filterDiv
               
               $timeCardsDiv
               
         </div>
         
         $navBar;
         
      </div>
HEREDOC;
      
      return ($html);
   }
   
   public function render()
   {
      echo (ViewTimeCards::getHtml());
   }
   
   private function filterDiv()
   {
      return ($this->filter->getHtml());
   }
   
   private function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->mainMenuButton();
      $navBar->highlightNavButton("New Time Card", "onNewTimeCard()", true);
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   
   private function timeCardsDiv()
   {
      $html =
      <<<HEREDOC
         <div class="time-cards-div">
            <table class="time-card-table">
               <tr>
                  <th>Date</th>
                  <th>Operator</th>
                  <th>Job #</th>
                  <th>Work Center #</th>
                  <th>Setup Time</th>
                  <th>Run Time</th>
                  <th>Pan Count</th>
                  <th>Part Count</th>
                  <th>Scrap Count</th>
                  <th/>
                  <th/>
               </tr>
HEREDOC;
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         // Start date.
         $startDate = new DateTime($this->filter->get('date')->startDate, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
         $startDateString = $startDate->format("Y-m-d");
         
         // End date.
         // Increment the end date by a day to make it inclusive.
         $endDate = new DateTime($this->filter->get('date')->endDate, new DateTimeZone('America/New_York'));
         $endDate->modify('+1 day');
         $endDateString = $endDate->format("Y-m-d");
         
         $employeeNumber = $this->filter->get('operator')->selectedEmployeeNumber;
         
         $result = $database->getTimeCards($employeeNumber, $startDateString, $endDateString);
         
         if ($result)
         {
            while ($row = $result->fetch_assoc())
            {
               $timeCardInfo = TimeCardInfo::load($row["timeCardId"]);
               
               if ($timeCardInfo)
               {
                  $operatorName = "unknown";
                  $user = User::getUser($timeCardInfo->employeeNumber);
                  if ($user)
                  {
                     $operatorName= $user->getFullName();
                  }
                  
                  $dateTime = new DateTime($timeCardInfo->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
                  $date = $dateTime->format("n-j-Y");
                  
                  $wcNumber = "unknown";
                  $jobInfo = JobInfo::load($timeCardInfo->jobNumber);
                  if ($jobInfo)
                  {
                     $wcNumber = $jobInfo->wcNumber;
                  }
                  
                  $viewEditIcon = "";
                  $deleteIcon = "";
                  if (Authentication::getPermissions() & (Permissions::ADMIN | Permissions::SUPER_USER))
                  {
                     $viewEditIcon =
                     "<i class=\"material-icons table-function-button\" onclick=\"onEditTimeCard('$timeCardInfo->timeCardId')\">mode_edit</i>";
                     
                     $deleteIcon =
                     "<i class=\"material-icons table-function-button\" onclick=\"onDeleteTimeCard('$timeCardInfo->timeCardId')\">delete</i>";
                  }
                  else
                  {
                     $viewEditIcon =
                     "<i class=\"material-icons table-function-button\" onclick=\"onViewTimeCard('$timeCardInfo->timeCardId')\">visibility</i>";
                  }
                  
                  $html .=
<<<HEREDOC
                     <tr>
                        <td>$date</td>
                        <td>$operatorName</td>
                        <td>$timeCardInfo->jobNumber</td>
                        <td>$wcNumber</td>
                        <td>{$timeCardInfo->formatSetupTime()}</td>
                        <td>{$timeCardInfo->formatRunTime()}</td>
                        <td>$timeCardInfo->panCount</td>
                        <td>$timeCardInfo->partCount</td>
                        <td>$timeCardInfo->scrapCount</td>
                        <td>$viewEditIcon</td>
                        <td>$deleteIcon</td>
                     </tr>
HEREDOC;
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
}
?>