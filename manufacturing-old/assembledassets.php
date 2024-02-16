+<?php

/* * **************************************
  /*  Author 	: Kvvaradha
  /*  Module 	: Extended HRM
  /*  E-mail 	: admin@kvcodes.com
  /*  Version : 1.0
  /*  Http 	: www.kvcodes.com
 * *************************************** */

$page_security = 'SA_STATUTORY_USER';
$path_to_root = "..";
include_once($path_to_root . "/includes/db_pager.inc");

//add_access_extensions();
//page(_($help_context = "statutory")); 
include_once($path_to_root . "/admin/db/attachments_db.inc");
include($path_to_root . "/managements/includes/db/statutory_db.inc");
include($path_to_root . "/managements/includes/db/ao.inc");
include($path_to_root . "/includes/session.inc");
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
page(_($help_context = "Explore Assets"), false, false, "", $js);

simple_page_mode(true);

check_db_has_employees(_("There is no employee in this system. Kindly Open <a href='" . $path_to_root . "/modules/ExtendedHRM/manage/employees.php'>Add And Manage Employees</a> to update it"));


$_POST['dynamic'] = 1;


if (isset($_POST['empl_id']) && !empty($_POST['empl_id']))
    $empl_id = $_POST['empl_id'];

if (isset($_GET['NewProduct'])) {
	$_SESSION['page_title'] = _($help_context = "Assemble Assets");
	create_new_ao();
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
	if (strlen($_POST['code']) == 0) 
	{
		display_error(_("The Asset code cannot be empty."));
		set_focus('name');
		return false;
	}
		if (strlen($_POST['asset_category_id']) == 0) 
	{
		display_error(_("The Category cannot be empty."));
		set_focus('name');
		return false;
	}
	
		if (strlen($_POST['asset_sub_cat_name']) == 0) 
	{
		display_error(_("The Sub Category cannot be empty."));
		set_focus('name');
		return false;
	}
// 	if(!empty($_POST['name']))
// 	{
// 		$regex = "/[^ ][ A-Za-z0-9\/\.]$/";
// 		if(preg_match($regex, get_post('name')) ==0) {
// 			display_error( _("Accept only Alphabets."));
// 			set_focus('name');
// 			return false;
// 		} 	
// 	}
	
/* 	if (strlen($_POST['description']) == 0) 
	{
		display_error(_("The Designation description cannot be empty."));
		set_focus('description');
		return false;
	}	
	 */
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
                          if (($order_item->quantity == $_POST['sl']) && $_POST['sl']!='NA') 
  			    {
			                    display_warning(_('Serial number already exist...!'));
                          }
                    }
		    
		}     
			

			 if ($allow_update)
			{ 
				//$myrow = db_fetch($result);
                                //   display_error(count($_SESSION['ao']->line_items)); 
				$_SESSION['ao']->add_to_order (count($_SESSION['ao']->line_items), $_POST['item_name'], !empty($_POST['sl'])?$_POST['sl']:"NA",$_POST['category_id'], $_POST['sub_cat_name'],$_POST['id']);
                           
				//unset_form_variables();
				$_POST['item_name']	= "";
	   		} 
	   		else 
	   		{
			     display_error(_("The selected item does not exist or it is a kit part and therefore cannot be purchased."));
		   	} 

		}  /* end of if not already on the order and allow input was true*/
    }
	line_start_focus();
}
function line_start_focus() {
  global 	$Ajax;

  $Ajax->activate('items_table');
  $Ajax->activate('_page_body');
  set_focus('_stock_id_edit');
}


