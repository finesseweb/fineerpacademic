<?php

$page_security = 'SA_MAINTAINPREVENTIVE';
$path_to_root = "../..";


include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
page(_($help_context = "Process Maintenance ")); 

//include($path_to_root . "/maintenance/includes/db/parameters_db.inc");
include($path_to_root . "/includes/ui.inc");

//include_once($path_to_root . "/manufacturing/includes/manufacturing_db.inc");
//include_once($path_to_root . "/manufacturing/includes/manufacturing_ui.inc");
$_SESSION['page_title'] = _($help_context = "New Process");
 
//page($_SESSION['page_title'], false, false, "", $js);
?>
<html lang="en">
<head>
    <link rel="stylesheet" href="<?php echo $path_to_root . "/js/jquery-ui.css" ?>">
    <script src="<?php echo $path_to_root . "/js/jquery-1.10.2.js" ?>"></script>
   <script src="<?php echo $path_to_root . "/js/jquery-ui.js"?>"></script>
  <script>
  $( function() {
    $( "#datepicker" ).datepicker();
  } );
  </script>
</head>
<body>

<?php
if(isset($_GET['success']))
{
	display_notification("Process Maintenance has been added!");
}

start_form();
    if (get_post('Show'))
{
	$Ajax->activate('trans_tbl');
}
//------------------------------------------------------------------------------------------------


//------------------------------------------------------------------------------------------------
$query = "SELECT prev.*,utly.name as uname,freq.frequency_name AS frequency,con.supp_name AS contractor,freq.frequency_des FROM ".TB_PREF."process_maintenance AS prev LEFT JOIN ".TB_PREF."utility AS utly ON utly.id= prev.utility_id LEFT JOIN ".TB_PREF."frequency_master AS freq ON freq.freq_id= prev.frequency_id  LEFT JOIN ".TB_PREF."contractor con ON con.supplier_id= prev.contractor_id WHERE utly.type=2 and prev.status=0";

$res=db_query($query);
$result=db_fetch($res);

div_start('trans_tbl');
start_table(TABLESTYLE);

$th = array(_("#"),_("Reference"), _("Date"),
	_("Utility"), _("Frequency"),_("Contractor"), _("Observations Date"), _("Observations"), _("Corrective Action Suggested"),_("Corrective Action Initiated"),_("EDIT"));
table_header($th);


function get_trans($prevent_id,$type)
{

	$label = $prevent_id;
	$class ='';
	$id=$prevent_id;
	$icon = '';
	$viewer = $path_to_root."maintenance/view/";
	if ($type == 'Process')
	hidden('Process_id',$prevent_id);
		$viewer .= "maintain_process_view.php?id=".$prevent_id;
	
	return viewer_link($label, $viewer, $class, $id,  $icon);
	
}

$k = 0; 
$i=1; //row colour counter
while ($myrow = db_fetch($res))
{
	alt_table_row_color($k);
    label_cell($i);
	label_cell(get_trans($myrow['process_id'],'Process'));
	label_cell($myrow["maintain_date"]);
	label_cell($myrow["uname"]);
	label_cell($myrow["frequency"]);
	label_cell($myrow["contractor"]);
   // $nextpendingdate = date("d-m-Y", strtotime($myrow["frequency_des"], strtotime($myrow["prv_ob_date"])));
	label_cell($myrow["ob_date"]);
	///label_cell($nextpendingdate);
	label_cell($myrow["ob_1"]);
	label_cell($myrow["ob_2"]);
	label_cell($myrow["ob_3"]);
	 edit_button_cell("Edit" . $myrow['process_id'], _("Edit")); 
	 delete_button_cell("Update".$myrow['process_id'], _("Update"));
	end_row();
 
	$i++;
}

end_table(2);
div_end();



end_form();


?>
<form id="basicForm" action="add_proces_maintenance.php" method="POST" >
<table width="30%" align="center" style="padding:10px;">
<tr >
    <input type="hidden" id="process_id" name="process_id" value="">
 <td>Date<input type="text"  class="sal" name="maintain_date" id="maintain_date"/></td>
 <td >Process
  <select type="text" name="utility_id" id="utility_id" >
 <?php  $sql= "SELECT id,name
   FROM ".TB_PREF."utility WHERE type=2"; 
   $res=db_query($sql);
     echo "<option>Select</option>";
	while($row = db_fetch($res))
	{  
	?>  
	<option value="<?php  echo $row['id']; ?>"><?php echo $row['name']; ?></option>
	<?php  
	 } ?>
  </select></td>  
  
  <td>Frequency
  <select type="text" name="frequency_id" id="frequency_id">
 <?php $sql= "SELECT freq_id,frequency_name
   FROM ".TB_PREF."frequency_master"; 
   $res=db_query($sql);
     echo "<option>Select</option>";
	while($row = db_fetch($res))
	{ 
	?>  
	<option value="<?php echo $row['freq_id']; ?>"><?php echo $row['frequency_name']; ?></option>
	<?php  
	} ?>
  </select></td>
  <td>Contractor
  <select type="text" name="contractor_id" id="contractor_id">
 <?php $sql= "SELECT supplier_id,supp_name FROM ".TB_PREF."contractor ORDER BY supplier_id"; 
   $res=db_query($sql);
     echo "<option>Select</option>";
	while($row = db_fetch($res))
	{ 
	?>  
	<option value="<?php echo $row['supplier_id']; ?>"><?php echo $row['supp_name']; ?></option>
	<?php  
	} ?>
  </select></td>
  </tr>
 </table>
 <div id="records"></div>
 <hr>
  <div id="addmore_fields">

   </br>
 
 <div align="center">
 <br>
 <br>
 <input type="submit" id="add_entry" name="add_entry" value="Submit" align="center"> 
 <input type="submit" id="update_entry" name="update_entry" value="Update" align="center"> 
 </div>
 </form>
 </body>
