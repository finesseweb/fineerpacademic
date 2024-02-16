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

include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include($path_to_root . "/includes/ui.inc");
$selected_id  = $_POST['selected_id'];
 //display_error($selected_id); die();
 //print_r($_POST); 
    if($selected_id){
             $query = "SELECT prev.*,s.description AS Item,freq.frequency_name AS frequency,con.supp_name AS contractor,freq.frequency_des FROM ".TB_PREF."prevent_maintain_entry AS prev LEFT JOIN ".TB_PREF."utility AS utly ON utly.sub_cat_id= prev.sub_cat_id LEFT JOIN ".TB_PREF."frequency_master AS freq ON freq.freq_id= prev.frequency_id LEFT JOIN ".TB_PREF."stock_master s ON s.stock_id= prev.utility_id LEFT JOIN ".TB_PREF."contractor con ON con.supplier_id= prev.contractor_id WHERE utly.type=1 and prevent_id = $selected_id";
             $res=db_query($query);  
             $myrow = db_fetch($res);
            
           $myrow['maintain_date']=date('d-m-Y',strtotime($myrow['maintain_date']));
           $myrow['prv_ob_date']=date('d-m-Y',strtotime($myrow['prv_ob_date']));
          echo  json_encode($myrow);
    }die;

?>
