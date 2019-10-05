function onSubmit()
{
   if (validateInspection())
   {
      var form = document.querySelector('#input-form');
      
      var xhttp = new XMLHttpRequest();
   
      // Bind the form data.
      var formData = new FormData(form);
   
      // Define what happens on successful data submission.
      xhttp.addEventListener("load", function(event) {
         console.log(this.responseText);
         var json = JSON.parse(event.target.responseText);

         if (json.success == true)
         {
            location.href = "inspections.php";
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
      requestUrl = "../api/saveInspection/"
      xhttp.open("POST", requestUrl);
   
      // The data sent is what the user provided in the form
      xhttp.send(formData);
   }
}

function onDeleteInspection(inspectionId)
{
   if (confirm("Are you sure you want to delete this inspection?"))
   {
      // AJAX call to delete an ispection.
      requestUrl = "../api/deleteInspection/?inspectionId=" + inspectionId;
      
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function()
      {
         if (this.readyState == 4 && this.status == 200)
         {
            var json = JSON.parse(this.responseText);
            
            if (json.success == true)
            {
               location.href = "inspections.php";            
            }
            else
            {
               alert(json.error);
            }
         }
      };
      xhttp.open("GET", requestUrl, true);
      xhttp.send();  
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

function updateTemplateId()
{
   inspectionType = document.getElementById("inspection-type-input").value;
   jobNumber = document.getElementById("job-number-input").value;
   wcNumber = document.getElementById("wc-number-input").value;
   
   if ((inspectionType != "") && (jobNumber != "") && (wcNumber != ""))
   {
      // AJAX call to populate template id based on selected inspection type, job number, and WC number.
      requestUrl = "../api/inspectionTemplate/?inspectionType=" + inspectionType + "&jobNumber=" + jobNumber + "&wcNumber=" + wcNumber;
      console.log(requestUrl);
      
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function()
      {
         if (this.readyState == 4 && this.status == 200)
         {
            var json = JSON.parse(this.responseText);
            
            if (json.success == true)
            {
               console.log("Selecting template id: " + json.templateId);
               document.getElementById("template-id-input").value = json.templateId;
            }
            else
            {
               console.log("API call to retrieve inspection template id failed.");
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

function validateInspection()
{
   valid = true;
   
   /*
   $validTimeCardId = (document.getElementById("time-card-id-input").validator.validate() &&
                       (document.getElementById("time-card-id-input").style.color != "#FF0000"));

   if (!$validTimeCardId)
   {
      alert("Please enter a valid time card ID.");    
   }
   else if (!(document.getElementById("job-number-input").validator.validate()))
   {
      alert("Start by selecting a valid time card ID or active job.");    
   }
   else if (!(document.getElementById("wc-number-input").validator.validate()))
   {
      alert("Please select a work center.");    
   }
   else if (isNaN(Date.parse(document.getElementById("manufacture-date-input").value)))
   {
      alert("Please enter a valid manufacture date.");    
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
   */
   
   return (valid);   
}