function hide(elementId)
{
   document.getElementById(elementId).style.display = "none";
}

function show(elementId)
{
   document.getElementById(elementId).style.display = "block";
}

function set(elementId, value)
{
   document.getElementById(elementId).value = value;
}

function clear(elementId)
{
   document.getElementById(elementId).value = null;
}

function enable(elementId)
{
   document.getElementById(elementId).disabled = false;
}

function disable(elementId)
{
   document.getElementById(elementId).disabled = true;
}

function formatToTwoDigits(value)
{
   return (("0" + value).slice(-2));
}

function onJobNumberChange()
{
   jobNumber = document.getElementById("job-number-input").value;
   
   clear("wc-number-input");
   
   updateGrossPartsPerHour();
   
   if (jobNumber == null)
   {
      disable("wc-number-input");
   }
   else
   {
      enable("wc-number-input");
      
      // Populate WC numbers based on selected job number.
      
      // AJAX call to populate WC numbers based on selected job number.
      requestUrl = "../api/wcNumbers/?jobNumber=" + jobNumber;
      
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
                  updateWcOptions(json.wcNumbers);               
               }
               else
               {
                  console.log("API call to retrieve WC numbers failed.");
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
   }
}

function updateWcOptions(wcNumbers)
{
   element = document.getElementById("wc-number-input");
   
   while (element.firstChild)
   {
      element.removeChild(element.firstChild);
   }

   for (var wcNumber of wcNumbers)
   {
      var option = document.createElement('option');
      option.innerHTML = wcNumber;
      option.value = wcNumber;
      element.appendChild(option);
   }
   
   element.value = null;
}

function onWcNumberChange()
{
   updateGrossPartsPerHour();
}

function onRunTimeChange()
{
   var hours = parseInt(document.getElementById("run-time-hour-input").value);
   var minutes = parseInt(document.getElementById("run-time-minute-input").value);
   
   var runTime = ((hours * 60) + minutes);
   
   document.getElementById("run-time-input").value = runTime;
   
   document.getElementById("run-time-minute-input").value = formatToTwoDigits(minutes);
   
   updateEfficiency();
}

function onSetupTimeChange()
{
   var hours = parseInt(document.getElementById("setup-time-hour-input").value);
   var minutes = parseInt(document.getElementById("setup-time-minute-input").value);
   
   var setupTime = ((hours * 60) + minutes);
   
   document.getElementById("setup-time-input").value = setupTime;
   
   document.getElementById("setup-time-minute-input").value = formatToTwoDigits(minutes);
   
   document.getElementById("approved-by-input").value = 0;
   
   updateApproval();
}

function onPartCountChange()
{
   updateEfficiency();
}

function updateGrossPartsPerHour()
{
   var jobNumber = document.getElementById("job-number-input").value;
   var wcNumber = parseInt(document.getElementById("wc-number-input").value);
   
   var grossPartsPerHour = 0;
   
   if ((jobNumber != "") && !isNaN(wcNumber) && (wcNumber != 0))
   {
      // Populate gross parts per hour based on selected job.
      
      // AJAX call to retrieve gross parts per hour by selected job.
      requestUrl = "../api/grossPartsPerHour/?jobNumber=" + jobNumber + "&wcNumber=" + wcNumber;
      
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function()
      {
         if (this.readyState == 4 && this.status == 200)
         {
            var grossPartsPerHour = 0;
            
            try
            {
               var json = JSON.parse(this.responseText);
               
               if (json.success == true)
               {
                  grossPartsPerHour = json.grossPartsPerHour;      
               }
               else
               {
                  console.log("API call to retrieve gross parts-per-hour failed.");
               }
            }
            catch (expection)
            {
               console.log("JSON syntax error");
               console.log(this.responseText);
            }
            
            document.getElementById("gross-parts-per-hour-input").value = grossPartsPerHour;

            updateEfficiency();
         }
      };
      xhttp.open("GET", requestUrl, true);
      xhttp.send();
   }
   else
   {
      document.getElementById("gross-parts-per-hour-input").value = 0;  

      updateEfficiency();
   }  
}

function updateEfficiency()
{
   var runTimeHourInput = document.getElementById("run-time-hour-input");
   var runTimeMinuteInput = document.getElementById("run-time-minute-input");
   var partCountInput = document.getElementById("part-count-input");
   var grossPartsPerHourInput = document.getElementById("gross-parts-per-hour-input");
   var efficiencyInput = document.getElementById("efficiency-input");
   
   if (runTimeHourInput.validator.isValid() && 
       runTimeMinuteInput.validator.isValid() &&
       partCountInput.validator.isValid())
   {
      var runTimeMinutes = ((parseInt(runTimeHourInput.value) * 60) + parseInt(runTimeMinuteInput.value));
      
      var partCount = partCountInput.value;
      
      var grossPartsPerHour = parseInt(grossPartsPerHourInput.value);
      
      if (grossPartsPerHour > 0)
      {
         var potentialParts = ((runTimeMinutes / 60) * grossPartsPerHour);
         
         if (potentialParts > 0)
         {
            var efficiency = ((partCount / potentialParts) * 100);
            
            efficiencyInput.value = efficiency.toFixed(2);
         }
      }
      else
      {
         clear("efficiency-input");
      }
   }
   else
   {
      clear("efficiency-input");
   }
}

