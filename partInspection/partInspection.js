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