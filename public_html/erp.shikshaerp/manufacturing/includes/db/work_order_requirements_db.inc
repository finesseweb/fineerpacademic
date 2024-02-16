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
function get_wo_requirements($woid)
{
	$sql = "SELECT req.*, item.description, item.mb_flag, loc.location_name, 
			center.name AS WorkCentreDescription,
			item.material_cost as ComponentCost,
			item.inventory_account,
			item.cogs_account
		FROM (".TB_PREF."wo_requirements req,"
			.TB_PREF."locations loc,"
			.TB_PREF."workcentres center)
			INNER JOIN ".TB_PREF."stock_master item ON req.stock_id=item.stock_id 
		WHERE workorder_id=".db_escape($woid)."
		AND loc.loc_code = req.loc_code
		AND center.id=workcentre";

	return db_query($sql, "The work order requirements could not be retrieved");    
}

function get_building_requirements($woid)
{
	$sql = "SELECT req.*,asset.qty_issued as units_issued,asset.qty_issued as units_req,asset.sl_no ,item.description, item.mb_flag, loc.location_name, 
			center.name AS WorkCentreDescription,
			item.material_cost as ComponentCost,
			item.inventory_account,
			item.cogs_account
		FROM (fa_building_issues  req,fa_asset_issue_items asset ,fa_locations loc,fa_workcentres center)
			INNER JOIN fa_stock_master item ON asset.stock_id=item.stock_id 
		WHERE req.workorder_id=$woid
        and req.issue_no = asset.issue_id
		AND loc.loc_code = req.loc_code
		AND center.id=workcentre_id";
	return db_query($sql, "The work order requirements could not be retrieved");    
}
function get_floor_requirements($woid=null)
{
     
   
	$sql = "SELECT req.*,asset.qty_issued as units_issued,asset.qty_issued as units_req,asset.sl_no ,item.description, item.mb_flag, loc.location_name, 
			center.name AS WorkCentreDescription,
			item.material_cost as ComponentCost,
			item.inventory_account,
			item.cogs_account
		FROM (fa_floor_issues  req,fa_floor_issue_items asset ,fa_locations loc,fa_workcentres center)
			INNER JOIN fa_stock_master item ON asset.stock_id=item.stock_id 
		WHERE req.workorder_id=$woid
        and req.issue_no = asset.issue_id
		AND loc.loc_code = req.loc_code
		AND center.id=workcentre_id";
       //display_error($sql); 
	return db_query($sql, "The work order requirements could not be retrieved");    
}

function get_room_requirements($woid)
{
	$sql = "SELECT req.*,asset.qty_issued as units_issued,asset.qty_issued as units_req,asset.sl_no ,item.description, item.mb_flag, loc.location_name, 
			center.name AS WorkCentreDescription,
			item.material_cost as ComponentCost,
			item.inventory_account,
			item.cogs_account
		FROM (fa_room_issues  req,fa_room_issue_items asset ,fa_locations loc,fa_workcentres center)
			INNER JOIN fa_stock_master item ON asset.stock_id=item.stock_id 
		WHERE req.workorder_id=$woid
        and req.issue_no = asset.issue_id
		AND loc.loc_code = req.loc_code
		AND center.id=workcentre_id";
	return db_query($sql, "The work order requirements could not be retrieved");    
}

function get_dept_requirements($woid)
{
	$sql = "SELECT req.*,asset.qty_issued as units_issued,asset.qty_issued as units_req,asset.sl_no ,item.description, item.mb_flag, loc.location_name, 
			center.name AS WorkCentreDescription,
			item.material_cost as ComponentCost,
			item.inventory_account,
			item.cogs_account
		FROM (fa_dept_issues req,fa_dept_issue_items asset,fa_locations loc,fa_workcentres center)
			INNER JOIN fa_stock_master item ON asset.stock_id=item.stock_id 
		WHERE req.workorder_id=$woid
        and req.issue_no = asset.issue_id
		AND loc.loc_code = req.loc_code
		AND center.id=workcentre_id";
        //display_error($sql);
	return db_query($sql, "The work order requirements could not be retrieved");    
}

function get_seat_requirements($woid)
{
	$sql = "SELECT req.*,asset.qty_issued as units_issued,asset.qty_issued as units_req,asset.sl_no ,item.description, item.mb_flag, loc.location_name, 
			center.name AS WorkCentreDescription,
			item.material_cost as ComponentCost,
			item.inventory_account,
			item.cogs_account
		FROM (fa_seat_issues req,fa_seat_issue_items asset,fa_locations loc,fa_workcentres center)
			INNER JOIN fa_stock_master item ON asset.stock_id=item.stock_id 
		WHERE req.workorder_id=$woid
        and req.issue_no = asset.issue_id
		AND loc.loc_code = req.loc_code
		AND center.id=workcentre_id";
	return db_query($sql, "The work order requirements could not be retrieved");    
}

function get_item_requirements($woid,$quantity='')
{
     //display_error($woid);
//     if($quantity==1){
//         $sql = "SELECT req.*,asset.qty_issued as units_issued,asset.qty_issued as units_req,asset.sl_no ,item.description, item.mb_flag, loc.location_name, 
//			center.name AS WorkCentreDescription,
//			item.material_cost as ComponentCost,
//			item.inventory_account,
//			item.cogs_account,seat.seat_no,building.name,floor.code,room.room_no,dept.description
//		FROM (fa_seat_issues req,fa_seat_issue_items asset,fa_locations loc,fa_workcentres center)
//			INNER JOIN fa_stock_master item ON asset.stock_id=item.stock_id 
//                        INNER JOIN fa_seat_allocation seat ON seat.id=req.workorder_id 
//                        INNER JOIN fa_seat_master seatm ON seatm.id=seat.master_id
//                        INNER JOIN fa_product_master building ON building.id=seatm.building
//                        INNER JOIN fa_asset_master floor ON floor.asset_id=seatm.floor
//                        INNER JOIN fa_room_main room ON room.id=seatm.room
//                        INNER JOIN fa_kv_departments dept ON dept.id=seatm.department
//		WHERE asset.stock_id='$woid'
//        and req.issue_no = asset.issue_id
//		AND loc.loc_code = req.loc_code
//		AND center.id=workcentre_id";
//     }
//      if($quantity==2){
//         $sql = "SELECT req.*,asset.qty_issued as units_issued,asset.qty_issued as units_req,asset.sl_no ,item.description, item.mb_flag, loc.location_name, 
//			center.name AS WorkCentreDescription,
//			item.material_cost as ComponentCost,
//			item.inventory_account,
//			item.cogs_account,seat.seat_no,building.name,floor.code,room.room_no,dept.description
//		FROM (fa_seat_issues req,fa_seat_issue_items asset,fa_locations loc,fa_workcentres center)
//			INNER JOIN fa_stock_master item ON asset.stock_id=item.stock_id 
//                        INNER JOIN fa_seat_allocation seat ON seat.id=req.workorder_id 
//                        INNER JOIN fa_seat_master seatm ON seatm.id=seat.master_id
//                        INNER JOIN fa_product_master building ON building.id=seatm.building
//                        INNER JOIN fa_asset_master floor ON floor.asset_id=seatm.floor
//                        INNER JOIN fa_room_main room ON room.id=seatm.room
//                        INNER JOIN fa_kv_departments dept ON dept.id=seatm.department
//		WHERE asset.stock_id='$woid'
//        and req.issue_no = asset.issue_id
//		AND loc.loc_code = req.loc_code
//		AND center.id=workcentre_id";
//     }
//      if($quantity==3){
//         $sql = "SELECT req.*,asset.qty_issued as units_issued,asset.qty_issued as units_req,asset.sl_no ,item.description, item.mb_flag, loc.location_name, 
//			center.name AS WorkCentreDescription,
//			item.material_cost as ComponentCost,
//			item.inventory_account,
//			item.cogs_account,seat.seat_no,building.name,floor.code,room.room_no,dept.description
//		FROM (fa_seat_issues req,fa_seat_issue_items asset,fa_locations loc,fa_workcentres center)
//			INNER JOIN fa_stock_master item ON asset.stock_id=item.stock_id 
//                        INNER JOIN fa_seat_allocation seat ON seat.id=req.workorder_id 
//                        INNER JOIN fa_seat_master seatm ON seatm.id=seat.master_id
//                        INNER JOIN fa_product_master building ON building.id=seatm.building
//                        INNER JOIN fa_asset_master floor ON floor.asset_id=seatm.floor
//                        INNER JOIN fa_room_main room ON room.id=seatm.room
//                        INNER JOIN fa_kv_departments dept ON dept.id=seatm.department
//		WHERE asset.stock_id='$woid'
//        and req.issue_no = asset.issue_id
//		AND loc.loc_code = req.loc_code
//		AND center.id=workcentre_id";
//     }
//     if($quantity==4){
//        $sql = "SELECT req.*,asset.qty_issued as units_issued,asset.qty_issued as units_req,asset.sl_no ,item.description, item.mb_flag, loc.location_name, 
//			center.name AS WorkCentreDescription,
//			item.material_cost as ComponentCost,
//			item.inventory_account,
//			item.cogs_account,building.name,floor.code,room.room_no,dept.description
//		FROM (fa_dept_issues req,fa_dept_issue_items asset,fa_locations loc,fa_workcentres center)
//			INNER JOIN fa_stock_master item ON asset.stock_id=item.stock_id 
//                        INNER JOIN fa_department_allocation seat ON seat.id=req.workorder_id 
//                        INNER JOIN fa_department_master seatm ON seatm.id=seat.master_id
//                        INNER JOIN fa_product_master building ON building.id=seatm.building
//                        INNER JOIN fa_asset_master floor ON floor.asset_id=seatm.floor
//                        INNER JOIN fa_room_main room ON room.id=seatm.room
//                        INNER JOIN fa_kv_departments dept ON dept.id=req.workorder_id 
//		WHERE asset.stock_id='$woid'
//        and req.issue_no = asset.issue_id
//		AND loc.loc_code = req.loc_code
//		AND center.id=workcentre_id"; 
//        
//        display_error($sql);
//     }
//     else {
	$sql = "(SELECT buil.name,floor.code,room.room_no,deptall.department_id,seatall.seat_no,seat.sl_no,seatissue.issue_date,seat.stock_id from fa_seat_issue_items as seat 
                    INNER JOIN fa_seat_issues seatissue ON seatissue.issue_no=seat.issue_id 
                    INNER JOIN fa_seat_allocation seatall ON seatall.id=seatissue.workorder_id
                    INNER JOIN fa_seat_master seatm ON seatm.id=seatall.master_id
                    INNER JOIN fa_department_allocation deptall ON deptall.id=seatm.department 
                    INNER JOIN fa_room_main room ON room.id=seatm.room
                    INNER JOIN fa_asset_master floor ON floor.asset_id=seatm.floor
                    INNER JOIN fa_product_master buil ON buil.id=seatm.building
                   WHERE seat.stock_id='$woid' order by sl_no asc)
                UNION 
                (SELECT buil.name,floor.code,room.room_no,deptall.department_id,dept.seat_no,dept.sl_no,deptissue.issue_date,dept.stock_id from fa_dept_issue_items as dept 
                INNER JOIN fa_dept_issues deptissue ON deptissue.issue_no=dept.issue_id 
                INNER JOIN fa_department_allocation deptall ON deptall.id=deptissue.workorder_id 
                INNER JOIN fa_department_master deptm ON deptm.id=deptall.master_id 
                INNER JOIN fa_room_main room ON room.id=deptm.room
                INNER JOIN fa_asset_master floor ON floor.asset_id=deptm.floor
                INNER JOIN fa_product_master buil ON buil.id=deptm.building
                WHERE dept.stock_id='$woid' order by sl_no asc)
                UNION 
                (SELECT buil.name,asstm.code,rm.room_no,ritem.department_id,ritem.seat_no,ritem.sl_no,rissue.issue_date,ritem.stock_id from fa_room_issue_items as ritem 
                INNER JOIN fa_room_issues rissue ON rissue.issue_no=ritem.issue_id 
                INNER JOIN fa_room_main rm ON rm.id=rissue.workorder_id 
                INNER JOIN fa_asset_master asstm ON asstm.asset_id=rm.asset_id 
                INNER JOIN fa_product_master buil ON buil.id=asstm.product_id
                
                WHERE ritem.stock_id='$woid' order by sl_no asc)
        
           UNION 
                (SELECT buil.name,assm.code,fitem.room_no,fitem.department_id,fitem.seat_no,fitem.sl_no, fissue.issue_date,fitem.stock_id from fa_floor_issue_items as fitem 
                INNER JOIN fa_floor_issues fissue ON fissue.issue_no=fitem.issue_id 
                INNER JOIN fa_asset_master assm ON assm.asset_id=fissue.workorder_id 
                INNER JOIN fa_product_master buil ON buil.id=assm.product_id
                
                WHERE fitem.stock_id='$woid' order by sl_no asc)
        UNION 
                (SELECT buil.name,bitem.code,bitem.room_no,bitem.department_id,bitem.seat_no,bitem.sl_no,bissue.issue_date,bitem.stock_id from fa_asset_issue_items as bitem 
                INNER JOIN fa_building_issues bissue ON bissue.issue_no=bitem.issue_id 
                INNER JOIN fa_product_master buil ON buil.id=bissue.workorder_id
                
                WHERE bitem.stock_id='$woid' order by sl_no asc)";
        
        
     //}
	return db_query($sql, "The work order requirements could not be retrieved");    
}

