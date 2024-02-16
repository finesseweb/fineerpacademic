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
$woid=$_GET['trans_no'];
$new_grp=$_GET['new_grp'];
display_heading('View Item Details' .'#'.$woid);

br(1);
//$myrow = get_work_order($woid);


echo "<center>";

// display the WO requirements
br(1);
   start_table(TABLESTYLE, "width='80%'");
        $th = array(_("Code"), _("Description"), _("Action"));

        table_header($th);
$result = get_assinged_item($type);
        $k = 0; //row colour counter
		$has_marked = false;

		if ($date == null)
			$date = Today();
                $trans_type=1;
      //  echo "<pre>";  print_r(db_fetch($result));
        while ($myrow = db_fetch($result))
        {
          
		alt_table_row_color($k);
                        
                        label_cell($myrow["name"]);
                        label_cell($myrow["description"]);
                        
                        echo '<td><a target="_blank" href="../manage/group_center_copy.php?old_grp='.$myrow["group_name"].'&new_grp_add='.$new_grp.'">Copy Item</a></td>';
			end_row();

		}

		end_table();
		if ($has_marked)
			display_note(_("Marked items have insufficient quantities in stock."), 0, 0, "class='red'");
    
echo "<br></center>";

//is_voided_display(ST_WORKORDER, $woid, _("This work order has been voided."));

//end_page(true, false, false, ST_WORKORDER, $woid);

