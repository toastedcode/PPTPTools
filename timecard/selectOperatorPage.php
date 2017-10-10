<?php
require_once '../database.php';

function selectOperatorPage($timeCardInfo)
{
    $database = new PPTPDatabase("localhost", "root", "", "pptp");
    
    $database->connect();
    
    if ($database->isConnected())
    {
        $result = $database->getOperators();
        
        echo '<script src="timeCard.js"></script>';
        
        echo '<form action="timeCard.php" method="POST">';
        echo '<input type="hidden" name="action" value="update_time_card_info"/>';
        
        // output data of each row
        while($row = $result->fetch_assoc())
        {
            $name = $row["FirstName"] . $row["LastName"];
            
            $employeeNumber = $row["EmployeeNumber"];
            
            $checked = ($timeCardInfo->employeeNumber == $employeeNumber) ? " checked" : "";
            
            echo "<input type=\"radio\" name=\"employeeNumber\" value=\"$employeeNumber\"$checked/>$name";
        }
        
        echo "<br/>";
        echo "<button type=\"button\" onclick=\"onCancel()\">Cancel</button>";
        echo "<button type=\"submit\" name=\"view\" value=\"select_work_center\">Next</button>";
        
        echo '</form>';
    }
}
?>