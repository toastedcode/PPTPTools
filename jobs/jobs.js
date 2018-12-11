function onNewJob()
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'jobs.php');
   
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'new_job');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'action');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'new_job');
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();    
}

function onDeleteJob(jobId)
{
   if (confirm("Are you sure you want to delete this job?"))
   {
      form = document.createElement('form');
      form.setAttribute('method', 'POST');
      form.setAttribute('action', 'jobs.php');
      
      input = document.createElement('input');
      input.setAttribute('name', 'action');
      input.setAttribute('type', 'hidden');
      input.setAttribute('value', 'delete_job');
      form.appendChild(input);
      
      input = document.createElement('input');
      input.setAttribute('name', 'jobId');
      input.setAttribute('type', 'hidden');
      input.setAttribute('value', jobId);
      form.appendChild(input);
      
      document.body.appendChild(form);
      form.submit();
   }
}

function onViewJob(jobId)
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'jobs.php');
   
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'view_job');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'jobId');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', jobId);
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();
}

function onEditJob(jobId)
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'jobs.php');
   
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'edit_job');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'action');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'edit_job');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'jobId');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', jobId);
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();
}

function validateJob()
{
   valid = false;

   if (!((document.getElementById("job-number-prefix-input").validator.validate()) &&
         (document.getElementById("job-number-suffix-input").validator.validate())))
   {
      alert("Please enter a valid job number.  (Format: Mxxxx-xxxx)");      
   }
   else if (!(document.getElementById("cycle-time-input").validator.validate()))
   {
      alert("Please enter a valid cycle time.");
   }
   else if (!(document.getElementById("net-percentage-input").validator.validate()))
   {
      alert("Please enter a valid net percentage.");
   }
   else
   {
      valid = true;
   }
   
   return (valid);
}

function autoFillPartNumber()
{
   jobNumberPrefixInput = document.getElementById('job-number-prefix-input');
   partNumberInput = document.getElementById('part-number-input');
   partNumberDisplayInput = document.getElementById('part-number-display-input');
   
   if (jobNumberPrefixInput && partNumberInput && partNumberDisplayInput)
   {
      partNumberInput.value = jobNumberPrefixInput.value;
      partNumberDisplayInput.value = jobNumberPrefixInput.value;
   }
   
   autoFillJobNumber();
}

function autoFillJobNumber()
{
   jobNumberPrefixInput = document.getElementById('job-number-prefix-input');
   jobNumberSuffixInput = document.getElementById('job-number-suffix-input');
   jobNumberInput = document.getElementById('job-number-input');
   
   if (jobNumberPrefixInput && jobNumberSuffixInput && jobNumberInput)
   {
      jobNumberInput.value = jobNumberPrefixInput.value + "-" + jobNumberSuffixInput.value;
   }
}

function autoFillPartStats()
{
   var cycleTimeInput = document.getElementById('cycle-time-input');
   var netPartsPerHourInput = document.getElementById('net-parts-per-hour-input');
   var grossPartsPerHourInput = document.getElementById('gross-parts-per-hour-input');
   var netPercentageInput = document.getElementById('net-percentage-input');
   
   if (cycleTimeInput.validator.validate())
   {
      var cycleTime = parseFloat(cycleTimeInput.value);
      var grossPartsPerHour = 0;
      
      if ((cycleTime > 0) && (cycleTime <= 60))
      {
         grossPartsPerHour = (3600 / cycleTime);
      }

      grossPartsPerHourInput.value = grossPartsPerHour.toFixed(2);
      
      if (netPercentageInput.validator.validate())
      {
         var netPercentage = parseFloat(netPercentageInput.value);
         
         var netPartsPerHour = (grossPartsPerHour * (netPercentage / 100));
         
         netPartsPerHourInput.value = netPartsPerHour.toFixed(2);
      }
      else
      {
         netPartsPerHourInput.value = "";
      }
   }
   else
   {
      grossPartsPerHourInput.value = "";
      netPartsPerHourInput.value = "";
   }
}

function PartNumberPrefixValidator(inputId, maxLength, minValue, maxValue, allowNull)
{
   this.inputId = inputId;
   this.minValue = minValue;
   this.maxValue = maxValue;
   this.maxLength = maxLength;
   this.allowNull = allowNull;
   
   PartNumberPrefixValidator.prototype.init = function()
   {
      var element = document.getElementById(this.inputId);
      
      if (element)
      {
         element.maxLength = this.maxLength;
         
         element.validator = this;
         
         this.validate();
      }
   }
   
   PartNumberPrefixValidator.prototype.isValid = function()
   {
      var valid = false;
   
      var element = document.getElementById(this.inputId);
      
      if (element)
      {
         var value = element.value;
         
         if ((value == null) || (value == "")) 
         {
            valid = this.allowNull;
         }
         else
         {
            var firstChar = element.value.charAt(0);
            
            var remainingChar = element.value.substring(1);
            
            valid = ((firstChar == 'M') &&
                     (!isNaN(remainingChar)) && 
                     (parseInt(remainingChar) >= this.minValue) && 
                     (parseInt(remainingChar) <= this.maxValue));
         }
      }
      
      return (valid);
   }
   
   PartNumberPrefixValidator.prototype.color = function(color)
   {
      var element = document.getElementById(this.inputId);
      
      if (element)
      {
         element.style.color = color;
      }
   }
   
   PartNumberPrefixValidator.prototype.validate = function()
   {
      var valid = this.isValid();
      
      if (valid)
      {
         this.color("#000000");
      }
      else
      {
         this.color("#FF0000");
      }

      return (valid);
   }
}

function PartNumberSuffixValidator(inputId, maxLength, minValue, maxValue, allowNull)
{
   const MAX_DIGITS = 2;
   
   this.inputId = inputId;
   this.minValue = minValue;
   this.maxValue = maxValue;
   this.maxLength = maxLength;
   this.allowNull = allowNull;
   
   PartNumberSuffixValidator.prototype.init = function()
   {
      var element = document.getElementById(this.inputId);
      
      if (element)
      {
         element.maxLength = this.maxLength;
         
         element.validator = this;
         
         this.validate();
      }
   }
   
   PartNumberSuffixValidator.prototype.isValid = function()
   {
      var valid = false;
   
      var element = document.getElementById(this.inputId);
      
      if (element)
      {
         var value = element.value;
         
         var lastChar = "";
         var remainingChar = value;
         if (value.length > MAX_DIGITS)
         {
            lastChar = element.value.charAt(value.length - 1);
            remainingChar = element.value.substring(0, (value.length - (MAX_DIGITS - 1)));
         }
         
         if ((value == null) || (value == "")) 
         {
            valid = this.allowNull;
         }
         else
         {
            valid = (((lastChar == "") || (lastChar.toUpperCase().match(/[A-Z]/i))) && 
                     (parseInt(remainingChar) >= this.minValue) && 
                     (parseInt(remainingChar) <= this.maxValue));
         }
      }
      
      return (valid);
   }
   
   PartNumberSuffixValidator.prototype.color = function(color)
   {
      var element = document.getElementById(this.inputId);
      
      if (element)
      {
         element.style.color = color;
      }
   }
   
   PartNumberSuffixValidator.prototype.validate = function()
   {
      var valid = this.isValid();
      
      if (valid)
      {
         this.color("#000000");
      }
      else
      {
         this.color("#FF0000");
      }

      return (valid);
   }
}