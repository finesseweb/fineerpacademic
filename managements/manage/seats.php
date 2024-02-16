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
page(_($help_context = "Add Seats"), false, false, "", $js);

simple_page_mode(true);

check_db_has_employees(_("There is no employee in this system. Kindly Open <a href='" . $path_to_root . "/modules/ExtendedHRM/manage/employees.php'>Add And Manage Employees</a> to update it"));


$_POST['dynamic'] = 1;


if (isset($_POST['empl_id']) && !empty($_POST['empl_id']))
    $empl_id = $_POST['empl_id'];

if (isset($_GET['NewProduct'])) {
	$_SESSION['page_title'] = _($help_context = "Assemble Assets");
	create_new_seat();
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
                     if (($order_item->seat_no == $_POST['seat_no']) ) 
  			    {
			                    display_warning(_('Seat  already exist...!'));
			                    $allow_update = false;
                       }
                    }
		    
		}     
			if($_POST['seatleft']<=0 || $_POST['qty']>$_POST['seatleft'])
			$allow_update = false;

			 if ($allow_update)
			{ 
				$_SESSION['ao']->add_to_order (count($_SESSION['ao']->line_items), $_POST['seat_no'], $_POST['qty']);
			//	$_POST['room_no']	= "";
		       // $_POST['seat_no']	= "";
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
    
   $last_id = is_seat( $_POST['name'],  $_POST['floor'], $_POST['room'], $_POST['department']);
    if(!$last_id){
        $last_id = add_seat_master($_POST['name'],  $_POST['floor'], $_POST['room'], $_POST['department']);
        
    }else{
        display_error(_("seat already exists .."));
    }
   // display_error($last_id);
        if($last_id){
            if (count($_SESSION['ao']->line_items) > 0)
		{  
		  foreach ($_SESSION['ao']->line_items as $line_no => $order_item) 
		    { 
        //                   if ((($order_item->department != $_POST['department']))) 
  			   // {
			                    add_seat_allocation($last_id, $order_item->seat_no);
                          //}
                    }
                      display_notification(_('Item  Created'));
                    create_new_seat();
                 
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
           update_seat_master($selected_id,$_POST['name'],  $_POST['floor'], $_POST['room'],$_POST['department']);
		    foreach ($_SESSION['ao']->line_items as $line_no => $order_item) 
		    { 
                          add_seat_allocation($selected_id, $order_item->seat_no);
                    }
                     display_notification(_('Item  Updated'));
                    create_new_seat();
                }
    $Mode = 'RESET';
}

if ($Mode == 'RESET') {
    $selected_id = -1;
    create_new_seat();
    $_POST['seat_no'] = '';
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
$result = get_seat_master(check_value('show_inactive'));
start_table(TABLESTYLE,'attend');
$th = array(_("Building"),("Floor"), _("Room"), _("Department"),("Edit"),'');
table_header($th);

$k = 0;

//$result = get_all_return(check_value('show_inactive'));
//echo $result["return_name"][0];
while ($myrow = db_fetch($result)) {
    alt_table_row_color($k);
    $building = get_product_name($myrow['building']);
      $asset =  db_fetch_row(get_asset_main(false,$myrow['floor']));
      $room = get_room_details($myrow['room']);
       $resultdept  = get_department_allocation_by_id($myrow['department']);
    label_cell($building[1],'',null,'center');
    label_cell($asset[2],'',null,'center');
    label_cell($room[2],'',null,'center');
    label_cell($resultdept['department_id'],'',null,'center');
   edit_button_cell("Edit" . $myrow['id'], _("Edit"));   
   // inactive_control_cell($myrow["id"], $myrow["inactive"], 'fa_department_master', 'id');
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
      if(($_POST['seatleft']<=0 || $_POST['qty']>$_POST['seatleft']))
			$allow_update = false;
				if($allow_update){
                 $_SESSION['ao']->update_order_item($line_no,$_POST['seat_no'],$_POST['qty']);
				}
				else
				{
				    display_error("The quantity you enter is unavailable . !");
				}
                 line_start_focus();
}
if($Mode=='Remove'){
      $line_no = $selected_id;
                 $_SESSION['ao']->remove_from_order($line_no);
                 $Ajax->activate('items_table');
                 line_start_focus();
                 
}



if ($selected_id != -1) {
    if ($Mode == 'Edit') {
         create_new_seat();
        $result = db_fetch(get_seat_master_id($selected_id));
        $results =  get_seat_allocation($selected_id);
           $_POST['name'] =$result['building'];
           $_POST['floor'] =$result['floor'];
           $_POST['room']  = $result['room'];
           $_POST['department']  = $result['department'];
          
            while($myrow = db_fetch($results)){
                $_POST['seat_no']=$myrow['seat_no'];
                $_POST['qty']=1;
                
                $_POST['seatleft']+=1;
                handle_add_new_item(); 
            }
        }
     hidden('selected_id', $selected_id);
        if((!$_POST['edit_id'] || $_POST['edit_id']!=$selected_id) && ($Mode == 'Edit'))
        hidden('edit_id',$selected_id);
        else
        hidden('edit_id',$_POST['edit_id']);
}

br(2);
//echo $_POST['upload'];
if ($Mode != 'View' ) {
    $fresh_item = true;
    start_table(TABLESTYLE2);
    product_list_cells(_('Building Name'), 'name', $_POST['name'], true);
    room_list_row1(_('Floor'), 'floor',$_POST['floor'], true, false,$_POST['name']);
    floor_list_row1(_('Room'), 'room',$_POST['room'], true, false,$_POST['floor']);
    room_list_department_row(_('Department'), 'department',$_POST['department'], true, false,$_POST['room']);
    $Ajax->activate('room');
      $Ajax->activate('department');
    $Ajax->activate('floor');
    $Ajax->activate('items_table');
    end_table(1);
   
}

    
    
div_start('controls', 'items_table');
start_table(TABLESTYLE2,'width="70%"');
  $th = array(_("Seat Id"),('Quantity'),_("#"), _('#'));
    table_header($th);
   $seat_details = get_seat_details($_POST['room'],$_POST['department']);
    $seatcount= get_seat_count($_POST['department']);
   display_error($seatcount);
    $_POST['quantity'] = $seat_details['quantity']-$seatcount;
    $total_quantity = $seat_details['quantity'];
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
		        if( $_POST['seatleft']<=0){
		            $_POST['quantity'] = 0;
		         //   if($selected_id != $line_no && $Mode!=='Save' )
		            addLabels($order_item->seat_no,$order_item->quantity,$line_no);
		          
		        }
		        else{
		          //  if($selected_id != $line_no && $Mode!=='Save')
		        addLabels($order_item->seat_no,$order_item->quantity,$line_no);
		        
		        }
		      //  if($Mode=='Save'){
		      //  addLabels($order_item->seat_no,$order_item->quantity,$line_no);
		      //  $selected_id = -1;
        //         }
		    }
		}
		$_POST['seatleft'] =  $_POST['quantity']-$quantity;
		   if($Mode=='Uploadedit'){
                      $_POST['qty'] = $_SESSION['ao']->line_items[$selected_id]->quantity;
                       $_POST['seatleft'] = $total_quantity - $leftquan;
                       $_POST['seat_no'] = $_SESSION['ao']->line_items[$selected_id]->seat_no;
                    text_cells(null,'seat_no',$_POST['seat_no'],16);
                    text_cells(null,'qty',$_POST['qty'],16);
                    text_cells(null, null,$_POST['seatleft']< 0?0:$_POST['seatleft'], 16,'', $title=false, 
                	$labparams="", $post_label="", $inparams="",$submit_on_change = false, $readonly = true);
                	hidden(_("seatleft"),$_POST['seatleft']< 0?0:$_POST['seatleft']);
        		    button_cell('Save' . $selected_id, _("Save"), _("Save"), ICON_UPDATE);  
		   }else
		   {
    		      text_cells(null,'seat_no',$_POST['seat_no'],16);
    		      text_cells(null,'qty',1,16);
                  text_cells(null, null,$_POST['seatleft']< 0?0:$_POST['seatleft'], 16,'', $title=false, 
                  $labparams="", $post_label="", $inparams="",$submit_on_change = false, $readonly = true);
    	          hidden(_("seatleft"),$_POST['seatleft']< 0?0:$_POST['seatleft']);
                  submit_cells('EnterLine', _("Add Item"), "colspan=1 align='center'",
    		    _('Add new item to document'), true); 
		   }
		    
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

function addLabels($seat_no,$qty,$line_no){
    start_row();
    label_cell($seat_no); 
    label_cell($qty); 
    edit_button_cell("Uploadedit" . $line_no, _("Edit"));  
    delete_button_cell("Remove" . $line_no, _("Remove")); 
    end_row();
}
?>
<?php

end_page();
?>