function get_user_requirements($woid)
{
	$sql = "SELECT * FROM fa_user_issues join fa_kv_departments on fa_kv_departments.id=fa_user_issues.department_id join fa_kv_empl_info on fa_kv_empl_info.empl_id=fa_user_issues.employee_id WHERE seat=$woid";
	return db_query($sql, "The work order requirements could not be retrieved");    
}

function get_item_details($woid)
{
	$sql = "SELECT * FROM fa_seat_issues join fa_seat_issue_items on fa_seat_issue_items.issue_id=fa_seat_issues.issue_no WHERE workorder_id=$woid";
	return db_query($sql, "The work order requirements could not be retrieved");    
}
function get_users_requirements($woid)
{
	$sql = "SELECT empl.empl_firstname,empl.empl_middlename,buil.name,asset.code,rm.room_no,dept.department_id,dept.department_id as dept_id,sll.seat_no,sll.id  FROM fa_user_issues as users
                join fa_department_allocation dept on dept.id=users.department 
                join fa_kv_empl_info on fa_kv_empl_info.empl_id=users.employee_id
                join fa_seat_allocation sll ON sll.id=users.seat 
                join fa_room_main rm ON rm.id=users.room 
                join fa_asset_master asset ON asset.asset_id=users.floor 
                join fa_product_master buil ON buil.id=users.name 
                join fa_kv_empl_info empl ON empl.empl_id=users.employee_id 
                WHERE users.employee_id='$woid'";
	return db_query($sql, "The work order requirements could not be retrieved");    
}
function get_requirements_costs($woid)
{
	$sql = "SELECT SUM(units_req*unit_cost) cost
		FROM ".TB_PREF."wo_requirements
		WHERE workorder_id=".db_escape($woid);

	$result = db_query($sql, "The work order requirements costs could not be retrieved");
	$costs = db_fetch($result);

	return $costs['cost'] ? $costs['cost'] : 0;
}

