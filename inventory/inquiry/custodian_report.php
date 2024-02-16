<?php
/**********************************************************************
  
	Released underhe GNU General Public License, GPL, 
	as published by the Free Software Foundation, either version 3 
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/
$page_security = 'SA_ITEMCATEGORY';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");
$js = "";
//if ($use_popup_windows)
	$js .= get_js_open_window(800, 500);
//if ($use_date_picker)
	$js .= get_js_date_picker();
page(_($help_context = "Item Sub Category"), false, false, "", $js);
//page(_($help_context = "Item Sub Category"));

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/inventory/includes/db/items_sub_category_db.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/ui/kv_departments.inc" );
include($path_to_root . "/modules/ExtendedHRM/includes/ui/employee.inc" );
simple_page_mode(true);
//----------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	//initialise no input errors assumed initially before we test
	$input_error = 0;

	if (strlen($_POST['sub_cat_name']) == 0)
	{
		$input_error = 1;
		display_error(_("The sub category name cannot be empty."));
		set_focus('sub_item_name');
	}
        if (!intval($_POST['code']))
	{
		$input_error = 1;
		display_error(_("The Code name cannot be String."));
		set_focus('sub_item_name');
	}
	if (strlen(db_escape($_POST['sub_cat_name']))>(20+2))
	{
		$input_error = 1;
		display_error(_("The sub item name is too long."));
		set_focus('sub_item_name');
	}
	if (strlen($_POST['description']) == 0)
	{
		$input_error = 1;
		display_error(_("The sub category description cannot be empty."));
		set_focus('description');
	}

	if ($input_error !=1) {
    	add_sub_category($selected_id, $_POST['sub_cat_name'], $_POST['description'], $_POST['category_id'],$_POST['slab_id'],$_POST['effective_date'],$_POST['code'],$_POST['department_id'],$_POST['employee_id']);
		if($selected_id != '')
			display_notification(_('Selected sub category been updated'));
		else
			display_notification(_('New sub category has been added'));
		$Mode = 'RESET';
	}
}

//----------------------------------------------------------------------------------

if ($Mode == 'Delete')
{

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'stock_master'

	if (sub_cat_used($selected_id))
	{
		display_error(_("Cannot delete this sub category because items have been created using this sub category."));

	}
	else
	{
		delete_sub_category($selected_id);
		display_notification(_('Selected sub category has been deleted'));
	}
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = '';
	$sav = get_post('show_inactive');
	unset($_POST);
	$_POST['show_inactive'] = $sav;
}

//----------------------------------------------------------------------------------

start_form();
start_table(TABLESTYLE2);
 custom_list_row(_("Filter by"), 'fliter_type', null, true, false, 'filter');
 //display_error($_POST['category_id']);
 
 if (list_updated('fliter_type') || list_updated('category_id')) {
        $Ajax->activate('_page_body');
    }
 if ($_POST['fliter_type']=='1'){
     
   
department_list_row_cust(_("Select a Department: "), 'department_id', false,false, true);
employee_list_cells1234(_("Select Custodian: "), 'employee_id', $_POST['employee_id'],_('All Employees'), true, check_value('show_inactive'),false,$_POST["department_id"]);
 }
 elseif ($_POST['fliter_type']=='2'){
      
stock_categories_list_row(_("Category:"), 'category_id', null, false, true);

$category_id = 1;

sub_category_list_row(_("Sub Category:"), 'sub_cat_name', null, false, true, $_POST['category_id']);

     

}
	
end_table(1);


if ($_POST['fliter_type']=='1'){
$result = get_all_sub_custodian($_POST['employee_id']);
}
elseif ($_POST['fliter_type']=='2'){
  $result = get_sub_category_all($_POST['sub_cat_name']);  
}
start_table(TABLESTYLE, "width='80%'");
$th = array(_('Category'), _('Sub Category Name'),_('GST Slab'),_('Code'),_('Effactive Date'), _('Description'), _('Department'), _('Custodian'), "", "");
inactive_control_column($th);

table_header($th);
$k = 0; //row colour counter

while ($myrow = db_fetch($result))
{

	alt_table_row_color($k);
	$category_name = get_category_name($myrow["category_id"]);
        $gst_slab = get_gst_slab($myrow["slab_id"]);
         $dep_name = get_department_name($myrow["department_id"]);
          $empl_name = get_employees_name($myrow["employee_id"]);
	label_cell($category_name);
	label_cell($myrow["sub_cat_name"]);
        label_cell($gst_slab);
        label_cell($myrow['code']);
        label_cell(date('d-m-Y',strtotime($myrow["effective_date"])));
	label_cell($myrow["description"]);
		label_cell($dep_name);
		label_cell($myrow["employee_id"].'-'.$empl_name);
	$id = htmlentities($myrow["sub_cat_name"]);
       
	inactive_control_cell($id, $myrow["inactive"], 'stock_sub_category', 'sub_cat_name', 'category_id');
	if(($myrow["sub_cat_name"] != 'N/A') && ($myrow['description'] != 'N/A')){
 	//edit_button_cell("Edit".$id, _("Edit"));
 //	delete_button_cell("Delete".$id, _("Delete"));
	}else{
		label_cell();
		label_cell();
		label_cell();
	}
	end_row();
}

inactive_control_row($th);
end_table(1);

//----------------------------------------------------------------------------------


end_form();

end_page();

?>
