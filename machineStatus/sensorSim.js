function ping()
{
   var sensorId = document.getElementById("sensor-id-input").value;

   var xhttp = new XMLHttpRequest();

   xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200)
      {
         console.log(this.responseText);
      }
   };

   var url = "sensor.php?sensorId=" + sensorId + "&action=ping";
   console.log(url);
   
   xhttp.open("GET", url, true);
   xhttp.send();
}

function count(partCount)
{
   var sensorId = document.getElementById("sensor-id-input").value;
   
   var xhttp = new XMLHttpRequest();

   xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200)
      {
         console.log(this.responseText);
      }
   };

   var url = "sensor.php?sensorId=" + sensorId + "&action=count&count=" + partCount;
   console.log(url);

   xhttp.open("GET", url, true);
   xhttp.send();
}
