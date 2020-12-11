<?php

require_once '../common/authentication.php';
require_once '../common/database.php';
require_once '../common/weeklySummaryReport.php';
require_once '../common/header.php';
require_once '../common/jobInfo.php';
require_once '../common/menu.php';
require_once '../common/newIndicator.php';
require_once '../common/permissions.php';
require_once '../common/roles.php';
require_once '../common/timeCardInfo.php';
require_once '../common/userInfo.php';

function getMfgDate()
{
   $mfgDate = Time::now("Y-m-d");
   
   if (isset($_SESSION["weeklySummaryReport.filter.mfgDate"]))
   {
      $mfgDate = $_SESSION["weeklySummaryReport.filter.mfgDate"];
   }
   
   return ($mfgDate);
}

function getFilterMfgDate()
{
   $mfgDate = getMfgDate();

   // Convert to Javascript date format.
   $dateTime = new DateTime($mfgDate, new DateTimeZone('America/New_York'));  // TODO: Replace
   $mfgDate = $dateTime->format(Time::$javascriptDateFormat);
   
   return ($mfgDate);
}

function getReportFilename()
{
   $mfgDate = getFilterMfgDate();
   
   $filename = "WeeklySummaryReport_" . $mfgDate . ".csv";
   
   return ($filename);
}

function getTableHeader()
{
   $mfgDate = getFilterMfgDate();
   
   $dateTime = new DateTime($mfgDate, new DateTimeZone('America/New_York'));
   
   $header =  "Week " . $dateTime->format("W") . ": " . $dateTime->format("l");
   
   return ($header);
}

// ********************************** BEGIN ************************************

Time::init();

session_start();

