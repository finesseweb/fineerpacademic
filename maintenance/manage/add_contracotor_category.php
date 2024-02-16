<?php

$page_security = 'SA_MAINTAINBREAKDOWN';
$path_to_root = "../..";


include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
page(_($help_context = "Contractor Maintenance ")); 

//include($path_to_root . "/maintenance/includes/db/parameters_db.inc");
include($path_to_root . "/includes/ui.inc");

//include_once($path_to_root . "/manufacturing/includes/manufacturing_db.inc");
//include_once($path_to_root . "/manufacturing/includes/manufacturing_ui.inc");
$_SESSION['page_title'] = _($help_context = "New Breakdown");
 
//page($_SESSION['page_title'], false, false, "", $js);
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
	display_notification("Breakdown Maintenance has been added!");
}
start_form();
    if (get_post('Show'))
{
	$Ajax->activate('trans_tbl');
}
//------------------------------------------------------------------------------------------------


//------------------------------------------------------------------------------------------------
$query = "SELECT prev.*,s.sub_cat_name AS Item,cat.description,con.supp_name AS contractor,con.contact,con.email,con.phone FROM ".TB_PREF."merge_contractor AS prev LEFT JOIN ".TB_PREF."stock_category AS cat ON cat.category_id= prev.cat_id  LEFT JOIN ".TB_PREF."stock_sub_category s ON s.sub_cat_id= prev.subcat_id LEFT JOIN ".TB_PREF."contractor con ON con.supplier_id= prev.contractor_id";
//display_error($query);
$res=db_query($query);
//$result=db_fetch($res);

div_start('trans_tbl');
start_table(TABLESTYLE);

$th = array(_("#"),_("Category"), _("Sub category"),
	_("Contractor"),_("Contact Person"),_("Email ID"),_("Phone"),_("EDIT"));
table_header($th);


function get_trans($break_id,$type)
{

	$label = $break_id;
	$class ='';
	$id=$break_id;
	$icon = '';
	 $viewer = $path_to_root."maintenance/view/";
	if ($type == 'Contrator')
	hidden('Break_id',$break_id);
		$viewer .= "maintain_breakdown_view.php?id=".$break_id;
	
	return viewer_link($label, $viewer, $class, $id,  $icon);
	
}

$k = 0; 
$i=1; //row colour counter
while ($myrow = db_fetch($res))
{
	alt_table_row_color($k);
        label_cell($i);
	label_cell($myrow["description"]);
	label_cell($myrow["Item"]);
	
	label_cell($myrow["contractor"]);
        label_cell($myrow["contact"]);
        label_cell($myrow["email"]);
        label_cell($myrow["phone"]);
  //  $nextpendingdate = date("d-m-Y", strtotime($myrow["frequency_des"], strtotime($myrow["prv_ob_date"])));
	//label_cell($myrow["break_st_time"]);
	//label_cell($myrow["break_end_time"]);
	//label_cell($myrow["ob_reason"]);
	//label_cell($myrow["ob_1"]);
	//label_cell($myrow["ob_2"]);
	 edit_button_cell("Edit" . $myrow['id'], _("Edit")); 
	 delete_button_cell("Update".$myrow['id'], _("Update"));
	end_row();
 
	$i++;
}

end_table(2);
div_end();



end_form();
//display_error($_POST['maintain_date']);
?>
<form id="basicForm" action="add_contrator_maintenance.php" method="POST" >
<table width="30%" align="center" style="padding:10px;">
<tr>
<input type="hidden" id="id" name="id" value="">   
<td> Category
     
     <select type="text" name="cat_id" id="cat_id" label="cat0" onchange="stockcat(this.value);">
	<?php $sql= "SELECT sc.category_id, sc.description, sc.inactive FROM ".TB_PREF."stock_category sc join fa_utility u on u.category_id=sc.category_id  where u.maintenance_type_id=1 group by u.category_id "; 
   $res=db_query($sql);
     echo "<option>Select</option>";
	while($row = db_fetch($res))
	{ 
	?>  
	<option value="<?php echo $row['category_id']; ?>"><?php echo $row['description']; ?></option>
	<?php  
	} ?>
  </select>
     
 </td>
 <td width="5px">
     Sub category
	<select type="text" name="sub_cat_id" id="subcat_id" label="sub_cat" onchange="stock_item(this.value);" >
	<?php $sql= "SELECT sub_cat_id,sub_cat_name FROM ".TB_PREF."stock_sub_category"; 
   $res=db_query($sql);
     echo "<option>Select</option>";
	while($row = db_fetch($res))
	{ 
	?>  
	<option value="<?php echo $row['sub_cat_id']; ?>"><?php echo $row['sub_cat_name']; ?></option>
	<?php  
	} ?>
  </select>
	</td> 
 
  
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

 <hr>
  

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
  $('#update_entry').hide();
