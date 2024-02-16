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
//----------------------------------------------------------------------------------------
function get_demand_qty($stock_id, $location)
{
	$sql = "SELECT SUM(line.quantity - line.qty_sent) AS QtyDemand
			FROM ".TB_PREF."sales_order_details line,
				".TB_PREF."sales_orders sorder
				WHERE line.order_no=sorder.order_no AND sorder.trans_type=".ST_SALESORDER
				." AND sorder.trans_type=line.trans_type"
				." AND line.stk_code = ".db_escape($stock_id);
	if ($location != "")
		$sql .= " AND sorder.from_stk_loc =".db_escape($location);

    $result = db_query($sql,"No transactions were returned");
	$row = db_fetch($result);
	if ($row === false)
		return 0;
	return $row['QtyDemand'];
}

$bom_list = array(); 
$qoh_stock = NULL;

function load_stock_levels($location)
{
	$date = date2sql(Today());

	$qoh_stock = array();
	$sql = "SELECT stock_id, SUM(qty)
		FROM ".TB_PREF."stock_moves
		WHERE tran_date <= '$date'";
	if ($location != '')
		$sql .= " AND loc_code = ".db_escape($location);
	$sql .= " GROUP BY stock_id";

	$result = db_query($sql, "QOH calculation failed");
	while ($row = db_fetch($result))
		$qoh_stock[$row[0]] = $row[1];

	return $qoh_stock;
}

// recursion fixed. Max 10 recursion levels.
function stock_demand_manufacture($stock_id, $qty, $demand_id, $location, $level=0) 
{
	global $bom_list, $qoh_stock;

	$demand = 0.0;
	if ($level > 10) {
		display_warning("BOM Too many Manufacturing levels deep $level");
		return $demand;
	}
	// Load all stock levels (stock moves) into $qoh_stock
	if ($qoh_stock == NULL)
		$qoh_stock = load_stock_levels($location);

	if (empty($qoh_stock[$stock_id]))
		$stock_qty = 0;
	else
		$stock_qty = $qoh_stock[$stock_id];

	if ($qty <= $stock_qty)
		return $demand;
	$bom = @$bom_list[$stock_id];
	if ($bom == NULL) {
		$sql = "SELECT parent, component, quantity FROM "
			.TB_PREF."bom WHERE parent = ".db_escape($stock_id);
		if ($location != "") $sql .= " AND loc_code = ".db_escape($location);
		$result = db_query($sql, "Could not search bom");
		$bom = array();
		// Even if we get no results, remember that fact 
		$bom[] = array($stock_id, '', 0); 
		while ($row = db_fetch_row($result)) {
			$bom[] = array($row[0], $row[1], $row[2]);
		}
		db_free_result($result);
		$bom_list[$stock_id] = $bom;
	}
	$len = count($bom);
	$i = 0;
	while ($i < $len) {
		$row = $bom[$i];
		$i++; 
		// Ignore the dummy entry
		if ($row[1] == '') continue;
		$q = $qty * $row[2];
		if ($row[1] == $demand_id) $demand += $q;
		$demand += stock_demand_manufacture($row[1], $q, $demand_id, $location, $level+1);
	}
	return $demand;
}

// recursion fixed by Tom Moulton
function get_demand_asm_qty($stock_id, $location) 
{
	$demand_qty = 0.0;
	$sql = "SELECT line.stk_code, SUM(line.quantity-line.qty_sent) AS Demmand
		   FROM ".TB_PREF."sales_order_details line,
				".TB_PREF."sales_orders sorder,
				".TB_PREF."stock_master item
		   WHERE sorder.order_no = line.order_no
		   		AND sorder.trans_type=".ST_SALESORDER
		   	 ." AND sorder.trans_type=line.trans_type
				AND line.quantity-line.qty_sent > 0
				AND item.stock_id=line.stk_code
				AND item.mb_flag='M'";
	if ($location != "")
		$sql .= " AND sorder.from_stk_loc =".db_escape($location);
	$sql .= " GROUP BY line.stk_code";
    $result = db_query($sql, "No transactions were returned");
	while ($row = db_fetch_row($result)) {
		$demand_qty += stock_demand_manufacture($row[0], $row[1], $stock_id, $location);
	}
	return $demand_qty;
}

function get_on_porder_qty($stock_id, $location)
{
	$sql = "SELECT SUM(line.quantity_ordered - line.quantity_received) AS qoo
		FROM ".TB_PREF."purch_order_details line 
			INNER JOIN ".TB_PREF."purch_orders po ON line.order_no=po.order_no
		WHERE line.item_code=".db_escape($stock_id);
	if ($location != "")
		$sql .= " AND po.into_stock_location=".db_escape($location);
	$sql .= " AND line.item_code=".db_escape($stock_id);
	$qoo_result = db_query($sql,"could not receive quantity on order for item");

	if (db_num_rows($qoo_result) == 1)
	{
		$qoo_row = db_fetch_row($qoo_result);
		$qoo =  $qoo_row[0];
	}
	else
	{
		$qoo = 0;
	}
	return $qoo;
}

