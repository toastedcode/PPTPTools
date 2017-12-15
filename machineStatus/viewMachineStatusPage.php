<?php

require_once '../database.php';
require_once '../navigation.php';
require_once 'machineStatusIndicator.php';

class MachineStatusPage
{
   public static function getHtml()
   {
      $machineStatus = MachineStatusPage::getMachineStatus();
      
      $navBar = MachineStatusPage::navBar();
      
      $html =
      <<<HEREDOC
      <form id="timeCardForm" action="timeCard.php" method="POST"></form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">View Machine Status</div>
         <div class="flex-horizontal content-div" style="height:400px;">
            $machineStatus
         </div>
         
         $navBar
               
      </div>

      <script src="machineStatus.js"></script>
      <script>
         // Start a one-second timer to update the machine status div.
         setInterval(function(){updateMachineStatus();}, 1000);
      </script>
HEREDOC;
      
      return ($html);
   }
   
   public static function render()
   {
      echo (MachineStatusPage::getHtml());
   }
   
   public static function getMachineStatus()
   {
      $html = "<div id=\"machine-status-container\" class=\"flex-horizontal\">";
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getSensors();
         
         if ($result)
         {
            while ($row = $result->fetch_assoc())
            {
               $machineStatusIndicator = new MachineStatusIndicator($row["sensorId"], $row["wcNumber"]);
               $html .= $machineStatusIndicator->getHtml();
            }
         }
      }
      
      $html .= "</div>";
      
      $database->disconnect();
      
      return ($html);
   }
   
   private static function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->mainMenuButton();
      $navBar->end();
      
      return ($navBar->getHtml());
   }
}
?>

<!-- ********************************** BEGIN ********************************************* -->

<?php 
if (isset($_GET["action"]) && ($_GET["action"] == "updateMachineStatus"))
{
   echo (MachineStatusPage::getMachineStatus());
}
?>