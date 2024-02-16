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
include_once($path_to_root . "/includes/ui.inc");

//--------------------------------------------------------------------------------------

function display_bom($item_check)
{
    $result = get_bom($item_check);

    if (db_num_rows($result) == 0)
    {
    	display_note(_("The bill of material for this item is empty."), 0, 1);
    }
    else
    {

        start_table(TABLESTYLE);
        $th = array(_("Component"), _("Description"), _("Work Centre"),
        	_("From Location"), _("Quantity"), _("Unit Cost"), _("Cost"));

        table_header($th);

        $j = 1;
        $k = 0; //row colour counter

        $total_cost = 0;

        while ($myrow = db_fetch($result))
        {

			alt_table_row_color($k);

			label_cell($myrow["component"]);
			label_cell($myrow["description"]);
			label_cell($myrow["WorkCentreDescription"]);
			label_cell($myrow["location_name"]);
			qty_cell($myrow["quantity"], false, get_qty_dec($myrow["component"]));
			amount_cell($myrow["ProductCost"]);
			amount_cell($myrow["ComponentCost"]);
			end_row();
        	$total_cost += $myrow["ComponentCost"];

        	$j++;
        	if ($j == 12)
        	{
        		$j = 1;
        		table_header($th);
        	}
		}

		$item = get_item($item_check);
		if ($item['labour_cost'] != 0)
		{
			$total_cost += $item['labour_cost'];
			alt_table_row_color($k);
	        label_cells(_("Standard Labour Cost"), number_format2($item['labour_cost'],user_price_dec()),
    	    	"colspan=6 align=left", "class=odd_row nowrap align=right");
    	    end_row();
		}
		if ($item['overhead_cost'] != 0)
		{
			$total_cost += $item['overhead_cost'];
			alt_table_row_color($k);
	        label_cells(_("Standard Overhead Cost"), number_format2($item['overhead_cost'],user_price_dec()),
    	    	"colspan=6 align=left", "nowrap align=right");
    	    end_row();
		}
        label_row("<b>" . _("Total Cost") . "</b>", "<b>" . number_format2($total_cost,user_price_dec()) ."</b>",
        	"colspan=6 align=right", "nowrap align=right");
		end_table();
    }
}



//=======================

function display_building_requirements($woid, $quantity, $show_qoh=false, $date=null)
{
	global $SysPrefs;

    $result = get_building_requirements($woid);
    

    if (db_num_rows($result) == 0)
    {
    	display_note(_("There are no Issue Items for this Building."), 1, 0);
    }
    else
    {

        start_table(TABLESTYLE, "width='80%'");
        $th = array(_("Component"), _("From Location"), _("Work Centre"), _("Serial No."), _("Issue Date."));

        table_header($th);

        $k = 0; //row colour counter
		$has_marked = false;

		if ($date == null)
			$date = Today();

        while ($myrow = db_fetch($result))
        {

			$qoh = 0;
			$show_qoh = true;
			// if it's a non-stock item (eg. service) don't show qoh
			if (!has_stock_holding($myrow["mb_flag"]))
				$show_qoh = false;

			if ($show_qoh)
				$qoh = get_qoh_on_date($myrow["stock_id"], $myrow["loc_code"], $date);

			if ($show_qoh && ($myrow["units_req"] * $quantity > $qoh) &&
				!$SysPrefs->allow_negative_stock())
			{
				// oops, we don't have enough of one of the component items
				start_row("class='stockmankobg'");
				$has_marked = true;
			}
			else
				alt_table_row_color($k);

        	if (user_show_codes())
        		label_cell($myrow["stock_id"]. " - " . $myrow["description"]);
        	else
        		label_cell($myrow["description"]);

			label_cell($myrow["location_name"]);
			label_cell($myrow["WorkCentreDescription"]);
			label_cell($myrow["sl_no"]);
                        label_cell(date('d-m-Y',strtotime($myrow["issue_date"])));
			end_row();

		}

		end_table();

		if ($has_marked)
			display_note(_("Marked items have insufficient quantities in stock."), 0, 0, "class='red'");
    }
}



