function filterToday()
{
   var startDateInput = document.querySelector('#start-date-input');
   var endDateInput = document.querySelector('#end-date-input');
   
   if ((startDateInput != null) && (endDateInput != null))
   {
      var today = new Date();
      
      startDateInput.value = formattedDate(today); 
      endDateInput.value = formattedDate(today);
   }
}

function filterYesterday()
{
   var startDateInput = document.querySelector('#start-date-input');
   var endDateInput = document.querySelector('#end-date-input');
   
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
   var startDateInput = document.querySelector('#start-date-input');
   var endDateInput = document.querySelector('#end-date-input');
   
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

/*
function printReport(report)
{
   // TODO: This function assumes the input id for various filter componets, including some that are not even defined
   //       in this file.  Rework to make this more agnostic to what components are part of this filter.
   
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', report);
   form.setAttribute("target", "_blank");

   // Employee number
   var employeeNumberInput = document.querySelector('#employeeNumberInput');
   if (employeeNumberInput != null)
   {
	   input = document.createElement('input');
	   input.setAttribute('name', 'employeeNumber');
	   input.setAttribute('type', 'hidden');
	   input.setAttribute('value', employeeNumberInput.value);
	   form.appendChild(input);
   }
   
   // Start date
   var startDateInput = document.querySelector('#start-date-input');
   if (startDateInput != null)
   {
	   input = document.createElement('input');
	   input.setAttribute('name', 'startDate');
	   input.setAttribute('type', 'hidden');
	   input.setAttribute('value', startDateInput.value);
	   form.appendChild(input);
   }
   
   // End date
   var endDateInput = document.querySelector('#end-date-input');
   if (endDateInput != null)
   {
	   input = document.createElement('input');
	   input.setAttribute('name', 'endDate');
	   input.setAttribute('type', 'hidden');
	   input.setAttribute('value', endDateInput.value);
	   form.appendChild(input);	   
   }
   
   // Only active.
   // Note: For ViewJobs filter.
   var onlyActiveInput = document.querySelector('#only-active-input');
   if (onlyActiveInput != null)
   {
      input = document.createElement('input');
      input.setAttribute('name', 'onlyActive');
      input.setAttribute('type', 'hidden');
      input.setAttribute('value', onlyActiveInput.checked);
      form.appendChild(input);	       
   }
   
   document.body.appendChild(form);
   form.submit();	
}
*/

function printReport(report)
{
   form = document.getElementById("filter-form");
   if (form)
   {
      form.setAttribute('method', 'POST');
      form.setAttribute('action', report);
      form.setAttribute("target", "_blank");
      
      form.submit(); 
      
      form.setAttribute('action', "");
      form.setAttribute("target", "");
   } 
}