<?php 

class serialize_asset{
var $trans_type; // order/grn/invoice (direct)
	var $line_items;/*Only used for modification of existing orders otherwise only established when order committed */
        var $item_category;
        var $item_sub_category;
        var $item_id;
        var $sl;
	var $lines_on_order = 0;
		// array of taxes manually inserted during sales invoice entry (direct invoice)
	function __construct()
	{
		/*Constructor function initialises a new purchase order object */
		$this->line_items = array();
                $this->lines_on_order =  0;
	}
function add_to_order($line_no, $stock_id, $sl, $category,$sub_category)
	{
		if (isset($sl) && $sl != 0)
		{
		$this->line_items[$line_no] = new assets_line_details($line_no, $stock_id, $sl, $category,$sub_category);
                        
                       
			$this->lines_on_order++;
			return 1;
		} 
		return 0;
	}
        
        
    function update_order_item($line_no, $stock_id, $sl, $category,$sub_category)
	{
		$this->line_items[$line_no]->item_category = $category;
		$this->line_items[$line_no]->item_sub_category = $sub_category;
		$this->line_items[$line_no]->sl = $sl;
		$this->line_items[$line_no]->stock_id = $stock_id;
	}

	function remove_from_order($line_no)
	{
		
		array_splice($this->line_items, $line_no, 1);
	}

}


class assets_line_details
{

	var $line_no;
	var $stock_id;
        var $sl;
	 var $item_category;
        var $item_sub_category;
        var $item_id;
	function __construct($line_no, $stock_id, $sl, $category,$sub_category)
	{
        
		/* Constructor function to add a new LineDetail object with passed params */
		$this->line_no = $line_no;
		$this->stock_id = $stock_id;
		$this->sl = $sl;
                $this->item_category = $category;
                        $this->item_sub_category = $sub_category;
           //   display_error($this->ist);
             // die();
		
	}
}

