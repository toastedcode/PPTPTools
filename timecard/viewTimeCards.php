<?php

require_once '../common/authentication.php';
require_once '../common/database.php';
require_once '../common/header.php';
require_once '../common/jobInfo.php';
require_once '../common/newIndicator.php';
require_once '../common/permissions.php';
require_once '../common/roles.php';
require_once '../common/timeCardInfo.php';
require_once '../common/userInfo.php';

function getFilterStartDate()
{
   $startDate = Time::now("Y-m-d");
   
   if (isset($_SESSION["timeCard.filter.startDate"]))
   {
      $startDate = $_SESSION["timeCard.filter.startDate"];
   }

   // Convert to Javascript date format.
   $dateTime = new DateTime($startDate, new DateTimeZone('America/New_York'));  // TODO: Replace
   $startDate = $dateTime->format(Time::$javascriptDateFormat);
   
   return ($startDate);
}

function getFilterEndDate()
{
   $startDate = Time::now("Y-m-d");
   
   if (isset($_SESSION["timeCard.filter.endDate"]))
   {
      $startDate = $_SESSION["timeCard.filter.endDate"];
   }
   
   // Convert to Javascript date format.
   $dateTime = new DateTime($startDate, new DateTimeZone('America/New_York'));  // TODO: Replace
   $startDate = $dateTime->format(Time::$javascriptDateFormat);
   
   return ($startDate);
}

// ********************************** BEGIN ************************************

Time::init();

session_start();

if (!Authentication::isAuthenticated())
{
   header('Location: ../home.php');
   exit;
}

// Post/Redirect/Get idiom.
// getFilter() stores all $_POST data in the $_SESSION variable.
// header() redirects to this page, but with a GET request.
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
   // Redirect to this page.
   header("Location: " . $_SERVER['REQUEST_URI']);
   exit();
}
?>

<!DOCTYPE html>
<html>

<head>

   <meta name="viewport" content="width=device-width, initial-scale=1">
   
   <link rel="stylesheet" type="text/css" href="../common/flex.css"/>
   <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>
   <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-blue.min.css"/>
   <link rel="stylesheet" type="text/css" href="../thirdParty/tabulator/css/tabulator.min.css"/>
   <link rel="stylesheet" type="text/css" href="../common/common.css"/>
   <link rel="stylesheet" type="text/css" href="../common/tooltip.css"/>
   <link rel="stylesheet" type="text/css" href="timeCard.css"/>
   
   <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
   <script src="../thirdParty/tabulator/js/tabulator.min.js"></script>
   <script src="../thirdParty/moment/moment.min.js"></script>
   <script src="../common/common.js"></script>
   <script src="../common/validate.js"></script>
   <script src="timeCard.js"></script>

</head>