//=====================

//=======================

function display_floor_requirements($woid,$quantity=null, $show_qoh=false, $date=null)
{
  
	global $SysPrefs,$Ajax;
    $result = get_floor_requirements($woid);
    

    if (db_num_rows($result) == 0)
    {
    	display_note(_("There are no Issue Items."), 1, 0);
    }
   else
    {
        start_table(TABLESTYLE, "width='80%'");
        $th = array(_("Component"), _("From Location"), _("Work Centre"), _("Serial No."), _("Issue Date."));

        table_header($th);

        $k = 0; //row colour counter
		$has_marked = false;

		if ($date == null)
			$date = Today();

        while ($myrow = db_fetch($result))
        {
			$qoh = 0;
			$show_qoh = true;
			// if it's a non-stock item (eg. service) don't show qoh
			if (!has_stock_holding($myrow["mb_flag"]))
				$show_qoh = false;

			if ($show_qoh)
				$qoh = get_qoh_on_date($myrow["stock_id"], $myrow["loc_code"], $date);

			if ($show_qoh && ($myrow["units_req"] * $quantity > $qoh) &&
				!$SysPrefs->allow_negative_stock())
			{
				// oops, we don't have enough of one of the component items
				start_row("class='stockmankobg'");
				$has_marked = true;
			}
			else
				alt_table_row_color($k);

        	if (user_show_codes())
        		label_cell($myrow["stock_id"]. " - " . $myrow["description"]);
        	else
        		label_cell($myrow["description"]);

			label_cell($myrow["location_name"]);
			label_cell($myrow["WorkCentreDescription"]);
			label_cell($myrow["sl_no"]);
                        label_cell(date('d-m-Y',strtotime($myrow["issue_date"])));
			end_row();

		}

		end_table();
		if ($has_marked)
			display_note(_("Marked items have insufficient quantities in stock."), 0, 0, "class='red'");
    }
}


function display_room_requirements($woid,$quantity, $show_qoh=false, $date=null)
{
	global $SysPrefs,$Ajax;
    $result = get_room_requirements($woid);
    

    if (db_num_rows($result) == 0)
    {
    	display_note(_("There are no Issue Items."), 1, 0);
    }
    else
    {
        start_table(TABLESTYLE, "width='80%'");
        $th = array(_("Component"), _("From Location"), _("Work Centre"), _("Serial No."), _("Issue Date."));

        table_header($th);

        $k = 0; //row colour counter
		$has_marked = false;

		if ($date == null)
			$date = Today();

        while ($myrow = db_fetch($result))
        {
			$qoh = 0;
			$show_qoh = true;
			// if it's a non-stock item (eg. service) don't show qoh
			if (!has_stock_holding($myrow["mb_flag"]))
				$show_qoh = false;

			if ($show_qoh)
				$qoh = get_qoh_on_date($myrow["stock_id"], $myrow["loc_code"], $date);

			if ($show_qoh && ($myrow["units_req"] * $quantity > $qoh) &&
				!$SysPrefs->allow_negative_stock())
			{
				// oops, we don't have enough of one of the component items
				start_row("class='stockmankobg'");
				$has_marked = true;
			}
			else
				alt_table_row_color($k);

        	if (user_show_codes())
        		label_cell($myrow["stock_id"]. " - " . $myrow["description"]);
        	else
        		label_cell($myrow["description"]);

			label_cell($myrow["location_name"]);
			label_cell($myrow["WorkCentreDescription"]);
			label_cell($myrow["sl_no"]);
                        label_cell(date('d-m-Y',strtotime($myrow["issue_date"])));
			end_row();

		}

		end_table();
		if ($has_marked)
			display_note(_("Marked items have insufficient quantities in stock."), 0, 0, "class='red'");
    }
}

