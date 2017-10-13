<?php
require_once '../database.php';

function selectOperatorPage($timeCardInfo)
{
    $database = new PPTPDatabase("localhost", "root", "", "pptp");
    
    $database->connect();
    
    if ($database->isConnected())
    {
        $result = $database->getOperators();

        echo
<<<HEREDOC
        <script src="timeCard.js"></script>
        <form id="timeCardForm" action="timeCard.php" method="POST">
HEREDOC;
        
        // output data of each row
        while($row = $result->fetch_assoc())
        {
            $name = $row["FirstName"] . $row["LastName"];
            
            $employeeNumber = $row["EmployeeNumber"];
            
            $checked = ($timeCardInfo->employeeNumber == $employeeNumber) ? " checked" : "";
            
            echo "<input type=\"radio\" name=\"employeeNumber\" value=\"$employeeNumber\"$checked/>$name";
        }

        echo
<<<HEREDOC
        <br/>
        </form>
HEREDOC;

        cancelButton("submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'cancel_time_card')");
        nextButton("if (validateOperator()) {submitForm('timeCardForm', 'timeCard.php', 'select_work_center', 'update_time_card_info');};");
    }
}
?>