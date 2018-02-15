function onDeletePartWasherEntry(panWasherEntryId)
{
   if (confirm("Are you sure you want to delete entry?"))
   {
      form = document.createElement('form');
      form.setAttribute('method', 'POST');
      form.setAttribute('action', 'partWasherLog.php');
      
      input = document.createElement('input');
      input.setAttribute('name', 'action');
      input.setAttribute('type', 'hidden');
      input.setAttribute('value', 'delete_part_washer_entry');
      form.appendChild(input);
      
      input = document.createElement('input');
      input.setAttribute('name', 'partWasherEntryId');
      input.setAttribute('type', 'hidden');
      input.setAttribute('value', panWasherEntryId);
      form.appendChild(input);
      
      document.body.appendChild(form);
      form.submit();
   }
}

/*
function onViewPanTicket(panTicketId)
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'panTicket.php');
   
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'view_pan_ticket');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'panTicketId');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', panTicketId);
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();
}

function onEditPanTicket(panTicketId)
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'panTicket.php');
   
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'edit_pan_ticket');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'action');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'edit_pan_ticket');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'panTicketId');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', panTicketId);
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();
}

function onPrintPanTicket(panTicketId)
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'printPanTicketPage.php');
   form.setAttribute("target", "_blank");
   
   input = document.createElement('input');
   input.setAttribute('name', 'panTicketId');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', panTicketId);
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();    
}
*/

function onNewPartWasherEntry()
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'partWasherLog.php');
   
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'select_operator');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'action');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'new_part_washer_entry');
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();  	
}

function onCancel()
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'partWasherLog.php');
   
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'view_part_washer_log');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'action');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'cancel_part_washer_entry');
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

function validatePanTicketId()
{
   valid = false;

   if (!(document.getElementById("pan-ticket-id-input").style.color == "rgb(0, 0, 0)"))
   {
      alert("Please enter a valid pan ticket id.")      
   }
   else
   {
      valid = true;
   }
   
   return (valid);
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

function validatePanTicket()
{
   return (validatePartNumber() && validateMaterialNumber());
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

function validatePartCount()
{
   var valid = false;

   if (!(document.getElementById("panCount-input").validator.validate()))
   {
      alert("Please enter a valid pan count.")
   }
   else if (!(document.getElementById("partCount-input").validator.validate()))
   {
         alert("Please enter a valid part count.");
   }
   else
   {
      valid = true;
   }

   return (valid);
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

function PanTicketIdValidator(inputId)
{
   this.inputId = inputId;
   
   PanTicketIdValidator.prototype.init = function()
   {
      var element = document.getElementById(this.inputId);
      
      if (element)
      {
         element.validator = this;
      }
   }
   
   PanTicketIdValidator.prototype.color = function(color)
   {
      var element = document.getElementById(this.inputId);
      
      if (element)
      {
         element.style.color = color;
      }
   }
   
   PanTicketIdValidator.prototype.validate = function()
   {
      var element = document.getElementById(this.inputId);
      
      if (element)
      {
         var panTicketId = element.value;
      
         requestURl = "validatePanTicket.php?panTicketId=" + panTicketId;
         
         var xhttp = new XMLHttpRequest();
         xhttp.validator = this;
         xhttp.onreadystatechange = function()
         {
            if (this.readyState == 4 && this.status == 200)
            {
               var response = JSON.parse(this.responseText);
               
               validator.onValidationReply(response.panTicketId, response.isValidPanTicket, response.panTicketDiv);
            }
         };
         
         xhttp.open("GET", requestURl, true);
         xhttp.send(); 
      }
   }
   
   PanTicketIdValidator.prototype.onValidationReply = function(panTicketId, isValidPanTicket, panTicketDiv)
   {
      if (isValidPanTicket)
      {
         this.color("#000000");
         
         var element = document.getElementById("pan-ticket-div");
         
         if (element)
         {
            element.innerHTML = panTicketDiv;
         }
      }
      else
      {
         this.color("#FF0000");
         
         var element = document.getElementById("pan-ticket-div");
         
         if (element)
         {
            element.innerHTML = "";
         }
      }
   }
}