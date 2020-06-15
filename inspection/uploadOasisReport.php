<?php
require_once '../common/inspection.php';
require_once '../common/database.php';
require_once '../common/oasisReport/oasisReport.php';

if (isset($_POST["submit"]))
{
   $target_dir = "../uploads/oasisReports/";
   $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
   
   if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file))
   {
      echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
      
      $oasisReport = OasisReport::parseFile($target_file);
      
      if (!$oasisReport)
      {
         echo "Failed to parse the file.";
      }
      else
      {
         $inspection = new Inspection();
         $inspection->initializeFromOasisReport($oasisReport);
         echo $oasisReport->toHtml();
         $database = PPTPDatabase::getInstance();
         $database->newInspection($inspection);
      }
   }
   else
   {
      echo "Sorry, there was an error uploading your file.";
   }
}
?>

<!DOCTYPE html>
<html>
<body>

<form method="post" enctype="multipart/form-data">
  Select report to upload:
  <input type="file" name="fileToUpload" id="fileToUpload">
  <input type="submit" value="Upload" name="submit">
</form>

</body>
</html>