function get_on_worder_qty($stock_id, $location)
{
 	$qoo = 0.0;
	$flag = get_mb_flag($stock_id);
	if ($flag == 'M')
	{
		$sql = "SELECT SUM((units_reqd-units_issued)) AS qoo
			FROM ".TB_PREF."workorders
			WHERE stock_id=".db_escape($stock_id)
				." AND released AND NOT closed";

		if ($location != "")
			$sql .= " AND loc_code=".db_escape($location);

		$qoo_result = db_query($sql,"could not receive quantity on order for item");
		if (db_num_rows($qoo_result) == 1)
		{
			$qoo_row = db_fetch_row($qoo_result);
			$qoo = $qoo_row[0];
		}
	}
	return $qoo;
}

//--------------------------------------------------------------------------------------

function add_bom($selected_parent, $component, $workcentre_added, $loc_code, $quantity,$date_)
{
    
   
   $issuedate= date2sql($date_);
   
	$sql = "INSERT INTO ".TB_PREF."bom (parent, component, workcentre_added, loc_code, quantity,issue_date)
		VALUES (".db_escape($selected_parent).", ".db_escape($component) . ","
		.db_escape($workcentre_added) . ", ".db_escape($loc_code) . ", "
		. $quantity . ",'".$issuedate."')";
  //display_error($sql);
	db_query($sql,"check failed");
        
        
        
}


function add_serial_no($osl_no,$component,$loc_code,$quantity,$mid)
{
	$sql = "UPDATE ".TB_PREF."grn_serial_no SET status='1', manufactured_id='$mid' where osl_no=".db_escape($osl_no);

	db_query($sql,"check failed");
       $quantity1= -$quantity; 
   add_stock_move(ST_WORKORDER, $component, $woid,$loc_code, $date, $ref, $quantity1, $unit_cost);     
        
}
function add_group($selected_parent, $component, $workcentre_added, $loc_code, $quantity,$osl_no,$center,$date)
{
    
  //  display_error($date);
	$sql = "INSERT INTO ".TB_PREF."sys_group (group_name, component, work_center, loc_code, quantity,issue_date)
		VALUES (".db_escape($selected_parent).", ".db_escape($component) . ","
		.db_escape($workcentre_added) . ", ".db_escape($loc_code) . ", "
		. $quantity . ",'".date2sql($date)."')";

	db_query($sql,"check failed");
    $date=date('Y-m-d');
    $quantity= -$quantity;
    
   $sql1 = "UPDATE ".TB_PREF."grn_serial_no SET status='1',group_center_id='$center' where osl_no=".db_escape($osl_no);
   //display_error($sql1);
   db_query($sql1,"check failed"); 
   
    add_stock_move(ST_WORKORDER, $component, $woid,$loc_code, $date, $ref, $quantity, $unit_cost);
	
}
//--------------------------------------------------------------------------------------

function update_bom($selected_parent, $selected_component, $workcentre_added, $loc_code, $quantity)
{
	$sql = "UPDATE ".TB_PREF."bom SET workcentre_added=".db_escape($workcentre_added)
	 . ",loc_code=".db_escape($loc_code) . ",
		quantity= " . $quantity . "
		WHERE parent=".db_escape($selected_parent) . "
		AND id=".db_escape($selected_component);
	check_db_error("Could not update this bom component", $sql);

	db_query($sql,"could not update bom");
}

function update_sys_group($selected_parent, $selected_component, $workcentre_added, $loc_code, $quantity,$pre_qua,$component)
{
   
	$sql = "UPDATE ".TB_PREF."sys_group SET work_center=".db_escape($workcentre_added)
	 . ",loc_code=".db_escape($loc_code) . ",
		quantity= " . $quantity . "
		WHERE group_name=".db_escape($selected_parent) . "
		AND id=".db_escape($selected_component);
	check_db_error("Could not update this bom component", $sql);

	db_query($sql,"could not update bom");
    $qua=$quantity-$pre_qua;
  
    if($qua>0)  {
    $quantity1= -$qua;
    add_stock_move(ST_WORKORDER, $component, $woid,$loc_code, $date, $ref, $quantity1, $unit_cost);
    }
    else {
      $quantity1= abs($qua);  
     add_stock_return(ST_WORKORDER, $component, $woid,$loc_code, $date, $ref, $quantity1, $unit_cost);
 
    }
    // display_error($quantity1); die(); 
   
    
}
	
function delete_bom($selected_id)
{
	$sql = "DELETE FROM ".TB_PREF."bom WHERE id=".db_escape($selected_id);
	db_query($sql,"Could not delete this bom components");
}

function delete_sys_manu($selected_id)
{
     display_error($selected_id); die(); 
    $sql1 = "SELECT * FROM ".TB_PREF."bom WHERE id=".db_escape($selected_id);
    $result = db_query($sql1, "could not get bom");
	$row= db_fetch($result);
    $component=$row['component'];
    $loc_code=$row['loc_code'];
    $quantity= $row['quantity'];
    $date=DATE('Y-m-d');
    $ref='0';
	$sql = "DELETE FROM ".TB_PREF."bom WHERE id=".db_escape($selected_id);
	db_query($sql,"Could not delete this bom components");
    
     add_stock_return(ST_WORKORDER, $component, $woid,$loc_code, $date, $ref, $quantity, $unit_cost);
}

function delete_sys_grp($location,$stock_id,$sl_no,$sysid,$date_,$status,$return_status,$memo_='')
{
    //display_error($date_);
    $sql1 = "SELECT * FROM ".TB_PREF."sys_group WHERE id=".db_escape($sysid);
    $result = db_query($sql1, "could not get bom");
	$row= db_fetch($result);
    $component=$row['component'];
    $loc_code=$row['loc_code'];
    $quantity= $row['quantity'];
    $date=$date_;
    $ref='0';
	$sql = "UPDATE ".TB_PREF."sys_group SET status=1 WHERE id=".db_escape($sysid);
	db_query($sql,"Could not delete this bom components");
        
     $sql2 = "UPDATE " . TB_PREF . "grn_serial_no SET status= 0,group_center_id=0 WHERE osl_no=" . db_escape($sl_no);

    db_query($sql2, "could not update Statutory");
     add_stock_return(ST_WORKORDER, $component, $woid,$loc_code, $date, $ref, $quantity, $unit_cost,$sysid,$status,$return_status,$sl_no);
}
function get_bom($item)
{
	$sql = "SELECT bom.*, loc.location_name,
		centre.name AS WorkCentreDescription,
    	item.description, item.mb_flag AS ResourceType, 
    	item.material_cost AS ProductCost, units,
    	bom.quantity * item.material_cost AS ComponentCost 
    	FROM ".TB_PREF."workcentres centre,
    		".TB_PREF."locations loc,
    		".TB_PREF."bom bom
	    		INNER JOIN ".TB_PREF."stock_master item ON bom.component = item.stock_id 
    	WHERE bom.parent = ".db_escape($item)."
		AND centre.id=bom.workcentre_added
		AND bom.loc_code = loc.loc_code ORDER BY bom.id";
	
//display_error($sql);
	return db_query($sql, "The bill of material could not be retrieved");
}


function get_manufactured($item)
{
	$sql = "SELECT manu.item_code,manu.id as mid,bom.id as bid,bom.component,bom.quantity,bom.workcentre_added,bom.issue_date,item.description,loc.location_name,loc.loc_code,
		centre.name AS WorkCentreDescription,units from ".TB_PREF."workcentres centre,".TB_PREF."locations loc, fa_wo_manufacture manu INNER JOIN fa_workorders wo on wo.id=manu.workorder_id"
                . " INNER JOIN fa_bom bom on bom.parent=wo.stock_id INNER JOIN ".TB_PREF."stock_master item ON bom.component = item.stock_id "
                . "where manu.item_code=".db_escape($item)." AND centre.id=bom.workcentre_added AND bom.loc_code = loc.loc_code and bom.status=0";
	
//display_error($sql);
	return db_query($sql, "The bill of material could not be retrieved");
}
function get_group($item)
{
    //display_error($item);
	$sql = "SELECT grp.*, loc.location_name,
		centre.name AS WorkCentreDescription,
    	item.description, item.mb_flag AS ResourceType, 
    	item.material_cost AS ProductCost, units,
    	grp.quantity * item.material_cost AS ComponentCost 
    	FROM ".TB_PREF."workcentres centre,
    		".TB_PREF."locations loc,
    		".TB_PREF."sys_group grp
	    		INNER JOIN ".TB_PREF."stock_master item ON grp.component = item.stock_id 
    	WHERE grp.group_name = ".db_escape($item)."
		AND centre.id=grp.work_center
		AND grp.loc_code = loc.loc_code and grp.status=0 ORDER BY grp.id";
	
//display_error($sql);
	return db_query($sql, "The bill of material could not be retrieved");
}

function get_assinged_item($item)
{
    //display_error($item);
	$sql = "SELECT * FROM ".TB_PREF."sys_group join fa_group on fa_group.id=fa_sys_group.group_name group by group_name ";
	
//display_error($sql);
	return db_query($sql, "The bill of material could not be retrieved");
}
function get_sysytem_group($grp_id)
{
	$sql = "SELECT grp.*, loc.location_name,
		centre.name AS WorkCentreDescription,
    	item.description, item.mb_flag AS ResourceType, 
    	item.material_cost AS ProductCost, units,
    	grp.quantity * item.material_cost AS ComponentCost 
    	FROM ".TB_PREF."workcentres centre,
    		".TB_PREF."locations loc,
    		".TB_PREF."sys_group grp
	    		INNER JOIN ".TB_PREF."stock_master item ON grp.component = item.stock_id 
    	WHERE grp.group_name = ".db_escape($grp_id)."
		AND centre.id=grp.work_center
		AND grp.loc_code = loc.loc_code ORDER BY grp.id";
//  display_error($sql) ;     
        
        return db_query($sql, "The bill of material could not be retrieved");
}

//--------------------------------------------------------------------------------------

function get_component_from_bom($selected_id)
{
	$sql = "SELECT bom.*, item.description
		FROM ".TB_PREF."bom bom,"
			.TB_PREF."stock_master item
		WHERE id=".db_escape($selected_id)."
		AND item.stock_id=bom.component";

	$result = db_query($sql, "could not get bom");
	return db_fetch($result);
}

function get_component_from_manu($selected_id)
{
	$sql = "SELECT bom.*, item.description
		FROM ".TB_PREF."bom bom,"
			.TB_PREF."fa_wo_manufacture item
		WHERE item_code=".db_escape($selected_id)."
		AND item.stock_id=bom.component";

	$result = db_query($sql, "could not get bom");
	return db_fetch($result);
}

function get_parent_from_manu($selected_id)
{
	$sql = "SELECT wo.*,item.workorder_id
		FROM ".TB_PREF."wo_manufacture item join fa_workorders wo on wo.id=item.workorder_id
		WHERE item.item_code=".db_escape($selected_id);

	$result = db_query($sql, "could not get bom");
	return db_fetch($result);
}
function get_component_from_sys($selected_id)
{
	$sql = "SELECT sys.*, item.description
		FROM ".TB_PREF."sys_group sys,"
			.TB_PREF."stock_master item
		WHERE id=".db_escape($selected_id)."
		AND item.stock_id=sys.component";

	$result = db_query($sql, "could not get bom");
	return db_fetch($result);
}
//--------------------------------------------------------------------------------------

function has_bom($item)
{
    $result = get_bom($item);
    
    return (db_num_rows($result) != 0);
}

//--------------------------------------------------------------------------------------

function is_component_already_on_bom($component, $workcentre_added, $loc_code, $selected_parent)
{
	$sql = "SELECT component
		FROM ".TB_PREF."bom
		WHERE parent=".db_escape($selected_parent)."
		AND component=".db_escape($component) . "
		AND workcentre_added=".db_escape($workcentre_added) . "
		AND loc_code=".db_escape($loc_code);
	$result = db_query($sql,"check failed");

	return (db_num_rows($result) > 0);
}
function is_component_already_on_sys($component, $workcentre_added, $loc_code, $selected_parent)
{
	$sql = "SELECT component
		FROM ".TB_PREF."sys_group
		WHERE group_name=".db_escape($selected_parent)."
		AND component=".db_escape($component) . "
		AND work_center=".db_escape($workcentre_added) . "
		AND loc_code=".db_escape($loc_code);
	$result = db_query($sql,"check failed");

	return (db_num_rows($result) > 0);
}

//--------------------------------------------------------------------------------------

function check_for_recursive_bom($ultimate_parent, $component_to_check)
{

	/* returns true ie 1 if the bom contains the parent part as a component
	ie the bom is recursive otherwise false ie 0 */

	$sql = "SELECT component FROM ".TB_PREF."bom WHERE parent=".db_escape($component_to_check);
	$result = db_query($sql,"could not check recursive bom");

	if ($result)
	{
		while ($myrow = db_fetch_row($result))
		{
			if ($myrow[0] == $ultimate_parent)
			{
				return 1;
			}

			if (check_for_recursive_bom($ultimate_parent, $myrow[0]))
			{
				return 1;
			}
		}
	}

	return 0;
}

function check_for_recursive_grp($ultimate_parent, $component_to_check)
{

	/* returns true ie 1 if the bom contains the parent part as a component
	ie the bom is recursive otherwise false ie 0 */

	$sql = "SELECT component FROM ".TB_PREF."sys_group WHERE component=".db_escape($component_to_check);
	$result = db_query($sql,"could not check recursive bom");

	if ($result)
	{
		while ($myrow = db_fetch_row($result))
		{
			if ($myrow[0] == $ultimate_parent)
			{
				return 1;
			}

			if (check_for_recursive_grp($ultimate_parent, $myrow[0]))
			{
				return 1;
			}
		}
	}

	return 0;
}
