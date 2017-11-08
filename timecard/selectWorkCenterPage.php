<?php
require_once '../database.php';

function selectWorkCenterPage($timeCardInfo)
{
   $database = new PPTPDatabase("localhost", "root", "", "pptp");
   
   $database->connect();
   
   if ($database->isConnected())
   {
      $result = $database->getWorkCenters();

      echo
      <<<HEREDOC
         <!-- List with avatar and controls -->
         <style>
         
         .operator-input
         {
            visibility: hidden;
            position: absolute;
         }
         
         .operator-select-button {
            float: left;
            display: table;
            margin: 20px 20px 0 0;
            padding: 5px 5px;
            width: 250px;
            height: 75px;
            font-size: 24px;
            line-height: 1.8;
            appearance: none;
            box-shadow: none;
            border-radius: 0;
            color: #fff;
            background-color: #6496c8;
            text-shadow: -1px 1px #417cb8;
            border: none;
         }
         
         .operator-select-button:hover {
            background-color: #346392;
            text-shadow: -1px 1px #27496d;
         }
         
         input:checked + label > div  {
            background-color: #27496d;
            text-shadow: -1px 1px #193047;
         }
         
         .button-icon {
            font-size: 80px;
         }
         </style>
         
        <script src="timeCard.js"></script>
      <form id="timeCardForm" action="timeCard.php" method="POST">
HEREDOC;

      // output data of each row
      while($row = $result->fetch_assoc())
      {
         $wcNumber = $row["WCNumber"];
         
         $checked = ($timeCardInfo->wcNumber == $wcNumber) ? " checked" : "";
         
         echo
         <<<HEREDOC
         <input type="radio" id="$wcNumber" class="operator-input" name="wcNumber" value="$wcNumber" $checked/>
         <label for="$wcNumber">
      <div type="button" class="operator-select-button">
         <i class="material-icons button-icon">build</i>
         <div style="display: table-cell; vertical-align: middle;">$wcNumber</div>
      </div>
   </label>
HEREDOC;
      }
      
      echo
<<<HEREDOC
        <br/>
        </form>
HEREDOC;

      cancelButton("submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'cancel_time_card')");
      backButton("if (validateWorkCenter()){submitForm('timeCardForm', 'timeCard.php', 'select_operator', 'update_time_card_info');};");
      nextButton("if (validateWorkCenter()){submitForm('timeCardForm', 'timeCard.php', 'select_job', 'update_time_card_info');};");
   }
}
?>