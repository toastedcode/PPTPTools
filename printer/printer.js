// Be sure to keep in sync with PrintJobStatus in printDefs.php
var PrintJobStatus = {
   UNKNOWN:  0,
   QUEUED:   1,
   PENDING:  2,
   PRINTING: 3,
   COMPLETE: 4,
   DELETED:  6
};

var PrintJobStatusLabels = ["", "Queued", "Pending", "Printing", "Complete", "Deleted"];

function PrintManager(printerContainerId, printQueueContainerId, previewId)
{
   // The div element containing the printer table
   var printerContainer = document.getElementById(printerContainerId);
   
   // The div element containing the print queue table.
   var printQueueContainer = document.getElementById(printQueueContainerId);
   
   // The img element to be used as the print preview.
   var previewImg = document.getElementById(previewId);
   
   // Indicator that the DYMO framework has been initialized.
   var frameworkInitialized = false;
   
   // Timer for refreshing the print queue from the server.
   var interval = null;
   
   // Array of print jobs representing the current print queue.
   var printQueue = null;
   
   // Array of dymo.label.framework.PrinterInfo objects representing the printers detected on the client PC.
   var printers = null;

   // Callback from DYMO framework.
   PrintManager.prototype.onFrameworkInitialized = function()
   {
      console.log("DYMO framework initialized");
      
      frameworkInitialized = true;
      
      if (dymo.label.framework.checkEnvironment())
      {
         console.log("Printing enabled.");
      }
      else
      {
         console.log("Printing not supported.");         
      }
      
   }.bind(this)
   
   // Registers all detected printers with the server.
   PrintManager.prototype.registerPrinters = function()
   {
      for (printer of printers)
      {
         this.registerPrinter(printer);
      }
      
   }.bind(this)
   
   // Queries DYMO framework for printers detected on the client PC
   PrintManager.prototype.refreshPrinters = function()
   {
      printers = dymo.label.framework.getPrinters();
     
      this.renderPrinters();
      
   }.bind(this)
   
   PrintManager.prototype.isPrinterOnline = function(printerName)
   {
      var isOnline = false;
      
      for (printer of printers)
      {
         if (printer.name == printerName)
         {
            isOnline = printer.isConnected;
            break;
         }           
      }
      
      return (isOnline);
   }
   
   PrintManager.prototype.start = function()
   {
      // Initialize framework.
      dymo.label.framework.init(this.onFrameworkInitialized);
      
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
      this.refreshPrinters();
      
      this.registerPrinters();
      
      this.fetchPrintQueue();
 
   }.bind(this)
   
   PrintManager.prototype.registerPrinter = function(printerInfo)
   {
      var xhttp = new XMLHttpRequest();
   
      // Bind the form data.
      var formData = new FormData();
      formData.set("printerName", printerInfo.name);
      formData.set("model", printerInfo.modelName);
      formData.set("isConnected", printerInfo.isConnected);
   
      // Define what happens on successful data submission.
      xhttp.addEventListener("load", function(event) {
         try
         {
            var json = JSON.parse(event.target.responseText);
   
            if (json.success == false)
            {
               console.log("Failed to register printer. " + json.error);
            }
         }
         catch (exception)
         {
            console.log("JSON syntax error. " + exception.message);
            console.log(event.target.responseText);
         }
      });
   
      // Define what happens on successful data submission.
      xhttp.addEventListener("error", function(event) {
        alert('Oops! Something went wrong.');
      });
   
      // Set up our request
      requestUrl = "../api/registerPrinter/"
      xhttp.open("POST", requestUrl);
   
      // The data sent is what the user provided in the form
      xhttp.send(formData);         
   }
   
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
                  manager.onPrintQueueUpdate(json.queue);               
               }
               else
               {
                  console.log("API call to retrieve print queue failed.");
               }
            }
            catch (exception)
            {
               console.log("JSON syntax error: " + exception.message);
               console.log(this.responseText);
            }
         }
      };
      xhttp.open("GET", requestUrl, true);
      xhttp.send();  
   };
   
   PrintManager.prototype.updatePrintJobStatus = function(printJobId, status)
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
                  console.log("Print job " + json.printJobId + " status updated: " + PrintJobStatusLabels[json.status]);            
               }
               else
               {
                  console.log("API call to update print job status failed: " + json.error);
               }
            }
            catch (exception)
            {
               console.log("JSON syntax error: " + exception.message);
               console.log(this.responseText);
            }
         }
      };
      xhttp.open("GET", requestUrl, true);
      xhttp.send();  
      
   }.bind(this);
   
   PrintManager.prototype.onPrintQueueUpdate = function(updatedPrintQueue)
   {
      printQueue = updatedPrintQueue;
      
      // Attempt to print any queued print jobs.
      if (frameworkInitialized)
      {
         for (printJob of printQueue)
         {
            if (printJob.status == PrintJobStatus.QUEUED)
            {
               if (this.print(printJob) == true)
               {
                  printJob.status = PrintJobStatus.COMPLETE;
                  
                  this.updatePrintJobStatus(printJob.printJobId, printJob.status);   
               }
               else
               {
                  // Leave in QUEUED state.                                 
               }
            }
         }
      }

      this.renderPrintQueue();
      
      if (printQueue.length > 0)
      {
         this.renderPreview(printQueue[0]);
      }
      else
      {
         this.renderPreview(null);
      }
      
   }.bind(this);
   
   PrintManager.prototype.cancelPrintJob = function(printJobId)
   {
      var manager = this;
      
      // AJAX call to cancel print job.
      requestUrl = "../api/cancelPrintJob/?printJobId=" + printJobId;
      
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
                  console.log("Cancelled print job " + json.printJobId);
                  
                  manager.update();
               }
               else
               {
                  alert("Failed to cancel print job.");
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
   
   PrintManager.prototype.renderPrinters = function()
   {
      const HEADINGS = new Array("Name", "Model", "Location", "Status");
      
      if (printerContainer != null)
      {
         // Clear table.
         while (printerContainer.firstChild)
         {
            printerContainer.removeChild(printerContainer.firstChild);
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
         
         if (printers != null)
         {
            for (printer of printers)
            {
               var row = table.insertRow();
               
               // Name
               var printFriendlyName = printer.name.substring(printer.name.lastIndexOf("\\") + 1);
               var cell = row.insertCell();
               var text = document.createTextNode(printFriendlyName);
               cell.appendChild(text);
               
               // Model
               cell = row.insertCell();
               text = document.createTextNode(printer.modelName);
               cell.appendChild(text);
               
               // Location
               cell = row.insertCell();
               text = document.createTextNode(printer.isLocal ? "Local" : "Network");
               cell.appendChild(text);
               
               // Status
               cell = row.insertCell();
               text = document.createTextNode(printer.isConnected ? "Online" : "Offline");
               cell.appendChild(text);
            }
         }
         
         printerContainer.appendChild(table);
      }
      
   }.bind(this);
   
   PrintManager.prototype.renderPrintQueue = function()
   {
      const HEADINGS = new Array("Date", "Owner", "Description", "Copies", "Status", "");
      
      if (printQueueContainer != null)
      {
         // Clear table.
         while (printQueueContainer.firstChild)
         {
            printQueueContainer.removeChild(printQueueContainer.firstChild);
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
         
         if (printQueue != null)
         {
            for (printJob of printQueue)
            {
               var row = table.insertRow();
               //row.onmouseover = this.renderPreview(printJob);
               
               // Date
               var cell = row.insertCell();
               var text = document.createTextNode(printJob.dateTime);
               cell.appendChild(text);
               
               // Owner
               cell = row.insertCell();
               text = document.createTextNode(printJob.ownerName);
               cell.appendChild(text);
               
               // Description
               cell = row.insertCell();
               text = document.createTextNode(printJob.description);
               cell.appendChild(text);
               
               // Copies
               cell = row.insertCell();
               text = document.createTextNode(printJob.copies);
               cell.appendChild(text);
               
               // Status
               cell = row.insertCell();
               text = document.createTextNode(PrintJobStatusLabels[printJob.status]);
               cell.appendChild(text);
               
               // Delete icon
               cell = row.insertCell();
               cell.innerHTML = 
                  "<i class=\"material-icons table-function-button\" onclick=\"printManager.cancelPrintJob(" + printJob.printJobId + ")\">delete</i>";
            }
         }
         
         printQueueContainer.appendChild(table);
      }
   }.bind(this);
   
   PrintManager.prototype.renderPreview = function(printJob)
   {
      if (previewImg != null)
      {
         if (printJob != null)
         {
            var label = dymo.label.framework.openLabelXml(printJob.xml);
            
            var pngData = label.render();
   
            previewImg.src = "data:image/png;base64," + pngData;
            
            previewImg.style.display  = "block";         
         }
         else
         {
            previewImg.style.display  = "none";
         }
      }
   }
   
   PrintManager.prototype.print = function(printJob)
   {
      var success = false;
      
      if (this.isPrinterOnline(printJob.printerName))
      {      
         console.log("Printing job " + printJob.printJobId);
         
         var printParams = {};
         printParams.copies = printJob.copies;
         printParams.jobTitle = printJob.description;
         
         try
         {
            var printParamsXML = dymo.label.framework.createLabelWriterPrintParamsXml(printParams);
   
            dymo.label.framework.printLabel(printJob.printerName, printParamsXML, printJob.xml);  // TODO: Use printJob.printer
            
            console.log("Printed!");
            
            success = true;
         }
         catch (exception)
         {
            console.log("Print error! " + exception.message);
         }
      }
      
      return (success);
      
   }.bind(this);
   
 }