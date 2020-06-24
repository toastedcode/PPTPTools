function onDeleteMaintenanceEntry(maintenanceEntryId)
{
   if (confirm("Are you sure you want to delete this log entry?"))
   {
      // AJAX call to delete part weight entry.
      requestUrl = "../api/deleteMaintenanceEntry/?entryId=" + maintenanceEntryId;
      
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function()
      {
         if (this.readyState == 4 && this.status == 200)
         {
            console.log(this.responseText);
            var json = JSON.parse(this.responseText);
            
            if (json.success == true)
            {
               location.href = "maintenanceLog.php";
            }
            else
            {
               console.log("API call to delete part washer entry failed.");
               alert(json.error);
            }
         }
      };
      xhttp.open("GET", requestUrl, true);
      xhttp.send(); 
   }
}

function onTodayButton()
{
   var today = new Date();
      
   document.querySelector('#maintenance-date-input').value = formattedDate(today); 
}

function onYesterdayButton()
{
   var yesterday = new Date();
   yesterday.setDate(yesterday.getDate() - 1);
   
   document.querySelector('#maintenance-date-input').value = formattedDate(yesterday); 
}

function onSubmit()
{
   if (validateMaintenanceLogEntry())
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
               location.href = "maintenanceLog.php";
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
      requestUrl = "../api/saveMaintenanceEntry/"
      xhttp.open("POST", requestUrl);
   
      // The data sent is what the user provided in the form
      xhttp.send(formData);
   }
}

function onCategoryChange()
{
}

function onMaintenanceTimeChange()
{
   var hours = parseInt(document.getElementById("maintenance-time-hour-input").value);
   var minutes = parseInt(document.getElementById("maintenance-time-minute-input").value);
   
   var maintenanceTime = ((hours * 60) + minutes);
   
   document.getElementById("maintenance-time-input").value = maintenanceTime;
   
   document.getElementById("maintenance-time-minute-input").value = twoDigitNumber(minutes);
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

function twoDigitNumber(value)
{
   return (("0" + value).slice(-2));
}

function formattedDate(date)
{
   // Convert to Y-M-D format, per HTML5 Date control.
   // https://stackoverflow.com/questions/12346381/set-date-in-input-type-date
   var day = ("0" + date.getDate()).slice(-2);
   var month = ("0" + (date.getMonth() + 1)).slice(-2);
   
   var formattedDate = date.getFullYear() + "-" + (month) + "-" + (day);

   return (formattedDate);
}

function validateMaintenanceLogEntry()
{
   valid = false;

   /*
   if (isNaN(Date.parse(document.getElementById("maintenance-date-input").value)))
   {
      alert("Please enter a valid manufacture date.");    
   }
   else if (!(document.getElementById("employee-number-input").validator.validate()))
   {
      alert("Please select an employee.");    
   }
   else if (!(document.getElementById("category-input").validator.validate()))
   {
      alert("Please select a maintenance category.");    
   }
   else if (!(document.getElementById("wc-number-input").validator.validate()))
   {
      alert("Please select a work center.");    
   }
   else if (!(document.getElementById("operator-input").validator.validate()))
   {
      alert("Please select an operator.");    
   }
   else
   */
   {
      valid = true;
   }
   
   return (valid);   
}