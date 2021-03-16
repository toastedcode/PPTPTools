function onSaveMaintenanceEntry()
{
   if (validateMaintenanceEntry())
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
      xhttp.open("POST", requestUrl, true);
   
      // The data sent is what the user provided in the form
      xhttp.send(formData);
   }
}

function onCancel()
{
   if (!isFormChanged("input-form") ||
       confirm("Are you sure?  All data will be lost."))
   {
      window.history.back();
   }
}

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
            var json = JSON.parse(this.responseText);
            
            if (json.success == true)
            {
               location.href = "maintenanceLog.php";
            }
            else
            {
               console.log("API call to delete maintenance entry failed.");
               alert(json.error);
            }
         }
      };
      xhttp.open("GET", requestUrl, true);
      xhttp.send(); 
   }
}

function onMaintenanceTypeChange()
{
   var maintenanceTypeId = parseInt(document.getElementById("maintenance-type-input").value);
   
   hide("repair-type-block");
   hide("preventative-type-block");
   hide("cleaning-type-block");
   
   disable("repair-type-input");
   disable("preventative-type-input");
   disable("cleaning-type-input");
   
   disable("part-number-input");
   disable("new-part-number-input");
   disable("new-part-description-input");
   
   switch (maintenanceTypeId)
   {
      case 1:
      {
         show("repair-type-block", "block");
         enable("repair-type-input");
         
         enable("part-number-input", "block");
         enable("new-part-number-input");
         enable("new-part-description-input");
         break;
      }
      
      case 2:
      {
         show("preventative-type-block", "block");
         enable("preventative-type-input");
         break;
      }
      
      case 3:
      {
         show("cleaning-type-block", "block");
         enable("cleaning-type-input");
         break;
      }
      
      default:
      {
         break;
      }      
   }
}

function onMaintenanceTimeChange()
{
   var hours = parseInt(document.getElementById("maintenance-time-hour-input").value);
   var minutes = parseInt(document.getElementById("maintenance-time-minute-input").value);
   
   var maintenanceTime = ((hours * 60) + minutes);
   
   document.getElementById("maintenance-time-input").value = maintenanceTime;
   
   document.getElementById("maintenance-time-minute-input").value = formatToTwoDigits(minutes);
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

function onJobNumberChange()
{
   jobNumber = document.getElementById("job-number-input").value;
   console.log(jobNumber);
   
   clear("wc-number-input");
      
   // Populate WC numbers based on selected job number.
   
   // AJAX call to populate WC numbers based on selected job number.
   requestUrl = "../api/wcNumbers/";
   if (jobNumber != 0)
   {
      requestUrl += "?jobNumber=" + jobNumber;
   }
   
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

function onPartNumberChange()
{
   document.getElementById("new-part-number-input").value = null;
   document.getElementById("new-part-description-input").value = null;
}

function onNewPartNumberChange()
{
   document.getElementById("part-number-input").selectedIndex = -1;
   document.getElementById("part-number-input").value = null;
}

function onExistingPartButton()
{
   hide("new-part-number-block");
   show("part-number-block", "flex"); 
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

function validateMaintenanceEntry()
{
   valid = false;
   
   var maintenanceType = parseInt(document.getElementById("maintenance-type-input").value);
   
   if (isNaN(Date.parse(document.getElementById("maintenance-date-input").value)))
   {
      alert("Please enter a valid maintenance date.");    
   }
   else if ((document.getElementById("maintenance-time-hour-input").value == 0) &&
            (document.getElementById("maintenance-time-minute-input").value == 0))
   {
      alert("Please enter some valid maintenance time.")  
   }
   else if (!(document.getElementById("employee-number-input").validator.validate()))
   {
      alert("Please select a technician.");    
   }
   else if (!(document.getElementById("wc-number-input").validator.validate()))
   {
      alert("Please select a work center.");    
   }
   else if (!(document.getElementById("maintenance-type-input").validator.validate()))
   {
      alert("Please select a maintenance type.");    
   }
   else if (((maintenanceType == 1) &&
             !(document.getElementById("repair-type-input").validator.validate())) ||
            ((maintenanceType == 2) &&
             !(document.getElementById("preventative-type-input").validator.validate())) ||            
            ((maintenanceType == 3) &&
             !(document.getElementById("cleaning-type-input").validator.validate())))             
   {
      alert("Please complete the maintenance type.");    
   }
   else if (((document.getElementById("new-part-number-input").value != "") &&
             (document.getElementById("new-part-description-input").value == "")) ||
            ((document.getElementById("new-part-description-input").value != "") &&
             (document.getElementById("new-part-number-input").value == "")))
   
   {
      alert("Please complete all new part info.");    
   }
   else
   {
      valid = true;
   }
   
   return (valid);     
}