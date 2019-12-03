// Be sure to keep in sync with PrintJobStatus in printDefs.php
var PrintJobStatus = {
   QUEUED:   1,
   PENDING:  2,
   PRINTING: 3,
   COMPLETE: 4,
   DELETED:  5,
};

function PrintQueue(container)
{
   this.container = container;
   
   this.printJobs = null;
   
   PrintQueue.prototype.nextPrintJob = function()
   {
      var nextJob = nullptr;
      
      for (printJob of this.printJobs)
      {
         if (printJob.status == PrintJobStatus.PENDING)
         {
            nextJob = printJob;
            break;
         }
      }
      
      return (nextJob);
   }
   
   PrintQueue.prototype.render = function()
   {
      const HEADINGS = new Array("Date", "Owner", "Description", "Status");
      
      if (this.container != null)
      {
         // Clear table.
         while (container.firstChild)
         {
            container.removeChild(container.firstChild);
         }
         
         //
         // Build table heading
         //
         
         var table = document.createElement("table");
         var thead = table.createTHead();
         var row = thead.insertRow();
         
         for (heading of HEADINGS)
         {
            var th = document.createElement("th");
            var text = document.createTextNode(heading);
            th.appendChild(text);
            row.appendChild(th);
         }
         
         //
         // Build table rows
         //
         
         if (this.printJobs != null)
         {
            for (printJob of this.printJobs)
            {
               var row = table.insertRow();
               
               // Date
               var cell = row.insertCell();
               var text = document.createTextNode(printJob.dateTime);
               cell.appendChild(text);
               
               // Owner
               cell = row.insertCell();
               text = document.createTextNode(printJob.owner);
               cell.appendChild(text);
               
               // Description
               cell = row.insertCell();
               text = document.createTextNode(printJob.description);
               cell.appendChild(text);
               
               // Status
               cell = row.insertCell();
               text = document.createTextNode(printJob.status);
               cell.appendChild(text);
            }
         }
         
         this.container.appendChild(table);
      }
   }.bind(this);
}


function Printer()
{   
   PrintManager.prototype.print = function(printJob)
   {
      console.log("Printing job " + printJob.printJobId);
   }
   
   PrintManager.prototype.cancel = function()
   {
   }
   
   PrintManager.prototype.isPrinting = function()
   {
      return (false);
   }

}

function PrintManager(container)
{
   var interval = null;
   
   var printQueue = new PrintQueue(container);
   
   var printer = new Printer();
   
   PrintManager.prototype.start = function()
   {
      // Initial update.
      this.update();
      
      // Update periodically.
      interval = setInterval(function(){this.update();}.bind(this), 5000);
   }.bind(this)
   
   PrintManager.prototype.stop = function()
   {
      clearInterval(interval);
   }
   
   PrintManager.prototype.update = function()
   {
      console.log("PrintManager::update()");
      this.fetchPrintQueue();
      
      if (!this.printer.isPrinting())
      {
         var printJob = this.printQueue.nextPrintJob();
         
         if (printJob != nullptr)
         {
            this.printer.print(printJob.xml);
            this.setPrintJobStatus(printJob.printJobId, PrintJobStatus.PRINTING)
         }
      }
   }.bind(this)
   
   PrintManager.prototype.fetchPrintQueue = function()
   {
      // AJAX call to fetch print queue.
      requestUrl = "../api/printQueue/?printerId=" + 101;
      
      var manager = this;
      
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function()
      {
         if (this.readyState == 4 && this.status == 200)
         {
            try
            {            
               var json = JSON.parse(this.responseText);
               
               if (json.success == true)
               {
                  manager.updatePrintQueue(json.queue);               
               }
               else
               {
                  console.log("API call to retrieve print queue failed.");
               }
            }
            catch (expection)
            {
               console.log("JSON syntax error");
               console.log(this.responseText);
            }
         }
      };
      xhttp.open("GET", requestUrl, true);
      xhttp.send();  
   };
   
   PrintManager.prototype.setPrintJobStatus = function(printJobId, status)
   {
      // AJAX call to fetch print queue.
      requestUrl = "../api/setPrintJobStatus/?printJobId=" + printJobId + "&status=" + status;
      
      var manager = this;
      
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function()
      {
         if (this.readyState == 4 && this.status == 200)
         {
            try
            {            
               var json = JSON.parse(this.responseText);
               
               if (json.success == true)
               {
                  console.log("Print job " + json.printJobId + " status updated.");            
               }
               else
               {
                  console.log("API call to retrieve print queue failed.");
               }
            }
            catch (expection)
            {
               console.log("JSON syntax error");
               console.log(this.responseText);
            }
         }
      };
      xhttp.open("GET", requestUrl, true);
      xhttp.send();  
   }.bind(this);
   
   PrintManager.prototype.updatePrintQueue = function(printJobs)
   {
      printQueue.printJobs = printJobs;
      printQueue.render();
   };
}