<?php 

class create_seat{
var $trans_type; // order/grn/invoice (direct)
	var $line_items;/*Only used for modification of existing orders otherwise only established when order committed */
    var $seat_no;
    var $qty;
	var $lines_on_order = 0;
function __construct()
{
		$this->line_items = array();
        $this->lines_on_order =  0;
}

function add_to_order($line_no,$seat_no,$qty)
	{
    		if (!empty($seat_no) && !empty($qty) && $qty==1 )
                {
    		    $this->line_items[$line_no] = new seat_line_details($line_no, $seat_no,$qty);
    			$this->lines_on_order++;
    			return 1;
    		} 
    		return 0;
	}
        
        
    function update_order_item($line_no, $seat_no,$qty)
	{
	
		$this->line_items[$line_no]->seat_no = $seat_no;
		$this->line_items[$line_no]->quantity = $qty;
	}
	

	function remove_from_order($line_no)
	{
		
		array_splice($this->line_items, $line_no, 1);
	}

}


class seat_line_details
{

	var $line_no;
	var $seat_no;
	var $quantity;
	function __construct($line_no, $seat_no,$qty)
	{
		$this->line_no = $line_no;
		$this->seat_no = $seat_no;
		$this->quantity = $qty;
	}
}

