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
$page_security = 'SA_BOM';
$path_to_root = "../..";
include_once($path_to_root . "/includes/session.inc");
//include_once($path_to_root . "/managements/includes/db/statutory_db.inc");
page(_($help_context = "Manufactured Items"));

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/manufacturing/includes/manufacturing_ui.inc");
check_db_has_bom_stock_items(_("There are no manufactured or kit items defined in the system."));

check_db_has_workcentres(_("There are no work centres defined in the system. BOMs require at least one work centre be defined."));

simple_page_mode(true);
$selected_component = $selected_id;
//--------------------------------------------------------------------------------------------------

if (isset($_GET['stock_id']))
{
	$_POST['stock_id'] = $_GET['stock_id'];
	$selected_parent =  $_GET['stock_id'];
}

//--------------------------------------------------------------------------------------------------
function can_process() 
{
	$regex_digit = "/^\d{1,3}$/";
	
        
        
       if(!empty($_POST['quantity']))
	{
		
		if(preg_match($regex_digit, get_post('quantity')) ==0) {
			display_error( _("Accept only Numbers."));
			set_focus('name');
			return false;
		} 	
	}

        return true;
}
function display_manufactured_items($selected_parent)
{
	$result = get_manufactured($selected_parent);
	div_start('bom');
	start_table(TABLESTYLE, "width='80%'");
	$th = array(_("Code"), _("Description"), _("Location"),
		_("Work Centre"), _("Quantity"), _("Units"),_("Issue Date"),'#','#');
	table_header($th);

	$k = 0;
	while ($myrow = db_fetch($result))
	{

		alt_table_row_color($k);

		label_cell($myrow["component"]);
		label_cell($myrow["description"]);
        label_cell($myrow["location_name"]);
        label_cell($myrow["WorkCentreDescription"]);
        qty_cell($myrow["quantity"], false, get_qty_dec($myrow["component"]));
        label_cell($myrow["units"]);
        label_cell(date('d-m-Y',strtotime($myrow["issue_date"])));
        
        hidden('m_id', $myrow["mid"]);
        label_cell(get_assigned_manu_item_view($myrow["mid"],$myrow["component"]));
 		//edit_button_cell("Edit".$myrow['id'], _("Edit"));
        label_cell(get_assigned_manufacture($myrow["mid"],$myrow["bid"],$myrow["component"],$myrow["loc_code"],$myrow["location_name"]));
 		///delete_button_cell("Delete".$myrow['bid'], _("Delete"));
        end_row();

	} //END WHILE LIST LOOP
	end_table();
	div_end();
}



//--------------------------------------------------------------------------------------------------

function on_submit($selected_parent, $selected_component=-1)
{
	if (!check_num('quantity', 0))
	{
		display_error(_("The quantity entered must be numeric and greater than zero."));
		set_focus('quantity');
		return;
	}

	if ($selected_component != -1)
	{
	    
		update_bom($selected_parent, $selected_component, $_POST['workcentre_added'], $_POST['loc_code'],
		input_num('quantity'));
		display_notification(_('Selected component has been updated'));
		$Mode = 'RESET';
	}
	else
	{
        add_bom($selected_parent, $_POST['component'], $_POST['workcentre_added'],$_POST['loc_code'], input_num('quantity'),$_POST['date_']);
	add_serial_no($_POST['osl_no'],$_POST['component'],$_POST['loc_code'],input_num('quantity'),$_POST['m_id']);
        display_notification(_("A new component part has been added to the bill of material for this item."));
	$Mode = 'RESET';
                                
                   
	}
}

//--------------------------------------------------------------------------------------------------

if ($Mode == 'Delete')
{
	delete_sys_manu($selected_id);
	display_notification(_("The component item has been deleted from this bom"));
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	unset($_POST['quantity']);
}

//--------------------------------------------------------------------------------------------------

start_form();

start_form(false, true);
start_table(TABLESTYLE_NOBORDER);
start_row();
stocked_manufactured_items_list_cells(_("Select a manufacturable item:"), 'stock_id', null, false, true);
end_row();
if (list_updated('stock_id'))
{
	$selected_id = -1;
	$Ajax->activate('_page_body');
}
end_table();
br();

end_form();
//--------------------------------------------------------------------------------------------------

if (get_post('quantity')!='' && can_process()){
    $Ajax->activate('_page_body');
}

if (get_post('stock_id') != '')
{ //Parent Item selected so display bom or edit component
	$selected_parent1 = $_POST['stock_id'];
           $myrow = get_parent_from_manu($selected_parent1);
          $selected_parent=$myrow['stock_id'];
	if (($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') && can_process())
		on_submit($selected_parent, $selected_id);
	//--------------------------------------------------------------------------------------

start_form();
	display_manufactured_items($selected_parent1);
	//--------------------------------------------------------------------------------------
	echo '<br>';

	start_table(TABLESTYLE2);

	if ($selected_id != -1)
	{
 		if ($Mode == 'Edit') {
			//editing a selected component from the link to the line item
			$myrow = get_component_from_manu($selected_id);

			$_POST['loc_code'] = $myrow["loc_code"];
			$_POST['component'] = $myrow["component"]; // by Tom Moulton
			$_POST['workcentre_added']  = $myrow["workcentre_added"];
			$_POST['quantity'] = number_format2($myrow["quantity"], get_qty_dec($myrow["component"]));
			label_row(_("Component:"), $myrow["component"] . " - " . $myrow["description"]);
		}
		hidden('selected_id', $selected_id);
	}
	else
	{
            
            category_list1_cells(_('Category Name'), 'cat_name', $_POST['cat_name'], true,true);
            subcat_list1_cells1(_('Sub category'), 'subcat_name',$_POST['subcat_name'], true, false,$_POST['cat_name']);

		start_row();
		label_cell(_("Component:"), "class='label'");

		echo "<td>";
		echo stock_component_items_list('component', $selected_parent, null, false, true,false,$_POST['subcat_name']);
		if (get_post('_component_update')) 
		{
			$Ajax->activate('quantity');
		}
		echo "</td>";
		end_row();
	}
	
        serial_grn_list_cells(_("Serial No:"), 'osl_no', $_POST['component']);
        date_row(_("Issue Date:"), 'date_');
	locations_list_row(_("Location to Draw From:"), 'loc_code', null);
	workcenter_list_row(_("Work Centre Added:"), 'workcentre_added', null);
	$dec = get_qty_dec(get_post('component'));
	$_POST['quantity'] = input_num('quantity',1);
	text_row(_("Quantity:"), 'quantity',$_POST['quantity'],5,null,null,null,null,true);

	end_table(1);
	submit_add_or_update_center($selected_id == -1, '', 'both');
	end_form();
}
// ----------------------------------------------------------------------------------

end_page();

