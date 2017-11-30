<?php
function selectActionPage()
{
   echo
   <<<HEREDOC
   <form action="timeCard.php" method="POST">
      <button type="submit" name="action" value="view_time_cards">View/Edit Time Card</button>
      <button type="submit" name="action" value="select_operator">New Time Card</button>
   </form>
HEREDOC;
}
?>