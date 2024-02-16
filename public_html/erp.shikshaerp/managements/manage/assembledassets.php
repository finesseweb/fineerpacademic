+<?php

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
page(_($help_context = "Item SL No"), false, false, "", $js);

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
                          
                          if (($order_item->osl == $_POST['osl']) && $_POST['osl']!='NA') 
  			    {
			                    display_warning(_('Official Serial number already exist...!'));
                          }
                    }
		    
		}     
			

			 if ($allow_update)
			{ 
				//$myrow = db_fetch($result);
                                //   display_error(count($_SESSION['ao']->line_items)); 
				$_SESSION['ao']->add_to_order(count($_SESSION['ao']->line_items), $_POST['item_name'], !empty($_POST['sl'])?$_POST['sl']:"NA",$_POST['category_id'], $_POST['sub_cat_name'],$_POST['id'],!empty($_POST['osl'])?$_POST['osl']:"NA");
                           
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
    
//     $last_id = is_sub_asset($_POST['asset_id'], $_POST['code']);
//     if(!$last_id){
//         $last_id = add_sub_asset_main($_POST['asset_id'], $_POST['code'],$_POST['asset_category_id'],$_POST['asset_sub_cat_name'],$_POST['units'],$_POST['mb_flag']);
//         $resultsub = get_asset_main(false,$myrow["asset_id"]);
//                     $row = db_fetch_row($resultsub);
//         add_assetitem($_POST['code'], $row[2], $row[2], $_POST['asset_category_id'],$_POST['asset_sub_cat_name'],
// 	1, $_POST['units'], $_POST['mb_flag']);
//     }else{
//         display_error(_("Asset already exists .."));
//     }
//         if($last_id){
//             if (count($_SESSION['ao']->line_items) > 0)
// 		{  
// 		  foreach ($_SESSION['ao']->line_items as $line_no => $order_item) 
// 		    { 
//                           if (($order_item->stock_id != $_POST['item_name'] )) 
//   			    {
// 			                    add_assemble_serial_main($_POST['asset_id'], $order_item->item_id, $order_item->quantity,$last_id);
//                           }
//                     }
//                       display_notification(_('Item  Created'));
//                     create_new_ao();
                 
//                 }
            
//             commit_transaction();
//         }
    
 
//         $_POST['upload'] = 1;
  

//     // }
//     $Ajax->activate('_page_body');


//     $Mode = 'RESET';
}