if ($Mode == 'ADD_ITEM' && can_process()) {
    begin_transaction();
    
    $last_id = is_sub_asset($_POST['asset_id'], $_POST['code']);
    if(!$last_id){
        $last_id = add_sub_asset_main($_POST['asset_id'], $_POST['code'],$_POST['asset_category_id'],$_POST['asset_sub_cat_name'],$_POST['units'],$_POST['mb_flag']);
        $resultsub = get_asset_main(false,$myrow["asset_id"]);
                    $row = db_fetch_row($resultsub);
        add_assetitem($_POST['code'], $row[2], $row[2], $_POST['asset_category_id'],$_POST['asset_sub_cat_name'],
	1, $_POST['units'], $_POST['mb_flag']);
    }else{
        display_error(_("Asset already exists .."));
    }
        if($last_id){
            if (count($_SESSION['ao']->line_items) > 0)
		{  
		  foreach ($_SESSION['ao']->line_items as $line_no => $order_item) 
		    { 
                          if (($order_item->stock_id != $_POST['item_name'] )) 
  			    {
			                    add_assemble_serial_main($_POST['asset_id'], $order_item->item_id, $order_item->quantity,$last_id);
                          }
                    }
                       display_notification(_('Item  Created'));
                    create_new_ao();
                 
                }
            
            commit_transaction();
        }
    
 
        $_POST['upload'] = 1;
  

    // }
    $Ajax->activate('_page_body');


    $Mode = 'RESET';
}

if ($Mode == 'UPDATE_ITEM' && can_process()) {
    if($selected_id != -1)
  //  update_asset($_POST['name'], $_POST['code'], $_POST['name']);
  $last_id = $selected_id;
  $update= true;
  if(strpos($selected_id,'_')){
   $ids = explode('_',$selected_id);
             $line_no = $ids[0];
             $asmbl_id = $ids[1];
             $sub_asset_id = $ids[2];
              $last_id = $sub_asset_id; 
  }
  if(!$last_id){
        $last_id = add_sub_asset_main($_POST['asset_id'], $_POST['code'],$_POST['asset_category_id'],$_POST['asset_sub_cat_name'], $_POST['units'], $_POST['mb_flag']);
        $resultsub = get_asset_main(false,$_POST["asset_id"]);
                    $row = db_fetch_row($resultsub);
        add_assetitem($_POST['code'], $row[2], $row[2], $_POST['asset_category_id'],$_POST['asset_sub_cat_name'],
	1, $_POST['units'], $_POST['mb_flag']);
	$update = false;
    }
    
     if (count($_SESSION['ao']->line_items) > 0 &&  $last_id){
     if($update)
		{   update_sub_asset_main($last_id,$_POST['asset_id'], $_POST['code'],$_POST['asset_category_id'],$_POST['asset_sub_cat_name']);
		$resultsub = get_asset_main(false,$_POST["asset_id"]);
                    $row = db_fetch_row($resultsub);
		update_assetitem($_POST['code'], $row[2], $row[2], $_POST['asset_category_id'],$_POST['asset_sub_cat_name'], 
	1, $_POST['units'], $_POST['mb_flag']);
		    delete_assemble_main($_POST['asset_id'],$last_id);}
		    foreach ($_SESSION['ao']->line_items as $line_no => $order_item) 
		    { 
                          if (($order_item->stock_id != $_POST['item_name'] )) 
  			    {
			                    add_assemble_serial_main($_POST['asset_id'], $order_item->item_id, $order_item->quantity,$last_id);
                          }
                    }
                     display_notification(_('Item  Updated'));
                    create_new_ao();
                }
    $Mode = 'RESET';
}

