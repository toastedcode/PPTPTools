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
      <div class="flex-vertical content">

         <div class="heading">Time Cards</div>

         <div class="description">Time cards record the time a machine operator spends working on a job, as well as a part count for that run.</div>
      
            $filterDiv
            
            $timeCardsDiv
         
            $navBar
         
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
      $html = "";
      
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
         
         if (($result) && ($database->countResults($result) > 0))
         {
            $html =
<<<HEREDOC
            <div class="table-container">
               <table class="time-card-table">
                  <tr>
                     <th>Date</th>
                     <th>Operator</th>
                     <th>Job #</th>
                     <th class="hide-on-mobile">Machine #</th>
                     <th class="hide-on-tablet">Heat #</th>
                     <th class="hide-on-tablet">Run Time</th>
                     <th class="hide-on-tablet">Setup Time</th>
                     <th class="hide-on-tablet">Basket Count</th>
                     <th>Part Count</th>
                     <th class="hide-on-tablet">Scrap Count</th>
                     <th/>
                     <th/>
                  </tr>
HEREDOC;
            
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
                  $jobInfo = JobInfo::load($timeCardInfo->jobId);
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
                        <td>$jobInfo->jobNumber</td>
                        <td class="hide-on-mobile">$wcNumber</td>
                        <td class="hide-on-tablet">$timeCardInfo->materialNumber</td>
                        <td class="hide-on-tablet">{$timeCardInfo->formatRunTime()}</td>
                        <td class="$approval hide-on-tablet">
                           {$timeCardInfo->formatSetupTime()}
                           <div class="approval $approval" $tooltip>Approved</div>
                           <div class="unapproval $approval">Unapproved</div>
                        </td>
                        <td class="hide-on-tablet">$timeCardInfo->panCount</td>
                        <td>$timeCardInfo->partCount</td>
                        <td class="hide-on-tablet">$timeCardInfo->scrapCount</td>
                        <td>$viewEditIcon</td>
                        <td>$deleteIcon</td>
                     </tr>
HEREDOC;
               }
            }
            
            $html .=
<<<HEREDOC
               </table>
            </div>
HEREDOC;
         }
         else
         {
            $html = "<div class=\"no-data\">No data is available for the selected range.  Use the filter controls above to select a new operator or date range.</div>";
         }
      }
      
      return ($html);
   }
}
?>