<?php 

class create_room{
var $trans_type; // order/grn/invoice (direct)
	var $line_items;/*Only used for modification of existing orders otherwise only established when order committed */
    var $room_no;
    var $qty;
    var $addqty;
    var $room_id;
	var $lines_on_order = 0;
function __construct()
{
		$this->line_items = array();
        $this->lines_on_order =  0;
}

function add_to_order($line_no, $room_no, $qty,$addqty,$room_id = 0)
	{
    		//if (isset($qty) && !empty($qty) )
    	//	{
    		$this->line_items[$line_no] = new room_line_details($line_no, $room_no, $qty,$addqty,$room_id);
                            
                           
    			$this->lines_on_order++;
    			return 1;
    	//	} 
    	//	return 0;
	}
        
        
    function update_order_item($line_no, $room_no, $qty,$addqty)
	{
	
		$this->line_items[$line_no]->room_no = $room_no;
		$this->line_items[$line_no]->quantity = $qty;
		$this->line_items[$line_no]->addqty = $addqty;
	}
	

	function remove_from_order($line_no)
	{
		
		array_splice($this->line_items, $line_no, 1);
	}

}


class room_line_details
{

	var $line_no;
	var $room_no;
    var $quantity;
    var $room_id;
    var $addqty;
	function __construct($line_no, $room_no, $qty,$addqty,$room_id)
	{
		$this->line_no = $line_no;
		$this->room_no = $room_no;
		$this->quantity = $qty;
		$this->addqty = $addqty;
		$this->room_id = $room_id;
		
	}
}

