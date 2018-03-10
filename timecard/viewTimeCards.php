<?php

require_once '../database.php';
require_once '../user.php';
require_once '../common/filter.php';
require_once '../common/jobInfo.php';

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
                  <th>Machine #</th>
                  <th>Material #</th>
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
                        <td>$timeCardInfo->materialNumber</td>
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