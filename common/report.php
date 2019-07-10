<?php
  
abstract class Report
{
   public function getHtml()
   {
      $html =
<<<HEREDOC
      <div class="flex-vertical content">

         <div class="heading">{$this->getTitle()}</div>

         <div class="description">{$this->getDescription()}</div>

         <div class="flex-vertical inner-content"> 
            {$this->getTable()}
         </div>
      </div>
HEREDOC;
         
      return ($html);
   }
      
   public function render()
   {
      echo ($this->getHtml());
   }
   
   protected function getTable()
   {
      $html = 
<<<HEREDOC
      <table class="line-inspections-table">
         <tr>
HEREDOC;
      
      $headers = $this->getHeaders();
      
      foreach ($headers as $header)
      {
         $html .= "<th>$header</th>";
      }
         
      $html .= "</tr>";
         
      $data = $this->getData();
      
      foreach ($data as $row)
      {
         $html .= "<tr>";
         
         foreach ($row as $col)
         {
            $html .= "<td>$col</td>";
         }
         
         $html .= "</tr>";
      }
         
      $html .= "</table>";
      
      return ($html);
   }
   
   abstract protected function getTitle();
   
   abstract protected function getDescription();
   
   abstract protected function getHeaders();
   
   abstract protected function getData();
}