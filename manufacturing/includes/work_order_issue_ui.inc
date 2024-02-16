<?php
/**********************************************************************
    Copyright (C) FrontAccounting, LLC.
	Released under the terms of the GNU General Public License, GPL, 
	as published by the Free Software Foundation, either version 3 
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/
include_once($path_to_root . "/includes/db_pager.inc");

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/ui/items_cart.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/ui/kv_departments.inc" );
include($path_to_root . "/modules/ExtendedHRM/includes/ui/employee.inc" );
//--------------------------------------------------------------------------------

function add_to_issue(&$order, $new_item, $new_item_qty, $standard_cost,$sl_no)
{
    if ($order->find_cart_item($new_item))
         display_error(_("For Part :") . $new_item . " " . "This item is already on this issue.  You can change the quantity issued of the existing line if necessary.");
	else
         $order->add_to_cart (count($order->line_items), $new_item, $new_item_qty, $standard_cost,null,$sl_no);
}


function add_to_issuem(&$order, $new_item, $new_item_qty, $standard_cost,$sl_no)
{
//echo "<pre>";print_r($order);
    if ($order->find_cart_itemm($sl_no))
         display_error(_("For Part :") . $new_item . " " . "This item is already on this issue.  You can change the quantity issued of the existing line if necessary.");
	else
         $order->add_to_cart (count($order->line_items), $new_item, $new_item_qty, $standard_cost,null,$sl_no);
}
//---------------------------------------------------------------------------------

function display_issue_items($title, &$order)
{ 
	global $path_to_root;

	display_heading($title);
    div_start('items_table');
	start_table(TABLESTYLE, "width='80%'");
	$th = array(_("Item Code"), _("Item Description"), _("Quantity"),
		_("Unit"), _("Unit Cost"),_("Serial No"), '');
	if ( count($order->line_items)) $th[] = '';

	table_header($th);
//	$total = 0;
	$k = 0;  //row colour counter

	if (count($order->line_items))
		$low_stock = $order->check_qoh($_POST['Location'], $_POST['date_'], !$_POST['IssueType']);
	$id = find_submit('Edit');
	foreach ($order->line_items as $line_no=>$stock_item)
	{

		if ($id != $line_no)
		{
			if (in_array($stock_item->stock_id, $low_stock))
				start_row("class='stockmankobg'");	// notice low stock status
			else 
				alt_table_row_color($k);

			view_stock_status_cell($stock_item->stock_id);
			label_cell($stock_item->item_description);
    		qty_cell($stock_item->quantity, false, get_qty_dec($stock_item->stock_id));
			label_cell($stock_item->units);
			amount_cell($stock_item->standard_cost);
                        label_cell($stock_item->sl_no);
//			amount_cell($stock_item->standard_cost * $stock_item->quantity);

			edit_button_cell("Edit$line_no", _("Edit"),
				_('Edit document line'));
			delete_button_cell("Delete$line_no", _("Delete"),
				_('Remove line from document'));
			end_row();
		}
		else
		{
			issue_edit_item_controls($order, $line_no);
		}
	}

	if ($id == -1)
		issue_edit_item_controls($order);

    end_table();
	if (@$low_stock)
		display_note(_("Marked items have insufficient quantities in stock as on day of issue."), 0, 1, "class='stockmankofg'");
	div_end();
}

function display_issue_itemsm($title, &$order=null,$group=null)
{ 
	global $path_to_root;
      
	display_heading($title);
    div_start('items_table');
	start_table(TABLESTYLE, "width='80%'");
	$th = array(_("Item Code"), _("Item Description"), _("Quantity"),
		_("Unit"), _("Unit Cost"),_("Serial No"), '');
	if (count($order->line_items)) $th[] = '';

	table_header($th);
//	$total = 0;
	$k = 0;  //row colour counter

	if (count($order->line_items))
		$low_stock = $order->check_qoh($_POST['Location'], $_POST['date_'], !$_POST['IssueType']);
	$id = find_submit('Edit');
       
	foreach ($order->line_items as $line_no=>$stock_item)
	{

		if ($id != $line_no)
		{
			if (in_array($stock_item->stock_id, $low_stock))
				start_row("class='stockmankobg'");	// notice low stock status
			else 
				alt_table_row_color($k);

			view_stock_status_cell($stock_item->stock_id,$group);
			label_cell($stock_item->item_description);
    		qty_cell($stock_item->quantity, false, get_qty_dec($stock_item->stock_id));
			label_cell($stock_item->units);
			amount_cell($stock_item->standard_cost);
                        label_cell($stock_item->sl_no);
//			amount_cell($stock_item->standard_cost * $stock_item->quantity);

			edit_button_cell("Edit$line_no", _("Edit"),
				_('Edit document line'));
			delete_button_cell("Delete$line_no", _("Delete"),
				_('Remove line from document'));
			end_row();
		}
		else
		{
			issue_edit_item_group($order, $line_no,$group);
		}
	}

	if ($id == -1)
		issue_edit_item_group($order,'',$group);

    end_table();
	if (@$low_stock)
		display_note(_("Marked items have insufficient quantities in stock as on day of issue."), 0, 1, "class='stockmankofg'");
	div_end();
}
//---------------------------------------------------------------------------------
function issue_edit_item_controlsm(&$order, $line_no=-1)
{
    
    
	global $Ajax;
	start_row();

	$id = find_submit('Edit');
	if ($line_no != -1 && $line_no == $id)
	{
		$_POST['stock_id'] = $order->line_items[$id]->stock_id;
		$_POST['qty'] = qty_format($order->line_items[$id]->quantity, 
			$order->line_items[$id]->stock_id, $dec);
		$std_cost = $order->line_items[$id]->standard_cost;
		$_POST['units'] = $order->line_items[$id]->units;
                $_POST['sl_no'] = $order->line_items[$id]->sl_no;
		hidden('stock_id', $_POST['stock_id']);
		label_cell($_POST['stock_id']);
		label_cell($order->line_items[$id]->item_description);
	    $Ajax->activate('items_table');
	}
	else
	{
  	//	$wo_details = get_work_order($_SESSION['issue_items']->order_id);

  		stock_component_items_list_cells(null, 'stock_id', 
			null, null, false, true);
		if (list_updated('stock_id')) {
			    $Ajax->activate('units');
			    $Ajax->activate('qty');
			    $Ajax->activate('std_cost');
                            $Ajax->activate('sl_no');
		}

    	$item_info = get_item_edit_info($_POST['stock_id']);

   		$dec = $item_info["decimals"];
   		$_POST['qty'] =	number_format2(0, $dec);
		$std_cost = $item_info["material_cost"];
		$_POST['units'] = $item_info["units"];
	}
 
	qty_cells(null, 'qty', $_POST['qty'], null, null, $dec);
        
	label_cell($_POST['units'], '', 'units');

	//amount_cells(null, 'std_cost', $_POST['std_cost']);
	hidden('std_cost', $std_cost);
	amount_cell($std_cost);
       // text_cells(null,'sl_no',null,16,16);
        serial_grn_list_cells(null, 'sl_no', 
			$_POST['stock_id'], null, false, true);

	if ($id != -1)
	{
		button_cell('UpdateItem', _("Update"),
				_('Confirm changes'), ICON_UPDATE);
		button_cell('CancelItemChanges', _("Cancel"),
				_('Cancel changes'), ICON_CANCEL);
		hidden('LineNo', $line_no);
 		set_focus('qty');
	}
	else
	{
		submit_cells('AddItem', _("Add Item"), "colspan=2",
		    _('Add new item to document'), true);
	}

	end_row();
}


function issue_edit_item_group(&$order, $line_no=-1,$group=null)
{
    
    
	global $Ajax;
	start_row();

	$id = find_submit('Edit');
	if ($line_no != -1 && $line_no == $id)
	{
		$_POST['stock_id'] = $order->line_items[$id]->stock_id;
		$_POST['qty'] = qty_format($order->line_items[$id]->quantity, 
			$order->line_items[$id]->stock_id, $dec);
		$std_cost = $order->line_items[$id]->standard_cost;
		$_POST['units'] = $order->line_items[$id]->units;
                $_POST['sl_no'] = $order->line_items[$id]->sl_no;
		hidden('stock_id', $_POST['stock_id']);
		label_cell($_POST['stock_id']);
		label_cell($order->line_items[$id]->item_description);
	    $Ajax->activate('items_table');
	}
	else
	{
  	//	$wo_details = get_work_order($_SESSION['issue_items']->order_id);

  		stock_component_items_list_cells4(null, 'stock_id', 
			null, null, false, true,'',$group);
		if (list_updated('stock_id')) {
			    $Ajax->activate('units');
			    $Ajax->activate('qty');
			    $Ajax->activate('std_cost');
                            $Ajax->activate('sl_no');
		}

    	$item_info = get_item_edit_info($_POST['stock_id']);

   		$dec = $item_info["decimals"];
   		$_POST['qty'] =	number_format2(0, $dec);
		$std_cost = $item_info["material_cost"];
		$_POST['units'] = $item_info["units"];
	}
 
	qty_cells(null, 'qty', $_POST['qty'], null, null, $dec);
        
	label_cell($_POST['units'], '', 'units');

	//amount_cells(null, 'std_cost', $_POST['std_cost']);
	hidden('std_cost', $std_cost);
	amount_cell($std_cost);
       // text_cells(null,'sl_no',null,16,16);
        serial_grn_list_cells(null, 'sl_no', 
			$_POST['stock_id'], null, false, true);

	if ($id != -1)
	{
		button_cell('UpdateItem', _("Update"),
				_('Confirm changes'), ICON_UPDATE);
		button_cell('CancelItemChanges', _("Cancel"),
				_('Cancel changes'), ICON_CANCEL);
		hidden('LineNo', $line_no);
 		set_focus('qty');
	}
	else
	{
		submit_cells('AddItem', _("Add Item"), "colspan=2",
		    _('Add new item to document'), true);
	}

	end_row();
}

function issue_edit_item_controls(&$order, $line_no=-1)
{
	global $Ajax;
	start_row();

	$id = find_submit('Edit');
	if ($line_no != -1 && $line_no == $id)
	{
		$_POST['stock_id'] = $order->line_items[$id]->stock_id;
		$_POST['qty'] = qty_format($order->line_items[$id]->quantity, 
			$order->line_items[$id]->stock_id, $dec);
		$std_cost = $order->line_items[$id]->standard_cost;
		$_POST['units'] = $order->line_items[$id]->units;
                $_POST['sl_no'] = $order->line_items[$id]->sl_no;
		hidden('stock_id', $_POST['stock_id']);
		label_cell($_POST['stock_id']);
		label_cell($order->line_items[$id]->item_description);
	    $Ajax->activate('items_table');
	}
	else
	{
  		$wo_details = get_work_order($_SESSION['issue_items']->order_id);

  		stock_component_items_list_cells(null, 'stock_id', 
			$wo_details["stock_id"], null, false, true);
		if (list_updated('stock_id')) {
			    $Ajax->activate('units');
			    $Ajax->activate('qty');
			    $Ajax->activate('std_cost');
                            $Ajax->activate('sl_no');
		}

    	$item_info = get_item_edit_info($_POST['stock_id']);

   		$dec = $item_info["decimals"];
   		$_POST['qty'] =	number_format2(0, $dec);
		$std_cost = $item_info["material_cost"];
		$_POST['units'] = $item_info["units"];
	}

	qty_cells(null, 'qty', $_POST['qty'], null, null, $dec);
	label_cell($_POST['units'], '', 'units');

	//amount_cells(null, 'std_cost', $_POST['std_cost']);
	hidden('std_cost', $std_cost);
	amount_cell($std_cost);
       // text_cells(null,'sl_no',null,16,16);
        serial_grn_list_cells(null, 'sl_no', 
			$_POST['stock_id'], null, false, true);

	if ($id != -1)
	{
		button_cell('UpdateItem', _("Update"),
				_('Confirm changes'), ICON_UPDATE);
		button_cell('CancelItemChanges', _("Cancel"),
				_('Cancel changes'), ICON_CANCEL);
		hidden('LineNo', $line_no);
 		set_focus('qty');
	}
	else
	{
		submit_cells('AddItem', _("Add Item"), "colspan=2",
		    _('Add new item to document'), true);
	}

	end_row();
}


//---------------------------------------------------------------------------------

function issue_options_controls()
{
	global $Refs;

	echo "<br>";
	start_table();
 	date_row(_("Issue Date:"), 'date_');
 	locations_list_row(_("From Location:"), 'Location');
 	workcenter_list_row(_("To Work Centre:"), 'WorkCentre');

    ref_row(_("Reference:"), 'ref', '', $Refs->get_next(ST_MANUISSUE, null, array('date'=> get_post('date_'), 'location' => get_post('Location'))));
 
 	if (!isset($_POST['IssueType']))
 		$_POST['IssueType'] = 0;
 
 	yesno_list_row(_("Type:"), 'IssueType', $_POST['IssueType'],
 		_("Return Items to Location"), _("Issue Items to Work order"));
 
	textarea_row(_("Memo"), 'memo_', null, 50, 3);

	end_table(1);
}

function display_search_details($item=''){
global $Refs,$Ajax;
	start_table(TABLESTYLE_NOBORDER);
echo "<tr>";
product_list_cells(_('Building Name'), 'name', $_POST['name'], true,true);
   if($item=="F"){
    room_list_cells1(_('Floor'), 'floor',$_POST['floor'], true, false,$_POST['name']);
    
    
   $Ajax->activate('floor');
   }
   if($item=="R"){
   room_list_cells1(_('Floor'), 'floor',$_POST['floor'], true, false,$_POST['name']);
   floor_list_cells1(_('Room'), 'room',$_POST['room'], true, false,$_POST['floor']);
   $Ajax->activate('room');
   $Ajax->activate('floor');
   }
   if($item=="D"){
    room_list_cells1(_('Floor'), 'floor',$_POST['floor'], true, false,$_POST['name']);
    floor_list_cells1(_('Room'), 'room',$_POST['room'], true, false,$_POST['floor']);
    dept_list_cells1(_('Department'), 'department',$_POST['department'], true, false,$_POST['room']);
    $Ajax->activate('department');
    $Ajax->activate('room');
    $Ajax->activate('floor');
   }
   if($item=="S"){
    room_list_cells1(_('Floor'), 'floor',$_POST['floor'], true, false,$_POST['name']);
    floor_list_cells1(_('Room'), 'room',$_POST['room'], true, false,$_POST['floor']);
    dept_list_cells1(_('Department'), 'department',$_POST['department'], true, false,$_POST['room']);
    seat_list_cells1(_('Seat'), 'seat',$_POST['seat'], true, false,$_POST['department']);
    $Ajax->activate('seat'); 
    $Ajax->activate('department');
    $Ajax->activate('room');
    $Ajax->activate('floor');
    
   }
   
   if($item=="U"){
    room_list_cells1(_('Floor'), 'floor',$_POST['floor'], true, false,$_POST['name']);
    floor_list_cells1(_('Room'), 'room',$_POST['room'], true, false,$_POST['floor']);
    dept_list_cells1(_('Department'), 'department',$_POST['department'], true, false,$_POST['room']);
    seat_list_cells1_user(_('Seat'), 'seat',$_POST['seat'], true, false,$_POST['department']);
    $Ajax->activate('seat'); 
    $Ajax->activate('department');
    $Ajax->activate('room');
    $Ajax->activate('floor');
    
   }
   
   echo "</tr>";
   end_table();
}

function display_cat_details($item=''){
global $Refs,$Ajax;
	start_table(TABLESTYLE_NOBORDER);
echo "<tr>";
category_list_cells(_('Category Name'), 'cat_name', $_POST['cat_name'], true,true);
subcat_list_cells1(_('Sub category'), 'subcat_name',$_POST['subcat_name'], true, false,$_POST['cat_name']);
group_list_cells1(_('Group'), 'group',$_POST['group'], true, false,$_POST['subcat_name']);

    $Ajax->activate('subcat_name'); 
    $Ajax->activate('department');
    $Ajax->activate('group');
    $Ajax->activate('floor');
    

   echo "</tr>";
   end_table();
}

function display_group_details($item=''){
global $Refs,$Ajax;
	start_table(TABLESTYLE_NOBORDER);
echo "<tr>";
group_sys_cells(_("Group Name:"), 'group_name',null,false,true);

   echo "</tr>";
   end_table();
}

function display_group_assinged($item=''){
global $Refs,$Ajax;
	start_table(TABLESTYLE_NOBORDER);
echo "<tr>";
group_assinged_cells(_("Group Name:"), 'group_name',null,false,true);

   echo "</tr>";
   end_table();
}

function display_item_details($item=''){
global $Refs,$Ajax;
	
 start_table(TABLESTYLE_NOBORDER);
 
  category_list1_cells(_('Category Name'), 'cat_name', $_POST['cat_name'], true,true);
    $Ajax->activate('subcat_name'); 
  subcat_list1_cells1(_('Sub category'), 'subcat_name',$_POST['subcat_name'], true, false,$_POST['cat_name']);
    $Ajax->activate('stock_id'); 
  end_table();
 
 start_table(TABLESTYLE_NOBORDER);
//search_list_cell('Search','search_type');
 echo "<tr>";
stock_component_items_list_cells1(null, 'stock_id', null, null, false, true,false,$_POST['subcat_name']);
 echo "</tr>";
 
 
   end_table();
}

function display_stockre_details($item=''){
global $Refs,$Ajax;
	
 start_table(TABLESTYLE_NOBORDER);
 locations_list_cells(_('Location Name'), 'loc_code', $_POST['loc_code'],true,true,false);
 
  category_list1_cells(_('Category Name'), 'cat_name', $_POST['cat_name'], true,true);
    $Ajax->activate('subcat_name'); 
  subcat_list1_cells1(_('Sub category'), 'subcat_name',$_POST['subcat_name'], true, false,$_POST['cat_name']);
    $Ajax->activate('stock_id'); 
  end_table();
 
 start_table(TABLESTYLE_NOBORDER);
//search_list_cell('Search','search_type');
 echo "<tr>";
stock_component_items_list_cells1(null, 'stock_id', null, null, false, true,false,$_POST['subcat_name']);
 echo "</tr>";
 
 
   end_table();
}


function display_date_manuf(){
global $Refs,$Ajax;
	start_table(TABLESTYLE_NOBORDER);
        start_row();
        date_cells1(_("From Date") . "", 'from_date');
date_cells1(_("To Date") . "", 'to_date');
submit_cells('Search', _("Search"), '', '', 'default');
end_row();
end_table();
}

function display_user_details($item=''){
global $Refs,$Ajax;
	start_table(TABLESTYLE_NOBORDER);
echo "<tr>";
department_list_cells(_("Select a Department: "), 'department_id', null,true, true, check_value('show_inactive'));
employee_list_cells1(_("Select an Employee: "), 'employee_id', null,_('All Employees'), true, check_value('show_inactive'),false,$_POST["department_id"]);

   echo "</tr>";
   end_table();
}
function issue_options_controlsm()
{
	global $Refs,$Ajax;
	echo "<br>";
	start_table();
 	date_row(_("Issue Date:"), 'date_');
 	locations_list_row(_("From Location:"), 'Location');
 	workcenter_list_row(_("To Work Centre:"), 'WorkCentre');
ref_row(_("Reference:"), 'ref', '', $Refs->get_next(ST_MANUISSUE, null, array('date'=> get_post('date_'), 'location' => get_post('Location'))));
   
 
	textarea_row(_("Memo"), 'memo_', null, 50, 3);

	end_table(1);
}


function return_options_controlsm($stock)
{
	global $Refs,$Ajax;
	echo "<br>";
	start_table();
           $result=  get_item_returns($stock);
           $row= db_fetch_assoc($result);
          // print_r($row);
           $_POST['name']=$row['build'];
           $_POST['floor']=$row['asset_id'];
           $_POST['room']=$row['rid'];
           $_POST['department']=$row['did'];
           $_POST['seat']=$row['sid'];
           $_POST['Location']=$row['loc_code'];
           $stock=$row['stock_id'];
           $_POST['sl_no']=$row['sl_no'];
        label_stock_status_cell($stock);
        hidden('stock_id', $stock);
        hidden('sl_no', $_POST['sl_no']);
        label_row('Serial No.', $_POST['sl_no']);
        label_row('Building Name', $row['name']);
        if($row['code']!='NA')
        label_row('Floor', $row['code']);
        if($row['room_no']!='NA')
        label_row('Room', $row['room_no']);
        if($row['department_id']!='NA')
        label_row('Department', $row['department_id']);
        if($row['seat_no']!='NA')
        label_row('Seat', $row['seat_no']);
        
        //product_list_row(_('Building Name'), 'name', $_POST['name'], true,true);
       // room_list_row1(_('Floor'), 'floor',$_POST['floor'], true, 'false',$_POST['name']);
       // floor_list_row1(_('Room'), 'room',$_POST['room'], true, false,$_POST['floor']);
        //dept_list_row1(_('Department'), 'department',$_POST['department'], true, false,$_POST['room']);
 	//seat_list_row1(_('Seat'), 'seat',$_POST['seat'], true, false,$_POST['department']);
        date_row(_("Return Date:"), 'date_');
        label_row('From Location', $_POST['Location']);
 	//locations_list_row(_("From Location:"), 'Location',$_POST['Location']);
 	//workcenter_list_row(_("To Work Centre:"), 'WorkCentre');
//ref_row(_("Reference:"), 'ref', '', $Refs->get_next(ST_MANUISSUE, null, array('date'=> get_post('date_'), 'location' => get_post('Location'))));
        custom_list_row(_("Item Status"), 'status', null, TRUE, false, 'return');
        
        //custom_list_row(_("Return"), 'return_status', null, TRUE, false, 'yes_no_select_box');
        hidden('return_status', '1');
	textarea_row(_("Remarks"), 'memo_', null, 50, 3);

	end_table(1);
}
function remove_manufacture_items($stock,$bid='',$comp,$loc,$location)
{
  
	global $Refs,$Ajax;
	echo "<br>";
	start_table();
           $result=  get_manu_item($stock,$comp);
           $row= db_fetch_assoc($result);
           //print_r($result);
           
           $_POST['Location']=$row['loc_code'];
           $stock=$row['stock_id'];
           $_POST['sl_no']=$row['sl_no'];
          label_stock_status_cell($stock);
          hidden('stock_id', $stock);
          hidden('sl_no', $row['osl_no']);
          hidden('bid', $bid);
          hidden('Location', $loc);
          hidden('return_status','1');
        label_row('Serial No.', $row['osl_no']);
       
        date_row(_("Return Date:"), 'date_');
 	//locations_list_row(_("From Location:"), 'Location',$_POST['Location']);
         label_cells('Location', $location);
 	//workcenter_list_row(_("To Work Centre:"), 'WorkCentre');
//ref_row(_("Reference:"), 'ref', '', $Refs->get_next(ST_MANUISSUE, null, array('date'=> get_post('date_'), 'location' => get_post('Location'))));
        custom_list_row(_("Item Status"), 'status', null, TRUE, false, 'return');
       // custom_list_row(_("Return"), 'return_status', null, TRUE, false, 'yes_no_select_box');
 
	textarea_row(_("Remarks"), 'memo_', null, 50, 3);

	end_table(1);
}

function remove_grpcenter_items($cent,$stock_id,$loc,$location,$sysid)
{
  //display_error($sysid);
	global $Refs,$Ajax;
	echo "<br>";
	start_table();
           $result=  get_center_item($cent,$stock_id);
           $row= db_fetch_assoc($result);
           //print_r($result);
           
           $_POST['Location']=$loc;
           $stock=$row['stock_id'];
           $_POST['sl_no']=$row['sl_no'];
          label_stock_status_cell($stock);
          hidden('stock_id', $stock);
          hidden('sl_no', $row['osl_no']);
          hidden('Location', $_POST['Location']);
           hidden('sysid', $sysid);
           hidden('return_status', '1');
        label_row('Serial No.', $row['osl_no']);
       
        date_row(_("Return Date:"), 'date_');
       // label_row('Location.', $_POST['Location']);
        label_cells('Location', $location);
 	///locations_list_row(_("From Location:"), 'Location',$_POST['Location']);
 	//workcenter_list_row(_("To Work Centre:"), 'WorkCentre');
//ref_row(_("Reference:"), 'ref', '', $Refs->get_next(ST_MANUISSUE, null, array('date'=> get_post('date_'), 'location' => get_post('Location'))));
        custom_list_row(_("Item Status"), 'status', null, TRUE, false, 'return');
        //custom_list_row(_("Return"), 'return_status', null, TRUE, false, 'yes_no_select_box');
 
	textarea_row(_("Remarks"), 'memo_', null, 50, 3);

	end_table(1);
}
function department_options_controlsm($selected_id)
{
     display_error($selected_id);
          $result=  get_user_seats($selected_id);
          $row= db_fetch_assoc($result);
           
         $_POST['department_id']=$row['department_id'];
         $_POST['employee_id']=$row['employee_id'];
	global $Refs,$Ajax;
	echo "<br>";
	start_table();
 	date_row(_("Issue Date:"), 'date_');
 	department_list_row(_("Select a Department: "), 'department_id', $_POST['department_id'],true, true, check_value('show_inactive'));
        employee_list_cells1(_("Select an Employee: "), 'employee_id', $_POST['employee_id'],_('All Employees'), true, check_value('show_inactive'),false,$_POST["department_id"]);


	//textarea_row(_("Memo"), 'memo_', null, 50, 3);

	end_table(1);
}


function department1_options_controlsm()
{
          
      //print_r($result);
	global $Refs,$Ajax;
	echo "<br>";
	start_table();
 	date_row(_("Issue Date:"), 'date_');
 	department_list_row(_("Select a Department: "), 'department_id', $_POST['department_id'],true, true, check_value('show_inactive'));
        employee_list_cells1(_("Select an Employee: "), 'employee_id', $_POST['employee_id'],_('All Employees'), true, check_value('show_inactive'),false,$_POST["department_id"]);


	//textarea_row(_("Memo"), 'memo_', null, 50, 3);

	end_table(1);
}

function return_stock_control($stock,$locat,$subcat)
{
	global $Refs,$Ajax;
	echo "<br>";
	start_table();
           $result=  get_stock_manage_status($stock,$locat,$subcat);
           $row= db_fetch_assoc($result);
          // print_r($row);
           $_POST['present_stock']=$row['totqty'];
           $_POST['invoice']=$row['reference'];
           $_POST['invoie_date']=$row['delivery_date'];
           $_POST['category_id']=$row['category_id'];
           $_POST['sub_cat_name']=$row['sub_cat_name'];
           $_POST['loc_code']=$row['location'];
           $stock=$row['stock_id'];
         
        label_stock_status_cell($stock);
        hidden('stock_id', $stock);
        hidden('present_stock', $_POST['totqty']);
        hidden('return_date', date('d-m-Y'));
        
         hidden('invoice', $_POST['invoice']);
         hidden('invoie_date', $_POST['invoie_date']);
         hidden('category_id', $_POST['category_id']);
         hidden('sub_cat_name',$_POST['sub_cat_name']);
         hidden('loc_code',$_POST['loc_code']);
          
        label_row('Description', $row['description']);
        label_row('Current Stock', $row['totqty']);
        label_row('Location', $row['store']);
        //label_row('Invoice No.', $row['reference']);
       // label_row('Invoice Date', date('d-m-Y',strtotime($row['delivery_date'])));
        label_row('Return Date', date('d-m-Y'));
        
        //product_list_row(_('Building Name'), 'name', $_POST['name'], true,true);
       // room_list_row1(_('Floor'), 'floor',$_POST['floor'], true, 'false',$_POST['name']);
       // floor_list_row1(_('Room'), 'room',$_POST['room'], true, false,$_POST['floor']);
        //dept_list_row1(_('Department'), 'department',$_POST['department'], true, false,$_POST['room']);
 	//seat_list_row1(_('Seat'), 'seat',$_POST['seat'], true, false,$_POST['department']);
       // date_row(_("Return Date:"), 'date_');
        ////label_row('From Location', $_POST['Location']);
 	//locations_list_row(_("From Location:"), 'Location',$_POST['Location']);
 	//workcenter_list_row(_("To Work Centre:"), 'WorkCentre');
//ref_row(_("Reference:"), 'ref', '', $Refs->get_next(ST_MANUISSUE, null, array('date'=> get_post('date_'), 'location' => get_post('Location'))));
       // custom_list_row(_("Item Status"), 'status', null, TRUE, false, 'return');
        
        //custom_list_row(_("Return"), 'return_status', null, TRUE, false, 'yes_no_select_box');
        //hidden('return_status', '1');
        text_cells(_("Return stock"), 'return_stock');
	textarea_row(_("Remarks"), 'memo_', null, 50, 3);

	end_table(1);
}
//---------------------------------------------------------------------------------