//--------------------------------------------------------------------------------------

function create_wo_requirements($woid, $stock_id)
{
	$sql = "INSERT INTO ".TB_PREF."wo_requirements (workorder_id, stock_id, workcentre, units_req, loc_code)
		SELECT ".db_escape($woid).", component, workcentre_added, quantity, loc_code
		FROM ".TB_PREF."bom WHERE parent=".db_escape($stock_id);

    db_query($sql, "The work order requirements could not be added");
}

//--------------------------------------------------------------------------------------

function delete_wo_requirements($woid)
{
	$sql="DELETE FROM ".TB_PREF."wo_requirements WHERE workorder_id=".db_escape($woid);
	db_query($sql,"The work order requirements could not be deleted");	
}


//--------------------------------------------------------------------------------------
/*
	Update total component units issued and average component cost
*/
function update_wo_requirement_issued($id, $quantity, $cost)
{
	$sql = "UPDATE ".TB_PREF."wo_requirements SET 
				unit_cost = (units_issued*unit_cost+".$quantity*$cost.")/(units_issued+".$quantity."),
				units_issued = units_issued + ".db_escape($quantity)."
			WHERE id = ".db_escape($id);

	db_query($sql, "The work requirements issued quantity couldn't be updated");
}

//--------------------------------------------------------------------------------------

