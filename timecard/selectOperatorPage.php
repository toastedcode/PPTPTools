<?php
require_once '../database.php';

function operator($employeeNumber, $name, $isChecked)
{
   $checked = $isChecked ? "checked" : "";
   
   $id = "list-option-" + $employeeNumber;

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
            width: 200px;
            height: 30px;
            font-size: 18px;
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
            font-size: 50px;
         }

         .select-operator-card {
            width: 80%;
            height: 625px;
            margin: auto;
         }

         .nav-div {
            margin: auto;
         }

         .inner-div {
            margin: auto;
            padding: 20px 20px 20px 20px;
            display: table;
         }

         .mdl-card__title {
           height: 50px;
           background: #f4b942;
         }
         </style>

        <script src="timeCard.js"></script>

        <div class="mdl-card mdl-shadow--2dp select-operator-card">

         <div class="mdl-card__title">
            <h6 class="mdl-card__title-text">Select operator</h6>
         </div>

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