<?php

require_once '../common/database.php';
require_once '../common/jobInfo.php';
require_once '../common/report.php';
require_once '../common/time.php';
require_once '../common/userInfo.php';

class JobsReport extends Report
{
    function __construct()
    {
       if (isset($_POST['filterJobNumber']))
       {
          $this->jobNumber = $_POST['filterJobNumber'];
       }
       
       for ($jobStatus = JobStatus::FIRST; $jobStatus < JobStatus::LAST; $jobStatus++)
       {
          $name = strtolower(JobStatus::getName($jobStatus));
          
          $this->jobStatuses[$jobStatus] = (isset($_POST[$name]) && boolval($_POST[$name]));
       }
   }
    
    protected function getTitle()
    {
        return ("Jobs Report");
    }
    
    protected function getDescription()
    {        
        $description = "Reporting all ";
        
        $slash = false;
        
        for ($jobStatus = JobStatus::FIRST; $jobStatus < JobStatus::LAST; $jobStatus++)
        {
           if ($this->jobStatuses[$jobStatus])
           {
              $name = JobStatus::getName($jobStatus);
              
              $description .= $slash ? "/" : "";
              $description .= $name;
              
              $slash = true;
           }
        }
        
        $description .= " jobs";
        
        if ($this->jobNumber != "All")
        {
           $description .= " matching $this->jobNumber.";
        }
        else
        {
           $description .= ".";
        }
        
        return ($description);
    }
    
    protected function getHeaders()
    {
        return (array("Job Number", "Author", "Date", "Part #", "Sample Weight", "Work Center #", "Cycle Time", "Net Percentage", "Status"));
    }
    
    protected function getData()
    {
        $data = array();
        
        $database = new PPTPDatabase();
        
        $database->connect();
        
        if ($database->isConnected())
        {            
           $result = $database->getJobs($this->jobNumber, $this->jobStatuses);
            
            if ($result && ($database->countResults($result) > 0))
            {
                while ($row = $result->fetch_assoc())
                {
                    $jobInfo = JobInfo::load($row["jobId"]);
                    
                    if ($jobInfo)
                    {
                        $creatorName = "unknown";
                        $user = UserInfo::load($jobInfo->creator);
                        if ($user)
                        {
                            $creatorName= $user->getFullName();
                        }
                        
                        $dateTime = new DateTime($jobInfo->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
                        $date = $dateTime->format("m-d-Y");
                        
                        $status = JobStatus::getName($jobInfo->status);
                        
                        $dataRow = array($jobInfo->jobNumber, $creatorName, $date, $jobInfo->partNumber, $jobInfo->sampleWeight, $jobInfo->wcNumber, $jobInfo->cycleTime, $jobInfo->netPercentage, $status);
                        
                        $data[] = $dataRow;
                    }
                }
            }
        }
        
        return ($data);
    }
    
    private $jobNumber = "All";
    
    private $jobStatuses = array();
}

$report = new JobsReport();

?>

<!DOCTYPE html>
<html>

<head>

<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" type="text/css" href="../common/flex.css"/>
<link rel="stylesheet" type="text/css" href="../common/common.css"/>

</head>

<body>

   <?php $report->render(); ?>

</body>

<script>
   javascript:window.print()
</script>

</html>

