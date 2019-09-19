function onTimeCardIdChange()
{
   timeCardId = parseInt(document.getElementById("time-card-id-input").value);
   
   if (isNaN(timeCardId) || (timeCardId == 0))
   {
      // Clear fields.
      clear("job-number-input");
      clear("wc-number-input");
      clear("manufacture-date-input");
      clear("operator-input");
      
      // Enable fields.
      enable("job-number-input");
      enable("manufacture-date-input");
      enable("today-button");
      enable("yesterday-button");
      enable("operator-input");
      
      // Disable WC number, as it's dependent on the job number.
      disable("wc-number-input");
   }
   else
   {
      // Disable fields.
      disable("job-number-input");
      disable("wc-number-input");
      disable("manufacture-date-input");
      disable("today-button");
      disable("yesterday-button");
      disable("operator-input");
      
      // AJAX call to populate input fields based on time card selection.
      requestUrl = "../api/timeCardInfo/?timeCardId=" + timeCardId + "&expandedProperties=true";
      
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function()
      {
         if (this.readyState == 4 && this.status == 200)
         {
            console.log(this.responseText);
            var json = JSON.parse(this.responseText);
            
            if (json.success == true)
            {
               updateTimeCardInfo(json.timeCardInfo, json.jobNumber, json.wcNumber);                   
            }
            else
            {
               console.log("API call to retrieve time card info failed.");
               
               // Clear fields.
               clear("job-number-input");
               clear("wc-number-input");
               clear("manufacture-date-input");
               clear("operator-input");
            }
         }
      };
      xhttp.open("GET", requestUrl, true);
      xhttp.send();      
   }
}

function onJobNumberChange()
{
   jobNumber = document.getElementById("job-number-input").value;
   
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
      };
      xhttp.open("GET", requestUrl, true);
      xhttp.send();  
   }
}

function onTodayButton()
{
   var today = new Date();
      
   document.querySelector('#manufacture-date-input').value = formattedDate(today); 
}

function onYesterdayButton()
{
   var yesterday = new Date();
   yesterday.setDate(yesterday.getDate() - 1);
   
   document.querySelector('#manufacture-date-input').value = formattedDate(yesterday); 
}

function onSubmit()
{
   if (validatePartWasherEntry())
   {
      var form = document.querySelector('#input-form');
      
      var xhttp = new XMLHttpRequest();
   
      // Bind the form data.
      var formData = new FormData(form);
   
      // Define what happens on successful data submission.
      xhttp.addEventListener("load", function(event) {
         var json = JSON.parse(event.target.responseText);
         
         if (json.success == true)
         {
            location.href = "partWasherLog.php?view=view_part_washer_log";
         }
         else
         {
            alert(json.error);
         }
      });
   
      // Define what happens on successful data submission.
      xhttp.addEventListener("error", function(event) {
        alert('Oops! Something went wrong.');
      });
   
      // Set up our request
      requestUrl = "../api/savePartWasherEntry/"
      xhttp.open("POST", requestUrl);
   
      // The data sent is what the user provided in the form
      xhttp.send(formData);
   }
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

function twoDigitNumber(value)
{
   return (("0" + value).slice(-2));
}

function updateTimeCardInfo(timeCardInfo, jobNumber, wcNumber)
{
   var operator = timeCardInfo.employeeNumber;
   var date = new Date(timeCardInfo.dateTime);
   var manufactureDate = formattedDate(date);
   
   // TODO: Add missing options.
   //updateJobNumberOptions(jobNumber);
   //updateOperatorOptions(operator);
   //updateWcNumberOptions(wcNumber);
   
   set("job-number-input", jobNumber);
   set("wc-number-input", wcNumber);
   set("operator-input", operator);
   set("manufacture-date-input", manufactureDate);
   
   onJobNumberChange();
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

function validatePartWasherEntry()
{
   valid = false;

   /*
   if (!validateTimeCardId())
   {
      alert("Please enter a valid time card ID.");      
   }
   */
   if (!(document.getElementById("job-number-input").validator.validate()))
   {
      alert("Please select a job.");    
   }
   else if (!(document.getElementById("wc-number-input").validator.validate()))
   {
      alert("Please select a work center.");    
   }
   else if (!(document.getElementById("operator-input").validator.validate()))
   {
      alert("Please select an operator.");    
   }
   else if (!(document.getElementById("part-washer-input").validator.validate()))
   {
      alert("Please select a part washer.");    
   }
   else if (!(document.getElementById("pan-count-input").validator.validate()))
   {
      alert("Please enter a valid pan count.");
   }
   else if (!(document.getElementById("part-count-input").validator.validate()))
   {
      alert("Please enter a valid part count.");
   }
   else
   {
      valid = true;
   }
   
   return (valid);   
}