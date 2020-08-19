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

function PrintManager(listener)
{
   // Indicator that the DYMO framework has been initialized.
   this.frameworkInitialized = false;
   
   // Timer for refreshing the print queue from the server.
   this.interval = null;
   
   // Array of print jobs representing the current print queue.
   this.printQueue = null;
   
   // Array of dymo.label.framework.PrinterInfo objects representing the printers detected on the client PC.
   this.localPrinters = null;
   
   // Array of PrinterInfo objects representing the currently available cloud printers.
   this.cloudPrinters = null;
   
   // Callback routine when any data is updated.
   this.listener = listener;

   // Callback from DYMO framework.
   PrintManager.prototype.onFrameworkInitialized = function()
   {
      console.log("DYMO framework initialized");
      
      this.frameworkInitialized = true;
      
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
      for (printer of this.localPrinters)
      {
         this.registerPrinter(printer);
      }
      
   }.bind(this)
   
   // Queries DYMO framework for printers detected on the client PC
   PrintManager.prototype.refreshLocalPrinters = function()
   {
      this.localPrinters = dymo.label.framework.getPrinters();
      
   }.bind(this)
   
   // Returns true if the specified local printer is online.
   PrintManager.prototype.isPrinterOnline = function(printerName)
   {
      var isOnline = false;
      
      for (printer of this.localPrinters)
      {
         if (printer.name == printerName)
         {
            isOnline = printer.isConnected;
            break;
         }           
      }
      
      return (isOnline);
   }
   
   // Starts the print manager's periodic polling of printers and the print queue.
   PrintManager.prototype.start = function()
   {
      // Initialize framework.
      dymo.label.framework.init(this.onFrameworkInitialized);
      
      // Initial update.
      this.update();
      
      // Update periodically.
      this.interval = setInterval(function(){this.update();}.bind(this), 5000);
   }.bind(this)
   
   // Stops the print manager's periodic polling of printers and the print queue.
   PrintManager.prototype.stop = function()
   {
      clearInterval(this.interval);
   }

   // Polls local printers, cloud printers, and the print queue.
   // Attempts printing if a print job is specified for a local printer.
   PrintManager.prototype.update = function()
   {
      this.refreshLocalPrinters();
      
      this.registerPrinters();
      
      this.refreshCloudPrinters();
      
      this.refreshPrintQueue();
      
      this.onPrintQueueUpdate();
      
      // Update any listener (i.e. GUI).
      if (this.listener != null)
      {
         this.listener.onUpdate(this.localPrinters, this.cloudPrinters, this.printQueue);
      }
 
   }.bind(this)
   
   // Registers all local printers with the server.
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
        console.log("registerPrinter: Failed to contact server.");
      });
   
      // Set up our request.
      requestUrl = "../api/registerPrinter/"
      xhttp.open("POST", requestUrl, true);
   
      // The data sent is what the user provided in the form
      xhttp.send(formData);         
   }
   
   // Polls the server for a list of available cloud printers.
   PrintManager.prototype.refreshCloudPrinters = function()
   {
      // AJAX call to fetch print queue.
      requestUrl = "../api/printerData/"
      
      var manager = this;
      
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function()
      {
         if (this.readyState == 4 && this.status == 200)
         {
            try
            {            
               var json = JSON.parse(this.responseText);

               manager.cloudPrinters = json;               
            }
            catch (exception)
            {
               console.log("JSON syntax error: " + exception.message);
               console.log(this.responseText);
            }
         }
      };
      
      // Define what happens on successful data submission.
      xhttp.addEventListener("error", function(event) {
        console.log("refreshCloudPrinters: Failed to contact server.");
      });
      
      // Set up our request.
      xhttp.open("GET", requestUrl, true);
      xhttp.send();  
   };
   
   // Polls the server for a list of current print jobs.
   PrintManager.prototype.refreshPrintQueue = function()
   {
      // AJAX call to fetch print queue.
      requestUrl = "../api/printQueueData/"
      
      var manager = this;
      
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function()
      {
         if (this.readyState == 4 && this.status == 200)
         {
            try
            {            
               var json = JSON.parse(this.responseText);

               manager.printQueue = json;
            }
            catch (exception)
            {
               console.log("JSON syntax error: " + exception.message);
               console.log(this.responseText);
            }
         }
      };
      
      // Define what happens on successful data submission.
      xhttp.addEventListener("error", function(event) {
        console.log("refreshPrintQueue: Failed to contact server.");
      });
      
      // Set up our request.
      xhttp.open("GET", requestUrl, true);
      xhttp.send();  
   };
   
   // Updates the server with the new status of a print job.
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
      
      // Define what happens on successful data submission.
      xhttp.addEventListener("error", function(event) {
        console.log("updatePrintJobStatus: Failed to contact server.");
      });
      
      // Set up our request.
      xhttp.open("GET", requestUrl, true);
      xhttp.send(); 
      
   }.bind(this);
   
   // Attempts printing of all queued print jobs.
   PrintManager.prototype.onPrintQueueUpdate = function()
   {
      // Attempt to print any queued print jobs.
      if (this.frameworkInitialized)
      {
         for (printJob of this.printQueue)
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
      
   }.bind(this);
   
   // Cancels a print job.
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
      
      // Define what happens on successful data submission.
      xhttp.addEventListener("error", function(event) {
        console.log("cancelPrintJob: Failed to contact server.");
      });
      
      // Set up our request.
      xhttp.open("GET", requestUrl, true);
      xhttp.send();   
   }.bind(this);
   
   // Attempts printing of a print job.
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