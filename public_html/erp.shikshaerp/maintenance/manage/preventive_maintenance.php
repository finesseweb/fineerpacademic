<?php

$page_security = 'SA_MAINTAINPREVENTIVE';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
page(_($help_context = "Preventive Maintenance ")); 

//include($path_to_root . "/maintenance/includes/db/parameters_db.inc");
include($path_to_root . "/includes/ui.inc");

//include_once($path_to_root . "/manufacturing/includes/manufacturing_db.inc");
//include_once($path_to_root . "/manufacturing/includes/manufacturing_ui.inc");
$_SESSION['page_title'] = _($help_context = "New Preventive");
 //simple_page_mode(true);
//page($_SESSION['page_title'], false, false, "", $js);

//-----------------------------------------------------------------------------------
// Ajax updates
//

?>
<html lang="en">
<head>
    
    <link rel="stylesheet" href="<?php echo $path_to_root . "/js/jquery-ui.css" ?>">
    <script src="<?php echo $path_to_root . "/js/jquery-1.10.2.js" ?>"></script>
   <script src="<?php echo $path_to_root . "/js/jquery-ui.js"?>"></script>
 
</head>
<body>

<?php
if(isset($_GET['success']))
{
	display_notification("Preventive Maintenance has been added!");
}
//simple_page_mode(true);
start_form();
    if (get_post('Show'))
{
	$Ajax->activate('trans_tbl');
}
//------------------------------------------------------------------------------------------------


//------------------------------------------------------------------------------------------------
$query = "SELECT prev.*,s.description AS Item,freq.frequency_name AS frequency,con.supp_name AS contractor,freq.frequency_des FROM ".TB_PREF."prevent_maintain_entry AS prev LEFT JOIN ".TB_PREF."utility AS utly ON utly.id= prev.utility_id LEFT JOIN ".TB_PREF."frequency_master AS freq ON freq.freq_id= prev.frequency_id LEFT JOIN ".TB_PREF."stock_master s ON s.stock_id= utly.items_id LEFT JOIN ".TB_PREF."contractor con ON con.supplier_id= prev.contractor_id WHERE utly.type=1";
$res=db_query($query);
//$result=db_fetch($res);

div_start('trans_tbl');
start_table(TABLESTYLE);

$th = array(_("#"),_("Reference"), _("Date"),
	_("Utility"), _("Frequency"),_("Contractor"), _("Observations Date"),_("Pending Date"), _("Observations"), _("Corrective Action Suggested"),_("Corrective Action Initiated"),_("EDIT"));
table_header($th);


function get_trans($prevent_id,$type)
{

	$label = $prevent_id;
	$class ='';
	$id=$prevent_id;
	$icon = '';
	 $viewer = $path_to_root."maintenance/view/";
	if ($type == 'Prevent')
	hidden('Prevent_id',$prevent_id);
		$viewer .= "maintain_preventive_view.php?id=".$prevent_id;
	
	return viewer_link($label, $viewer, $class, $id,  $icon);
	
}

$k = 0; 
$i=1; //row colour counter
while ($myrow = db_fetch($res))
{
	alt_table_row_color($k);
    label_cell($i);
	label_cell(get_trans($myrow['prevent_id'],'Prevent'));
	label_cell($myrow["maintain_date"]);
	label_cell($myrow["Item"]);
	label_cell($myrow["frequency"]);
	label_cell($myrow["contractor"]);
    $nextpendingdate = date("d-m-Y", strtotime($myrow["frequency_des"], strtotime($myrow["prv_ob_date"])));
	label_cell($myrow["prv_ob_date"]);
	label_cell($nextpendingdate);
	label_cell($myrow["prv_ob_1"]);
	label_cell($myrow["prv_ob_2"]);
	label_cell($myrow["prv_ob_3"]);
	 edit_button_cell("Edit" . $myrow['prevent_id'], _("Edit")); 
	 delete_button_cell("Update".$myrow['prevent_id'], _("Update"));
	end_row();
 
	$i++;
}

end_table(2);
div_end();



end_form();


?>

<form id="basicForm" action="add_preventive_maintenance.php" method="POST" >
    
