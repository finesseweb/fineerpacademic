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
//echo '<pre>'; print_r($_POST); die;

$cat_id=$_POST["cat_id"];
$sub_cat_id=$_POST["sub_cat_id"];
$contractor_id=$_POST["contractor_id"];
if(isset($_POST['add_entry'])){

$sql="INSERT INTO ".TB_PREF."merge_contractor(cat_id,subcat_id,contractor_id) VALUES('$cat_id','$sub_cat_id','$contractor_id')";

db_query($sql,"something went wrong");

$last_inserted_id = db_insert_id($sql);



header("Location: add_contracotor_category.php?All=1&success=1");
}

if(isset($_POST['update_entry'])){
 //echo '<pre>'; print_r($_POST); die;   

$cat_id=$_POST["cat_id"];
$sub_cat_id=$_POST["sub_cat_id"];

$contractor_id=$_POST["contractor_id"];
$id=$_POST["id"];

$sql="UPDATE ".TB_PREF."merge_contractor SET contractor_id='$contractor_id',cat_id='$cat_id',subcat_id='$sub_cat_id' where id='$id'";

db_query($sql,"something went wrong");  
header("Location: add_contracotor_category.php?All=1&success=1");
}


?>