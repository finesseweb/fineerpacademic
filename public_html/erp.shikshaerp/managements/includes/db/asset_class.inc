<?php 

class create_assets{
var $trans_type; // order/grn/invoice (direct)
	var $line_items;/*Only used for modification of existing orders otherwise only established when order committed */
        var $item_category;
        var $item_sub_category;
        var $item_id;
        var $qty;
        var $osl;
	var $lines_on_order = 0;
		// array of taxes manually inserted during sales invoice entry (direct invoice)
	function __construct()
	{
		/*Constructor function initialises a new purchase order object */
		$this->line_items = array();
                $this->lines_on_order =  0;
	}
function add_to_order($line_no, $stock_id, $qty, $category,$sub_category,$item_id,$osl)
	{
		if (isset($qty) && !empty($qty) && isset($osl) && !empty($osl))
		{
		$this->line_items[$line_no] = new assets_line_details($line_no, $stock_id, $qty, $category,$sub_category,$item_id,$osl);
		$this->lines_on_order++;
			return 1;
		} 
		return 0;
	}
        
        
    function update_order_item($line_no, $stock_id, $qty, $category,$sub_category,$item_id,$osl)
	{
		$this->line_items[$line_no]->item_category = $category;
		$this->line_items[$line_no]->item_sub_category = $sub_category;
		$this->line_items[$line_no]->quantity = $qty;
		$this->line_items[$line_no]->stock_id = $stock_id;
		$this->line_items[$line_no]->item_id = $item_id;
		$this->line_items[$line_no]->osl = $osl;
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
    var $quantity;
	var $item_category;
    var $item_sub_category;
    var $item_id;
    var $osl;
	function __construct($line_no, $stock_id, $qty, $category,$sub_category,$item_id,$osl)
	{
//	display_error($line_no);
		$this->line_no = $line_no;
		$this->stock_id = $stock_id;
		$this->quantity = $qty;
                $this->item_category = $category;
                        $this->item_sub_category = $sub_category;
                        $this->item_id = $item_id;
                        $this->osl = $osl;
		
	}
}