<script type="text/javascript">
function myFunction() {
	var name= document.getElementById("check_name").value;
    document.getElementById("check_sign").value = name;
}

function myFunct() {
	var name= document.getElementById("inspect_name").value;
    document.getElementById("inspect_sign").value = name;
}

function myFun() {
	var quan_offered= document.getElementById("quan_offered").value;
    var quan_accepted= document.getElementById("quan_accepted").value;
    var quan_rejected= quan_offered - quan_accepted;
	document.getElementById("quan_rejected").value = quan_rejected;
  } 

</script>
 
 <script>
 $('#maintain_date').datepicker({
	dateFormat: 'dd-mm-yy',
	prevText: '<i class="fa fa-chevron-left"></i>',
	nextText: '<i class="fa fa-chevron-right"></i>',
	maxDate: 0 
});
 </script>
 <script>
 $('#inspect_date').datepicker({
	dateFormat: 'dd-mm-yy',
	prevText: '<i class="fa fa-chevron-left"></i>',
	nextText: '<i class="fa fa-chevron-right"></i>',
	maxDate: 0 
});
 </script>
 
 <script>
 $('#update_entry').hide(); 
$("#utility_id").change(function() {
	 var utility_id=$("#utility_id").val();
	 // alert(utility_id);
	 $.ajax({ 
			type: "POST",
			url:'<?php echo $path_to_root . "/maintenance/manage/ajax_get_utlyfrequency.php";?>',
			data: { utility_id : utility_id}
		}).done( function( data ) { 
	     //	alert(data);
			$("#frequency_id").html(data);
		});
});
 $("#frequency_id").change(function() {
	var frequency_id=$(this).val();
	 var utility_id=$("#utility_id").val();
	//  alert(utility_id);
	 
	  $.ajax({ 
			type: "POST",
			url:'<?php echo $path_to_root . "/maintenance/manage/ajax_get_processrecords.php";?>',
			data: { frequency_id : frequency_id,utility_id : utility_id}
		}).done( function( data ) { 
	     //	alert(data);
			
			$("#records").html(data);
		});
});  

 $('[name^="Edit"]').click(function(){
 
   var text = $(this).attr('name');
    var matches = text.match(/[(0-9)]+/);
    
     $.ajax({ 
			type: "POST",
			url:'<?php echo $path_to_root . "/maintenance/manage/ajax_get_editprocessrecords.php";?>',
			data: { selected_id: matches[0]}
		}).done( function( data ) { 
                    
                    //alert(data);
	     var res = JSON.parse(data);
	     
	        updatevale(res);
	         var utility_id=$("#utility_id").val();
	
	 $.ajax({ 
			type: "POST",
			url:'<?php echo $path_to_root . "/maintenance/manage/ajax_get_utlyfrequency.php";?>',
			data: { utility_id : utility_id}
		}).done( function( data ) { 
	     //	alert(data);
			$("#frequency_id").html(data);
			$("#frequency_id").val(res['frequency_id']);
		});
		
	var frequency_id=res['frequency_id'];
	 var utility_id=$("#utility_id").val();
	//  alert(utility_id);
	 
	  $.ajax({ 
			type: "POST",
			url:'<?php echo $path_to_root . "/maintenance/manage/ajax_get_processrecords.php";?>',
			data: { frequency_id : frequency_id,utility_id : utility_id}
		}).done( function( data ) { 
	     //	alert(data);
			
			$("#records").html(data);
			updatevale(res);
		});
		$('#add_entry').hide();
                $('#update_entry').show();
		});
   
    
});
$('[name^="Update"]').click(function(){
   var text = $(this).attr('name');
    var matches = text.match(/[(0-9)]+/);
       alert(matches);  
     $.ajax({ 
	 type: "POST",
	url:'<?php echo $path_to_root . "/maintenance/manage/ajax_update_status_pro.php";?>',
	data: { selected_id: matches[0]}
		}).done( function( data ) { 
                    
                //  alert(data);  
	      
	     
		});
   
    
});



var updatevale = (res)=>{
    for(x in res){
	        $('#'+x).val(res[x]);
	        }
}
 </script>
 
</html>
<?php
end_page();
?>
<style>
input.sal{
width:70px;
}
</style>