<table width="30%" align="center" style="padding:10px;">
<tr >
 <td> Date<input class="sal" type="text" name="maintain_date" id="maintain_date" value ="<?=$_POST['maintain_date'];?>"/></td>
 <td >Utility
  <select type="text" name="utility_id" id="utility_id" >
 <?php  
 $sql= "SELECT utl.id,utl.name,s.description AS Item
   FROM ".TB_PREF."utility AS utl,".TB_PREF."stock_master s WHERE utl.maintenance_type_id=1 AND utl.type=1 AND utl.items_id=s.stock_id"; 
   $res=db_query($sql);
     echo "<option>Select</option>";
	while($row = db_fetch($res))
	{ 
	    
	?>  
	<option value="<?php  echo $row['id']; ?>" ><?php echo $row['Item']; ?></option>
	<?php  
	 } ?>
  </select></td>  
  
  <td >Frequency
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
 	$(document).ready(function () {
  //called when key is pressed in textbox
  $("#quan_accepted").keypress(function (e) {
     //if the letter is not digit then display error and don't type anything
     if (e.which != 8 && e.which != 0 && (e.which < 40 || e.which > 57)) {
        //display error message
        $("#errmsg").html("Digits Only").show().fadeOut("slow");
               return false;
    }
   });
});
 	
	$(document).ready(function () {
  //called when key is pressed in textbox
  $("#quan_rejected").keypress(function (e) {
     //if the letter is not digit then display error and don't type anything
     if (e.which != 8 && e.which != 0 && (e.which < 40 || e.which > 57)) {
        //display error message
        $("#errmsg").html("Digits Only").show().fadeOut("slow");
               return false;
    }
   });
});
 

  
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
			url:'<?php echo $path_to_root . "/maintenance/manage/ajax_get_preventiverecords.php";?>',
			data: { frequency_id : frequency_id,utility_id : utility_id}
		}).done( function( data ) { 
	     //	alert(data);
			
			$("#records").html(data);
		});
});  

 $("#AddButton").click(function() {
	 //alert('xxx');
	 var count=$("#count_val").val();
	 var count_val=parseInt(count)+1;
	        append_html = '<table id="sub'+count_val+'" style="margin-top:10px;">';
			append_html += '<tr><td><input type="text" label="drawings'+count_val+'" name="test[as_per_drawings][]" /></td>';
			append_html += '<td><input type="text" label="test1_'+count_val+'" name="test[t1][]" /></td>';
			append_html += '<td><input type="text" label="test2_'+count_val+'" name="test[t2][]" /></td>';
			append_html += '<td><input type="text" label="test3_'+count_val+'" name="test[t3][]" /></td>';
			append_html += '<td><input type="text" label="test4_'+count_val+'" name="test[t4][]" /></td>';
			append_html += '<td><input type="text" label="test5_'+count_val+'" name="test[t5][]" /></td>';
			append_html += '<td><input type="text" label="test6_'+count_val+'" name="test[t6][]" /></td>';
			append_html += '<td><input type="text" label="test7_'+count_val+'" name="test[t7][]" /></td>';
			append_html += '<td><input type="text" label="test8_'+count_val+'" name="test[t8][]" /></td>';
			append_html += '<td><input type="text" label="test9_'+count_val+'" name="test[t9][]" /></td>';
			append_html += '<td><input type="text" label="test10_'+count_val+'" name="test[t10][]" /></td>';
			append_html += '<td><input type="text" label="test11_'+count_val+'" name="test[t11][]" /></td>';
			append_html += '<td><input type="text" label="test12_'+count_val+'" name="test[t12][]" /></td>';
			append_html += '<td><input type="text" label="test13_'+count_val+'" name="test[t13][]" /></td>';
			append_html += '<td><input type="text" label="test14_'+count_val+'" name="test[t14][]" /></td>';
			append_html += '<td><input type="text" label="test15_'+count_val+'" name="test[t15][]" /></td>';
			append_html += '<td><input type="button" class="removeclass" style="" value="&nbsp;Remove&nbsp;" onclick="select_remove('+count_val+')"></td></tr>';
			append_html += '</table>';
		$('#fields').append(append_html);
		$("#count_val").val(count_val);
});
function select_remove(num){
	$('#sub'+num+'').remove();
}

$('[name^="Edit"]').click(function(){
   var text = $(this).attr('name');
    var matches = text.match(/[(0-9)]+/);
     $.ajax({ 
			type: "POST",
			url:'<?php echo $path_to_root . "/maintenance/manage/ajax_get_editpreventiverecords.php";?>',
			data: { selected_id: matches[0]}
		}).done( function( data ) { 
	     var res = JSON.parse(data);
	     
	        updatevale(res);
	         var utility_id=$("#utility_id").val();
	 // alert(utility_id);
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
			url:'<?php echo $path_to_root . "/maintenance/manage/ajax_get_preventiverecords.php";?>',
			data: { frequency_id : frequency_id,utility_id : utility_id}
		}).done( function( data ) { 
	     //	alert(data);
			
			$("#records").html(data);
			updatevale(res);
		});
		
		});
   
    
});
$('[name^="Update"]').click(function(){
   var text = $(this).attr('name');
    var matches = text.match(/[(0-9)]+/);
     $.ajax({ 
			type: "POST",
			url:'<?php echo $path_to_root . "/maintenance/manage/ajax_get_editpreventiverecords.php";?>',
			data: { selected_id: matches[0]}
		}).done( function( data ) { 
	     var res = JSON.parse(data);
	     
	        updatevale(res);
	         var utility_id=$("#utility_id").val();
	 // alert(utility_id);
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
			url:'<?php echo $path_to_root . "/maintenance/manage/ajax_get_preventiverecords.php";?>',
			data: { frequency_id : frequency_id,utility_id : utility_id}
		}).done( function( data ) { 
	     //	alert(data);
			
			$("#records").html(data);
			updatevale(res);
		});
		
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