if ($Mode == 'UPDATE_ITEM' ) {
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
    delete_grn_serial($_POST['asset_id'],$_POST['stock_id']);
     if (count($_SESSION['ao']->line_items) > 0 ){
		    foreach ($_SESSION['ao']->line_items as $line_no => $order_item) 
		    { 
                          if (($order_item->stock_id != $_POST['item_name'] )) 
  			    {
			                    add_grn_serial_main($_POST['asset_id'],  $order_item->quantity,$order_item->stock_id,$_POST['warranty'],$_POST['to_date'],$_POST['from_date'],$order_item->osl);
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
$result = get_all_grn_main(check_value('show_inactive'));
start_table(TABLESTYLE,'attend');
$th = array(_("Purchase  NO"),("Stock Id"), _("Warranty"),_('From Date'),_('To Date'),("Edit"),'');
table_header($th);

$k = 0;

//$result = get_all_return(check_value('show_inactive'));
//echo $result["return_name"][0];
while ($myrow = db_fetch($result)) {
   
    alt_table_row_color($k);
    label_cell($myrow['pro_type'].'-'.$myrow['grn_batch_id'],'',null,'center');
    label_cell($myrow['stock_id'],'',null,'center');
    label_cell($myrow['warranty'].' year','',null,'center');
    label_cell(sql2date($myrow['from_date']),'',null,'center');
    label_cell(sql2date($myrow['to_date']),'',null,'center');
    edit_button_cell("Edit" . $myrow['grn_batch_id'].'-'.$myrow['stock_id'], _("Edit"));   
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
                $grn_id = $ids[2];
        $_POST['sub_asset_id'] = $grn_id;
            $assetresult = get_stock_master($asmbl_id,$_POST['asset_id']);
               if (count($_SESSION['ao']->line_items) > 0)
		{  
		  foreach ($_SESSION['ao']->line_items as $line_noi => $order_item) 
		    { 
                          if (($order_item->quantity == $_POST['sl']) && $_POST['sl']!='NA') 
  			    {
			                    display_warning(_('Serial number already exist...!'));
                          }
                          
                          if (($order_item->osl == $_POST['osl']) && $_POST['osl']!='NA') 
  			    {
			                    display_warning(_('Official Serial number already exist...!'));
                          }
                    }
		    
		} 
            while ($row = db_fetch($assetresult)){
                 $_POST['category_id'] =$row['category_id'];
                $_POST['sub_cat_name'] =$row['sub_cat_name'];
                $_POST['item_name'] = $row['stock_id'];
                $_POST['id'] = $asmbl_id;
                 $_SESSION['ao']->update_order_item($line_no,$_POST['item_name'], $_POST['sl'],$_POST['category_id'], $_POST['sub_cat_name'],$asmbl_id,$_POST['osl']);
                 line_start_focus();
            }
    //  $_POST['id'] =  $asmbl_id; 
    // handle_add_new_item(); 
}

if($Mode=='Uploadedit'){
		     $ids = explode('_',$selected_id);
             $line_no = $ids[0];
             $asmbl_id = $ids[1];
                 $grn_id = $ids[2];
        $_POST['sub_asset_id'] = $grn_id;
             $assetresult = get_stock_master($asmbl_id,$_POST['asset_id']);
            while ($row = db_fetch($assetresult)){
                 $_POST['category_id'] =$row['category_id'];
                $_POST['sub_cat_name'] =$row['sub_cat_name'];
                $_POST['item_name'] = $row['stock_id'];
                $_POST['id'] = $asmbl_id;
            }
    
}


if ($selected_id != -1) {
    if ($Mode == 'Edit') {
        $ids = explode('-',$selected_id);
         $_POST['sub_asset_id'] =$ids[0];
         $_POST['stock_id'] = $ids[1];
             $asset_result =   get_grn_main(false,$_POST['sub_asset_id'],$_POST['stock_id']);
              $sl_result = get_all_grn_main_order_no(check_value('show_inactive'),$_POST['sub_asset_id'],$_POST['stock_id']);
              $sl_result_fetched = db_fetch_row($sl_result);
              while ($row = db_fetch($asset_result)){
                 $_POST['asset_category_id'] =$row['category_id'];
                $_POST['asset_sub_cat_name'] =$row['sub_cat_name'];
                $_POST['code'] = $row['item_code'];
                $_POST['warranty'] = $sl_result_fetched['3'];
                $_POST['stock_id'] = $row['stock_id'];
                $_POST['to_date'] = sql2date($sl_result_fetched['4']);
                $_POST['from_date'] = sql2date($sl_result_fetched['5']);
                $_POST['asset_id'] = $_POST['sub_asset_id'];
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
    if($Mode !='Edit' && !$_POST['sub_asset_id']){
       grn_list_cells1(_('Select PO/MO'), 'asset_id', $_POST['asset_id'], true);
    }
    else
     grn_list_cells2(_('Select PO/MO'), 'asset_id', $_POST['asset_id'], true);
       //	type_list_row(_("Type:"), 'types', null, false, true);
       stock_items_list_row(_("Select an item:"), 'stock_id', null,
	  null, true,$_POST['asset_id']);
  $Ajax->activate('stock_id');
  $type = get_item_types($_POST['stock_id'],$_POST['asset_id']);
  
if($type[0]==1){
    if($Mode !=='Edit'){
    $datetime1 = date_create($type[1]);
  $datetime2 = date_create(date());
  $interval = date_diff($datetime1, $datetime2);
  $days = $datetime1>$datetime2?$interval->format('%R%d'):-$interval->format('%d');
  $month = $datetime1>$datetime2?$interval->format('%R%m'):-$interval->format('%m');
  $year = $datetime1>$datetime2?$interval->format('%R%y'):-$interval->format('%y');
  $_POST['warranty'] = !empty($_POST['warranty'])?$_POST['warranty']:$type[2];
   $incdate = year2monthsNdays($_POST['warranty']);
 $date = date("Y-m-d");
  $_POST['to_date'] = sql2date(date('Y-m-d', strtotime($date. " $days days $month month  $year year")));
  $lastmonth = ($month+$incdate['month']);
  $lastyear = ($year+$incdate['year']);
  $_POST['from_date'] = sql2date(date('Y-m-d', strtotime($date. " $days days +$lastmonth month +$lastyear year")));
    }
text_row(_("Warranty:"), 'warranty',$_POST['warranty'], 20, 200);
date_row(_("From Date") . ":", 'to_date',20,null);
date_row(_("To Date") . ":", 'from_date',20,null);
}
    end_table(1);
   
    
    if(isset($_POST['asset_id']) && $_POST['asset_id'] && $Mode!='Uploadedit' && $Mode !='Save' ){
   // $selected_id = $_POST['asset_id'];
        
     $result = get_grn_main(false,$_POST['asset_id'],$_POST['stock_id']);
        create_new_ao();
       // $myrow = db_fetch($result);
       // display_error($selected_id);
        while ($myrow = db_fetch($result)) {
             $_POST['code'] = $myrow['item_code'];
            $_POST['name'] = $_POST['asset_id'];
            $sub_asset_id=$selected_id;
            if(strpos($selected_id,'_') && !$sub_asset_id){
                $ids = explode('_',$selected_id);
             $line_no = $ids[0];
             $asmbl_id = $ids[1];
             $sub_asset_id = $ids[2];}
            $assetresult = get_all_grn($_POST['asset_id'],$myrow['item_code']);
            $counter=$row['qty'];
            if($assetresult->num_rows>0){
            while ($row = db_fetch($assetresult)){
                $_POST['category_id'] =$row['category_id'];
                $_POST['sub_cat_name'] =$row['sub_cat_name'];
                $_POST['item_name'] = $row['stock_id'];
                $_POST['id'] = $row['stock_id']; 
                $_POST['sl'] = !empty($row['sl_no'])?$row['sl_no']:'NA';
                $_POST['osl'] = !empty($row['osl_no'])?$row['osl_no']:'NA';
                handle_add_new_item();
                
            }
            }
            else{
    
                $assetresult = get_stock_master($_POST['code'],$_POST['asset_id']);
                
                while ($row = db_fetch($assetresult)){
                    for($i= 1 ; $i<=$myrow['quantity_received'];$i++){
                        $_POST['category_id'] =$row['category_id'];
                        $_POST['sub_cat_name'] =$row['sub_cat_name'];
                        $_POST['item_name'] = $row['stock_id'];
                        $_POST['id'] =  $row['stock_id']; 
                        $_POST['sl'] = 'NA';
                        $_POST['osl'] = 'NA';
                        handle_add_new_item();
                    }
                
            }
            }
    
}
line_start_focus();
}
    
    
div_start('controls', 'items_table');
start_table(TABLESTYLE2,'width="70%"');
  $th = array(_("Item Category"), _("Item Sub Category"),_("Item code"), _('Item Name'), _('Serial No'),('Official serial No.'), _('#'));
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
		    {		  addLabels($order_item->item_category, $order_item->item_sub_category, $order_item->stock_id,$order_item->quantity,$line_no,$order_item->item_id,$sub_asset_id,$order_item->osl);
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
                    text_cells(null, 'sl', $_POST['sl'], 16);
                    text_cells(null, 'osl', $_POST['osl'], 16);
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

function addLabels($category_id, $sub_cat_name, $item_name,$qty,$line_no,$item_id,$sub_asset,$osl){
    start_row();
$getcode =get_sub_code($sub_cat_name);
$category = get_category($category_id);
label_cell($category['description']);
label_cell($getcode['sub_cat_name']); 
$getcode = get_sub_code($sub_cat_name);
label_cell($getcode['code']);
label_cell($item_name); 
label_cell($qty); 
label_cell($osl); 
edit_button_cell("Uploadedit" . $line_no.'_'.$item_id.'_'.$sub_asset, _("Edit"));  
end_row();
}
?>
<?php

end_page();
?>