function void_wo_requirements($woid)
{
	$sql = "UPDATE ".TB_PREF."wo_requirements SET units_issued = 0
		WHERE workorder_id = ".db_escape($woid);

	db_query($sql, "The work requirements issued quantity couldn't be voided");	
}

function get_item_returns($woid)
{
     //display_error($woid);
//     if($quantity==1){
//         $sql = "SELECT req.*,asset.qty_issued as units_issued,asset.qty_issued as units_req,asset.sl_no ,item.description, item.mb_flag, loc.location_name, 
//			center.name AS WorkCentreDescription,
//			item.material_cost as ComponentCost,
//			item.inventory_account,
//			item.cogs_account,seat.seat_no,building.name,floor.code,room.room_no,dept.description
//		FROM (fa_seat_issues req,fa_seat_issue_items asset,fa_locations loc,fa_workcentres center)
//			INNER JOIN fa_stock_master item ON asset.stock_id=item.stock_id 
//                        INNER JOIN fa_seat_allocation seat ON seat.id=req.workorder_id 
//                        INNER JOIN fa_seat_master seatm ON seatm.id=seat.master_id
//                        INNER JOIN fa_product_master building ON building.id=seatm.building
//                        INNER JOIN fa_asset_master floor ON floor.asset_id=seatm.floor
//                        INNER JOIN fa_room_main room ON room.id=seatm.room
//                        INNER JOIN fa_kv_departments dept ON dept.id=seatm.department
//		WHERE asset.stock_id='$woid'
//        and req.issue_no = asset.issue_id
//		AND loc.loc_code = req.loc_code
//		AND center.id=workcentre_id";
//     }
//      if($quantity==2){
//         $sql = "SELECT req.*,asset.qty_issued as units_issued,asset.qty_issued as units_req,asset.sl_no ,item.description, item.mb_flag, loc.location_name, 
//			center.name AS WorkCentreDescription,
//			item.material_cost as ComponentCost,
//			item.inventory_account,
//			item.cogs_account,seat.seat_no,building.name,floor.code,room.room_no,dept.description
//		FROM (fa_seat_issues req,fa_seat_issue_items asset,fa_locations loc,fa_workcentres center)
//			INNER JOIN fa_stock_master item ON asset.stock_id=item.stock_id 
//                        INNER JOIN fa_seat_allocation seat ON seat.id=req.workorder_id 
//                        INNER JOIN fa_seat_master seatm ON seatm.id=seat.master_id
//                        INNER JOIN fa_product_master building ON building.id=seatm.building
//                        INNER JOIN fa_asset_master floor ON floor.asset_id=seatm.floor
//                        INNER JOIN fa_room_main room ON room.id=seatm.room
//                        INNER JOIN fa_kv_departments dept ON dept.id=seatm.department
//		WHERE asset.stock_id='$woid'
//        and req.issue_no = asset.issue_id
//		AND loc.loc_code = req.loc_code
//		AND center.id=workcentre_id";
//     }
//      if($quantity==3){
//         $sql = "SELECT req.*,asset.qty_issued as units_issued,asset.qty_issued as units_req,asset.sl_no ,item.description, item.mb_flag, loc.location_name, 
//			center.name AS WorkCentreDescription,
//			item.material_cost as ComponentCost,
//			item.inventory_account,
//			item.cogs_account,seat.seat_no,building.name,floor.code,room.room_no,dept.description
//		FROM (fa_seat_issues req,fa_seat_issue_items asset,fa_locations loc,fa_workcentres center)
//			INNER JOIN fa_stock_master item ON asset.stock_id=item.stock_id 
//                        INNER JOIN fa_seat_allocation seat ON seat.id=req.workorder_id 
//                        INNER JOIN fa_seat_master seatm ON seatm.id=seat.master_id
//                        INNER JOIN fa_product_master building ON building.id=seatm.building
//                        INNER JOIN fa_asset_master floor ON floor.asset_id=seatm.floor
//                        INNER JOIN fa_room_main room ON room.id=seatm.room
//                        INNER JOIN fa_kv_departments dept ON dept.id=seatm.department
//		WHERE asset.stock_id='$woid'
//        and req.issue_no = asset.issue_id
//		AND loc.loc_code = req.loc_code
//		AND center.id=workcentre_id";
//     }
//     if($quantity==4){
//        $sql = "SELECT req.*,asset.qty_issued as units_issued,asset.qty_issued as units_req,asset.sl_no ,item.description, item.mb_flag, loc.location_name, 
//			center.name AS WorkCentreDescription,
//			item.material_cost as ComponentCost,
//			item.inventory_account,
//			item.cogs_account,building.name,floor.code,room.room_no,dept.description
//		FROM (fa_dept_issues req,fa_dept_issue_items asset,fa_locations loc,fa_workcentres center)
//			INNER JOIN fa_stock_master item ON asset.stock_id=item.stock_id 
//                        INNER JOIN fa_department_allocation seat ON seat.id=req.workorder_id 
//                        INNER JOIN fa_department_master seatm ON seatm.id=seat.master_id
//                        INNER JOIN fa_product_master building ON building.id=seatm.building
//                        INNER JOIN fa_asset_master floor ON floor.asset_id=seatm.floor
//                        INNER JOIN fa_room_main room ON room.id=seatm.room
//                        INNER JOIN fa_kv_departments dept ON dept.id=req.workorder_id 
//		WHERE asset.stock_id='$woid'
//        and req.issue_no = asset.issue_id
//		AND loc.loc_code = req.loc_code
//		AND center.id=workcentre_id"; 
//        
//        display_error($sql);
//     }
//     else {
	$sql = "(SELECT buil.name,buil.id as build,floor.asset_id,floor.code,room.id as rid,room.room_no,deptall.department_id,deptall.id as did,seatm.id as sid,seatall.seat_no,seat.sl_no,seatissue.issue_date,seat.stock_id,seatissue.loc_code from fa_seat_issue_items as seat 
                    INNER JOIN fa_seat_issues seatissue ON seatissue.issue_no=seat.issue_id 
                    INNER JOIN fa_seat_allocation seatall ON seatall.id=seatissue.workorder_id
                    INNER JOIN fa_seat_master seatm ON seatm.id=seatall.master_id
                    INNER JOIN fa_department_allocation deptall ON deptall.id=seatm.department 
                    INNER JOIN fa_room_main room ON room.id=seatm.room
                    INNER JOIN fa_asset_master floor ON floor.asset_id=seatm.floor
                    INNER JOIN fa_product_master buil ON buil.id=seatm.building
                   WHERE seat.sl_no='$woid' order by sl_no asc)
                UNION 
                (SELECT buil.name,buil.id as build,floor.asset_id,floor.code,room.id as rid,room.room_no,deptall.department_id,deptall.id as did,dept.seat_id as sid,dept.seat_no,dept.sl_no,deptissue.issue_date,dept.stock_id,deptissue.loc_code from fa_dept_issue_items as dept
                INNER JOIN fa_dept_issues deptissue ON deptissue.issue_no=dept.issue_id 
                INNER JOIN fa_department_allocation deptall ON deptall.id=deptissue.workorder_id 
                INNER JOIN fa_department_master deptm ON deptm.id=deptall.master_id 
                INNER JOIN fa_room_main room ON room.id=deptm.room
                INNER JOIN fa_asset_master floor ON floor.asset_id=deptm.floor
                INNER JOIN fa_product_master buil ON buil.id=deptm.building
                WHERE dept.sl_no='$woid' order by sl_no asc)
                UNION 
                (SELECT buil.name,buil.id as build,floor.asset_id,floor.code,rm.id as rid,rm.room_no,ritem.department_id,ritem.department_id as did,ritem.seat_no,ritem.seat_no as sid,ritem.sl_no,rissue.issue_date,ritem.stock_id,rissue.loc_code from fa_room_issue_items as ritem 
                INNER JOIN fa_room_issues rissue ON rissue.issue_no=ritem.issue_id 
                INNER JOIN fa_room_main rm ON rm.id=rissue.workorder_id 
                INNER JOIN fa_asset_master floor ON floor.asset_id=rm.asset_id 
                INNER JOIN fa_product_master buil ON buil.id=floor.product_id
                
                WHERE ritem.sl_no='$woid' order by sl_no asc)
        
           UNION 
                (SELECT buil.name,buil.id as build,floor.asset_id,floor.code,fitem.room_no,fitem.room_id as rid,fitem.department_id,fitem.department_id as did,fitem.seat_no,fitem.seat_no as sid,fitem.sl_no, fissue.issue_date,fitem.stock_id,fissue.loc_code from fa_floor_issue_items as fitem 
                INNER JOIN fa_floor_issues fissue ON fissue.issue_no=fitem.issue_id 
                INNER JOIN fa_asset_master floor ON floor.asset_id=fissue.workorder_id 
                INNER JOIN fa_product_master buil ON buil.id=floor.product_id
                
                WHERE fitem.sl_no='$woid' order by sl_no asc)
        UNION 
                (SELECT buil.name,buil.id as build,bitem.asset_id,bitem.code,bitem.room_no,bitem.room_no as rid,bitem.department_id,bitem.department_id as did,bitem.seat_no,bitem.seat_no as sid,bitem.sl_no,bissue.issue_date,bitem.stock_id,bissue.loc_code from fa_asset_issue_items as bitem 
                INNER JOIN fa_building_issues bissue ON bissue.issue_no=bitem.issue_id 
                INNER JOIN fa_product_master buil ON buil.id=bissue.workorder_id
                
                WHERE bitem.sl_no='$woid' order by sl_no asc)";
        
        
     //}
	return db_query($sql, "The work order requirements could not be retrieved");    
}