var touched = false;

function onTouched()
{
   touched = true;
}

function onSubmit()
{
   if (validateTimeCard())
   {
      var form = document.querySelector('#input-form');
      
      var xhttp = new XMLHttpRequest();
   
      // Bind the form data.
      var formData = new FormData(form);
   
      // Define what happens on successful data submission.
      xhttp.addEventListener("load", function(event) {
         try
         {
            var json = JSON.parse(event.target.responseText);
   
            if (json.success == true)
            {
               location.href = "viewTimeCards.php";
            }
            else
            {
               alert(json.error);
            }
         }
         catch (expection)
         {
            console.log("JSON syntax error");
            console.log(this.responseText);
         }
      });
   
      // Define what happens on successful data submission.
      xhttp.addEventListener("error", function(event) {
        alert('Oops! Something went wrong.');
      });
   
      // Set up our request
      requestUrl = "../api/saveTimeCard/"
      xhttp.open("POST", requestUrl);
   
      // The data sent is what the user provided in the form
      xhttp.send(formData);
   }
}

function onCancel()
{
   if (!touched || confirm("Are you sure?  All data will be lost."))
   {
      window.history.back();
   }
}

function onDeleteTimeCard(timeCardId)
{
   if (confirm("Are you sure you want to delete this log entry?"))
   {
      // AJAX call to delete part weight entry.
      requestUrl = "../api/deleteTimeCard/?timeCardId=" + timeCardId;
      
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
                  location.href = "viewTimeCards.php";
               }
               else
               {
                  console.log("API call to delete part weight entry failed.");
                  alert(json.error);
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
   }
}

function validateTimeCard()
{
   valid = false;

   if (!(document.getElementById("operator-input").validator.validate()))
   {
      alert("Please select an operator.");    
   }
   else if (!(document.getElementById("job-number-input").validator.validate()))
   {
      alert("Please select an active job.");    
   }
   else if (!(document.getElementById("wc-number-input").validator.validate()))
   {
      alert("Please select a work center.");    
   }
   else if (!(document.getElementById("material-number-input").validator.validate()))
   {
      alert("Please enter a valid heat number.");    
   }
   else if (!(document.getElementById("run-time-hour-input").validator.validate() &&
              document.getElementById("run-time-minute-input").validator.validate()))
   {
      alert("Please enter a valid run time.")      
   }
   else if (!(document.getElementById("setup-time-hour-input").validator.validate() &&
              document.getElementById("setup-time-minute-input").validator.validate()))
   {
      alert("Please enter a valid setup time.")
   }
   // J. Orbin requested that users be able enter incomplete time sheets.  (11/21/2019)
   /*
   else if ((document.getElementById("setup-time-hour-input").value == 0) &&
            (document.getElementById("setup-time-minute-input").value == 0) &&
            (document.getElementById("run-time-hour-input").value == 0) &&
            (document.getElementById("run-time-minute-input").value == 0))
   {
      alert("Please enter some valid times.")  
   }
   */
   else if (!(document.getElementById("pan-count-input").validator.validate()))
   {
      alert("Please enter a valid basket count.");    
   }
   else if (!(document.getElementById("part-count-input").validator.validate()))
   {
      alert("Please enter a valid part count.");    
   }
   else if (!(document.getElementById("scrap-count-input").validator.validate()))
   {
      alert("Please enter a valid scrap count.");    
   }
   // J. Orbin requested that users be able enter incomplete time sheets.  (11/21/2019)
   /*
   else if ((document.getElementById("part-count-input").value == 0) &&
            (document.getElementById("scrap-count-input").value == 0))
   {
      alert("Please enter some part counts.");   
   }
   */
   else
   {
      valid = true;
   }
   
   return (valid);   
}

function updateApproval()
{
   var setupTimeHourInput = document.getElementById("setup-time-hour-input");
   var setupTimeMinuteInput = document.getElementById("setup-time-minute-input");  
   
   var requiresApproval = false;
   var isApproved = document.getElementById("approved-by-input").value != 0;
   
   if (setupTimeHourInput.validator.isValid() && 
       setupTimeMinuteInput.validator.isValid())
   {
      var setupTimeMinutes = ((parseInt(setupTimeHourInput.value) * 60) + parseInt(setupTimeMinuteInput.value));
      
      if (setupTimeMinutes > 0)
      {
         requiresApproval = true;
      }
   }
   
   // Start by hiding everything.
   hide("approve-button");
   hide("approved-text");
   hide("unapprove-button");
   hide("unapproved-text");
   
   if (requiresApproval)
   {
      if (isApproved)
      {
         if (userCanApprove())
         {
            show("unapprove-button");
         }

         show("approved-text");
      }
      else
      {
         if (userCanApprove())
         {
            show("approve-button");
         }

         show("unapproved-text");
      }
   }
}

function approve(approvedBy)
{
   document.getElementById("approved-by-input").value = approvedBy;
   
   updateApproval();
}

function unapprove(approvedBy)
{
   document.getElementById("approved-by-input").value = 0;
   
   updateApproval();
}