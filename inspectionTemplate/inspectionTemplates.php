<?php

require_once '../common/authentication.php';
require_once '../common/database.php';
require_once '../common/filter.php';
require_once '../common/header.php';
require_once '../common/inspectionTemplate.php';
require_once '../common/navigation.php';

// *****************************************************************************
//                            InspectionTypeFilterComponent

class InspectionTypeFilterComponent extends FilterComponent
{
   public $selectedInspectionType;
   
   function __construct($label)
   {
      $this->label = $label;
   }
   
   public function getHtml()
   {
      $all = InspectionType::UNKNOWN;
      
      $selected = "";
      
      $options = "<option $selected value=\"$all\">All</option>";
      
      for ($inspectionType = InspectionType::FIRST; 
           $inspectionType != InspectionType::LAST; 
           $inspectionType++)
      {
         $label = InspectionType::getLabel($inspectionType);
         $selected = ($inspectionType == $this->selectedInspectionType) ? "selected" : "";
         $options .= "<option $selected value=\"$inspectionType\">$label</option>";
      }
      
      $html =
<<<HEREDOC
      <div class="flex-horizontal filter-component hide-on-tablet">
         <div>$this->label:&nbsp</div>
         <div><select id="filter-inspection-type-input" name="filterInspectionType">$options</select></div>
      </div>
HEREDOC;
      
      return ($html);
   }
   
   public function update()
   {
      if (isset($_POST['filterInspectionType']))
      {
         $this->selectedInspectionType = $_POST['filterInspectionType'];
      }
   }
}

// *****************************************************************************

function getNavBar()
{
   $navBar = new Navigation();
   
   $navBar->start();
   $navBar->mainMenuButton();
   $navBar->highlightNavButton("New Template", "location.replace('viewInspectionTemplate.php');", true);
   $navBar->end();
   
   return ($navBar->getHtml());
}

function getFilter()
{
   $filter = null;
   
   if (isset($_SESSION["inspectionTemplateFilter"]))
   {
      $filter = $_SESSION["inspectionTemplateFilter"];
   }
   else
   {  
      $filter = new Filter();
      
      $filter->addByName("inspectionType", new InspectionTypeFilterComponent("Inspection Type"));
      $filter->add(new FilterButton());
      
      $_SESSION["inspectionTemplateFilter"] = $filter;
   }
   
   $filter->update();
   
   return ($filter);
}

function getTable($filter)
{
   $html = "";
   
   global $ROOT;
   
   $result = PPTPDatabase::getInstance()->getInspectionTemplates($filter->get('inspectionType')->selectedInspectionType);
   
   if ($result && (MySqlDatabase::countResults($result) > 0))
   {
      $html =
<<<HEREDOC
      <div class="table-container">
         <table class="part-weight-log-table">
            <tr>
               <th>Name</th>
               <th>Inspection Type</th>
               <th>Description</th>
               <th>Property Count</th>
               <th></th>
               <th></th>
               <th></th>
            </tr>
HEREDOC;
      
      while ($row = $result->fetch_assoc())
      {
         $inspectionTemplate = InspectionTemplate::load($row["templateId"]);
         
         $inspectionTypeLabel = InspectionType::getLabel($inspectionTemplate->inspectionType);
         
         $propertyCount = count($inspectionTemplate->inspectionProperties);
         
         if ($inspectionTemplate)
         {
            $viewEditIcon = "";
            $copyIcon = "";
            $deleteIcon = "";
            if (Authentication::checkPermissions(Permission::EDIT_INSPECTION_TEMPLATE))
            {
               $viewEditIcon =
                  "<a href=\"$ROOT/inspectionTemplate/viewInspectionTemplate.php?templateId=$inspectionTemplate->templateId\"><i class=\"material-icons table-function-button\">mode_edit</i></a>";
               
               $copyIcon =
               "<i class=\"material-icons table-function-button\" onclick=\"location.href = 'viewInspectionTemplate.php?copyFrom=$inspectionTemplate->templateId';\">file_copy</i>";
               
               $deleteIcon =
                  "<i class=\"material-icons table-function-button\" onclick=\"onDeleteInspectionTemplate($inspectionTemplate->templateId)\">delete</i>";
            }
            else
            {
               $viewEditIcon =
                  "<a href=\"$ROOT/inspectionTemplate/viewInspectionTemplate.php?templateId=$inspectionTemplate->templateId\"><i class=\"material-icons table-function-button\">visibility</i></a>";
            }
            
            $html .=
<<<HEREDOC
            <tr>
               <td>$inspectionTemplate->name</td>
               <td>$inspectionTypeLabel</td>
               <td>$inspectionTemplate->description</td>
               <td>$propertyCount</td>
               <td>$viewEditIcon</td>
               <td>$copyIcon</td>
               <td>$deleteIcon</td>
            </tr>
HEREDOC;
         }  // end if ($partWeightEntry)
      }  // end while ($row = $result->fetch_assoc())
      
      $html .=
<<<HEREDOC
         </table>
      </div>
HEREDOC;
   }
   else
   {
      $html = "<div class=\"no-data\">No data is available for the selected range.  Use the filter controls above to select a new operator or date range.</div>";
   }  // end if ($result && (Database::countResults($result) > 0))
   
   return ($html);
}

?>

<!-- ********************************** BEGIN ********************************************* -->

<?php 
Time::init();

session_start();

if (!Authentication::isAuthenticated())
{
   header('Location: ../home.php');
   exit;
}

$filter = getFilter();
?>

<!DOCTYPE html>
<html>

<head>

   <meta name="viewport" content="width=device-width, initial-scale=1">
   
   <link rel="stylesheet" type="text/css" href="../common/flex.css"/>
   <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>
   <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-blue.min.css"/>
   <link rel="stylesheet" type="text/css" href="../common/common.css"/>
   <link rel="stylesheet" type="text/css" href="../common/tooltip.css"/>
   <link rel="stylesheet" type="text/css" href="inspectionTemplate.css"/>
   
   <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
   <script src="../common/common.js"></script>
   <script src="../common/validate.js"></script>
   <script src="inspectionTemplate.js"></script>

</head>

<body>

   <?php Header::render("PPTP Tools"); ?>
   
   <div class="flex-horizontal main">
     
     <div class="flex-horizontal sidebar hide-on-tablet"></div> 
   
     <div class="flex-vertical content">

        <div class="heading">Inspection Templates</div>

        <div class="description">Blah blah blah</div>

        <div class="flex-vertical inner-content">
        
           <?php echo $filter->getHtml(); ?>
           
           <?php echo getTable($filter); ?>
      
        </div>
         
        <?php echo getNavBar(); ?>
         
     </div>
     
   </div>
   
   <script>
      preserveSession();
   </script>

</body>

</html>