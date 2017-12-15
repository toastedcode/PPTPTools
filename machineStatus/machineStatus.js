function updateStatus(wcNumber)
{
   requestURl = "retrieveStatus.php?wcNumber=" + wcNumber;
   
   var xhttp = new XMLHttpRequest();
   xhttp.onreadystatechange = function()
   {
      if (this.readyState == 4 && this.status == 200)
      {
         //alert(this.responseText);
         machineStatus = JSON.parse(this.responseText);
         
         updatePage(wcNumber, machineStatus);
      }
   };
   xhttp.open("GET", requestURl, true);
   xhttp.send();
}

function updatePage(wcNumber, machineStatus)
{
   // Update status.
   
   // Update status time.
   
   // Update part count.
   updatePartCount(wcNumber, machineStatus.partCount);
   
   // Update bar graph.
}

function updatePartCount(wcNumber, partCount)
{
   partCountDivId = "part-count-" + wcNumber;
   
   partCountDiv = document.getElementById(partCountDivId);
   
   if (partCountDiv)
   {
      partCountDiv.innerHTML = machineStatus.partCount;
   }
}