/*
function onDeleteTimeCard(timeCardId)
{
   if (confirm("Are you sure you want to delete this time card?"))
   {
      form = document.createElement('form');
      form.setAttribute('method', 'POST');
      form.setAttribute('action', 'timeCard.php');
      
      input = document.createElement('input');
      input.setAttribute('name', 'action');
      input.setAttribute('type', 'hidden');
      input.setAttribute('value', 'delete_time_card');
      form.appendChild(input);
      
      input = document.createElement('input');
      input.setAttribute('name', 'view');
      input.setAttribute('type', 'hidden');
      input.setAttribute('value', 'view_time_cards');
      form.appendChild(input);
      
      input = document.createElement('input');
      input.setAttribute('name', 'timeCardId');
      input.setAttribute('type', 'hidden');
      input.setAttribute('value', timeCardId);
      form.appendChild(input);
      
      document.body.appendChild(form);
      form.submit();
   }
}

function onViewTimeCard(timeCardId)
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'timeCard.php');
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'view_time_card');
   form.appendChild(input);
   input = document.createElement('input');
   input.setAttribute('name', 'timeCardId');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', timeCardId);
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();
}

function onEditTimeCard(timeCardId)
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'timeCard.php');
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'edit_time_card');
   form.appendChild(input);
   input = document.createElement('input');
   input.setAttribute('name', 'action');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'edit_time_card');
   form.appendChild(input);
   input = document.createElement('input');
   input.setAttribute('name', 'timeCardId');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', timeCardId);
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();
}

function onPrintTimeCard(timeCardId)
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'printTimeCard.php');
   form.setAttribute("target", "_blank");
   input = document.createElement('input');
   input.setAttribute('name', 'timeCardId');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', timeCardId);
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();    
}

function onNewTimeCard()
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'timeCard.php');
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'select_work_center');
   form.appendChild(input);
   input = document.createElement('input');
   input.setAttribute('name', 'action');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'new_time_card');
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();  	
}

function onCancel()
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'timeCard.php');
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'show_time_cards');
   form.appendChild(input);
   input = document.createElement('input');
   input.setAttribute('name', 'action');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'cancel_time_card');
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();  	
}

function submitForm(form, page, view, action)
{
   //alert(form + ", " + page + ", " + view + ", " + action);
   
   if (!form)
   {
      form = document.createElement('form');
      form.setAttribute('method', 'POST');
      document.body.appendChild(form);
   }
   else
   {
      form = document.getElementById(form);
   }
   
   form.setAttribute('action', page);
   
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', view);
   form.appendChild(input);

   input = document.createElement('input');
   input.setAttribute('name', 'action');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', action);
   form.appendChild(input);
   
   form.submit();
}

function validateOperator()
{
   radioButtons = document.getElementsByName("employeeNumber"); 
   
   var valid = false;
   
   for (var i = 0; i < radioButtons.length; i++)
   {
      valid |= radioButtons[i].checked;
   }
   
   if (!valid)
   {
      alert("Please select an operator.")
   }
   
   return (valid);
}

function validateWorkCenter()
{
   var valid = false;
   
   radioButtons = document.getElementsByName("wcNumber"); 
   
   for (var i = 0; i < radioButtons.length; i++)
   {
      valid |= radioButtons[i].checked;
   }
   
   if (!valid)
   {
      alert("Please select a work center.")
   }
   
   return (valid);
}

function validateJob()
{
   valid = false;

   radioButtons = document.getElementsByName("jobId");
   
   for (var i = 0; i < radioButtons.length; i++)
   {
      valid |= radioButtons[i].checked;
   }
   
   if (!valid)
   {
      alert("Please select a job.")
   }
   
   return (valid);
}

function validateMaterialNumber()
{
   valid = false;

   if (!(document.getElementById("material-number-input").validator.validate()))
   {
      alert("Please enter a valid material number.")      
   }
   else
   {
      valid = true;
   }
   
   return (valid);    
}

function validateTime()
{
   var valid = false;
   
   if (!(document.getElementById("setupTimeHour-input").validator.validate() &&
         document.getElementById("setupTimeMinute-input").validator.validate()))
   {
      alert("Please enter a valid setup time.")
   }
   else if (!(document.getElementById("runTimeHour-input").validator.validate() &&
              document.getElementById("runTimeMinute-input").validator.validate()))
   {
      alert("Please enter a valid run time.")      
   }
   else if ((document.getElementById("setupTimeHour-input").value == 0) &&
		    (document.getElementById("setupTimeMinute-input").value == 0) &&
		    (document.getElementById("runTimeHour-input").value == 0) &&
		    (document.getElementById("runTimeMinute-input").value == 0))
   {
	   alert("Please enter some valid times.")  
   }
   else
   {
      valid = true;
   }
   
   return (valid);
}

function validatePartCount()
{
   var valid = false;

   if (!(document.getElementById("panCount-input").validator.validate()))
   {
      alert("Please enter a valid basket count.")
   }
   else
   {
      partsCountInput = document.getElementById("partsCount-input");
      scrapCountInput = document.getElementById("scrapCount-input");
      
      totalCount = parseInt(partsCountInput.value) + parseInt(scrapCountInput.value);
      
      if (!partsCountInput.validator.validate())
      {
         alert("Please enter a valid good parts count.");
      }
      else if (!scrapCountInput.validator.validate())
      {
         alert("Please enter a valid scrap count.");
      }
      else if (totalCount == 0)
      {
         alert("Please enter some part counts.");
      }
      else
      {
         valid = true;
      }
   }

   return (valid);
}

function validateCard()
{
   return (validateMaterialNumber() && validateTime() && validatePartCount());
}

function changeSetupTimeHour(delta)
{
   var newValue = 0;
	
   var field = document.querySelector('#setupTimeHour-input');
   
   if ((field.value == null) || (field.value == ""))
   {
      newValue = (delta < 0) ? 0 : delta;
   }
   else
   {
      newValue = parseInt(field.value, 10) + delta;
   }
   
   // Constrain values.
   newValue = Math.max(0, Math.min(newValue, 10));
   
   field.value = newValue;
   
   if (field.validator != null)
   {
      field.validator.validate();
   }
   
   autoFillTotalTime();
}

function changeSetupTimeMinute(delta)
{
   var newValue = 0;
   
   var field = document.querySelector('#setupTimeMinute-input');
   
   if ((field.value == null) || (field.value == ""))
   {
      newValue = (delta < 0) ? 0 : delta;
   }
   else
   {
      newValue = parseInt(field.value, 10) + delta;
   }
   
   // Constrain values.
   newValue = Math.max(0, Math.min(newValue, 45));
   
   field.value = newValue;
   
   if (field.validator != null)
   {
      field.validator.validate();
   }
   
   autoFillTotalTime();
}

function changeRunTimeHour(delta)
{
   var newValue = 0;
   
   var field = document.querySelector('#runTimeHour-input');
   
   if ((field.value == null) || (field.value == ""))
   {
      newValue = (delta < 0) ? 0 : delta;
   }
   else
   {
      newValue = parseInt(field.value, 10) + delta;
   }
   
   // Constrain values.
   newValue = Math.max(0, Math.min(newValue, 10));
   
   field.value = newValue;
   
   if (field.validator != null)
   {
      field.validator.validate();
   }
   
   autoFillTotalTime();
}

function changeRunTimeMinute(delta)
{
   var newValue = 0;
   
   var field = document.querySelector('#runTimeMinute-input');
   
   if ((field.value == null) || (field.value == ""))
   {
      newValue = (delta < 0) ? 0 : delta;
   }
   else
   {
      newValue = parseInt(field.value, 10) + delta;
   }
   
   // Constrain values.
   newValue = Math.max(0, Math.min(newValue, 45));
   
   field.value = newValue;
   
   if (field.validator != null)
   {
      field.validator.validate();
   }
   
   autoFillTotalTime();
}

function filterToday()
{
   var startDateInput = document.querySelector('#startDateInput');
   var endDateInput = document.querySelector('#endDateInput');
   
   if ((startDateInput != null) && (endDateInput != null))
   {
      var today = new Date();
      
      startDateInput.value = formattedDate(today); 
      endDateInput.value = formattedDate(today);
   }
}

function filterYesterday()
{
   var startDateInput = document.querySelector('#startDateInput');
   var endDateInput = document.querySelector('#endDateInput');
   
   if ((startDateInput != null) && (endDateInput != null))
   {
      var yesterday = new Date();
      yesterday.setDate(yesterday.getDate() - 1);
      
      startDateInput.value = formattedDate(yesterday); 
      endDateInput.value = formattedDate(yesterday);
   }
}

function filterThisWeek()
{
   var startDateInput = document.querySelector('#startDateInput');
   var endDateInput = document.querySelector('#endDateInput');
   
   if ((startDateInput != null) && (endDateInput != null))
   {
      var today = new Date();
      var startOfWeek = new Date();
      startOfWeek.setDate(today.getDate() - today.getDay());
      
      startDateInput.value = formattedDate(startOfWeek); 
      endDateInput.value = formattedDate(today);
   }
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

function autoFillEfficiency()
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
   }
}

function autoFillTotalTime()
{
   var runTimeHourInput = document.getElementById("runTimeHour-input");
   var runTimeMinuteInput = document.getElementById("runTimeMinute-input");
   var setupTimeHourInput = document.getElementById("setupTimeHour-input");
   var setupTimeMinuteInput = document.getElementById("setupTimeMinute-input");
   var totalTimeHourInput = document.getElementById("totalTimeHour-input");
   var totalTimeMinuteInput = document.getElementById("totalTimeMinute-input");
   
   if (setupTimeHourInput.validator.isValid() && 
       setupTimeMinuteInput.validator.isValid() &&
       runTimeHourInput.validator.isValid() &&
       runTimeMinuteInput.validator.isValid())
   {
      var runTimeMinutes = ((parseInt(runTimeHourInput.value) * 60) + parseInt(runTimeMinuteInput.value));
      var setupTimeMinutes = ((parseInt(setupTimeHourInput.value) * 60) + parseInt(setupTimeMinuteInput.value));
      
      var totalTimeMinutes = runTimeMinutes + setupTimeMinutes;
        
      totalTimeHourInput.value = Math.floor(totalTimeMinutes / 60);
      totalTimeMinuteInput.value = (totalTimeMinutes % 60);
   }
   else
   {
      totalTimeHourInput.value = 0;
      totalTimeMinuteInput.value = 0;
   }
}

function updateApproval()
{
   var setupTimeHourInput = document.getElementById("setup-time-hour-input");
   var setupTimeMinuteInput = document.getElementById("setup-time-minute-input");  
   var approvedByInput = document.getElementById("approved-by-input");
   
   var approval = "no-approval-required";
   
   if (setupTimeHourInput.validator.isValid() && 
       setupTimeMinuteInput.validator.isValid())
   {
      var setupTimeMinutes = ((parseInt(setupTimeHourInput.value) * 60) + parseInt(setupTimeMinuteInput.value));
      
      if (setupTimeMinutes > 0)
      {
         approval = (parseInt(approvedByInput.value) > 0) ? "approved" : "unapproved";
      }
   }
   
   setApproval("setupTimeHour-input", approval);
   setApproval("setupTimeMinute-input", approval);
   setApproval("approval-div", approval);
   setApproval("unapproval-div", approval);
   setApproval("approve-button", approval);
   setApproval("unapprove-button", approval);
}

function setApproval(elementId, approval)
{
   var element = document.getElementById(elementId);
   
   if (element)
   {
      // Clear existing class tags.
      element.classList.remove('approved');
      element.classList.remove('unapproved');
      element.classList.remove('no-approval-required');
      
      // Set new approval tag.
      element.classList.add(approval);
   }   
}

function approve(approvedBy)
{
   var approvedByInput = document.getElementById("approvedBy-input");
   
   approvedByInput.value = approvedBy;
   
   updateApproval();
}

function unapprove(approvedBy)
{
   var approvedByInput = document.getElementById("approvedBy-input");
   
   approvedByInput.value = 0;
   
   updateApproval();
}
*/

