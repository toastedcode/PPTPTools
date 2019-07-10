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

function printReport(report)
{
   var employeeNumberInput = document.querySelector('#employeeNumberInput');
   var startDateInput = document.querySelector('#start-date-input');
   var endDateInput = document.querySelector('#end-date-input');
   
   if ((employeeNumberInput != null) && (startDateInput != null) && (endDateInput != null))
   {
	   form = document.createElement('form');
	   form.setAttribute('method', 'POST');
	   form.setAttribute('action', report);
	   form.setAttribute("target", "_blank");
	
	   input = document.createElement('input');
	   input.setAttribute('name', 'employeeNumber');
	   input.setAttribute('type', 'hidden');
	   input.setAttribute('value', employeeNumberInput.selected);
	   form.appendChild(input);
	
	   input = document.createElement('input');
	   input.setAttribute('name', 'startDate');
	   input.setAttribute('type', 'hidden');
	   input.setAttribute('value', startDateInput.value);
	   form.appendChild(input);
	
	   input = document.createElement('input');
	   input.setAttribute('name', 'endDate');
	   input.setAttribute('type', 'hidden');
	   input.setAttribute('value', endDateInput.value);
	   form.appendChild(input);
	   
	   document.body.appendChild(form);
	   form.submit();	
   }
}