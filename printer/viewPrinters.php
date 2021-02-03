<?php

require_once '../common/activity.php';
require_once '../common/database.php';
require_once '../common/header.php';
require_once '../common/menu.php';
require_once '../common/permissions.php';
require_once '../common/roles.php';
require_once '../common/version.php';

// ********************************** BEGIN ************************************

Time::init();

session_start();

Authentication::authenticate();

if (!Authentication::isAuthenticated())
{
   header('Location: ../login.php');
   exit;
}

?>

<html>

<head>

   <meta name="viewport" content="width=device-width, initial-scale=1">

   <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>
   <link rel="stylesheet" type="text/css" href="../thirdParty/tabulator/css/tabulator.min.css"/>
   
   <link rel="stylesheet" type="text/css" href="../common/theme.css<?php echo versionQuery();?>"/>
   <link rel="stylesheet" type="text/css" href="../common/common.css<?php echo versionQuery();?>"/>
   
   <script src="../thirdParty/tabulator/js/tabulator.min.js"></script>
   <script src="../thirdParty/moment/moment.js"></script>
   <!-- script src="../thirdParty/moment/moment.min.js"></script> TODO: Figure this out. -->
   <script src="../thirdParty/dymo/DYMO.Label.Framework.3.0.js" type="text/javascript" charset="UTF-8"></script>
   
   <script src="../common/common.js<?php echo versionQuery();?>"></script>
   <script src="printer.js<?php echo versionQuery();?>"></script>
      
</head>

