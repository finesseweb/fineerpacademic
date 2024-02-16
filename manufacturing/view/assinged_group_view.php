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
$page_security = 'SA_MANUFTRANSVIEW';
$path_to_root = "../..";

include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include_once($path_to_root . "/manufacturing/includes/manufacturing_db.inc");
include_once($path_to_root . "/manufacturing/includes/manufacturing_ui.inc");
$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(800, 500);
page(_($help_context = "View Item Details"), true, false, "", $js);

//-------------------------------------------------------------------------------------------------
$woid = 0;
if (!isset($_GET['type_id']) || !isset($_GET['trans_no'])) 
{ /*Script was not passed the correct parameters */

	display_note(_("The script must be called with a valid transaction type and transaction number to review the general ledger postings for."));
	end_page();
}
$woid=$_GET['type_id'];
$type=$_GET['trans_no'];
display_heading('View Item Details' .'#'.$woid);

br(1);
//$myrow = get_work_order($woid);


echo "<center>";

// display the WO requirements
br(1);
   start_table(TABLESTYLE, "width='80%'");
        $th = array(_("Item Name"),_("Serial No"),_("Warranty"),_("From Date"),_("To Date"),_("Supplier"));

        table_header($th);
$result = get_grp_item_details($woid,$type);
        $k = 0; //row colour counter
		$has_marked = false;

		if ($date == null)
			$date = Today();
                $trans_type=1;
     ///  echo "<pre>";  print_r(db_fetch($result));
        while ($myrow = db_fetch($result))
        {
          
		alt_table_row_color($k);
                        
                        label_cell($myrow["stock_id"]);
			 label_cell($myrow["osl_no"]);   
                        label_cell($myrow["warranty"]);                          
                        label_cell(date('d-m-Y',strtotime($myrow["from_date"])));
                         label_cell(date('d-m-Y',strtotime($myrow["to_date"])));
                         label_cell($myrow["supp_name"]); 
                       // label_cell(get_assigned_sys_view_grp($myrow['group_id'],'View Item'));
                        
			end_row();

		}

		end_table();
		if ($has_marked)
			display_note(_("Marked items have insufficient quantities in stock."), 0, 0, "class='red'");
    
echo "<br></center>";

//is_voided_display(ST_WORKORDER, $woid, _("This work order has been voided."));

//end_page(true, false, false, ST_WORKORDER, $woid);