// *****************************************************************************

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
   else if ((document.getElementById("setup-time-hour-input").value == 0) &&
            (document.getElementById("setup-time-minute-input").value == 0) &&
            (document.getElementById("run-time-hour-input").value == 0) &&
            (document.getElementById("run-time-minute-input").value == 0))
   {
      alert("Please enter some valid times.")  
   }
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
   else if ((document.getElementById("part-count-input").value == 0) &&
            (document.getElementById("scrap-count-input").value == 0))
   {
      alert("Please enter some part counts.");   
   }
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
   var approvedByInput = document.getElementById("approved-by-input");
   
   var approval = "no-approval-required";
   
   if (setupTimeHourInput.validator.isValid() && 
       setupTimeMinuteInput.validator.isValid())
   {
      var setupTimeMinutes = ((parseInt(setupTimeHourInput.value) * 60) + parseInt(setupTimeMinuteInput.value));
      
      if (setupTimeMinutes > 0)
      {
         approval = (parseInt(approvedByInput.value) > 0) ? "approved" : "unapproved";
      }
   }
   
   setApproval("setup-time-hour-input", approval);
   setApproval("setup-time-minute-input", approval);
   setApproval("approval-div", approval);
   setApproval("unapproval-div", approval);
   setApproval("approve-button", approval);
   setApproval("unapprove-button", approval);
}

function setApproval(elementId, approval)
{
   var element = document.getElementById(elementId);
   
   if (element)
   {
      // Clear existing class tags.
      element.classList.remove('approved');
      element.classList.remove('unapproved');
      element.classList.remove('no-approval-required');
      
      // Set new approval tag.
      element.classList.add(approval);
   }   
}

function approve(approvedBy)
{
   var approvedByInput = document.getElementById("approved-by-input");
   
   approvedByInput.value = approvedBy;
   
   updateApproval();
}

function unapprove(approvedBy)
{
   var approvedByInput = document.getElementById("approvedBy-input");
   
   approvedByInput.value = 0;
   
   updateApproval();
}