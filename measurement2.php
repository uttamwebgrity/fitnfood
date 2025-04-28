<?php
include_once("includes/header.php");
?>
<style type="text/css">
table { 
  width: 100%; 
  border-collapse: collapse; 
   font:12px/20px "open_sansregular";
}
tr:nth-of-type(odd) { 
  background: #fff; 
}

th { 
  background: #0479d4; 
  color:#fff; 
  font-weight: bold; 
   font:12px/20px "open_sanssemibold";
}
td, th { 
  padding: 6px; 
  border: 1px solid #0479d4; 
  text-align: left; 
}
@media 
only screen and (max-width: 760px),
(min-device-width: 768px) and (max-device-width: 1024px)  {

	/* Force table to not be like tables anymore */
	table, thead, tbody, th, td, tr { 
		display: block; 
	}
	
	/* Hide table headers (but not display: none;, for accessibility) */
	thead tr { 
		position: absolute;
		top: -9999px;
		left: -9999px;
	}
	
	tr { border: 1px solid #0479d4; }
	
	td { 
		/* Behave  like a "row" */
		border: none;
		border-bottom: 1px solid #0479d4; 
		position: relative;
		padding-left: 50%; 
	}
	
	td:before { 
		/* Now like a table header */
		position: absolute;
		/* Top/left values mimic padding */
		top: 6px;
		left: 6px;
		width: 45%; 
		padding-right: 10px; 
		white-space: nowrap;
	}
	
	/*
	Label the data
	*/
	td:nth-of-type(1):before { content: "Date measured"; font:12px/20px "open_sanssemibold"; color:#0479d4 }
	td:nth-of-type(2):before { content: "Push-up (max 1 min)"; font:12px/20px "open_sanssemibold"; color:#0479d4 }
	td:nth-of-type(3):before { content: "Crunches (max 1min)"; font:12px/20px "open_sanssemibold"; color:#0479d4 }
	td:nth-of-type(4):before { content: "Challenge number"; font:12px/20px "open_sanssemibold"; color:#0479d4 }
	td:nth-of-type(5):before { content: "Quickest time/ reps"; font:12px/20px "open_sanssemibold"; color:#0479d4 }
	td:nth-of-type(6):before { content: "Location ranking"; font:12px/20px "open_sanssemibold"; color:#0479d4 }
	td:nth-of-type(7):before { content: "Fitnfood overall rank "; font:12px/20px "open_sanssemibold"; color:#0479d4 }
}

</style>


<div class="inrBnr">
	<?php	
	 $db_common->static_page_banner($dynamic_content['page_id']);?>
	
</div>
<div class="bodyContent">
	<div class="mainDiv2">
	<div class="order_listingBcmb">
    	<ul>
        	<li><a href="my-account/">My Account »</a></li>
            <li>Measurement</li>
        </ul>
    </div>
    <div class="measurementPnl">
    	<table>
	<thead>
	<tr>
		<th>Date measured</th>
		<th>Push-up (max 1 min)</th>
		<th>Crunches (max 1min) </th>
        <th>Challenge number</th>
        <th>Quickest time/ reps </th>
        <th>Location ranking</th>
        <th>Fitnfood overall rank </th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td>15/09/2013</td>
		<td>25</td>
		<td>45</td>
        <td>45</td>
        <td>45</td>
        <td>45</td>
        <td>45</td>
	</tr>
	<tr>
		<td>15/09/2013</td>
		<td>25</td>
		<td>45</td>
        <td>45</td>
        <td>45</td>
        <td>45</td>
        <td>45</td>
	</tr>
    <tr>
		<td>15/09/2013</td>
		<td>25</td>
		<td>45</td>
        <td>45</td>
        <td>45</td>
        <td>45</td>
        <td>45</td>
	</tr>
    <tr>
		<td>15/09/2013</td>
		<td>25</td>
		<td>45</td>
        <td>45</td>
        <td>45</td>
        <td>45</td>
        <td>45</td>
	</tr>
	</tbody>
</table>
    </div>
	</div>
</div>
<?php
include_once("includes/footer.php");
?>