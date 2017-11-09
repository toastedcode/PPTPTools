<?php
require_once '../database.php';

function operator($employeeNumber, $name, $isChecked)
{
   $checked = $isChecked ? "checked" : "";
   
   $id = "list-option-" + $employeeNumber;
   
   /*
   echo
<<<HEREDOC
   <li class="mdl-list__item">
      <span class="mdl-list__item-primary-content">
         <i class="material-icons  mdl-list__item-avatar">person</i>
         $name
      </span>
      <span class="mdl-list__item-secondary-action">
         <label class="demo-list-radio mdl-radio mdl-js-radio mdl-js-ripple-effect" for="$id">
            <input type="radio" id="$id" class="mdl-radio__button" name="employeeNumber" value="$employeeNumber" $checked/>
         </label>
      </span>
   </li>
HEREDOC;
   */

   echo
<<<HEREDOC
   <input type="radio" id="$id" class="operator-input" name="employeeNumber" value="$employeeNumber" $checked/>
   <label for="$id">
      <div type="button" class="operator-select-button">
         <i class="material-icons button-icon">person</i>
         <div style="display: table-cell; vertical-align: middle;">$name</div>
      </div>
   </label>
HEREDOC;

   
}

function selectOperatorPage($timeCardInfo)
{
    $database = new PPTPDatabase("localhost", "root", "", "pptp");
    
    $database->connect();
    
    if ($database->isConnected())
    {
        $result = $database->getOperators();

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
            width: 270px;
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

         .select-operator-card {
            width: 80%;
            height: 700px;
            margin: auto;
            padding: 10px;
         }

         .nav-div {
            padding-top: 30px;
            margin: auto;
         }

         .inner-div {
            margin: auto;
            padding: 20px 20px 20px 20px;
            display: table;
         }
         </style>

        <script src="timeCard.js"></script>

        <div class="mdl-card mdl-shadow--2dp select-operator-card">

        <div class="inner-div">

        <form id="timeCardForm" action="timeCard.php" method="POST">
HEREDOC;
        
        // output data of each row
        while($row = $result->fetch_assoc())
        {
            $name = $row["FirstName"] . " " . $row["LastName"];
            
            $employeeNumber = $row["EmployeeNumber"];
            
            $isChecked = ($timeCardInfo->employeeNumber == $employeeNumber);
            
            operator($employeeNumber, $name, $isChecked);
        }

        echo
<<<HEREDOC
        </form>

        </div>
HEREDOC;

        echo "<div class=\"nav-div\">";
        
        cancelButton("submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'cancel_time_card')");
        nextButton("if (validateOperator()) {submitForm('timeCardForm', 'timeCard.php', 'select_work_center', 'update_time_card_info');};");
       
        echo "</div>";
        echo "</div>";
    }
}
?>