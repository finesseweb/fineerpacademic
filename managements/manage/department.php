<?php

/* * **************************************
  /*  Author 	: Kvvaradha
  /*  Module 	: Extended HRM
  /*  E-mail 	: admin@kvcodes.com
  /*  Version : 1.0
  /*  Http 	: www.kvcodes.com
 * *************************************** */

$page_security = 'SA_STATUTORY_USER';
$path_to_root = "../..";
include_once($path_to_root . "/includes/db_pager.inc");

//add_access_extensions();
//page(_($help_context = "statutory")); 
include_once($path_to_root . "/admin/db/attachments_db.inc");
include($path_to_root . "/managements/includes/db/statutory_db.inc");
include($path_to_root . "/managements/includes/db/ao.inc");
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include($path_to_root . "/managements/includes/db/nameReturn_db.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include_once($path_to_root . "/inventory/includes/inventory_db.inc");
include_once($path_to_root . "/inventory/includes/db/items_sub_category_db.inc");
include($path_to_root . "/includes/ui.inc");

if (isset($_GET['vw']))
    $view_id = $_GET['vw'];
else
    $view_id = find_submit('view');
if ($view_id != -1) {
    $row = get_employee_cv($view_id);
    if ($row['filename'] != "") {
        if (in_ajax()) {
            $Ajax->popup($_SERVER['PHP_SELF'] . '?vw=' . $view_id);
        } else {
            $type = ($row['filetype']) ? $row['filetype'] : 'application/octet-stream';
            header("Content-type: " . $type);
            header('Content-Length: ' . $row['filesize']);
            //if ($type == 'application/octet-stream')
            //	header('Content-Disposition: attachment; filename='.$row['filename']);
            //else
            header("Content-Disposition: inline");
            $dir = company_path() . '/statutory/' . $_POST['statutory'] . $_POST['return_name'] . $_POST['freq_name'] . $_POST['year'];
            echo file_get_contents($dir . $row['unique_name']);
            exit();
        }
    }
}
if (list_updated('category_id')) {
	$Ajax->activate('sub_cat_name');
}
if (list_updated('sub_cat_name')) {
	$Ajax->activate('item_name');
}
$empl_id = $_SESSION['wa_current_user']->empl_id;

$version_id = get_company_prefs('version_id');

$js = '';
if ($version_id['version_id'] == '2.4.1') {
    if ($SysPrefs->use_popup_windows)
        $js .= get_js_open_window(900, 500);

    if (user_use_date_picker())
        $js .= get_js_date_picker();
}else {
    if ($use_popup_windows)
        $js .= get_js_open_window(900, 500);
    if ($use_date_picker)
        $js .= get_js_date_picker();
}
page(_($help_context = "Add Department"), false, false, "", $js);

simple_page_mode(true);

check_db_has_employees(_("There is no employee in this system. Kindly Open <a href='" . $path_to_root . "/modules/ExtendedHRM/manage/employees.php'>Add And Manage Employees</a> to update it"));


$_POST['dynamic'] = 1;


if (isset($_POST['empl_id']) && !empty($_POST['empl_id']))
    $empl_id = $_POST['empl_id'];

if (isset($_GET['NewProduct'])) {
	$_SESSION['page_title'] = _($help_context = "Assemble Assets");
	create_new_depart();
}
if (isset($_POST['EnterLine'])){
     handle_add_new_item(); 
}

if(isset($_POST['asset_category_id'])){
    global 	$Ajax;
    $Ajax->activate('asset_sub_cat_name');
}
         


function can_process() 
{
// 	if (strlen($_POST['code']) == 0) 
// 	{
// 		display_error(_("The Asset code cannot be empty."));
// 		set_focus('name');
// 		return false;
// 	}
// 		if (strlen($_POST['asset_category_id']) == 0) 
// 	{
// 		display_error(_("The Category cannot be empty."));
// 		set_focus('name');
// 		return false;
// 	}
	
// 		if (strlen($_POST['asset_sub_cat_name']) == 0) 
// 	{
// 		display_error(_("The Sub Category cannot be empty."));
// 		set_focus('name');
// 		return false;
// 	}

	return true;
}

function handle_add_new_item()
{       
$allow_update = true;
	if ($allow_update == true)
	{ 
            $newIndex = count($_SESSION['ao']->line_items);
		

		 if ($allow_update == true)
		{
                    if (count($_SESSION['ao']->line_items) > 0)
		{  
		  foreach ($_SESSION['ao']->line_items as $line_no => $order_item) 
		    { 
                     if (($order_item->department == $_POST['department']) ) 
  			    {
			                    display_warning(_('department  already exist...!'));
                       }
                    }
		    
		}   
			if(($_POST['seatleft']<=0 || $_POST['qty']>$_POST['seatleft']))
			$allow_update = false;

			 if ($allow_update)
			{ 
				$_SESSION['ao']->add_to_order (count($_SESSION['ao']->line_items), $_POST['department'], $_POST['qty'],$_POST['depart_id']);
			//	$_POST['room_no']	= "";
		        $_POST['qty']	= "";
	   		} 
	   		else 
	   		{
			     display_error(_("The quantity of seat is unavailable"));
		   	} 

		}  /* end of if not already on the order and allow input was true*/
    }
	line_start_focus();
}
function line_start_focus() {
  global $Ajax;

  $Ajax->activate('items_table');
  $Ajax->activate('_page_body');
}


if ($Mode == 'ADD_ITEM' && can_process()) {
    begin_transaction();
    
   $last_id = is_deparment( $_POST['name'],  $_POST['floor'], $_POST['room']);
    if(!$last_id){
        $last_id = add_department_master($_POST['name'],  $_POST['floor'], $_POST['room']);
        
    }else{
        display_error(_("Department already exists .."));
    }
   // display_error($last_id);
        if($last_id){
            if (count($_SESSION['ao']->line_items) > 0)
		{  
		  foreach ($_SESSION['ao']->line_items as $line_no => $order_item) 
		    { 
        //                   if ((($order_item->department != $_POST['department']))) 
  			   // {
			                    add_department_allocation($last_id, $order_item->department, $order_item->quantity);
                          //}
                    }
                      display_notification(_('Item  Created'));
                    create_new_depart();
                 
                }
            
            commit_transaction();
        }
    
 
        $_POST['upload'] = 1;
  

    // }
    $Ajax->activate('_page_body');


    $Mode = 'RESET';
}

if ($Mode == 'UPDATE_ITEM' ) {
   
  $selected_id=$_POST['edit_id'];
  $update= true;
  
   
     if (count($_SESSION['ao']->line_items) > 0){
           update_department_master($selected_id,$_POST['name'],  $_POST['floor'], $_POST['room']);
		    foreach ($_SESSION['ao']->line_items as $line_no => $order_item) 
		    { 
                          add_department_allocation($selected_id, $order_item->department, $order_item->quantity);
                    }
                     display_notification(_('Item  Updated'));
                    create_new_depart();
                }
    $Mode = 'RESET';
}

if ($Mode == 'RESET') {
    $selected_id = -1;
    $_POST['edit_id'] = -1;
    create_new_depart();
    $_POST['department'] = '';
    $_POST['qty'] = '';
}



function edit_link($row) {
    return button('Edit' . $row["id"], _("Edit"), _("Edit"), ICON_EDIT);
    hidden('id', $row["id"]);
}

function view_link($row) {
    return button('view' . $row["id"], _("View"), _("View"), ICON_VIEW);
}

function download_link($row) {
    return button('Download' . $row["id"], _("Download"), _("Download"), ICON_DOWN);
}

function delete_link($row) {
    submit_js_confirm("Delete" . $row["id"], sprintf(_("You are about to delete a Department Do you want to continue?"), $row["id"]));
    return button('Delete' . $row["id"], _("Delete"), _("Delete"), ICON_DELETE);
    hidden('id', $row["id"]);
}

start_form(true);
$result = get_department_master(false);
start_table(TABLESTYLE,'attend');
$th = array(_("Building"),("Floor"), _("Room"),("Edit"),'');
//inactive_control_column_with_edit_only($th);
table_header($th);

$k = 0;

//$result = get_all_return(check_value('show_inactive'));
//echo $result["return_name"][0];
while ($myrow = db_fetch($result)) {
    alt_table_row_color($k);
    $building = get_product_name($myrow['building']);
      $asset =  db_fetch_row(get_asset_main(false,$myrow['floor']));
      $room = get_room_details($myrow['room']);
      
    label_cell($building[1],'',null,'center');
    label_cell($asset[2],'',null,'center');
    label_cell($room[2],'',null,'center');
  
    edit_button_cell("Edit" . $myrow['id'], _("Edit"));   
   //  inactive_control_cell($myrow['room'], $myrow["inactive_room"], 'room_main', 'id');
    //delete_button_cell("Delete".$myrow['id'], _("Delete"));
    end_row();
}

//inactive_control_row($th);
end_table();
echo '<br>';

//---------------------------------------------------------------------------------------
//viewing_controls();
if($Mode=='Save'){
      $line_no = $selected_id;
      $allow_update = true;
      $row = get_alloted_seat($_POST['room'],$_POST['department']);
      if(($_POST['seatleft']<=0 || $_POST['qty']>$_POST['seatleft'] || $_POST['qty'] < $row['qty']))
			$allow_update = false;
			if($allow_update){
                 $_SESSION['ao']->update_order_item($line_no,$_POST['department'], $_POST['qty']);
			}
			else{
			    display_error("Seat quantity is unavailable");
			}
			if((!$_POST['edit_id']))
                $selected_id = -1;
                 line_start_focus();
}
if($Mode=='Remove'){
      $line_no = $selected_id;
      $row = get_alloted_seat($_POST['room'],$_POST['department']);
      $allow_update = true;
         if(($row['qty']))
			$allow_update = false;
			if($allow_update){
                 $_SESSION['ao']->remove_from_order($line_no);
			}
			else
			{
			     display_error("Alloted Department cannot be removed !;");
			}
                 line_start_focus();
}



if ($selected_id != -1) {
    if ($Mode == 'Edit') {
           create_new_depart();
        $result = db_fetch(get_department_master_id($selected_id));
        $results =  get_department_allocation($selected_id);
           $_POST['name'] =$result['building'];
           $_POST['floor'] =$result['floor'];
           $_POST['room']  = $result['room'];
           
            while($myrow = db_fetch($results)){
                $_POST['department']=$myrow['department_id'];
                $_POST['qty']=$myrow['quantity'];
                $_POST['seatleft']+=$myrow['quantity'];
                $_POST['depart_id'] = $myrow['id'];
                handle_add_new_item();}
        }
        
    
        hidden('selected_id', $selected_id);
        if((!$_POST['edit_id'] || $_POST['edit_id']!=$selected_id) && ($Mode == 'Edit'))
        hidden('edit_id',$selected_id);
        else
        hidden('edit_id',$_POST['edit_id']);
}
br(2);
if ($Mode != 'View' ) {
    $fresh_item = true;
    start_table(TABLESTYLE2);
    product_list_cells(_('Building Name'), 'name', $_POST['name'], true);
    room_list_row1(_('Floor'), 'floor',$_POST['floor'], true, false,$_POST['name']);
    floor_list_row1(_('Room'), 'room',$_POST['room'], true, false,$_POST['floor']);
    $Ajax->activate('floor');
    $Ajax->activate('room');
    $Ajax->activate('items_table');
    end_table(1);
   
}

    
    
div_start('controls', 'items_table');
start_table(TABLESTYLE2,'width="70%"');
  $th = array(_("Department"), _("Seating Capacity"),_("#"), _('#'),_(''));
  inactive_control_column_with_edit_only($th);
    table_header($th);
    $room_details = get_room_details($_POST['room']);
    $_POST['quantity'] = $room_details['3'];
    $total_quantity = $room_details['3'];
     
    if (count($_SESSION['ao']->line_items) > 0)
		{  
            // $line_no = $selected_id;
             $quantity = 0;
             $leftquan = 0;
		    foreach ($_SESSION['ao']->line_items as $line_no => $order_item) 
		    {
		        $quantity+= $order_item->quantity;
		        if($selected_id != $line_no )
		        $leftquan+= $order_item->quantity;
		        
              
		        if( $_POST['seatleft']<0){
		            $_POST['quantity'] = 0;
		            addLabels($order_item->department, $order_item->quantity,$line_no,$order_item->depart_id);
		        }
		        else{
		        addLabels($order_item->department, $order_item->quantity,$line_no,$order_item->depart_id);
		        }
		    }
		}
		$_POST['seatleft'] =  $_POST['quantity']-$quantity;
		   if($Mode=='Uploadedit'){
                  $_POST['qty'] = $_SESSION['ao']->line_items[$selected_id]->quantity;
                   $_POST['seatleft'] = $total_quantity - $leftquan;
                   $_POST['department'] = $_SESSION['ao']->line_items[$selected_id]->department;
                    department_list_cellsone(false, 'department', $_POST['department']);
                    text_cells(null, 'qty', $_POST['qty'], 16);
                    text_cells(null, null,$_POST['seatleft']< 0?0:$_POST['seatleft'], 16,'', $title=false, 
                	$labparams="", $post_label="", $inparams="",$submit_on_change = false, $readonly = true);
                	hidden(_("seatleft"),$_POST['seatleft']< 0?0:$_POST['seatleft']);
        		    button_cell('Save' . $selected_id, _("Save"), _("Save"), ICON_UPDATE);  
		   }else
		   {
		      department_list_cellsone(null, 'department', null);
              text_cells(null, 'qty', null, 16);
              text_cells(null, null,$_POST['seatleft']< 0?0:$_POST['seatleft'], 16,'', $title=false, 
              $labparams="", $post_label="", $inparams="",$submit_on_change = false, $readonly = true);
	          hidden(_("seatleft"),$_POST['seatleft']< 0?0:$_POST['seatleft']);
              submit_cells('EnterLine', _("Add Item"), "colspan=1 align='center'",
		    _('Add new item to document'), true); 
		   }
		   inactive_control_row($th); 
    end_table(1);
    submit_add_or_update_center($selected_id == -1, '', 'both');
    div_end();

if($selected_id != -1){
    start_table(TABLESTYLE_NOBORDER);
    start_row();
    end_row();
    end_table();
}
end_form();

function addLabels($department,$quantity,$line_no,$depart_id){
    start_row();
 //   $result  = get_department($department);
        $result  = get_department_des($department);
    
label_cell($result['description']); 
label_cell($quantity); 
 
edit_button_cell("Uploadedit" . $line_no, _("Edit"));  
delete_button_cell("Remove" . $line_no, _("Remove")); 
if($depart_id){
$myrow = get_department_allocation_by_id($depart_id);
inactive_control_cell($depart_id, $myrow['inactive'], 'department_allocation','id');
}
end_row();
}
?>
<?php

end_page();
?>