<body>

   <?php Header::render("PPTP Tools"); ?>
   
   <div class="flex-horizontal main">
   
   <style>
      .menu a {
         margin: 10px 10px 10px 10px;
         display: flex;
         flex-direction: row;
         align-items:flex-start;
         justify-content: flex-start;
      }
   </style>
   
   <div class="menu flex-vertical" style="justify-content: flex-start; margin-right:20px;">
      <a href="#"><i class="material-icons action-button-icon">assignment</i><div>Time Card</div></a>
      <a href="#"><i class="material-icons action-button-icon">schedule</i><div>Jobs</div></a>
      <a href="#"><i class="material-icons action-button-icon">fingerprint</i><div>Weight Log</div></a>
      <a href="#"><i class="material-icons action-button-icon">opacity</i><div>Inspections</div></a>
      <a href="#"><i class="material-icons action-button-icon">format_list_bulleted</i><div>Inspection Templates</div></a>
   </div>
     
     <div class="flex-vertical content" style="margin-top: 10px;">
     
        <!-- div id="back-button"><i class="material-icons table-function-button">arrow_back</i></div-->
        
        <br>

        <div class="heading">Time Cards</div>

        <div class="description">Time cards record the time a machine operator spends working on a job, as well as a part count for that run.</div>

        <div class="flex-vertical inner-content">
           
           <div class="flex-horizontal">
              <div>Start date</div>
              &nbsp;
              <input id="start-date-filter" type="date" value="<?php echo getFilterStartDate()?>">
              &nbsp;&nbsp;
              <div>End date</div>
              &nbsp;
              <input id="end-date-filter" type="date" value="<?php echo getFilterEndDate()?>">
              &nbsp;&nbsp;
              <button id="today-button">Today</button>
              &nbsp;&nbsp;
              <button id="yesterday-button">Yesterday</button>
           </div>
           
           <br>
           
           <button id="new-time-card-button">New Time Card</button>

           <br> 
           
           <div id="time-card-table"></div>

           <br> 
           
           <button id="download-button">Download CSV</button>
      
        </div>
         
     </div>
     
   </div>
   
   <script>
      preserveSession();

      function getTableQuery()
      {
         return ("<?php echo $ROOT ?>/api/timeCardData/");
      }

      function getTableQueryParams()
      {
         
         var params = new Object();
         params.startDate =  document.getElementById("start-date-filter").value;
         params.endDate =  document.getElementById("end-date-filter").value;

         return (params);
      }

      var url = getTableQuery();
      var params = getTableQueryParams();
      
      // Create Tabulator on DOM element time-card-table.
      var table = new Tabulator("#time-card-table", {
         height:350, // set height of table (in CSS or here), this enables the Virtual DOM and improves render speed dramatically (can be any valid css height value)
         layout:"fitData",
         ajaxURL:url,
         ajaxParams:params,
         //Define Table Columns
         columns:[
            {title:"Id",           field:"timeCardId",     hozAlign:"left"},
            {title:"Date",         field:"dateTime",       hozAlign:"left", 
               formatter:"datetime",  // Requires moment.js 
               formatterParams:{
                  outputFormat:"MM/DD/YYYY",
                  invalidPlaceholder:"---"
               }
            },
            {title:"Operator",     field:"operator",       hozAlign:"left", headerFilter:true},
            {title:"Job #",        field:"jobNumber",      hozAlign:"left", headerFilter:true},
            {title:"Machine #",    field:"wcNumber",       hozAlign:"left", headerFilter:true},
            {title:"Heat #",       field:"materialNumber", hozAlign:"left"},
            {title:"Run Time",     field:"runTime",        hozAlign:"left"},
            {title:"Setup Time",   field:"setupTime",      hozAlign:"left"},
            {title:"Basket Count", field:"panCount",       hozAlign:"left"},
            {title:"Part Count",   field:"partCount",      hozAlign:"left"},
            {title:"Scrap Count",  field:"scrapCount",     hozAlign:"left"},
            {title:"Efficiency",   field:"efficiency",     hozAlign:"left", 
               formatter:function(cell, formatterParams, onRendered){
                  return (parseFloat(cell.getValue()).toFixed(2) + "%");
                }
            },
            {title:"", field:"panTicket",                
               formatter:function(cell, formatterParams, onRendered){
                  return ("<i class=\"material-icons table-function-button\">receipt</i>");
               }
            },
            {title:"", field:"delete",                
               formatter:function(cell, formatterParams, onRendered){
                  return ("<i class=\"material-icons table-function-button\">delete</i>");
               }
            }
         ],
         cellClick:function(e, cell){
            var timeCardId = parseInt(cell.getRow().getData().timeCardId);
            console.log(timeCardId);
            
            if (cell.getColumn().getField() == "delete")
            {
               onDeleteTimeCard(timeCardId);
            }
            else if (cell.getColumn().getField() == "panTicket")
            {
               document.location = "<?php echo $ROOT?>/panTicket/viewPanTicket.php?panTicketId=" + timeCardId;
            }
            else // Any other column
            {
               // Open time card for viewing/editing.
               document.location = "<?php echo $ROOT?>/timecard/viewTimeCard.php?timeCardId=" + timeCardId;               
            }
         },
         rowClick:function(e, row){
            // No row click function needed.
         },
      });

      function updateFilter(event)
      {
         if (document.readyState === "complete")
         {
            var filterId = event.srcElement.id;
   
            if ((filterId == "start-date-filter") ||
                (filterId == "end-date-filter"))
            {
               var url = getTableQuery();
               var params = getTableQueryParams();

               table.setData(url, params)
               .then(function(){
                  // Run code after table has been successfuly updated
               })
               .catch(function(error){
                  // Handle error loading data
               });

               if (filterId == "start-date-filter")
               {
                  setSession("timeCard.filter.startDate", document.getElementById("start-date-filter").value);
               }
               else if (filterId == "end-date-filter")
               {
                  setSession("timeCard.filter.endDate", document.getElementById("end-date-filter").value);
               }
            }
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
            

      function filterToday()
      {
         var startDateFilter = document.querySelector('#start-date-filter');
         var endDateFilter = document.querySelector('#end-date-filter');
         
         if ((startDateFilter != null) && (endDateFilter != null))
         {
            var today = new Date();
            
            startDateFilter.value = formattedDate(today); 
            endDateFilter.value = formattedDate(today);

            startDateFilter.dispatchEvent(new Event('change'));
            endDateFilter.dispatchEvent(new Event('change'));  // TODO: Avoid calling this!  "An active ajax request was blocked ..."
         }         
      }

      function filterYesterday()
      {
         var startDateFilter = document.querySelector('#start-date-filter');
         var endDateFilter = document.querySelector('#end-date-filter');
         
         if ((startDateFilter != null) && (endDateFilter != null))
         {
            var yesterday = new Date();
            yesterday.setDate(yesterday.getDate() - 1);
            
            startDateFilter.value = formattedDate(yesterday); 
            endDateFilter.value = formattedDate(yesterday);

            startDateFilter.dispatchEvent(new Event('change'));
            endDateFilter.dispatchEvent(new Event('change'));  // TODO: Avoid calling this!  "An active ajax request was blocked ..."
         }      
      }

      // Setup event handling on all DOM elements.
      //document.getElementById("back-button").onclick = function(){window.history.back();};
      document.getElementById("start-date-filter").addEventListener("change", updateFilter);      
      document.getElementById("end-date-filter").addEventListener("change", updateFilter);
      document.getElementById("today-button").onclick = filterToday;
      document.getElementById("yesterday-button").onclick = filterYesterday;
      document.getElementById("new-time-card-button").onclick = function(){location.href = 'viewTimeCard.php';};
      document.getElementById("download-button").onclick = function(){table.download("csv", "data.csv", {delimiter:"."})};
   </script>

</body>

</html>