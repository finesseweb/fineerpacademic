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

page(_($help_context = "Group Of System"));

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");

check_db_has_bom_stock_items(_("There are no manufactured or kit items defined in the system."));

check_db_has_workcentres(_("There are no work centres defined in the system. BOMs require at least one work centre be defined."));

simple_page_mode(true);
$selected_component = $selected_id;
//--------------------------------------------------------------------------------------------------
$woid=$_GET['old_grp'];
$new_grp=$_GET['new_grp_add'];
global $new_grp;
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

if (isset($_POST['ADD_ITEM']))
{
    print_r($_POST);
    display_error(count($_POST));
//    for($i=0;$i<count($_POST['old_component']);$i++){
// 
//	//$failed_data= add_group($_POST['new_grp'], $_POST['old_component'][$i], $_POST['old_work'][$i],
//				//	$_POST['old_location'][$i], $_POST['old_qua'][$i]);
//        $sql = "INSERT INTO ".TB_PREF."sys_group (group_name, component, work_center, loc_code, quantity)
//		VALUES (".db_escape($_POST['new_grp']).", ".db_escape($_POST['old_component'][$i]) . ","
//		.db_escape($_POST['old_work'][$i]) . ", ".db_escape($_POST['old_location'][$i]) . ", "
//		. db_escape($_POST['old_qua'][$i]). ")";
//
//    db_query($sql,"check failed");
    //$date=date('Y-m-d');
    //$quantity= -$quantity;
    //add_stock_move(ST_WORKORDER, $component, $woid,$loc_code, $date, $ref, $quantity, $unit_cost);
    //}
	//if ($failed_data != null) 
	//{
		//display_error(_("The process cannot be completed because there is an insufficient total quantity for a component.") . "<br>"
	//	. _("Component is :"). $failed_data[0] . "<br>"
	//	. _("From location :"). $failed_data[1] . "<br>");
	//} 
	//else 
	//{
		//meta_forward($_SERVER['PHP_SELF'], "AddedID=".$_POST['floor']);
	//}

}

function display_bom_items($woid)
{
     display_error($woid);
	$result = get_group($woid);
	div_start('bom');
	start_table(TABLESTYLE, "width='60%'");
	$th = array(_("Code"), _("Description"), _("Location"),
		_("Work Centre"), _("Quantity"), _("Units"),'','');
	table_header($th);

	$k = 0;
	while ($myrow = db_fetch($result))
	{

		alt_table_row_color($k);

		label_cell($myrow["component"]);
                hidden('old_component[]', $myrow["component"]);
		label_cell($myrow["description"]);
                hidden('old_description[]', $myrow["description"]);
                label_cell($myrow["location_name"]);
                hidden('old_location[]', $myrow["loc_code"]);
                label_cell($myrow["WorkCentreDescription"]);
                hidden('old_work[]', $myrow["work_center"]);
                qty_cell($myrow["quantity"], false, get_qty_dec($myrow["component"]));
                hidden('old_qua[]', $myrow["quantity"]);
                label_cell($myrow["units"]);
                hidden('old_unit[]', $myrow["units"]);
                hidden('new_grp[]', $_GET['new_grp_add']);
 		//edit_button_cell("Edit".$myrow['id'], _("Edit"));
 		//delete_button_cell("Delete".$myrow['id'], _("Delete"));
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
	    
		update_sys_group($selected_parent, $selected_component, $_POST['workcentre_added'], $_POST['loc_code'],
		input_num('quantity'),input_num('pre_qua'),$_POST['component']);
		display_notification(_('Selected component has been updated'));
		$Mode = 'RESET';
	}
	else
	{

		/*Selected component is null cos no item selected on first time round
		so must be adding a record must be Submitting new entries in the new
		component form */

		//need to check not recursive bom component of itself!
		if (!check_for_recursive_grp($selected_parent, $_POST['component']))
		{

			/*Now check to see that the component is not already on the bom */
			if (!is_component_already_on_sys($_POST['component'], $_POST['workcentre_added'],
				$_POST['loc_code'], $selected_parent))
			{
				//add_group($selected_parent, $_POST['component'], $_POST['workcentre_added'],
				//	$_POST['loc_code'], input_num('quantity'));
				display_notification(_("A new component part has been added to the bill of material for this item."));
				$Mode = 'RESET';
			}
			else
			{
				/*The component must already be on the bom */
				display_error(_("The selected component is already on this bom. You can modify it's quantity but it cannot appear more than once on the same bom."));
			}

		} //end of if its not a recursive bom
		else
		{
			display_error(_("The selected component is a parent of the current item. Recursive BOMs are not allowed."));
		}
	}
}

//--------------------------------------------------------------------------------------------------

if ($Mode == 'Delete')
{
	delete_sys_grp($selected_id);
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
//group_list_cells(_("Group Name:"), 'group_name',$_GET['old_grp'],false,true);
//label_cell(get_assigned_grp($_POST['group_name'],'Copy from Existing Item'));
end_row();
//if (list_updated('group_name'))
//{
//	$selected_id = -1;
//	$Ajax->activate('_page_body');
//}
end_table();
br();

end_form();
//--------------------------------------------------------------------------------------------------

if (get_post('quantity')!='' && can_process()){
    $Ajax->activate('_page_body');
}

//if (get_post('group_name') != '')
//{ //Parent Item selected so display bom or edit component
	$selected_parent = $_POST['group_name'];
   
	if (($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') && can_process())
		on_submit($selected_parent, $selected_id);
	//--------------------------------------------------------------------------------------

start_form();
	display_bom_items($woid);
       
	//--------------------------------------------------------------------------------------
	echo '<br>';

	start_table(TABLESTYLE2);

	if ($selected_id != -1)
	{
 		if ($Mode == 'Edit') {
			//editing a selected component from the link to the line item
			$myrow = get_component_from_sys($selected_id);

			$_POST['loc_code'] = $myrow["loc_code"];
			$_POST['component'] = $myrow["component"]; // by Tom Moulton
			$_POST['work_center']  = $myrow["work_center"];
			$_POST['quantity'] = number_format2($myrow["quantity"], get_qty_dec($myrow["component"]));
			label_row(_("Component:"), $myrow["component"] . " - " . $myrow["description"]);
            hidden('pre_qua',$myrow["quantity"]);
            hidden('component',$myrow["component"]);
             hidden('group_name', $new_grp);
		}
		hidden('selected_id', $selected_id);
	}
	else
	{
		//start_row();
		//label_cell(_("Component:"), "class='label'");

		//echo "<td>";
		//echo stock_component_items_list('component', $selected_parent, null, false, true);
		//if (get_post('_component_update')) 
		//{
		//	$Ajax->activate('quantity');
	//	}
	//	echo "</td>";
		//end_row();
	}
	

	//locations_list_row(_("Location to Draw From:"), 'loc_code', null);
	//workcenter_list_row(_("Work Centre Added:"), 'workcentre_added', null);
	//$dec = get_qty_dec(get_post('component'));
	//$_POST['quantity'] = input_num('quantity',1);
	//text_row(_("Quantity:"), 'quantity',$_POST['quantity'],5,null,null,null,null,true);

	end_table(1);
	submit_add_or_update_center($selected_id == -1, '', 'both');
	end_form();
//}
// ----------------------------------------------------------------------------------

end_page();