<body class="flex-vertical flex-top flex-left">

   <?php Header::render("PPTP Tools"); ?>
   
   <div class="main flex-horizontal flex-top flex-left">
   
      <?php Menu::render(Activity::PRINT_MANAGER); ?>
      
      <div class="content flex-vertical flex-top flex-left">
      
         <div class="flex-horizontal flex-v-center flex-h-center">
            <div class="heading">Printers</div>&nbsp;&nbsp;
            <i id="help-icon" class="material-icons icon-button">help</i>
         </div>
         
         <!-- div id="description" class="description">PPTP Tools supports cloud printing of pan tickets using Dymo label printers.<br><br><b>Local Printers</b> are the printers being served by this device.<br><br><b>Cloud printers</b> are printers available all users in the system.<br><br><b>Print queue</b> shows what pan tickts are currently being printed</div-->
         <div id="description" class="description"><p>PPTP Tools supports cloud printing of pan tickets using Dymo label printers.</p><p><b>Local Printers</b> are the printers being served by this device, including local and network printers.</p><p><b>Cloud Printers</b> are printers available all users in the system.</p><p>The <b>Print Queue</b> allows you to see what pan tickets are currently being printed.</p></div>
         
         <br>

         <div class="form-section-header">Local Printers</div>

         <div id="local-printer-table"></div>
         
         <br>
         
         <div class="form-section-header">Cloud Printers</div>
         
         <div id="cloud-printer-table"></div>
         
         <br>
         
         <div class="form-section-header">Print Queue</div>
         
         <div id="print-queue-table"></div>
         
         <br>
         
         <div class="form-section-header">Now Printing</div>
         
         <img id="print-preview-image" style="display:none;" src="" alt="label preview"/>
         

      </div> <!-- content -->
      
   </div> <!-- main -->
   
   <script>
   
      preserveSession();

      /*
      function getCloudPrinterTableQuery()
      {
         return ("<?php echo $ROOT ?>/api/printerData/");
      }

      function getPrintJobTableQuery()
      {
         return ("<?php echo $ROOT ?>/api/printQueueData/");
      }
      */
      
      // Create Tabulator on DOM element local-printer-table.
      var localPrinterTable = new Tabulator("#local-printer-table", {
         //height:500, // set height of table (in CSS or here), this enables the Virtual DOM and improves render speed dramatically (can be any valid css height value)
         layout:"fitData",
         responsiveLayout:"hide", // enable responsive layouts
         //Define Table Columns
         columns:[
            {title:"Name",     field:"name", hozAlign:"left"},
            {title:"Model",    field:"modelName",       hozAlign:"left"},
            {title:"Location", field:"isLocal",    hozAlign:"left",
               formatter:function(cell, formatterParams, onRendered){
                  return (cell.getValue() ? "Local" : "Network");
               }
            },            
            {title:"Status",   field:"isConnected",      hozAlign:"left",
               formatter:function(cell, formatterParams, onRendered){
                  return (cell.getValue() ? "Online" : "Offline");
               }
            }
         ],
      });

      // Create Tabulator on DOM element cloud-printer-table.
      var cloudPrinterTable = new Tabulator("#cloud-printer-table", {
         //height:500, // set height of table (in CSS or here), this enables the Virtual DOM and improves render speed dramatically (can be any valid css height value)
         layout:"fitData",
         responsiveLayout:"hide", // enable responsive layouts
         //Define Table Columns
         columns:[
            {title:"Name",     field:"displayName", hozAlign:"left"},
            {title:"Model",    field:"model",       hozAlign:"left"},
            //{title:"Location", field:"location",    hozAlign:"left",},
            {title:"Status",   field:"status",      hozAlign:"left"}
         ],
      });

      // Create Tabulator on DOM element print-queue-table.
      var printQueueTable = new Tabulator("#print-queue-table", {
         //height:500, // set height of table (in CSS or here), this enables the Virtual DOM and improves render speed dramatically (can be any valid css height value)
         layout:"fitData",
         responsiveLayout:"hide", // enable responsive layouts
         //Define Table Columns
         columns:[
            {title:"Date",            field:"dateTime",       hozAlign:"left", responsive:0,
               formatter:"datetime",  // Requires moment.js 
               formatterParams:{
                  outputFormat:"MM/DD/YYYY",
                  invalidPlaceholder:"---"
               }
            },
            {title:"Time",            field:"dateTime",       hozAlign:"left", responsive:0,
               formatter:"datetime",  // Requires moment.js 
               formatterParams:{
                  outputFormat:"hh:mm A",
                  invalidPlaceholder:"---"
               }
            },
            {title:"Owner",       field:"ownerName",          hozAlign:"left"},
            {title:"Description", field:"description",        hozAlign:"left"},
            {title:"Destination", field:"printerDisplayName", hozAlign:"left"},
            {title:"Copies",      field:"copies",             hozAlign:"left"},
            {title:"Status",      field:"statusLabel",        hozAlign:"left"},
            {title:"",            field:"delete",             hozAlign:"center",
               formatter:function(cell, formatterParams, onRendered){
                  return ("<i class=\"material-icons icon-button\">delete</i>");
               }
            }
         ],
         cellClick:function(e, cell){
            var printJobId = parseInt(cell.getRow().getData().printJobId);
            
            if (cell.getColumn().getField() == "delete")
            {
               printManager.cancelPrintJob(printJobId);
            }
         },
         
      });

      function renderPrintPreview(printJob)
      {
         previewImage = document.getElementById("print-preview-image");
         
         if (previewImage != null)
         {
            if (printJob != null)
            {
               var label = dymo.label.framework.openLabelXml(printJob.xml);
               
               var pngData = label.render();
      
               previewImage.src = "data:image/png;base64," + pngData;
               
               previewImage.style.display  = "block";         
            }
            else
            {
               previewImage.style.display  = "none";
            }
         }
      }

      function PrintManagerListener()
      {
         PrintManagerListener.prototype.onUpdate = function(localPrinters, cloudPrinters, printQueue)
         {
            if (localPrinters != null)
            {
               localPrinterTable.setData(localPrinters)
               .then(function(){
                  // Run code after table has been successfuly updated
               })
               .catch(function(error){
                  // Handle error loading data
               });
            }

            if (cloudPrinters != null)
            {
               cloudPrinterTable.setData(cloudPrinters)
               .then(function(){
                  // Run code after table has been successfuly updated
               })
               .catch(function(error){
                  // Handle error loading data
               });
            }

            if (printQueue != null)
            {
               printQueueTable.setData(printQueue)
               .then(function(){
                  // Run code after table has been successfuly updated
               })
               .catch(function(error){
                  // Handle error loading data
               });

               if (printQueue.length > 0)
               {
                  renderPrintPreview(printQueue[0]);
               }
               else
               {
                  renderPrintPreview(null);
               }
            }
         }
      }

      var printManager = new PrintManager(new PrintManagerListener());

      printManager.start();

      // Setup event handling on all DOM elements.
      document.getElementById("help-icon").onclick = function(){document.getElementById("description").classList.toggle('shown');};
      document.getElementById("menu-button").onclick = function(){document.getElementById("menu").classList.toggle('shown');};
   </script>
   
</body>

</html>
