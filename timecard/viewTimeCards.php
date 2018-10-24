<?php

require_once '../common/database.php';
require_once '../common/filter.php';
require_once '../common/jobInfo.php';
require_once '../common/newIndicator.php';
require_once '../common/permissions.php';
require_once '../common/roles.php';
require_once '../common/userInfo.php';

class ViewTimeCards
{
   private $filter;
   
   public function __construct()
   {
      if (isset($_SESSION["timeCardFilter"]))
      {
         $this->filter = $_SESSION["timeCardFilter"];
      }
      else
      {
         $user = Authentication::getAuthenticatedUser();
         
         $operators = null;
         $selectedOperator = null;
         $allowAll = false;
         if (Authentication::checkPermissions(Permission::VIEW_OTHER_USERS))
         {
            // Allow selection from all operators.
            $operators = UserInfo::getUsersByRole(Role::OPERATOR);
            $selectedOperator = "All";
            $allowAll = true;
         }
         else
         {
            // Limit to own time cards.
            $operators = array($user);
            $selectedOperator = $user->employeeNumber;
            $allowAll = false;
         }
         
         $this->filter = new Filter();
   
         $this->filter->addByName("operator", new UserFilterComponent("Operator", $operators, $selectedOperator, $allowAll));
         $this->filter->addByName('date', new DateFilterComponent());
         $this->filter->add(new FilterButton());
         $this->filter->add(new FilterDivider());
         $this->filter->add(new TodayButton());
         $this->filter->add(new YesterdayButton());
         $this->filter->add(new ThisWeekButton());
      }
      
      $this->filter->update();
      
      $_SESSION["timeCardFilter"] = $this->filter;
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
                  <th>Heat #</th>
                  <th>Run Time</th>
                  <th>Setup Time</th>
                  <th>Basket Count</th>
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
                  $user = UserInfo::load($timeCardInfo->employeeNumber);
                  if ($user)
                  {
                     $operatorName= $user->getFullName();
                  }
                  
                  $dateTime = new DateTime($timeCardInfo->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
                  $date = $dateTime->format("n-j-Y");
                  
                  $newIndicator = new NewIndicator($dateTime, 60);
                  $new = $newIndicator->getHtml();
                  
                  $wcNumber = "unknown";
                  $jobInfo = JobInfo::load($timeCardInfo->jobNumber);
                  if ($jobInfo)
                  {
                     $wcNumber = $jobInfo->wcNumber;
                  }
                  
                  $approval = "no-approval-required";
                  $tooltip = "";
                  if ($timeCardInfo->requiresApproval())
                  {
                     if ($timeCardInfo->isApproved())
                     {
                        $approval = "approved";

                        $user = UserInfo::load($timeCardInfo->approvedBy);
                        if ($user)
                        {
                           $tooltip = "tooltip=\"Approved by " . $user->getFullName() . "\"";
                        }
                        else
                        {
                           $tooltip = "tooltip=\"Approved\"";
                        }
                     }
                     else
                     {
                        $approval = "unapproved";
                     }
                  }
                  
                  $viewEditIcon = "";
                  $deleteIcon = "";
                  if (Authentication::checkPermissions(Permission::EDIT_TIME_CARD))
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
                        <td>$date $new</td>
                        <td>$operatorName</td>
                        <td>$timeCardInfo->jobNumber</td>
                        <td>$wcNumber</td>
                        <td>$timeCardInfo->materialNumber</td>
                        <td>{$timeCardInfo->formatRunTime()}</td>
                        <td class="$approval">
                           {$timeCardInfo->formatSetupTime()}
                           <div class="approval $approval" $tooltip>Approved</div>
                           <div class="unapproval $approval">Unapproved</div>
                        </td>
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