function display_dept_requirements($woid,$quantity, $show_qoh=false, $date=null)
{
	global $SysPrefs,$Ajax;
    $result = get_dept_requirements($woid);
    

    if (db_num_rows($result) == 0)
    {
    	display_note(_("There are no Issue Items."), 1, 0);
    }
    else
    {
        start_table(TABLESTYLE, "width='80%'");
        $th = array(_("Component"), _("From Location"), _("Work Centre"), _("Serial No."), _("Serial No."));

        table_header($th);

        $k = 0; //row colour counter
		$has_marked = false;

		if ($date == null)
			$date = Today();

        while ($myrow = db_fetch($result))
        {
			$qoh = 0;
			$show_qoh = true;
			// if it's a non-stock item (eg. service) don't show qoh
			if (!has_stock_holding($myrow["mb_flag"]))
				$show_qoh = false;

			if ($show_qoh)
				$qoh = get_qoh_on_date($myrow["stock_id"], $myrow["loc_code"], $date);

			if ($show_qoh && ($myrow["units_req"] * $quantity > $qoh) &&
				!$SysPrefs->allow_negative_stock())
			{
				// oops, we don't have enough of one of the component items
				start_row("class='stockmankobg'");
				$has_marked = true;
			}
			else
				alt_table_row_color($k);

        	if (user_show_codes())
        		label_cell($myrow["stock_id"]. " - " . $myrow["description"]);
        	else
        		label_cell($myrow["description"]);

			label_cell($myrow["location_name"]);
			label_cell($myrow["WorkCentreDescription"]);
			label_cell($myrow["sl_no"]);
                        label_cell($myrow["issue_date"]);
			end_row();

		}

		end_table();
		if ($has_marked)
			display_note(_("Marked items have insufficient quantities in stock."), 0, 0, "class='red'");
    }
}
function display_seat_requirements($woid,$quantity, $show_qoh=false, $date=null)
{
	global $SysPrefs,$Ajax;
    $result = get_seat_requirements($woid);
    

    if (db_num_rows($result) == 0)
    {
    	display_note(_("There are no Issue Items."), 1, 0);
    }
    else
    {
        start_table(TABLESTYLE, "width='80%'");
        $th = array(_("Component"), _("From Location"), _("Work Centre"), _("Serial No."),_("Issue Date."));

        table_header($th);

        $k = 0; //row colour counter
		$has_marked = false;

		if ($date == null)
			$date = Today();

        while ($myrow = db_fetch($result))
        {
			$qoh = 0;
			$show_qoh = true;
			// if it's a non-stock item (eg. service) don't show qoh
			if (!has_stock_holding($myrow["mb_flag"]))
				$show_qoh = false;

			if ($show_qoh)
				$qoh = get_qoh_on_date($myrow["stock_id"], $myrow["loc_code"], $date);

			if ($show_qoh && ($myrow["units_req"] * $quantity > $qoh) &&
				!$SysPrefs->allow_negative_stock())
			{
				// oops, we don't have enough of one of the component items
				start_row("class='stockmankobg'");
				$has_marked = true;
			}
			else
				alt_table_row_color($k);

        	if (user_show_codes())
        		label_cell($myrow["stock_id"]. " - " . $myrow["description"]);
        	else
        		label_cell($myrow["description"]);

			label_cell($myrow["location_name"]);
			label_cell($myrow["WorkCentreDescription"]);
			label_cell($myrow["sl_no"]);
                        label_cell(date('d-m-Y',strtotime($myrow["issue_date"])));
			end_row();

		}

		end_table();
		if ($has_marked)
			display_note(_("Marked items have insufficient quantities in stock."), 0, 0, "class='red'");
    }
}
function display_item_requirements($woid,$quantity, $show_qoh=false, $date=null)
{
	global $SysPrefs,$Ajax;
    $result = get_item_requirements($woid);
    

    if (db_num_rows($result) == 0)
    {
    	//display_note(_("There are no Issue Items."), 1, 0);
   }
    else
   {
        start_table(TABLESTYLE, "width='80%'");
        $th = array(("Serial no"),_("Building"), _("Floor"), _("Room"), _("Department"), _("Seat"), _("Issue Date"), _("#"));

        table_header($th);

        $k = 0; //row colour counter
		$has_marked = false;

		if ($date == null)
			$date = Today();
        // echo "<pre>";  print_r(db_fetch($result));
        while ($myrow = db_fetch($result))
        {
           // $redata= &$myrow;
			$qoh = 0;
			$show_qoh = true;
			// if it's a non-stock item (eg. service) don't show qoh
			if (!has_stock_holding($myrow["mb_flag"]))
				$show_qoh = false;

			if ($show_qoh)
				$qoh = get_qoh_on_date($myrow["stock_id"], $myrow["loc_code"], $date);

			if ($show_qoh && ($myrow["units_req"] * $quantity > $qoh) &&
				!$SysPrefs->allow_negative_stock())
			{
				// oops, we don't have enough of one of the component items
				start_row("class='stockmankobg'");
				$has_marked = true;
			}
			else
				alt_table_row_color($k);
                           $retrun= get_return_on_date($myrow["sl_no"], $myrow["loc_code"]);
                           //print_r($retrun['2']);
        	          if($myrow["sl_no"]!=$retrun['sl_no']) {
        		label_cell($myrow["sl_no"]);
                        label_cell($myrow["name"]);
			label_cell($myrow["code"]);
                        label_cell($myrow["room_no"]);
                        label_cell($myrow["department_id"]);
                        label_cell($myrow["seat_no"]);
                        label_cell(date('d-m-Y',strtotime($myrow["issue_date"])));
                        
                        label_cell(get_assigned_item_view_str($myrow["sl_no"]));
                          }
			end_row();
                        
		}

		end_table();
		if ($has_marked)
			display_note(_("Marked items have insufficient quantities in stock."), 0, 0, "class='red'");
    }
}

