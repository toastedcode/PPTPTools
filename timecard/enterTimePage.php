<?php

function enterTimePage($timeCardInfo)
{
   echo
   <<<HEREDOC
   <form action="timeCard.php" method="POST">
   
      <input type="hidden" name="view" value="enter_part_count"/>';
      <input type="hidden" name="action" value="update_time_card_info"/>';
      
      Setup time (hours):<br>
      <button type="button" onclick="changeSetupTimeHour(-1)">-</button>
      <input id="setupTimeHour-input" name="setupTimeHour" type="number" min="0" max="10" value="0">
      <button type="button" onclick="changeSetupTimeHour(1)">+</button>
      <button type="button" onclick="changeSetupTimeMinute(-15)">-</button>
      <input id="setupTimeMinute-input" name="setupTimeMinute" type="number" min="0" max="45" value="0">
      <button type="button" onclick="changeSetupTimeMinute(15)"/>+</button>
      <br>
      
      Run time (hours):<br>
      <button type="button" onclick="changeRunTimeHour(-1)">-</button>
      <input id="runTimeHour-input" name="runTimeHour" type="number" min="1" max="10" value="0">
      <button type="button" onclick="changeRunTimeHour(1)">+</button>
      <button type="button" onclick="changeRunTimeMinute(-15)">-</button>
      <input id="runTimeMinute-input" name="runTimeMinute" type="number" min="0" max="45" value="0">
      <button type="button" onclick="changeRunTimeMinute(15)">+</button>
      <br>
      
      <br><br>
      <input type="submit" value="Submit">

   </form>
   
   <script>
      function changeSetupTimeHour(delta)
      {
         var field = document.querySelector('#setupTimeHour-input');
         var newValue = parseInt(field.value, 10) + delta;
         
         // Constrain values.
         newValue = Math.max(0, Math.min(newValue, 10));
         
         field.value = newValue;
      }
      
      function changeSetupTimeMinute(delta)
      {
         var field = document.querySelector('#setupTimeMinute-input');
         var newValue = parseInt(field.value, 10) + delta;
         
         // Constrain values.
         newValue = Math.max(0, Math.min(newValue, 45));
         
         field.value = newValue;
      }
      
      function changeRunTimeHour(delta)
      {
         var field = document.querySelector('#runTimeHour-input');
         var newValue = parseInt(field.value, 10) + delta;
         
         // Constrain values.
         newValue = Math.max(0, Math.min(newValue, 10));
         
         field.value = newValue;
      }
      
      function changeRunTimeMinute(delta)
      {
         var field = document.querySelector('#runTimeMinute-input');
         var newValue = parseInt(field.value, 10) + delta;
         
         // Constrain values.
         newValue = Math.max(0, Math.min(newValue, 45));
         
         field.value = newValue;
      }
   </script>
HEREDOC;
}
   
?>