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
//--------------------------------------------------------------------------------------

function add_work_order_issue($woid, $ref, $to_work_order, $items, $location, $workcentre,
	$date_, $memo_,$sl_no)
{
	global $Refs;

	begin_transaction();
	$args = func_get_args();
	$args = (object)array_combine(array('woid', 'ref', 'to_work_order', 'items', 'location', 
		'workcentre', 'date_', 'memo_'), $args);
	$args->trans_no = 0;
	hook_db_prewrite($args, ST_MANUISSUE);

	$sql = "INSERT INTO ".TB_PREF."wo_issues (workorder_id, reference, issue_date, loc_code, workcentre_id)
		VALUES (".db_escape($woid).", ".db_escape($ref).", '" .
		date2sql($date_) . "', ".db_escape($location).", ".db_escape($workcentre).")";
	db_query($sql,"The work order issue could not be added");

	$number = db_insert_id();

	$issue_total = $total_cost = 0;

	$wo = get_work_order($woid);

	foreach ($items as $item)
	{
		if ($to_work_order)	// otherwise it is material return to inventory
			$item->quantity = -$item->quantity;

		$unit_cost = get_unit_cost($item->stock_id);
		// insert a -ve stock move for each item
		add_stock_move(ST_MANUISSUE, $item->stock_id, $number,
			$location, $date_, $memo_, -$item->quantity, $unit_cost);

		$sql = "INSERT INTO ".TB_PREF."wo_issue_items (issue_id, stock_id, qty_issued, unit_cost,sl_no)
			SELECT ".db_escape($number).",".db_escape($item->stock_id).",".db_escape($item->quantity).", material_cost ,".db_escape($item->sl_no)."
			FROM ".TB_PREF."stock_master
			WHERE stock_id=".db_escape($item->stock_id);

		db_query($sql,"A work order issue item could not be added");
        //===========update serial no status==== if issued//
            updategrnserial($item->sl_no);
		
		$unit_cost = get_unit_cost($item->stock_id);
		$issue_cost = $unit_cost * $item->quantity;

        $stockitem = get_item($item->stock_id);

		// Compatibility for Service Items
		if (!is_service($stockitem["mb_flag"]))
			$ivaccount = $stockitem["inventory_account"];
		else
			$ivaccount = $stockitem["cogs_account"];

        $total_cost += add_gl_trans_std_cost(ST_MANUISSUE, $number, $date_, $ivaccount, 0, 0,
                $date_.": "._("Issue of")." ".$stockitem["description"], -$issue_cost);
	}

    $stockitem = get_item($wo['stock_id']);

   	$wip_account = $stockitem["wip_account"];

    add_gl_trans_std_cost(ST_MANUISSUE, $number, $date_, $wip_account,
        0, 0, $date_.": "._("Issue to")." ".$stockitem["description"], -$total_cost);

	if ($memo_)
		add_comments(ST_MANUISSUE, $number, $date_, $memo_);

	$Refs->save(ST_MANUISSUE, $number, $ref);
	add_audit_trail(ST_MANUISSUE, $number, $date_);

	$args->trans_no = $number;
	hook_db_postwrite($args, ST_MANUISSUE);

	commit_transaction();
}



function add_building_issues($woid, $ref, $to_work_order, $items, $location, $workcentre,
	$date_, $memo_,$sl_no,$tb='')
{
	global $Refs;

	begin_transaction();
	$args = func_get_args();
	$args = (object)array_combine(array('woid', 'ref', 'to_work_order', 'items', 'location', 
		'workcentre', 'date_', 'memo_'), $args);
	$args->trans_no = 0;
	hook_db_prewrite($args, ST_MANUISSUE);
	$issueID="";
	
        $issueID = getissueID($woid);
                if(!$issueID){
                $sql = "INSERT INTO ".TB_PREF."building_issues (workorder_id, reference, issue_date, loc_code, workcentre_id)
        		VALUES (".db_escape($woid).", ".db_escape($ref).", '" .
        		date2sql($date_) . "', ".db_escape($location).", ".db_escape($workcentre).")";
        		db_query($sql,"The work order issue could not be added");
	$number = db_insert_id();
        		}
        		else
        		$number  = $issueID;
$issue_total = $total_cost = 0;

	//$wo = get_work_order($woid);

	foreach ($items as $item)
	{
		if ($to_work_order)	// otherwise it is material return to inventory
			$item->quantity = -$item->quantity;

		$unit_cost = get_unit_cost($item->stock_id);
		// insert a -ve stock move for each item
		add_stock_move(ST_MANUISSUE, $item->stock_id, $number,
			$location, $date_, $memo_, -$item->quantity, $unit_cost);
 $table = "asset_issue_items"; 


		$sql = "INSERT INTO ".TB_PREF."$table (issue_id, stock_id, qty_issued, unit_cost,sl_no)
			SELECT ".db_escape($number).",".db_escape($item->stock_id).",".db_escape($item->quantity).", material_cost ,".db_escape($item->sl_no)."
			FROM ".TB_PREF."stock_master
			WHERE stock_id=".db_escape($item->stock_id);

		db_query($sql,"A work order issue item could not be added");
        //===========update serial no status==== if issued//
            updategrnserial($item->sl_no);
		
		$unit_cost = get_unit_cost($item->stock_id);
		$issue_cost = $unit_cost * $item->quantity;

        $stockitem = get_item($item->stock_id);

		// Compatibility for Service Items
		if (!is_service($stockitem["mb_flag"]))
			$ivaccount = $stockitem["inventory_account"];
		else
			$ivaccount = $stockitem["cogs_account"];

        $total_cost += add_gl_trans_std_cost(ST_MANUISSUE, $number, $date_, $ivaccount, 0, 0,
                $date_.": "._("Issue of")." ".$stockitem["description"], -$issue_cost);
	}

    $stockitem = get_item($wo['stock_id']);

   	$wip_account = $stockitem["wip_account"];

    add_gl_trans_std_cost(ST_MANUISSUE, $number, $date_, $wip_account,
        0, 0, $date_.": "._("Issue to")." ".$stockitem["description"], -$total_cost);

	if ($memo_)
		add_comments(ST_MANUISSUE, $number, $date_, $memo_);

	$Refs->save(ST_MANUISSUE, $number, $ref);
	add_audit_trail(ST_MANUISSUE, $number, $date_);

	$args->trans_no = $number;
	hook_db_postwrite($args, ST_MANUISSUE);

	commit_transaction();
}


function add_floor_issues($woid, $ref, $to_work_order, $items, $location, $workcentre,
	$date_, $memo_,$sl_no,$tb='')
{
	global $Refs;

	begin_transaction();
	$args = func_get_args();
	$args = (object)array_combine(array('woid', 'ref', 'to_work_order', 'items', 'location', 
		'workcentre', 'date_', 'memo_'), $args);
	$args->trans_no = 0;
	hook_db_prewrite($args, ST_MANUISSUE);
	$issueID="";
if($tb=='F'){
$issueID = getfloorissueID($woid);
if(!$issueID){
$sql = "INSERT INTO ".TB_PREF."floor_issues (workorder_id, reference, issue_date, loc_code, workcentre_id)
		VALUES (".db_escape($woid).", ".db_escape($ref).", '" .
		date2sql($date_) . "', ".db_escape($location).", ".db_escape($workcentre).")";
		db_query($sql,"The work order issue could not be added");
	$number = db_insert_id();
		}
		else
	$number  = $issueID;
		
}

	
	

	$issue_total = $total_cost = 0;

	//$wo = get_work_order($woid);
display_error($items); die();
	foreach ($items as $item)
	{
		if ($to_work_order)	// otherwise it is material return to inventory
			$item->quantity = -$item->quantity;

		$unit_cost = get_unit_cost($item->stock_id);
		// insert a -ve stock move for each item
		add_stock_move(ST_MANUISSUE, $item->stock_id, $number,
			$location, $date_, $memo_, -$item->quantity, $unit_cost);
//$table = "asset_issue_items";
if($tb=='F'){
$table = "floor_issue_items";

}


		$sql = "INSERT INTO ".TB_PREF."$table (issue_id, stock_id, qty_issued, unit_cost,sl_no)
			SELECT ".db_escape($number).",".db_escape($item->stock_id).",".db_escape($item->quantity).", material_cost ,".db_escape($item->sl_no)."
			FROM ".TB_PREF."stock_master
			WHERE stock_id=".db_escape($item->stock_id);

		db_query($sql,"A work order issue item could not be added");
        //===========update serial no status==== if issued//
            updategrnserial($item->sl_no);
		
		$unit_cost = get_unit_cost($item->stock_id);
		$issue_cost = $unit_cost * $item->quantity;

        $stockitem = get_item($item->stock_id);

		// Compatibility for Service Items
		if (!is_service($stockitem["mb_flag"]))
			$ivaccount = $stockitem["inventory_account"];
		else
			$ivaccount = $stockitem["cogs_account"];

        $total_cost += add_gl_trans_std_cost(ST_MANUISSUE, $number, $date_, $ivaccount, 0, 0,
                $date_.": "._("Issue of")." ".$stockitem["description"], -$issue_cost);
	}

    $stockitem = get_item($wo['stock_id']);

   	$wip_account = $stockitem["wip_account"];

    add_gl_trans_std_cost(ST_MANUISSUE, $number, $date_, $wip_account,
        0, 0, $date_.": "._("Issue to")." ".$stockitem["description"], -$total_cost);

	if ($memo_)
		add_comments(ST_MANUISSUE, $number, $date_, $memo_);

	$Refs->save(ST_MANUISSUE, $number, $ref);
	add_audit_trail(ST_MANUISSUE, $number, $date_);

	$args->trans_no = $number;
	hook_db_postwrite($args, ST_MANUISSUE);

	commit_transaction();
}

function add_room_issues($woid, $ref, $to_work_order, $items, $location, $workcentre,
	$date_, $memo_,$sl_no,$tb='')
{
	global $Refs;

	begin_transaction();
	$args = func_get_args();
	$args = (object)array_combine(array('woid', 'ref', 'to_work_order', 'items', 'location', 
		'workcentre', 'date_', 'memo_'), $args);
	$args->trans_no = 0;
	hook_db_prewrite($args, ST_MANUISSUE);
	$issueID="";
if($tb=='R'){
$issueID = getroomissueID($woid);
if(!$issueID){
$sql = "INSERT INTO ".TB_PREF."room_issues (workorder_id, reference, issue_date, loc_code, workcentre_id)
		VALUES (".db_escape($woid).", ".db_escape($ref).", '" .
		date2sql($date_) . "', ".db_escape($location).", ".db_escape($workcentre).")";
		db_query($sql,"The work order issue could not be added");
	$number = db_insert_id();
		}
		else
	$number  = $issueID;
		
}
$issue_total = $total_cost = 0;

	//$wo = get_work_order($woid);

	foreach ($items as $item)
	{
		if ($to_work_order)	// otherwise it is material return to inventory
			$item->quantity = -$item->quantity;

		$unit_cost = get_unit_cost($item->stock_id);
		// insert a -ve stock move for each item
		add_stock_move(ST_MANUISSUE, $item->stock_id, $number,
			$location, $date_, $memo_, -$item->quantity, $unit_cost);
//$table = "asset_issue_items";
if($tb=='R'){
$table = "room_issue_items";

}


		$sql = "INSERT INTO ".TB_PREF."$table (issue_id, stock_id, qty_issued, unit_cost,sl_no)
			SELECT ".db_escape($number).",".db_escape($item->stock_id).",".db_escape($item->quantity).", material_cost ,".db_escape($item->sl_no)."
			FROM ".TB_PREF."stock_master
			WHERE stock_id=".db_escape($item->stock_id);

		db_query($sql,"A work order issue item could not be added");
        //===========update serial no status==== if issued//
            updategrnserial($item->sl_no);
		
		$unit_cost = get_unit_cost($item->stock_id);
		$issue_cost = $unit_cost * $item->quantity;

        $stockitem = get_item($item->stock_id);

		// Compatibility for Service Items
		if (!is_service($stockitem["mb_flag"]))
			$ivaccount = $stockitem["inventory_account"];
		else
			$ivaccount = $stockitem["cogs_account"];

        $total_cost += add_gl_trans_std_cost(ST_MANUISSUE, $number, $date_, $ivaccount, 0, 0,
                $date_.": "._("Issue of")." ".$stockitem["description"], -$issue_cost);
	}

    $stockitem = get_item($wo['stock_id']);

   	$wip_account = $stockitem["wip_account"];

    add_gl_trans_std_cost(ST_MANUISSUE, $number, $date_, $wip_account,
        0, 0, $date_.": "._("Issue to")." ".$stockitem["description"], -$total_cost);

	if ($memo_)
		add_comments(ST_MANUISSUE, $number, $date_, $memo_);

	$Refs->save(ST_MANUISSUE, $number, $ref);
	add_audit_trail(ST_MANUISSUE, $number, $date_);

	$args->trans_no = $number;
	hook_db_postwrite($args, ST_MANUISSUE);

	commit_transaction();
}



function add_return_issues($woid, $slno, $name, $floor, $room, $dept,$seat,
	$date_,$status, $memo_,$return,$tb='',$bid='',$loc_code)
{
  //  display_error($bid);
   // die();
	global $Refs;

	begin_transaction();
	$args = func_get_args();
	$args = (object)array_combine(array('woid', 'ref', 'to_work_order', 'items', 'location', 
		'workcentre', 'date_', 'memo_'), $args);
	$args->trans_no = 0;
	hook_db_prewrite($args, ST_MANUISSUE);
	$issueID="";
if($tb=='R'){
$issueID = getrerutnID($woid);
if(!$issueID){
$sql = "INSERT INTO ".TB_PREF."return (stock_id,building,floor,room,department,seat,return_status,return_date,item_status,sl_no,loc_code)
		VALUES (".db_escape($woid).", ".db_escape($name).", ".db_escape($floor).", ".db_escape($room).",".db_escape($dept).",".db_escape($seat).",".db_escape($return).", '" .
		date2sql($date_) . "', ".db_escape($status).",".db_escape($slno).",".db_escape($loc_code).")";
		db_query($sql,"The work order issue could not be added");
	$number = db_insert_id();
		}
		else
	$number  = $issueID;
		
}

if($bid) {
   // $sql = "DELETE FROM ".TB_PREF."bom WHERE id=".db_escape($bid);
    $sql2 = "UPDATE ".TB_PREF."bom  SET status=1 WHERE id=".db_escape($bid);
	db_query($sql2,"Could not delete this bom components");
}

 $sql1 = "UPDATE " . TB_PREF . "grn_serial_no SET status= 0,manufactured_id=0 WHERE osl_no=" . db_escape($slno);

    db_query($sql1, "could not update Statutory");
 
//$issue_total = $total_cost = 0;

	//$wo = get_work_order($woid);

//	foreach ($items as $item)
//	{
//		if ($to_work_order)	// otherwise it is material return to inventory
//			$item->quantity = -$item->quantity;
//
//		$unit_cost = get_unit_cost($item->stock_id);
//		// insert a -ve stock move for each item
//		add_stock_move(ST_MANUISSUE, $item->stock_id, $number,
//			$location, $date_, $memo_, -$item->quantity, $unit_cost);
////$table = "asset_issue_items";
//
//        //===========update serial no status==== if issued//
//            updategrnserial($item->sl_no);
//		
//		$unit_cost = get_unit_cost($item->stock_id);
//		$issue_cost = $unit_cost * $item->quantity;
//
//        $stockitem = get_item($item->stock_id);
//
//		// Compatibility for Service Items
//		if (!is_service($stockitem["mb_flag"]))
//			$ivaccount = $stockitem["inventory_account"];
//		else
//			$ivaccount = $stockitem["cogs_account"];
//
//        $total_cost += add_gl_trans_std_cost(ST_MANUISSUE, $number, $date_, $ivaccount, 0, 0,
//                $date_.": "._("Issue of")." ".$stockitem["description"], -$issue_cost);
//	}
//
//    $stockitem = get_item($wo['stock_id']);
//
//   	$wip_account = $stockitem["wip_account"];
//
//    add_gl_trans_std_cost(ST_MANUISSUE, $number, $date_, $wip_account,
//        0, 0, $date_.": "._("Issue to")." ".$stockitem["description"], -$total_cost);
//
//	if ($memo_)
//		add_comments(ST_MANUISSUE, $number, $date_, $memo_);
//
//	$Refs->save(ST_MANUISSUE, $number, $ref);
//	add_audit_trail(ST_MANUISSUE, $number, $date_);
//
//	$args->trans_no = $number;
//	hook_db_postwrite($args, ST_MANUISSUE);

	commit_transaction();
} 

function add_dept_issues($woid, $ref, $to_work_order, $items, $location, $workcentre,
	$date_, $memo_,$sl_no,$tb='')
{
	global $Refs;

	begin_transaction();
	$args = func_get_args();
	$args = (object)array_combine(array('woid', 'ref', 'to_work_order', 'items', 'location', 
		'workcentre', 'date_', 'memo_'), $args);
	$args->trans_no = 0;
	hook_db_prewrite($args, ST_MANUISSUE);
	$issueID="";
if($tb=='D'){
$issueID = getdeptissueID($woid);
if(!$issueID){
$sql = "INSERT INTO ".TB_PREF."dept_issues (workorder_id, reference, issue_date, loc_code, workcentre_id)
		VALUES (".db_escape($woid).", ".db_escape($ref).", '" .
		date2sql($date_) . "', ".db_escape($location).", ".db_escape($workcentre).")";
		db_query($sql,"The work order issue could not be added");
	$number = db_insert_id();
		}
		else
	$number  = $issueID;
		
}
$issue_total = $total_cost = 0;

	//$wo = get_work_order($woid);

	foreach ($items as $item)
	{
		if ($to_work_order)	// otherwise it is material return to inventory
			$item->quantity = -$item->quantity;

		$unit_cost = get_unit_cost($item->stock_id);
		// insert a -ve stock move for each item
		add_stock_move(ST_MANUISSUE, $item->stock_id, $number,
			$location, $date_, $memo_, -$item->quantity, $unit_cost);
//$table = "asset_issue_items";
if($tb=='D'){
$table = "dept_issue_items";

}


		$sql = "INSERT INTO ".TB_PREF."$table (issue_id, stock_id, qty_issued, unit_cost,sl_no)
			SELECT ".db_escape($number).",".db_escape($item->stock_id).",".db_escape($item->quantity).", material_cost ,".db_escape($item->sl_no)."
			FROM ".TB_PREF."stock_master
			WHERE stock_id=".db_escape($item->stock_id);

		db_query($sql,"A work order issue item could not be added");
        //===========update serial no status==== if issued//
            updategrnserial($item->sl_no);
		
		$unit_cost = get_unit_cost($item->stock_id);
		$issue_cost = $unit_cost * $item->quantity;

        $stockitem = get_item($item->stock_id);

		// Compatibility for Service Items
		if (!is_service($stockitem["mb_flag"]))
			$ivaccount = $stockitem["inventory_account"];
		else
			$ivaccount = $stockitem["cogs_account"];

        $total_cost += add_gl_trans_std_cost(ST_MANUISSUE, $number, $date_, $ivaccount, 0, 0,
                $date_.": "._("Issue of")." ".$stockitem["description"], -$issue_cost);
	}

    $stockitem = get_item($wo['stock_id']);

   	$wip_account = $stockitem["wip_account"];

    add_gl_trans_std_cost(ST_MANUISSUE, $number, $date_, $wip_account,
        0, 0, $date_.": "._("Issue to")." ".$stockitem["description"], -$total_cost);

	if ($memo_)
		add_comments(ST_MANUISSUE, $number, $date_, $memo_);

	$Refs->save(ST_MANUISSUE, $number, $ref);
	add_audit_trail(ST_MANUISSUE, $number, $date_);

	$args->trans_no = $number;
	hook_db_postwrite($args, ST_MANUISSUE);

	commit_transaction();
}

function add_seat_issues($woid, $ref, $to_work_order, $items, $location, $workcentre,
	$date_, $memo_,$sl_no,$tb='')
{
	global $Refs;

	begin_transaction();
	$args = func_get_args();
	$args = (object)array_combine(array('woid', 'ref', 'to_work_order', 'items', 'location', 
		'workcentre', 'date_', 'memo_'), $args);
	$args->trans_no = 0;
	hook_db_prewrite($args, ST_MANUISSUE);
	$issueID="";
if($tb=='S'){
$issueID = getseatissueID($woid);
if(!$issueID){
$sql = "INSERT INTO ".TB_PREF."seat_issues (workorder_id, reference, issue_date, loc_code, workcentre_id)
		VALUES (".db_escape($woid).", ".db_escape($ref).", '" .
		date2sql($date_) . "', ".db_escape($location).", ".db_escape($workcentre).")";
		db_query($sql,"The work order issue could not be added");
	$number = db_insert_id();
		}
		else
	$number  = $issueID;
		
}
$issue_total = $total_cost = 0;

	//$wo = get_work_order($woid);

	foreach ($items as $item)
	{
		if ($to_work_order)	// otherwise it is material return to inventory
			$item->quantity = -$item->quantity;

		$unit_cost = get_unit_cost($item->stock_id);
		// insert a -ve stock move for each item
		add_stock_move(ST_MANUISSUE, $item->stock_id, $number,
			$location, $date_, $memo_, -$item->quantity, $unit_cost);
//$table = "asset_issue_items";
if($tb=='S'){
$table = "seat_issue_items";

}
$sql = "INSERT INTO ".TB_PREF."$table (issue_id, stock_id, qty_issued, unit_cost,sl_no)
			SELECT ".db_escape($number).",".db_escape($item->stock_id).",".db_escape($item->quantity).", material_cost ,".db_escape($item->sl_no)."
			FROM ".TB_PREF."stock_master
			WHERE stock_id=".db_escape($item->stock_id);

		db_query($sql,"A work order issue item could not be added");
        //===========update serial no status==== if issued//
            updategrnserial($item->sl_no);
		
		$unit_cost = get_unit_cost($item->stock_id);
		$issue_cost = $unit_cost * $item->quantity;

        $stockitem = get_item($item->stock_id);

		// Compatibility for Service Items
		if (!is_service($stockitem["mb_flag"]))
			$ivaccount = $stockitem["inventory_account"];
		else
			$ivaccount = $stockitem["cogs_account"];

        $total_cost += add_gl_trans_std_cost(ST_MANUISSUE, $number, $date_, $ivaccount, 0, 0,
                $date_.": "._("Issue of")." ".$stockitem["description"], -$issue_cost);
	}

    $stockitem = get_item($wo['stock_id']);

   	$wip_account = $stockitem["wip_account"];

    add_gl_trans_std_cost(ST_MANUISSUE, $number, $date_, $wip_account,
        0, 0, $date_.": "._("Issue to")." ".$stockitem["description"], -$total_cost);

	if ($memo_)
		add_comments(ST_MANUISSUE, $number, $date_, $memo_);

	$Refs->save(ST_MANUISSUE, $number, $ref);
	add_audit_trail(ST_MANUISSUE, $number, $date_);

	$args->trans_no = $number;
	hook_db_postwrite($args, ST_MANUISSUE);

	commit_transaction();
}
function add_user_issues($name, $floor, $room, $department, $seat, $issue_date,
	$department_id,$employee_id)
{
	global $Refs;

	begin_transaction();
	$args = func_get_args();
	$args = (object)array_combine(array('woid', 'ref', 'to_work_order', 'items', 'location', 
		'workcentre', 'date_', 'memo_'), $args);
	$args->trans_no = 0;
	hook_db_prewrite($args, ST_MANUISSUE);
	$issueID="";

$issueID = getfloorissueID($woid);
if(!$issueID){
$sql = "INSERT INTO ".TB_PREF."user_issues (name,floor,room,department,seat,issue_date,department_id,employee_id)
		VALUES (".db_escape($name).", ".db_escape($floor)."," .db_escape($room) . ", ".db_escape($department).", ".db_escape($seat).",'".date2sql($issue_date)."',".db_escape($department_id).",".db_escape($employee_id).")";
		db_query($sql,"The work order issue could not be added");
	$number = db_insert_id();
		}
		else
	$number  = $issueID;

   $sql1="UPDATE fa_seat_allocation SET status=1 where seat_no=".db_escape($seat);             
     db_query($sql1,"The work order issue could not be added");           
      commit_transaction();
}

function add_user_group($name, $floor,$issue_date)
{
  global $Refs;

	begin_transaction();
	

//$issueID = getfloorissueID($woid);
//if(!$issueID){
$sql = "INSERT INTO ".TB_PREF."user_assign_group (group_id,user_id,added_date)
		VALUES (".db_escape($name).", ".db_escape($floor).",'".date2sql($issue_date)."')";
		db_query($sql,"The work order issue could not be added");
        
        //display_error($sql);die();
	$sql1 = "UPDATE " . TB_PREF . "group SET assinged= 1 WHERE 	id=" . db_escape($name);

    db_query($sql1, "could not update Statutory");
		
      commit_transaction();  
}
function updategrnserial($sl_no){
  $sql = "UPDATE " . TB_PREF . "grn_serial_no SET status= 1 WHERE osl_no=" . db_escape($sl_no);

    db_query($sql, "could not update Statutory");
}
function getissueID($woid){
	$sql = "SELECT * FROM ".TB_PREF."building_issues WHERE workorder_id=".db_escape($woid)
	." ORDER BY issue_no";
    $result =  db_query($sql, "The work order issues could not be retrieved");
    $myrow = db_fetch_row($result);
    return $myrow[0];

}

function check_user_exit($woid){
	$sql = "SELECT * FROM ".TB_PREF."user_assign_group WHERE user_id=".db_escape($woid);
    $result =  db_query($sql, "The work order issues could not be retrieved");
    $myrow = db_fetch_row($result);
    return $myrow[0];

}
function getfloorissueID($woid){
	$sql = "SELECT * FROM ".TB_PREF."floor_issues WHERE workorder_id=".db_escape($woid)
	." ORDER BY issue_no";
    $result =  db_query($sql, "The work order issues could not be retrieved");
    $myrow = db_fetch_row($result);
    return $myrow[0];

}
function getroomissueID($woid){
	$sql = "SELECT * FROM ".TB_PREF."room_issues WHERE workorder_id=".db_escape($woid)
	." ORDER BY issue_no";
    $result =  db_query($sql, "The work order issues could not be retrieved");
    $myrow = db_fetch_row($result);
    return $myrow[0];

}
function getrerutnID($woid){
	$sql = "SELECT * FROM ".TB_PREF."return WHERE sl_no=".db_escape($woid)
	." ORDER BY sl_no";
    $result =  db_query($sql, "The work order issues could not be retrieved");
    $myrow = db_fetch_row($result);
    return $myrow[0];

}
function getdeptissueID($woid){
	$sql = "SELECT * FROM ".TB_PREF."dept_issues WHERE workorder_id=".db_escape($woid)
	." ORDER BY issue_no";
    $result =  db_query($sql, "The work order issues could not be retrieved");
    $myrow = db_fetch_row($result);
    return $myrow[0];

}
function getseatissueID($woid){
	$sql = "SELECT * FROM ".TB_PREF."seat_issues WHERE workorder_id=".db_escape($woid)
	." ORDER BY issue_no";
    $result =  db_query($sql, "The work order issues could not be retrieved");
    $myrow = db_fetch_row($result);
    return $myrow[0];

}


function getseatItemID($woid){
	$sql = "SELECT * FROM ".TB_PREF."seat_issue_items WHERE stock_id=".db_escape($woid)
	." ORDER BY id";
    $result =  db_query($sql, "The work order issues could not be retrieved");
    $myrow = db_num_rows($result);
    return $myrow;

}
function getdeptItemID($woid){
	$sql = "SELECT * FROM ".TB_PREF."dept_issue_items WHERE stock_id=".db_escape($woid)
	." ORDER BY id";
    $result =  db_query($sql, "The work order issues could not be retrieved");
    $myrow = db_num_rows($result);
    return $myrow;

}
//--------------------------------------------------------------------------------------

function get_work_order_issues($woid)
{
	$sql = "SELECT * FROM ".TB_PREF."wo_issues WHERE workorder_id=".db_escape($woid)
	." ORDER BY issue_no";
    return db_query($sql, "The work order issues could not be retrieved");
}

function get_additional_issues($woid)
{
	$sql = "SELECT issue.*, item.*, stock.mb_flag
		FROM ".TB_PREF."wo_issues issue, "
			.TB_PREF."wo_issue_items item
			LEFT JOIN ".TB_PREF."stock_master stock ON stock.stock_id=item.stock_id
		WHERE issue.issue_no=item.issue_id
		AND issue.workorder_id=".db_escape($woid)
		." ORDER BY item.id";
    return db_query($sql, "The work order issues could not be retrieved");
}
//--------------------------------------------------------------------------------------

function get_work_order_issue($issue_no)
{
	$sql = "SELECT DISTINCT issue.*, wo.stock_id, wo.closed,
		item.description, loc.location_name, center.name AS WorkCentreName,manu.item_code
		FROM ".TB_PREF."wo_issues issue,"
			.TB_PREF."workorders wo,"
                        .TB_PREF."wo_manufacture manu,"
			.TB_PREF."stock_master item,"
			.TB_PREF."locations loc,"
			.TB_PREF."workcentres center
		WHERE issue_no=".db_escape($issue_no)."
		AND wo.id = issue.workorder_id
		AND loc.loc_code = issue.loc_code
		AND center.id = issue.workcentre_id
		AND item.stock_id = wo.stock_id
                AND manu.reference = issue.reference";
    $result = db_query($sql, "A work order issue could not be retrieved");

    return db_fetch($result);
}

//--------------------------------------------------------------------------------------

function get_work_order_issue_details($issue_no)
{
	$sql = "SELECT issue.*, item.description, item.units
		FROM ".TB_PREF."wo_issue_items issue,"
			.TB_PREF."stock_master item
		WHERE issue_id=".db_escape($issue_no)."
		AND item.stock_id=issue.stock_id
		ORDER BY issue.id";
    return db_query($sql, "The work order issue items could not be retrieved");
}

function get_work_order_issue_ref($trans_no)
{
	$sql = "SELECT issue.*
		FROM ".TB_PREF."wo_issues issue Left join ".TB_PREF."wo_manufacture pro on pro.reference = issue.reference where pro.reference is null and issue.workorder_id = $trans_no";
        //display_error($sql);
    return db_query($sql, "The work order issue items could not be retrieved");
}

//--------------------------------------------------------------------------------------

function exists_work_order_issue($issue_no)
{
	$sql = "SELECT issue_no FROM ".TB_PREF."wo_issues WHERE issue_no=".db_escape($issue_no);
	$result = db_query($sql, "Cannot retreive a wo issue");

    
}

//--------------------------------------------------------------------------------------

function check_void_wo_issue($issue_no)
{
	$issue = get_work_order_issue($issue_no);

	return $issue['closed'] ? $issue['workorder_id'] : 0;
}

//--------------------------------------------------------------------------------------
function check_issues_serial($serial_no)
{
	$sql = "SELECT issue.*, item.*, stock.mb_flag
		FROM ".TB_PREF."wo_issues issue, "
			.TB_PREF."wo_issue_items item
			LEFT JOIN ".TB_PREF."stock_master stock ON stock.stock_id=item.stock_id
		WHERE issue.issue_no=item.issue_id
		AND item.sl_no=".db_escape($serial_no)
		." ORDER BY item.id";
   $result = db_query($sql, "The work order issues could not be retrieved");
   return (db_num_rows($result) > 0);
}

function void_work_order_issue($type_no)
{
	begin_transaction();
	hook_db_prevoid(ST_MANUISSUE, $type_no);

	$void_entry = get_voided_entry(ST_MANUISSUE, $type_no);
	if ($void_entry)
		return;

	if (check_void_wo_issue($type_no))
		return;

	// FIXME update issued material cost

	// void the actual issue items and their quantities
	$sql = "UPDATE ".TB_PREF."wo_issue_items SET qty_issued = 0
		WHERE issue_id=".db_escape($type_no);

	db_query($sql,"A work order issue item could not be voided");

	void_gl_trans(ST_MANUISSUE, $type_no);

	void_stock_move(ST_MANUISSUE, $type_no);

	commit_transaction();
}
function add_return_item($name, $floor, $room, $department, $seat, $issue_date,
	$department_id,$employee_id)
{
	global $Refs;

	begin_transaction();
	$args = func_get_args();
	$args = (object)array_combine(array('woid', 'ref', 'to_work_order', 'items', 'location', 
		'workcentre', 'date_', 'memo_'), $args);
	$args->trans_no = 0;
	hook_db_prewrite($args, ST_MANUISSUE);
	$issueID="";

$issueID = getfloorissueID($woid);
if(!$issueID){
$sql = "INSERT INTO ".TB_PREF."user_issues (name,floor,room,department,seat,issue_date,department_id,employee_id)
		VALUES (".db_escape($name).", ".db_escape($floor)."," .db_escape($room) . ", ".db_escape($department).", ".db_escape($seat).",'".date2sql($issue_date)."',".db_escape($department_id).",".db_escape($employee_id).")";
		db_query($sql,"The work order issue could not be added");
	$number = db_insert_id();
		}
		else
	$number  = $issueID;
		
      commit_transaction();
}