function display_item_returned($woid,$quantity, $show_qoh=false, $date=null)
{
	global $SysPrefs,$Ajax;
    $result = get_item_requirements($woid);
    

    if (db_num_rows($result) == 0)
    {
    	//display_note(_("There are no Issue Items."), 1, 0);
   }
    else
   {
        start_table(TABLESTYLE, "width='80%'");
        $th = array(("Serial no"),_("Building"), _("Floor"), _("Room"), _("Department"), _("Seat"), _("Issue Date"), _("Returned Date"));

        table_header($th);

        $k = 0; //row colour counter
		$has_marked = false;

		if ($date == null)
			$date = Today();
        // echo "<pre>";  print_r(db_fetch($result));
        while ($myrow = db_fetch($result))
        {
           // $redata= &$myrow;
			$qoh = 0;
			$show_qoh = true;
			// if it's a non-stock item (eg. service) don't show qoh
			if (!has_stock_holding($myrow["mb_flag"]))
				$show_qoh = false;

			if ($show_qoh)
				$qoh = get_qoh_on_date($myrow["stock_id"], $myrow["loc_code"], $date);

			if ($show_qoh && ($myrow["units_req"] * $quantity > $qoh) &&
				!$SysPrefs->allow_negative_stock())
			{
				// oops, we don't have enough of one of the component items
				start_row("class='stockmankobg'");
				$has_marked = true;
			}
			else
				alt_table_row_color($k);
                           $retrun= get_return_on_date($myrow["sl_no"], $myrow["loc_code"]);
                           //print_r($retrun);
        	          if($myrow["sl_no"]==$retrun['sl_no']) {
        		label_cell($myrow["sl_no"]);
                        label_cell($myrow["name"]);
			label_cell($myrow["code"]);
                        label_cell($myrow["room_no"]);
                        label_cell($myrow["department_id"]);
                        label_cell($myrow["seat_no"]);
                        label_cell(date('d-m-Y',strtotime($myrow["issue_date"])));
                        
                        label_cell(date('d-m-Y',strtotime($retrun["return_date"])));
                          }
			end_row();
                        
		}

		end_table();
		if ($has_marked)
			display_note(_("Marked items have insufficient quantities in stock."), 0, 0, "class='red'");
    }
}

