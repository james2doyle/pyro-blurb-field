<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * blurb Field Type
 *
 * @package		Addons\Field Types
 * @author		James Doyle (james2doyle)
 * @license		MIT License
 * @link		http://github.com/james2doyle/pyro-blurb-field
 */
class Field_blurb
{
	public $field_type_slug    = 'blurb';
	public $db_col_type        = 'text';
	public $version            = '1.0.0';
	public $custom_parameters		= array('amount', 'folder_choice', 'use_markdown');
	public $author             = array(
    'name'=>'James Doyle',
    'url'=>'http://github.com/james2doyle/pyro-blurb-field'
    );

	// --------------------------------------------------------------------------

	public function __construct()
	{
		$this->CI =& get_instance();
	}

	// --------------------------------------------------------------------------

	/**
	 * Output form input
	 *
	 * @param	array
	 * @param	array
	 * @return	string
	 */
	public function form_output($data)
	{
		$amount = $data['custom']['amount'];
		$folderid = (int)$data['custom']['folder_choice'];
		$output = unserialize($data['value']);
		$this->CI->load->library('files/files');
		$folder_contents = Files::folder_contents($folderid);
		$imagearray = array();
		foreach ($folder_contents['data']['file'] as $item) {
			$checkmime = explode('/', $item->mimetype);
			if ($checkmime[0] == 'image') {
				$imagearray[$item->id] = $item->name;
			}
		}
		$template = '<ul class="blurb-list" id="'.$data['form_slug'].'">';
		for ($i=0; $i < $amount; $i++) {
			$template .= '<li>
      <label>'.lang('streams:blurb.title_label').' '.($i+1).'</label>
      <input type="text" name="'.$data['form_slug'].'['.$i.'][title]" value="'.$output[$i]['title'].'">';
      if ($folderid !== 0) {
        $template .= '<label>'.lang('streams:blurb.image_label').' '.($i+1).'</label>
        '.form_dropdown($data['form_slug']."[".$i."][image]", array(0 => 'None')+$imagearray, $output[$i]['image']);
      }
      $template .= '<label>'.lang('streams:blurb.body_label').' '.($i+1).'</label>
      <textarea name="'.$data['form_slug'].'['.$i.'][body]" cols="35" rows="10">'.$output[$i]['body'].'</textarea>
      <label>'.lang('streams:blurb.link_label').' '.($i+1).'</label>
      <input type="text" name="'.$data['form_slug'].'['.$i.'][link]" value="'.$output[$i]['link'].'"></li>';
    }
    return $template.'</ul>';
  }

  public function event($field)
  {
    $this->CI->type->add_js('blurb', 'blurb.js');
    $this->CI->type->add_css('blurb', 'blurb.css');
  }

  public function pre_save($input)
  {
    return serialize($input);
  }

  public function pre_output($input, $data)
  {
    $this->CI->load->helper('markdown');
    $use_markdown = (boolean)$data['use_markdown'];
    $input = unserialize($input);
    $output = array();
		// using for to keep track of how many. used the id to check for the last entry in the layout
    for ($i=0; $i < count($input); $i++) {
      if ($use_markdown) {
        $raw_body = isset($input[$i]['body']) ? $input[$i]['body'] : '';
        $body = isset($input[$i]['body']) ? parse_markdown($input[$i]['body']) : '';
      } else {
        $raw_body = isset($input[$i]['body']) ? $input[$i]['body'] : '';
        $body = $raw_body;
      }
      $output[] = array(
        'id' => ($i+1),
        'title' => isset($input[$i]['title']) ? $input[$i]['title'] : '',
        'image' => isset($input[$i]['image']) ? $input[$i]['image'] : '',
        'link' => isset($input[$i]['link']) ? $input[$i]['link'] : '',
        'body' => $body,
        'raw_body' => $raw_body
        );
    }
    return $output;
  }
	/**
	 * Param Amount
	 *
	 * @access	public
	 * @param	[int - value]
	 * @return	int
	 */
	public function param_amount($value = null)
	{
		if (!$value) {
			$value = 0;
		}
		// some inline styles for this specific form/page
		$styles = '<style type="text/css">#amount_value {font-size: 2em; font-weight: bold;float: left;margin: 0 1em 0 0;}</style>';
		// some vanilla js to add the increase decrease function to the counter
		$scripts = '<script>(function(){
			var _input = document.getElementsByName("amount")[0];
			var _value = document.getElementById("amount_value");
			var _up = document.getElementById("amount_up");
			var _down = document.getElementById("amount_down");
			var _count = '.$value.';
			_up.addEventListener("click", handleUpClick, false);
			_down.addEventListener("click", handleDownClick, false);
			function handleUpClick(e) {
				_count += 1;
				e.preventDefault();
				_input.value = _count;
				_value.innerText = _input.value;
				return false;
			}
			function handleDownClick(e) {
				if (_count !== 0) {
					_count -= 1;
					_input.value = _count;
					_value.innerText = _input.value;
				}
				e.preventDefault();
				return false;
			}
		})();</script>';
// the pseudo input template
$template = '<div id="amount_field_input">
<div id="amount_value">'.$value.'</div>
<a href="#" class="button" id="amount_up">+</a>&nbsp;
<a href="#" class="button" id="amount_down">-</a>
</div>'.$styles.$scripts;
// hide the original input and access it using javascript through the dummy
return array(
	'input'			=> form_hidden('amount', $value, 'id="amount_input"').$template,
	'instructions'	=> lang('streams:blurb.amount_instr'));
}
  /**
  	 * Param Folder Choice - taken from the image field type
  	 *
  	 * @access	public
  	 * @param	[string - value]
  	 * @return	string
  	 */
  public function param_folder_choice($value = null)
  {
  	// Get the folders
  	$this->CI->load->model('files/file_folders_m');
  	$tree = $this->CI->file_folders_m->get_folders();
  	$tree = (array)$tree;
  	if ( ! $tree)
  	{
  		return '<em>'.lang('streams:blurb.need_folder').'</em>';
  	}
  	$choices = array(0 => 'None');
  	foreach ($tree as $tree_item)
  	{
  			// We are doing this to be backwards compat
  			// with PyroStreams 1.1 and below where
  			// This is an array, not an object
  		$tree_item = (object)$tree_item;
  		$choices[$tree_item->id] = $tree_item->name;
  	}
  	return array(
  		'input'			=> form_dropdown('folder_choice', $choices, $value),
  		'instructions'	=> lang('streams:blurb.folder_choice_instr'));
  }

  /**
     * Param Folder Choice - taken from the image field type
     *
     * @access  public
     * @param [string - value]
     * @return  string
     */
  public function param_use_markdown($value = 0)
  {
    return array(
      'input'     => form_checkbox('use_markdown', 1, $value).lang('streams:blurb.use_markdown_label'),
      'instructions'  => lang('streams:blurb.use_markdown_instr'));
  }
}