$("#utility_id").change(function() {
	 var utility_id=$("#utility_id").val();
	//  alert(utility_id);
	 
	 $.ajax({ 
			type: "POST",
			url:'<?php echo $path_to_root . "/maintenance/manage/ajax_get_breakdownrecords.php";?>',
			data: { utility_id : utility_id}
		}).done( function( data ) { 
	     //	alert(data);
			
			$("#records").html(data);
		});
	/* $.ajax({ 
			type: "POST",
			url:'<?php echo $path_to_root . "/maintenance/manage/ajax_get_breakdownfrequency.php";?>',
			data: { utility_id : utility_id}
		}).done( function( data ) { 
	     //	alert(data);
			$("#frequency_id").html(data);
		}); */
});
/* $("#frequency_id").change(function() {
	var frequency_id=$(this).val();
	 var utility_id=$("#utility_id").val();
	//  alert(utility_id);
	 
	  $.ajax({ 
			type: "POST",
			url:'<?php echo $path_to_root . "/maintenance/manage/ajax_get_breakdownrecords.php";?>',
			data: { frequency_id : frequency_id,utility_id : utility_id}
		}).done( function( data ) { 
	     //	alert(data);
			
			$("#records").html(data);
		});
});  */ 
     var utility_id=$("#utility_id").val();
     
    $.ajax({ 
			type: "POST",
			url:'<?php echo $path_to_root . "/maintenance/manage/ajax_get_breakdownrecords.php";?>',
			data: { utility_id : utility_id}
		}).done( function( data ) { 
	     //	alert(data);
			
			$("#records").html(data);
		});  
$('[name^="Edit"]').click(function(){
   var text = $(this).attr('name');
  
    var matches = text.match(/[(0-9)]+/);
     //alert(matches);
     $.ajax({ 
	type: "POST",
			url:'<?php echo $path_to_root . "/maintenance/manage/ajax_get_editcontractorcatrecords.php";?>',
			data: { selected_id: matches[0]}
		}).done( function( data ) { 
                    
	            var res = JSON.parse(data);
                   // alert(data);
	     //$("#records").html(data)
                   
	        // var utility_id=$("#utility_id").val();
	 // alert(utility_id);
//	 $.ajax({ 
//			type: "POST",
//			url:'<?php echo $path_to_root . "/maintenance/manage/ajax_get_utlyfrequency.php";?>',
//			data: { utility_id : utility_id}
//		}).done( function( data ) { 
//	     //	alert(data);
//			$("#frequency_id").html(data);
//			$("#frequency_id").val(res['frequency_id']);
//		});
		
	//var frequency_id=res['frequency_id'];
	// var utility_id=$("#utility_id").val();
	//  alert(utility_id);
	 
//	  $.ajax({ 
//			type: "POST",
//			url:'<?php echo $path_to_root . "/maintenance/manage/ajax_get_preventiverecords.php";?>',
//			data: { frequency_id : frequency_id,utility_id : utility_id}
//		}).done( function( data ) { 
//	     //	alert(data);
//			
//			$("#records").html(data);
//			updatevale(res);
//		});
		updatevale(res);
                $('#add_entry').hide();
                $('#update_entry').show();
		});
   
    
});


$('[name^="Update"]').click(function(){
   var text = $(this).attr('name');
    var matches = text.match(/[(0-9)]+/);
     $.ajax({ 
	 type: "POST",
	url:'<?php echo $path_to_root . "/maintenance/manage/ajax_update_status_break.php";?>',
	data: { selected_id: matches[0]}
		}).done( function( data ) { 
                    
                //  alert(data);  
	      location.reload();
	     
		});
   
    
});

 var updatevale = (res)=>{
    for(x in res){
	        $('#'+x).val(res[x]);
	        }
}





$("#cat_id").change(function() {


 //var sub_cat=$("#sub_cat_id").val();
          var cat=$("#cat_id").val();
	$.ajax({ 
			type: "POST",
			url:'<?php echo $path_to_root . "/maintenance/manage/ajax_get_sub_cat.php";?>',
			data: { cat : cat}
		}).done( function( data ) { 
	     //	alert(data);
		
			$("#subcat_id").html(data);
		
		  var sub_cat = $("#subcat_id").val();
	   // alert(cat);
		// alert(sub_cat);
		$.ajax({ 
			type: "POST",
			url:'<?php echo $path_to_root . "/maintenance/manage/ajax_get_item.php";?>',
			data: { cat : cat ,sub_cat : sub_cat }
		}).done( function( data ) { 
	     	//alert(data);
		
                $("#utility_id").html(data);
			$("#items_id").html(data);
		     
			 var item = $("#items_id").val();	
	    $.ajax({ 
			type: "POST",
			url:'<?php echo $path_to_root . "/maintenance/manage/ajax_get_breakdown_stock.php";?>',
			data: { item : item}
		}).done( function( data ) { 
	    // 	alert(data);
		 var result = $.parseJSON(data);
			$("#stock_qty").val(result[0]);
		  });	
		
		   });
  });	
});


$("#sub_cat_id").change(function() {
	 var sub_cat=$("#sub_cat_id").val();
          var cat=$("#cat_id").val();
	 // alert(utility_id);
	$.ajax({ 
			type: "POST",
			url:'<?php echo $path_to_root . "/maintenance/manage/ajax_get_item.php";?>',
			data: { cat : cat ,sub_cat : sub_cat }
		}).done( function( data ) { 
	     	//alert(data);
		
                $("#utility_id").html(data);
		});
});
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