function display_users_requirements($woid,$quantity, $show_qoh=false, $date=null)
{
	global $SysPrefs,$Ajax;
    $result = get_users_requirements($woid);
   // display_error($woid);

    if (db_num_rows($result) == 0)
    {
    	//display_note(_("There are no Issue Items."), 1, 0);
   }
    else
   {
        start_table(TABLESTYLE, "width='80%'");
        $th = array(("User Name"),_("Building"), _("Floor"), _("Room"), _("Department"), _("Seat"),_("#"));

        table_header($th);

        $k = 0; //row colour counter
		$has_marked = false;

		if ($date == null)
			$date = Today();
        // echo "<pre>";  print_r(db_fetch($result));
        while ($myrow = db_fetch($result))
        {
            $trans_type = $myrow["id"];
            $trans_type1 = $myrow["dept_id"];
           // $trans='ST_SEAT';
           // $trans1='ST_DEPT';
			$qoh = 0;
			$show_qoh = true;
			// if it's a non-stock item (eg. service) don't show qoh
			if (!has_stock_holding($myrow["mb_flag"]))
				$show_qoh = false;

			if ($show_qoh)
				$qoh = get_qoh_on_date($myrow["stock_id"], $myrow["loc_code"], $date);

			if ($show_qoh && ($myrow["units_req"] * $quantity > $qoh) &&
				!$SysPrefs->allow_negative_stock())
			{
				// oops, we don't have enough of one of the component items
				start_row("class='stockmankobg'");
				$has_marked = true;
			}
			else
				alt_table_row_color($k);

        	
        		label_cell($myrow["empl_firstname"].' '.$myrow["empl_middlename"]);
                        label_cell($myrow["name"]);
			label_cell($myrow["code"]);
                        label_cell($myrow["room_no"]);
                        label_cell($myrow["department_id"]);
                        //label_cell($myrow["seat_no"]);
                         label_cell(get_assigned_trans_view_str($trans_type,$myrow["seat_no"]));
                        label_cell(get_assigned_trans_view_grp($woid,'View Item'));
                       
			end_row();

		}

		end_table();
		if ($has_marked)
			display_note(_("Marked items have insufficient quantities in stock."), 0, 0, "class='red'");
    }
}
function display_user_requirements($woid,$quantity, $show_qoh=false, $date=null)
{
	global $SysPrefs,$Ajax;
    $result = get_user_requirements($woid);
    

    if (db_num_rows($result) == 0)
    {
    	display_note(_("There are no Issue Items."), 1, 0);
    }
    else
    {
        start_table(TABLESTYLE, "width='80%'");
        $th = array( _("Department"), _("User"), _("EMP No."),_("Action"),_("#"));

        table_header($th);

        $k = 0; //row colour counter
		$has_marked = false;

		if ($date == null)
			$date = Today();
$trans_type=$woid;
        while ($myrow = db_fetch($result))
        {
			$qoh = 0;
			$show_qoh = true;
			// if it's a non-stock item (eg. service) don't show qoh
			if (!has_stock_holding($myrow["mb_flag"]))
				$show_qoh = false;

			if ($show_qoh)
				$qoh = get_qoh_on_date($myrow["stock_id"], $myrow["loc_code"], $date);

			if ($show_qoh && ($myrow["units_req"] * $quantity > $qoh) &&
				!$SysPrefs->allow_negative_stock())
			{
				// oops, we don't have enough of one of the component items
				start_row("class='stockmankobg'");
				$has_marked = true;
			}
			else
				alt_table_row_color($k);


			label_cell($myrow["description"]);
			label_cell($myrow["empl_firstname"]);
			label_cell($myrow["empl_id"]);
                     
                        edit_button_cell("Edit" . $myrow['id'], _("Edit"));
                        label_cell(get_assigned_trans_view_grp($myrow["empl_id"],'View Item'));
			end_row();

		}

		end_table();
		if ($has_marked)
			display_note(_("Marked items have insufficient quantities in stock."), 0, 0, "class='red'");
    }
}