if ($Mode == 'RESET') {
    $selected_id = -1;
    create_new_ao();
    $_POST['name'] = '';
    $_POST['code'] = '';
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
$result = get_all_sub_asset_main(check_value('show_inactive'));
start_table(TABLESTYLE);
$th = array(_("Asset code"), _('Item Code'), _('Category'), _('Sub Category'), ("Edit"),'');
table_header($th);

$k = 0;

//$result = get_all_return(check_value('show_inactive'));
//echo $result["return_name"][0];
while ($myrow = db_fetch($result)) {
    alt_table_row_color($k);
    $getcode =get_sub_code($myrow["sub_cat_id"]);
                    $category = get_category($myrow["cat_id"]);
                    $resultsub = get_asset_main(false,$myrow["asset_id"]);
                    $row = db_fetch_row($resultsub);
    label_cell($row['2'],'',null,'center');
    label_cell($myrow["code"],'',null,'center');
    label_cell($category['description'],'',null,'center');
    label_cell($getcode['sub_cat_name'],'',null,'center');
    edit_button_cell("Edit" . $myrow['sub_asset_id'], _("Edit"));   
     //inactive_control_cell($myrow["id"], $myrow["inactive"], 'statutory_main', 'id');
    //delete_button_cell("Delete".$myrow['id'], _("Delete"));
    end_row();
}

//inactive_control_row($th);
end_table();
echo '<br>';

//---------------------------------------------------------------------------------------
//viewing_controls();
if($Mode=='Save'){
      $ids = explode('_',$selected_id);
             $line_no = $ids[0];
             $asmbl_id = $ids[1];
            $assetresult = get_all_assets_assembeld($asmbl_id);
               if (count($_SESSION['ao']->line_items) > 0)
		{  
		  foreach ($_SESSION['ao']->line_items as $line_noi => $order_item) 
		    { 
                          if (($order_item->quantity == $_POST['sl']) && $_POST['sl']!='NA') 
  			    {
			                    display_warning(_('Serial number already exist...!'));
                          }
                    }
		    
		} 
            while ($row = db_fetch($assetresult)){
                 $_POST['category_id'] =$row['item_category_id'];
                $_POST['sub_cat_name'] =$row['item_sub_category_id'];
                $_POST['item_name'] = $row['stock_id'];
                $_POST['id'] = $asmbl_id;
                 $_SESSION['ao']->update_order_item($line_no,$_POST['item_name'], $_POST['sl'],$_POST['category_id'], $_POST['sub_cat_name'],$asmbl_id);
                 line_start_focus();
            }
    //  $_POST['id'] =  $asmbl_id; 
    // handle_add_new_item(); 
}

if($Mode=='Uploadedit'){
		     $ids = explode('_',$selected_id);
             $line_no = $ids[0];
             $asmbl_id = $ids[1];
             
             $assetresult = get_all_assets_assembeld($asmbl_id);
            while ($row = db_fetch($assetresult)){
                 $_POST['category_id'] =$row['item_category_id'];
                $_POST['sub_cat_name'] =$row['item_sub_category_id'];
                $_POST['item_name'] = $row['stock_id'];
                $_POST['id'] = $asmbl_id;
            }
    
}


if ($selected_id != -1) {

    if ($Mode == 'Edit') {
             $asset_result =   get_sub_asset_main($selected_id);
              while ($row = db_fetch($asset_result)){
                 $_POST['asset_category_id'] =$row['cat_id'];
                $_POST['asset_sub_cat_name'] =$row['sub_cat_id'];
                $_POST['code'] = $row['code'];
                $_POST['asset_id'] = $row['asset_id'];
                $_POST['sub_asset_id'] = $row['sub_asset_id'];
                $_POST['units'] = $row['units'];
                $_POST['mb_flag'] = $row['mb_flag'];
            }
        }
set_focus('sl');
    hidden('selected_id', $selected_id);
}

br(2);
//echo $_POST['upload'];
if ($Mode != 'View' ) {
    $fresh_item = true;
    start_table(TABLESTYLE2);
       product_code_cells(_('Asset Name'), 'asset_id', $_POST['asset_id'], true);
        text_row("Asset Code", 'code', $_POST['code'], 50,null);
        stock_categories_list_row(_("Category:"), 'asset_category_id', null, false, true);
	$asset_category_id = $_POST['asset_category_id'];
	    sub_category_list_row(_("Sub Category:"), 'asset_sub_cat_name', null, false, true, $asset_category_id);
		$getcode =get_sub_code($_POST['asset_sub_cat_name']);
	   label_row(_("Code:"), $getcode['code']);
	   stock_item_types_list_row(_("Item Type:"), 'mb_flag', null, $fresh_item);
	   stock_units_list_row(_('Units of Measure:'), 'units', null, $fresh_item);
    end_table(1);
    
    
    if(isset($_POST['asset_id']) && $_POST['asset_id'] && $Mode!='Uploadedit' && $Mode !='Save' ){
   // $selected_id = $_POST['asset_id'];
     $result = get_asset_main(false,$_POST['asset_id']);
        create_new_ao();
        while ($myrow = db_fetch($result)) {
           $_POST['code'] = $myrow['code'];
            $_POST['name'] = $_POST['asset_id'];
            $sub_asset_id=$selected_id;
            if(strpos($selected_id,'_') && !$sub_asset_id){
                $ids = explode('_',$selected_id);
             $line_no = $ids[0];
             $asmbl_id = $ids[1];
             $sub_asset_id = $ids[2];}
            $assetresult = get_all_assembeld_main($myrow['asset_id'],$sub_asset_id);
            $counter=$row['qty'];
            if($assetresult->num_rows>0){
            while ($row = db_fetch($assetresult)){
                $_POST['category_id'] =$row['item_category_id'];
                $_POST['sub_cat_name'] =$row['item_sub_category_id'];
                $_POST['item_name'] = $row['stock_id'];
                $_POST['id'] =  $row['id']; 
                $_POST['sl'] = !empty($row['serial_no'])?$row['serial_no']:'NA';
                handle_add_new_item();
                
            }
            }
            else{
                $assetresult = get_all_assembeld_assets($myrow['asset_id']);
                while ($row = db_fetch($assetresult)){
                    for($i= 1 ; $i<=$row['qty'];$i++){
                        $_POST['category_id'] =$row['item_category_id'];
                        $_POST['sub_cat_name'] =$row['item_sub_category_id'];
                        $_POST['item_name'] = $row['stock_id'];
                        $_POST['id'] =  $row['id']; 
                        $_POST['sl'] = 'NA';
                        handle_add_new_item();
                    }
                
            }
            }
    
}

}
    
    
div_start('controls', 'items_table');
start_table(TABLESTYLE2,'width="70%"');
  $th = array(_("Item Category"), _("Item Sub Category"),_("Item code"), _('Item Name'), _('Serial No'), _('#'));
    table_header($th);
            if (count($_SESSION['ao']->line_items) > 0)
		{  
		    $sub_asset_id=$_POST['sub_asset_id'];
		    if(strpos($selected_id,'_')){
                $ids = explode('_',$selected_id);
             $line_no = $ids[0];
             $asmbl_id = $ids[1];
             $sub_asset_id = $ids[2];}
		    foreach ($_SESSION['ao']->line_items as $line_no => $order_item) 
		    {		  addLabels($order_item->item_category, $order_item->item_sub_category, $order_item->stock_id,$order_item->quantity,$line_no,$order_item->item_id,$sub_asset_id);
                    }
            }
                
		   if($Mode=='Uploadedit'){
                
                    $getcode =get_sub_code($_POST['sub_cat_name']);
                    $category = get_category($_POST['category_id']);
                    label_cell($category['description']);
                    label_cell($getcode['sub_cat_name']); 
                    $getcode =get_sub_code($_POST['sub_cat_name']);
                    label_cell($getcode['code']);
                    label_cell($_POST['item_name']); 
                    text_cells(null, 'sl', null, 16);
        		    button_cell('Save' . $selected_id, _("Save"), _("Save"), ICON_UPDATE);  
		   }
    end_table(1);
    submit_add_or_update_center($selected_id == -1, '', 'both');
    div_end();
}
else {
    start_table(TABLESTYLE_NOBORDER);
    start_row();
    label_cells($label, "<button class='ajaxsubmit' type='submit' name='RESET' id='RESET' value='Cancel' title='Remove'><img src='../../themes/default/images/escape.png' height='12' alt=''><span>Cancel</span></button>");
    end_row();
    end_table();
}
end_form();

function addLabels($category_id, $sub_cat_name, $item_name,$qty,$line_no,$item_id,$sub_asset){
    start_row();
    $getcode =get_sub_code($sub_cat_name);
    $category = get_category($category_id);
label_cell($category['description']);
label_cell($getcode['sub_cat_name']); 
$getcode =get_sub_code($sub_cat_name);
            label_cell($getcode['code']);
label_cell($item_name); 
label_cell($qty); 
edit_button_cell("Uploadedit" . $line_no.'_'.$item_id.'_'.$sub_asset, _("Edit"));  
end_row();
}
?>
<?php

end_page();
?>