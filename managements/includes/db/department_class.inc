<?php 

class create_depart{
var $trans_type; // order/grn/invoice (direct)
	var $line_items;/*Only used for modification of existing orders otherwise only established when order committed */
    var $department;
    var $depart_id;
    var $qty;
	var $lines_on_order = 0;
function __construct()
{
		$this->line_items = array();
        $this->lines_on_order =  0;
}

function add_to_order($line_no, $department, $qty,$depart_id = 0)
	{
    		if (isset($qty) && !empty($qty) )
                {
    		    $this->line_items[$line_no] = new depart_line_details($line_no, $department, $qty, $depart_id);
    			$this->lines_on_order++;
    			return 1;
    		} 
    		return 0;
	}
        
        
    function update_order_item($line_no, $department, $qty)
	{
	
		$this->line_items[$line_no]->$department = $department;
		$this->line_items[$line_no]->quantity = $qty;
	}
	

	function remove_from_order($line_no)
	{
		
		array_splice($this->line_items, $line_no, 1);
	}

}


class depart_line_details
{

	var $line_no;
	var $department;
	var $depart_id;
    var $quantity;
	function __construct($line_no, $department, $qty,$depart_id)
	{
		$this->line_no = $line_no;
		$this->department = $department;
		$this->quantity = $qty;
		$this->depart_id = $depart_id;
	}
}