function display_group_requirements($woid,$quantity, $show_qoh=false, $date=null)
{
		global $SysPrefs,$Ajax;
    $result = get_user_assinged($woid);
    
//print_r($result);
    if (db_num_rows($result) == 0)
    {
    	display_note(_("There are no Issue Items."), 1, 0);
    }
    else
    {
        start_table(TABLESTYLE, "width='80%'");
        $th = array( _("Group Name"), _("User ID"), _("User Name."),_("Action"),_("#"));

        table_header($th);

        $k = 0; //row colour counter
		$has_marked = false;

		if ($date == null)
			$date = Today();
$trans_type=$woid;
        while ($myrow = db_fetch($result))
        {
			$qoh = 0;
			$show_qoh = true;
			// if it's a non-stock item (eg. service) don't show qoh
			if (!has_stock_holding($myrow["mb_flag"]))
				$show_qoh = false;

			if ($show_qoh)
				$qoh = get_qoh_on_date($myrow["stock_id"], $myrow["loc_code"], $date);

			if ($show_qoh && ($myrow["units_req"] * $quantity > $qoh) &&
				!$SysPrefs->allow_negative_stock())
			{
				// oops, we don't have enough of one of the component items
				start_row("class='stockmankobg'");
				$has_marked = true;
			}
			else
				alt_table_row_color($k);


			label_cell($myrow["name"]);
			label_cell($myrow["user_id"]);
			label_cell($myrow["empl_firstname"]);
            delete_button_cell("Delete".$myrow['group_id'], _("Release"));
            label_cell(get_assigned_sys_view_grp($myrow['group_id'],'View Item'));
			end_row();

		}

		end_table();
		if ($has_marked)
			display_note(_("Marked items have insufficient quantities in stock."), 0, 0, "class='red'");
    }
}
//=====================
function display_user_group($woid,$quantity, $show_qoh=false, $date=null)
{
	global $SysPrefs,$Ajax;
    $result = get_user_assing($woid);
    
//print_r($result);
    if (db_num_rows($result) == 0)
    {
    	display_note(_("There are no Issue Items."), 1, 0);
    }
    else
    {
        start_table(TABLESTYLE, "width='80%'");
        $th = array( _("Group Name"), _("User ID"), _("User Name."),_("Action"));

        table_header($th);

        $k = 0; //row colour counter
		$has_marked = false;

		if ($date == null)
			$date = Today();
$trans_type=$woid;
        while ($myrow = db_fetch($result))
        {
			$qoh = 0;
			$show_qoh = true;
			// if it's a non-stock item (eg. service) don't show qoh
			if (!has_stock_holding($myrow["mb_flag"]))
				$show_qoh = false;

			if ($show_qoh)
				$qoh = get_qoh_on_date($myrow["stock_id"], $myrow["loc_code"], $date);

			if ($show_qoh && ($myrow["units_req"] * $quantity > $qoh) &&
				!$SysPrefs->allow_negative_stock())
			{
				// oops, we don't have enough of one of the component items
				start_row("class='stockmankobg'");
				$has_marked = true;
			}
			else
				alt_table_row_color($k);


			label_cell($myrow["name"]);
			label_cell($myrow["user_id"]);
			label_cell($myrow["empl_firstname"]);
                     
                        //edit_button_cell("Edit" . $myrow['id'], _("Edit"));
                        label_cell(get_assigned_sys_view_grp($myrow['group_id'],'View Item'));
			end_row();

		}

		end_table();
		if ($has_marked)
			display_note(_("Marked items have insufficient quantities in stock."), 0, 0, "class='red'");
    }
}
//--------------------------------------------------------------------------------------

