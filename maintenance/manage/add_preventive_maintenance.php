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

//echo '<pre>'; print_r($_POST['preventive']); die;
$utility_id=$_POST["utility_id"];
$cat_id=$_POST["cat_id"];
$sub_cat_id=$_POST["sub_cat_id"];
$maintain_date=$_POST["maintain_date"];
$frequency_id=$_POST["frequency_id"];
$contractor_id=$_POST["contractor_id"];
$ob_date = $_POST["prv_ob_date"];
$ob_1 = $_POST["prv_ob_1"];
$ob_2 = $_POST["prv_ob_2"];
$ob_3 = $_POST["prv_ob_3"];
 
 
if(isset($_POST['add_entry'])){

 $sqlf="SELECT * FROM ".TB_PREF."frequency_master  where freq_id='$cat_id'";
 $resf = db_query($sqlf);
 $row44 = db_fetch($resf);
 $totdays= $row44['total_days'];
 
 $checks_ids=implode(',',$_POST["prv_check"]);
 //print_r($row44['total_days']);
  //print_r($_POST);
// $sqlp="SELECT maintain_date  FROM fa_prevent_maintain_entry where frequency_id='$cat'";
// $resp = db_query($sqlp);
// $row4p = db_fetch($resp);
 //$nextpredate= date($row4p['maintain_date'],+$totdays);
 $nextpredate= date('Y-m-d',strtotime($maintain_date));
 $ob_date= date('Y-m-d',strtotime($ob_date));
 $nextpredate1= date('Y-m-d', strtotime($nextpredate . "+$totdays day"));
 //$nextpredate1 =date('Y-m-d', strtotime(' +1 day'));
 //print_r($nextpredate1);
 //$date_=date('Y-m-d');   
    

$sql="INSERT INTO ".TB_PREF."prevent_maintain_entry(maintain_date,utility_id,frequency_id,contractor_id,prv_ob_date,prv_ob_1,prv_ob_2,prv_ob_3,cat_id,sub_cat_id,next_prem_date,parameters) VALUES('$nextpredate','$utility_id','$frequency_id','$contractor_id','$ob_date','$ob_1','$ob_2','$ob_3','$cat_id','$sub_cat_id','$nextpredate1','$checks_ids')";

db_query($sql,"something went wrong");
$last_inserted_id = db_insert_id($sql);

$checks_id=$_POST["checks_id"];


//print_r($checks_id);die;
foreach(array_filter($checks_id) as $checks)
{
  if(isset($_POST['prv_check_'.$checks]))
  {
     $sql2="INSERT INTO ".TB_PREF."prevent_maintain_params(prevent_id,parameters) VALUES('$last_inserted_id','$checks')";

db_query($sql2,"something went wrong");
  }
}

$items=$_POST['preventive'];
 //echo'<pre>';print_r($items);die;

//for($i=0;$i<$items;$i++)



	foreach(array_filter($items['items_id']) as $k => $item_id)
	{
	//print_r($items['loc_code'][$k]);die;
	$sql2="INSERT INTO ".TB_PREF."preventmaintain_entry_items(prevent_id,cat_id,sub_cat_id,item_id,quantity,stock_qty) VALUES ('".$last_inserted_id."','".$items['cat_id'][$k]."','".$items['sub_cat_id'][$k]."','".$item_id."','".$items['qty'][$k]."','".$items['stock_qty'][$k]."')";
//	echo '<pre>'; print_r($sql2); die; 
	db_query($sql2,"something went wrong");
	
	$query = "INSERT INTO ".TB_PREF."stock_moves(stock_id,qty,loc_code)VALUES(".db_escape($item_id).",".db_escape(-$items['qty'][$k]).",".db_escape($items['loc_code'][$k]).")";
    db_query($query, "The stock not deducted");
	}
	
	
  $new_items=$_POST['New'];
  // echo'<pre>';print_r($new_items);die;

	foreach(array_filter($new_items['n_item']) as $k => $n_item)
	{
	//echo'sdfsdfsd';die;
	$sql1="INSERT INTO ".TB_PREF."prevent_new_items(prevent_id,n_item,n_qty,n_bill_date,n_billno,n_contractor,n_comments) VALUES ('".$last_inserted_id."','".$n_item."','".$new_items['n_qty'][$k]."','".$new_items['n_bill_date'][$k]."','".$new_items['n_billno'][$k]."','".$new_items['n_contractor'][$k]."','".$new_items['n_comments'][$k]."')";
    //echo '<pre>'; print_r($sql1); die; 
	db_query($sql1,"something went wrong");
	
	}

        
$sqlm="INSERT INTO fa_maintenance_validate(cat_id,date) VALUES('".$cat_id."','".date2sql($maintain_date)."')"  ;      
   db_query($sqlm,"something went wrong"); 
   
   
header("Location: preventive_maintenance.php?All=1&success=1");
}

if($_POST['update_entry']) {
   //print_r($_POST) ;
    
    
    
   
$utility_id=$_POST["utility_id"];
$maintain_date=$_POST["maintain_date"];
$frequency_id=$_POST["frequency_id"];
$cat_id=$_POST["cat_id"];
$sub_cat_id=$_POST["sub_cat_id"];
$contractor_id=$_POST["contractor_id"];
$ob_date = $_POST["prv_ob_date"];
$ob_1 = $_POST["prv_ob_1"];
$ob_2 = $_POST["prv_ob_2"];
$ob_3 = $_POST["prv_ob_3"];
$id= $_POST["prevent_id"];

$sqlf="SELECT * FROM ".TB_PREF."frequency_master  where freq_id='$cat_id'";
 $resf = db_query($sqlf);
 $row44 = db_fetch($resf);
 $totdays= $row44['total_days'];
 
 $nextpredate= date('Y-m-d',strtotime($maintain_date));
  $ob_date= date('Y-m-d',strtotime($ob_date));
 $nextpredate1= date('Y-m-d', strtotime($nextpredate . "+$totdays day"));
 
$checks_id=implode(',',$_POST["prv_check"]);
//print_r($checks_id);die;
//foreach(array_filter($checks_id) as $checks)
//{
//  if(isset($_POST['prv_check_'.$checks]))
//  {
//    $sql3= "SELECT * FROM ".TB_PREF."prevent_maintain_params where prevent_id='$id'"  ;
//    $rest=db_query($sql3);
//    $resuta= db_fetch($rest);
//    
//     // if($resuta) {
//     //   $uod="UPDATE ".TB_PREF."prevent_maintain_params SET parameters='$checks' where prevent_id"  
//     // }
//     $sql2="INSERT INTO ".TB_PREF."prevent_maintain_params(prevent_id,parameters) VALUES('$last_inserted_id','$checks')";
//
//db_query($sql2,"something went wrong");
//  }
//}


 $sql="UPDATE ".TB_PREF."prevent_maintain_entry SET maintain_date='$nextpredate',utility_id='$utility_id',frequency_id='$frequency_id',contractor_id='$contractor_id',prv_ob_date='$ob_date',prv_ob_1='$ob_1',prv_ob_2='$ob_2',prv_ob_3='$ob_3',parameters='$checks_id',next_prem_date='$nextpredate1' where prevent_id='$id'";
  db_query($sql,"something went wrong"); 
  header("Location: preventive_maintenance.php?All=1&success=1");
    
}
?>