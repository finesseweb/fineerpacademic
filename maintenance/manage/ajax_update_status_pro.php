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
//-----------------------------------------------------------------------------
//
//	Entry/Modify Sales Quotations
//	Entry/Modify Sales Order
//	Entry Direct Delivery
//	Entry Direct Invoice
//

$path_to_root = "../..";


include_once($path_to_root . "/includes/session.inc");

$prevent_id = $_POST['selected_id'];

$status = '1';
//print_r($prevent_id);die;
if(!empty($prevent_id)){
	$sql = "UPDATE ".TB_PREF."process_maintenance SET status=".db_escape($status)." WHERE process_id=".db_escape($prevent_id);
	//display_error($sql);
	db_query($sql, "could not update Help Desk Complaint");	
}

?>