if (!Authentication::isAuthenticated())
{
   header('Location: ../login.php');
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

<html>

<head>

   <meta name="viewport" content="width=device-width, initial-scale=1">

   <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>
   <link rel="stylesheet" type="text/css" href="../thirdParty/tabulator/css/tabulator.min.css"/>
   
   <link rel="stylesheet" type="text/css" href="../common/theme.css"/>
   <link rel="stylesheet" type="text/css" href="../common/common.css"/>
   
   <script src="../thirdParty/tabulator/js/tabulator.min.js"></script>
   <script src="../thirdParty/moment/moment.min.js"></script>
   
   <script src="../common/common.js"></script>
   <script src="../common/validate.js"></script>
      
</head>

<body class="flex-vertical flex-top flex-left">

   <?php Header::render("PPTP Tools"); ?>
   
   <div class="main flex-horizontal flex-top flex-left" style="width: auto;">
   
      <?php Menu::render(Activity::REPORT); ?>
      
      <div class="content flex-vertical flex-top flex-left">
      
         <div class="flex-horizontal flex-v-center flex-h-center">
            <div class="heading">Weekly Summary Report</div>&nbsp;&nbsp;
            <i id="help-icon" class="material-icons icon-button">help</i>
         </div>
         
         <div id="description" class="description">Something something something ...</div>
         
         <br>
         
         <div class="flex-horizontal flex-v-center flex-left">
            <div style="white-space: nowrap">Manufacture date</div>
            &nbsp;
            <input id="mfg-date-filter" type="date" value="<?php echo getFilterMfgDate()?>">
            &nbsp;&nbsp;
            <button id="today-button" class="small-button">Today</button>
            &nbsp;&nbsp;
            <button id="yesterday-button" class="small-button">Yesterday</button>
         </div>
         
         <br>
         
         <div id="report-table-header" class="table-header"></div>
         
         <br>
        
         <div id="weekly-summary-table"></div>
         
         <br>
         
         <div class="table-header">Weekly Bonus</div>
         
         <br>
        
         <div id="bonus-table"></div>
         
         <br>
        
         <div id="download-link" class="download-link">Download CSV file</div>
         
         <div id="print-link" class="download-link">Print</div>         
         
      </div> <!-- content -->
      
   </div> <!-- main -->
   
   <script>
   
      const WEEKLY_SUMMARY_TABLE = <?php echo WeeklySummaryReportTable::WEEKLY_SUMMARY; ?>;
      const BONUS_TABLE = <?php echo WeeklySummaryReportTable::BONUS; ?>;
   
      preserveSession();

      function getTableQuery()
      {
         return ("<?php echo $ROOT ?>/api/weeklySummaryReportData/");
      }

      function getTableQueryParams(table)
      {
         
         var params = new Object();
         params.mfgDate =  document.getElementById("mfg-date-filter").value;
         params.table = table;

         return (params);
      }
      
      // Array of tables.
      var tables = [];

      var url = getTableQuery();
      var params = getTableQueryParams(WEEKLY_SUMMARY_TABLE);
      
      tables[WEEKLY_SUMMARY_TABLE] = new Tabulator("#weekly-summary-table", {
         //height:500,            // set height of table (in CSS or here), this enables the Virtual DOM and improves render speed dramatically (can be any valid css height value)
         layout:"fitData",
         cellVertAlign:"middle",
         printAsHtml:true,          //enable HTML table printing
         printRowRange:"all",       // print all rows 
         printHeader:"<h1>Totals<h1>",
         ajaxURL:url,
         ajaxParams:params,
         //Define Table Columns
         columns:[
            {title:"Operator",   field:"operator",       hozAlign:"left", headerFilter:true, print:true},
            {title:"Employee #", field:"employeeNumber", hozAlign:"left",                    print:true},
            // Sunday
            {
               title:"Sunday",
               columns:[
                  {title:"Hours",         field:"sunday.runTime",         hozAlign:"left", print:true,},
                  {title:"Efficiency",    field:"sunday.efficiency",      hozAlign:"left", print:true,
                     formatter:function(cell, formatterParams, onRendered){
                        return (cell.getValue() + "%");
                     }
                  },
                  {title:"Machine Hours", field:"sunday.machineHoursMade", hozAlign:"left", print:true},
                  {title:"Ratio",         field:"sunday.ratio",            hozAlign:"left", print:true}
               ],
            },
            // Monday
            {
               title:"Monday",
               columns:[
                  {title:"Hours",         field:"monday.runTime",         hozAlign:"left", print:true,},
                  {title:"Efficiency",    field:"monday.efficiency",      hozAlign:"left", print:true,
                     formatter:function(cell, formatterParams, onRendered){
                        return (cell.getValue() + "%");
                     }
                  },
                  {title:"Machine Hours", field:"monday.machineHoursMade", hozAlign:"left", print:true},
                  {title:"Ratio",         field:"monday.ratio",            hozAlign:"left", print:true}
               ],
            },
            // Tuesday
            {
               title:"Tuesday",
               columns:[
                  {title:"Hours",         field:"tuesday.runTime",         hozAlign:"left", print:true,},
                  {title:"Efficiency",    field:"tuesday.efficiency",      hozAlign:"left", print:true,
                     formatter:function(cell, formatterParams, onRendered){
                        return (cell.getValue() + "%");
                     }
                  },
                  {title:"Machine Hours", field:"tuesday.machineHoursMade", hozAlign:"left", print:true},
                  {title:"Ratio",         field:"tuesday.ratio",            hozAlign:"left", print:true}
               ],
            },
            // Wednesday
            {
               title:"Wednesday",
               columns:[
                  {title:"Hours",         field:"wednesday.runTime",         hozAlign:"left", print:true,},
                  {title:"Efficiency",    field:"wednesday.efficiency",      hozAlign:"left", print:true,
                     formatter:function(cell, formatterParams, onRendered){
                        return (cell.getValue() + "%");
                     }
                  },
                  {title:"Machine Hours", field:"wednesday.machineHoursMade", hozAlign:"left", print:true},
                  {title:"Ratio",         field:"wednesday.ratio",            hozAlign:"left", print:true}
               ],
            },
            // Thursday
            {
               title:"Thursday",
               columns:[
                  {title:"Hours",         field:"thursday.runTime",         hozAlign:"left", print:true,},
                  {title:"Efficiency",    field:"thursday.efficiency",      hozAlign:"left", print:true,
                     formatter:function(cell, formatterParams, onRendered){
                        return (cell.getValue() + "%");
                     }
                  },
                  {title:"Machine Hours", field:"thursday.machineHoursMade", hozAlign:"left", print:true},
                  {title:"Ratio",         field:"thursday.ratio",            hozAlign:"left", print:true}
               ],
            },
            // Friday
            {
               title:"Friday",
               columns:[
                  {title:"Hours",         field:"friday.runTime",         hozAlign:"left", print:true,},
                  {title:"Efficiency",    field:"friday.efficiency",      hozAlign:"left", print:true,
                     formatter:function(cell, formatterParams, onRendered){
                        return (cell.getValue() + "%");
                     }
                  },
                  {title:"Machine Hours", field:"friday.machineHoursMade", hozAlign:"left", print:true},
                  {title:"Ratio",         field:"friday.ratio",            hozAlign:"left", print:true}
               ],
            },
            // Saturday
            {
               title:"Saturday",
               columns:[
                  {title:"Hours",         field:"saturday.runTime",         hozAlign:"left", print:true,},
                  {title:"Efficiency",    field:"saturday.efficiency",      hozAlign:"left", print:true,
                     formatter:function(cell, formatterParams, onRendered){
                        return (cell.getValue() + "%");
                     }
                  },
                  {title:"Machine Hours", field:"saturday.machineHoursMade", hozAlign:"left", print:true},
                  {title:"Ratio",         field:"saturday.ratio",            hozAlign:"left", print:true}
               ],
            }
         ]
      });

      params = getTableQueryParams(BONUS_TABLE);
      
      tables[BONUS_TABLE] = new Tabulator("#bonus-table", {
         //height:500,            // set height of table (in CSS or here), this enables the Virtual DOM and improves render speed dramatically (can be any valid css height value)
         layout:"fitData",
         cellVertAlign:"middle",
         printAsHtml:true,          //enable HTML table printing
         printRowRange:"all",       // print all rows 
         printHeader:"<h1>Totals<h1>",
         ajaxURL:url,
         ajaxParams:params,
         //Define Table Columns
         columns:[
            {title:"Operator",   field:"operator",       hozAlign:"left", headerFilter:true, print:true},
            {title:"Employee #", field:"employeeNumber", hozAlign:"left",                    print:true},         
            {title:"Hours",      field:"totalRunTime",   hozAlign:"left",                    print:true},
            {
               title:"75%",
               columns:[
                  {title:"$0.25", field:"tier1", hozAlign:"left", print:true, formatter:"money" }
               ]
            },
            {
               title:"80%",
               columns:[
                  {title:"$0.50", field:"tier2", hozAlign:"left", print:true, formatter:"money" }
               ]
            },
            {
               title:"85%",
               columns:[
                  {title:"$1.00", field:"tier3", hozAlign:"left", print:true, formatter:"money" }
               ]
            },
            {
               title:"90%",
               columns:[
                  {title:"$1.50", field:"tier4", hozAlign:"left", print:true, formatter:"money" }
               ]
            },
            {
               title:"95%",
               columns:[
                  {title:"$2.00", field:"tier5", hozAlign:"left", print:true, formatter:"money" }
               ]
            },
            {
               title:"100%",
               columns:[
                  {title:"$3.00", field:"tier6", hozAlign:"left", print:true, formatter:"money" }
               ]
            },
         ]
      });      

      function updateFilter(event)
      {
         if (document.readyState === "complete")
         {
            var filterId = event.srcElement.id;
   
            if (filterId == "mfg-date-filter")
            {
               tables[WEEKLY_SUMMARY_TABLE].setData(getTableQuery(), getTableQueryParams(WEEKLY_SUMMARY_TABLE))
               .then(function(){
                  // Run code after table has been successfuly updated
               })
               .catch(function(error){
                  // Handle error loading data
               });
               
               tables[BONUS_TABLE].setData(getTableQuery(), getTableQueryParams(BONUS_TABLE))
               .then(function(){
                  // Run code after table has been successfuly updated
               })
               .catch(function(error){
                  // Handle error loading data
               });

               if (filterId == "mfg-date-filter")
               {
                  setSession("weeklySummaryReport.filter.mfgDate", document.getElementById("mfg-date-filter").value);
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
         var mfgDateFilter = document.querySelector('#mfg-date-filter');
         
         if (mfgDateFilter != null)
         {
            var today = new Date();
            
            mfgDateFilter.value = formattedDate(today); 

            mfgDateFilter.dispatchEvent(new Event('change'));
         }         
      }

      function filterYesterday()
      {
         var mfgDateFilter = document.querySelector('#mfg-date-filter');
         
         if (mfgDateFilter != null)
         {
            var yesterday = new Date();
            yesterday.setDate(yesterday.getDate() - 1);
            
            mfgDateFilter.value = formattedDate(yesterday); 

            mfgDateFilter.dispatchEvent(new Event('change'));
         }      
      }

      // Setup event handling on all DOM elements.
      window.addEventListener('resize', function() { tables[WEEKLY_SUMMARY_TABLE].redraw(); tables[BONUS_TABLE].redraw(); });
      document.getElementById("mfg-date-filter").addEventListener("change", updateFilter);      
      document.getElementById("today-button").onclick = filterToday;
      document.getElementById("yesterday-button").onclick = filterYesterday;
      document.getElementById("download-link").onclick = function(){table.download("csv", "<?php echo getReportFilename() ?>", {delimiter:"."})};
      document.getElementById("print-link").onclick = function(){tables[WEEKLY_SUMMARY_TABLE].print(false, true);};

      document.getElementById("help-icon").onclick = function(){document.getElementById("description").classList.toggle('shown');};
      document.getElementById("menu-button").onclick = function(){document.getElementById("menu").classList.toggle('shown');};
   </script>
   
</body>

</html>