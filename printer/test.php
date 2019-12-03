<?php
?>

<html>
   <div id="table-container"></div>
</html>

<script src="printer.js"></script>
<script>
   var printManager = new PrintManager(document.getElementById("table-container"));

   /*
   printManager.printQueue.printJobs = [
      {date: "11/30/2019", owner:"Jason Tost", description:"Pan Ticket 101", status:"PRINTING"},
      {date: "11/30/2019", owner:"Steve Smith", description:"Pan Ticket 151", status:"PENDING"},
   ];
   */

   printManager.start();
</script>