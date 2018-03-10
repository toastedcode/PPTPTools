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
      input.setAttribute('name', 'timeCardId');
      input.setAttribute('type', 'hidden');
      input.setAttribute('value', timeCardId);
      form.appendChild(input);
      
      employeeNumber = document.getElementById('employeeNumberInput').value;
      input = document.createElement('input');
      input.setAttribute('name', 'employeeNumber');
      input.setAttribute('type', 'hidden');
      input.setAttribute('value', employeeNumber);
      form.appendChild(input);
      
      startDate = document.getElementById('startDateInput').value;
      input = document.createElement('input');
      input.setAttribute('name', 'startDate');
      input.setAttribute('type', 'hidden');
      input.setAttribute('value', startDate);
      form.appendChild(input);
      
      endDate = document.getElementById('endDateInput').value;
      input = document.createElement('input');
      input.setAttribute('name', 'endDate');
      input.setAttribute('type', 'hidden');
      input.setAttribute('value', endDate);
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
   
   if (radioButtons.length == 0)
   {
      valid = true;
   }
   else
   {
      for (var i = 0; i < radioButtons.length; i++)
      {
         valid |= radioButtons[i].checked;
      }
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

   radioButtons = document.getElementsByName("jobNumber");
   
   if (radioButtons.length == 0)
   {
      valid = true;
   }
   else
   {
      for (var i = 0; i < radioButtons.length; i++)
      {
         valid |= radioButtons[i].checked;
      }
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
      alert("Please enter a valid pan count.")
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