function display_wo_requirements($woid, $quantity, $show_qoh=false, $date=null)
{
	global $SysPrefs;

    $result = get_wo_requirements($woid);

    if (db_num_rows($result) == 0)
    {
    	display_note(_("There are no Issue item for the floor."), 1, 0);
    }
    else
    {

        start_table(TABLESTYLE, "width='80%'");
        $th = array(_("Component"), _("From Location"), _("Work Centre"),
        	_("Unit Quantity"), _("Total Quantity"), _("Units Issued"), _("On Hand"));

        table_header($th);

        $k = 0; //row colour counter
		$has_marked = false;

		if ($date == null)
			$date = Today();

        while ($myrow = db_fetch($result))
        {

			$qoh = 0;
			$show_qoh = true;
			// if it's a non-stock item (eg. service) don't show qoh
			if (!has_stock_holding($myrow["mb_flag"]))
				$show_qoh = false;

			if ($show_qoh)
				$qoh = get_qoh_on_date($myrow["stock_id"], $myrow["loc_code"], $date);

			if ($show_qoh && ($myrow["units_req"] * $quantity > $qoh) &&
				!$SysPrefs->allow_negative_stock())
			{
				// oops, we don't have enough of one of the component items
				start_row("class='stockmankobg'");
				$has_marked = true;
			}
			else
				alt_table_row_color($k);

        	if (user_show_codes())
        		label_cell($myrow["stock_id"]. " - " . $myrow["description"]);
        	else
        		label_cell($myrow["description"]);

			label_cell($myrow["location_name"]);
			label_cell($myrow["WorkCentreDescription"]);
			$dec = get_qty_dec($myrow["stock_id"]);
            qty_cell($myrow["units_req"], false, $dec);
			qty_cell($myrow["units_req"] * $quantity, false, $dec);
            qty_cell($myrow["units_issued"], false, $dec);
			if ($show_qoh)
				qty_cell($qoh, false, $dec);
			else
				label_cell("");
			end_row();

		}

		end_table();

		if ($has_marked)
			display_note(_("Marked items have insufficient quantities in stock."), 0, 0, "class='red'");
    }
}

//--------------------------------------------------------------------------------------

function display_wo_productions($woid)
{
	global $path_to_root;

    $result = get_work_order_productions($woid);

    if (db_num_rows($result) == 0)
    {
    	display_note(_("There are no Productions for this Order."), 1, 1);
    }
    else
    {
        start_table(TABLESTYLE);
        $th = array(_("#"), _("Reference"), _("Date"), _("Quantity"));

        table_header($th);

        $k = 0; //row colour counter
		$total_qty = 0;

        while ($myrow = db_fetch($result))
        {

			alt_table_row_color($k);

			$total_qty += $myrow['quantity'];

        	label_cell(get_trans_view_str(ST_MANURECEIVE, $myrow["id"]));
			label_cell($myrow['reference']);
			label_cell(sql2date($myrow["date_"]));
			qty_cell($myrow['quantity'], false, get_qty_dec($myrow['reference']));
			end_row();
		}//end of while

		label_row(_("Total"), number_format2($total_qty,user_qty_dec()),
			"colspan=3", "nowrap align=right");

		end_table();
    }
}

//--------------------------------------------------------------------------------------

function  display_wo_issues($woid)
{
	global $path_to_root;

    $result = get_work_order_issues($woid);

    if (db_num_rows($result) == 0)
    {
    	display_note(_("There are no Issues for this Order."), 0, 1);
    }
    else
    {
        start_table(TABLESTYLE);
        $th = array(_("#"), _("Reference"), _("Date"));

        table_header($th);

        $k = 0; //row colour counter

        while ($myrow = db_fetch($result))
        {

			alt_table_row_color($k);

        	label_cell(get_trans_view_str(ST_MANUISSUE, $myrow["issue_no"]));
			label_cell($myrow['reference']);
			label_cell(sql2date($myrow["issue_date"]));
			end_row();
		}

		end_table();
    }
}

//--------------------------------------------------------------------------------------

