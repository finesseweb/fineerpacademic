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
class managements_app extends application
{
	function __construct()
	{
		parent::__construct("managements", _($this->help_context = "&Assest Managements"));
			
		$this->add_module(_("Transactions"));
                $this->add_lapp_function(0, _("Building"),
			"managements/manage/createassets.php", 'SA_CREATE_ASSETS', MENU_TRANSACTION);
		$this->add_lapp_function(0, _("Floor"),
			"managements/manage/assets.php?NewProduct=Yes", 'SA_ASSEMBLE', MENU_TRANSACTION);
				$this->add_lapp_function(0, _("Room"),
			"managements/manage/room.php?NewProduct=Yes", 'SA_ASSEMBLE_ASSETS', MENU_TRANSACTION);
				$this->add_lapp_function(0, _("Department"),
			"managements/manage/department.php?NewProduct=Yes", 'SA_ASSEMBLE_ASSETS', MENU_TRANSACTION);
				$this->add_lapp_function(0, _("Seats"),
			"managements/manage/seats.php?NewProduct=Yes", 'SA_ASSEMBLE_ASSETS', MENU_TRANSACTION);
			
			$this->add_module(_("Maintenance Masters"));
		
			$this->add_lapp_function(1, _("Building Items"),
			"manufacturing/buildingitem.php?NewProduct=Yes", 'SA_ASSEMBLE_ASSETS', MENU_TRANSACTION);
			$this->add_lapp_function(1, _("Floor Items"),
			"manufacturing/flooritem.php?NewProduct=Yes", 'SA_ASSEMBLE_ASSETS', MENU_TRANSACTION);
                        $this->add_lapp_function(1, _("Room Items"),
			"manufacturing/roomitem.php?NewProduct=Yes", 'SA_ASSEMBLE_ASSETS', MENU_TRANSACTION);
			$this->add_lapp_function(1, _("Department Items"),
			"manufacturing/deptitem.php?NewProduct=Yes", 'SA_ASSEMBLE_ASSETS', MENU_TRANSACTION);
			$this->add_lapp_function(1, _("Seat Items"),
			"manufacturing/seatitem.php?NewProduct=Yes", 'SA_ASSEMBLE_ASSETS', MENU_TRANSACTION);
		        $this->add_lapp_function(1, _("User Assing"),
			"manufacturing/user_assing.php?NewProduct=Yes", 'SA_ASSEMBLE_ASSETS', MENU_TRANSACTION);
		
                        $this->add_module(_("Report Masters"));
                     $this->add_lapp_function(2, _("User Assinged"),
			"manufacturing/user_assinged.php?NewProduct=Yes", 'SA_ASSEMBLE_ASSETS', MENU_TRANSACTION); 
                     $this->add_lapp_function(2, _("Item Assinged"),
			"manufacturing/item_assinged.php?NewProduct=Yes", 'SA_ASSEMBLE_ASSETS', MENU_TRANSACTION);
                      $this->add_lapp_function(2, _("Item Returned"),
			"manufacturing/item_retrun.php?NewProduct=Yes", 'SA_ASSEMBLE_ASSETS', MENU_TRANSACTION);
                        $this->add_extensions();
	}
}


?>