function display_wo_payments($woid)
{
	global $path_to_root, $wo_cost_types;

    $result = get_wo_costing($woid);

    if (db_num_rows($result) == 0)
    {
    	display_note(_("There are no additional costs for this Order."), 0, 1);
    }
    else
    {
        start_table(TABLESTYLE);
        $th = array(_("#"), _("Type"), _("Date"), _("Amount"));

        table_header($th);

        $k = 0;
        while ($myrow = db_fetch($result))
        {
			alt_table_row_color($k);

        	label_cell(get_gl_view_str( $myrow["trans_type"], $myrow["trans_no"], $myrow["trans_no"]));
        	label_cell($wo_cost_types[$myrow['cost_type']]);
        	$date = sql2date($myrow["tran_date"]);
        	label_cell($date);
			amount_cell($myrow['amount']);
			end_row();
		}

		end_table();
    }
}

//--------------------------------------------------------------------------------------

function display_wo_details($woid, $suppress_view_link=false)
{
	global $wo_types_array;

	$myrow = get_work_order($woid, true);

    if ($myrow == false)
    {
    	display_note(_("The work order number sent is not valid."));
    	exit;
    }

   	start_table(TABLESTYLE, "width='80%'");

	if ($myrow["released"] == true)
		$th = array(_("#"), _("Reference"), _("Type"), _("Manufactured Item"),
			_("Into Location"), _("Date"), _("Required By"), _("Quantity Required"),
			_("Released Date"), _("Manufactured"));
	else
		$th = array(_("#"), _("Reference"), _("Type"), _("Manufactured Item"),
			_("Into Location"), _("Date"), _("Required By"), _("Quantity Required"));

	table_header($th);
	start_row();
	if ($suppress_view_link)
		label_cell($myrow["id"]);
	else
		label_cell(get_trans_view_str(ST_WORKORDER, $myrow["id"]));
	label_cell($myrow["wo_ref"]);
	label_cell($wo_types_array[$myrow["type"]]);
	view_stock_status_cell($myrow["stock_id"], $myrow["StockItemName"]);
	label_cell($myrow["location_name"]);
	label_cell(sql2date($myrow["date_"]));
	label_cell(sql2date($myrow["required_by"]));
	$dec = get_qty_dec($myrow["stock_id"]);
	qty_cell($myrow["units_reqd"], false, $dec);

	if ($myrow["released"] == true)
	{
		label_cell(sql2date($myrow["released_date"]));
		qty_cell($myrow["units_issued"], false, $dec);
	}
      //  label_cell(pager_link(_('Finished'),"/manufacturing/assembledassets.php?trans_no={$myrow['id']}"));
	end_row();

	comments_display_row(ST_WORKORDER, $woid);

	end_table();

    if ($myrow["closed"] == true)
    {
    	display_note(_("This work order is closed."));
    }
}

//--------------------------------------------------------------------------------------

function display_wo_details_quick($woid, $suppress_view_link=false)
{
	global $wo_types_array;

	$myrow = get_work_order($woid, true);

    if ($myrow === false)
    {
    	display_note(_("The work order number sent is not valid."));
    	exit;
    }

   	start_table(TABLESTYLE, "width='80%'");

	$th = array(_("#"), _("Reference"), _("Type"), _("Manufactured Item"),
		_("Into Location"), _("Date"), _("Quantity"));
	table_header($th);

	start_row();
	if ($suppress_view_link)
		label_cell($myrow["id"]);
	else
		label_cell(get_trans_view_str(ST_WORKORDER, $myrow["id"]));
	label_cell($myrow["wo_ref"]);
	label_cell($wo_types_array[$myrow["type"]]);
	view_stock_status_cell($myrow["stock_id"], $myrow["StockItemName"]);
	label_cell($myrow["location_name"]);
	label_cell(sql2date($myrow["date_"]));

	qty_cell($myrow["units_issued"], false, get_qty_dec($myrow["stock_id"]));

	end_row();

	comments_display_row(ST_WORKORDER, $woid);

	end_table();

    if ($myrow["closed"] == true)
    {
    	display_note(_("This work order is closed."));
    }
}

