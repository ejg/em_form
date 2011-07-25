<?php

/*
 * form_helper extension
 *
 * form_pairs
 *
 * creates a label/form element pair wrapped in a div
 *
 * $data is an array that has the form field attributes plus label, type and class.
 * $value is the default value
 * $extra is a string of extra information (js or such)
 *
 *
 */
class Form_Pair_Core {

	private $name;
	private $data = array();
	private $label = '';
	private $label_extra = '';
	private $value = '';
	private $id = '';
	private $extra = '';
	private $div = 'row';
	private $type = 'input';
	private $options = array();
	private $default = FALSE;
	private $form_id = '';

	private $pair = '';

	/*
	 *  sets the name and data fields based on whether an array or string was passed in
	 *
	 *  sets the form_id so field ids are unique
	 *
	 */
	public function __construct($data,$form_id)
	{
		if (is_array($data))
		{
			$this->name = $data['name'];
			$this->data = $data;
		}
		else
		{
			$this->name = $data;
			$this->data['name'] = $data;
		}
		$this->form_id = ($form_id == '') ? '' : $form_id.'_';
	}

	public function get_name()
	{
		return $this->name;
	}

	public function id_add($string)
	{
		$this->name = $this->name.'-'.$string;
		return $this;
	}

	public function label($label)
	{
		$this->label = $label;
		return $this;
	}

	/*
	 *  set the extra label info, i.e. title
	 *  the parameter is a string
	 */
	public function label_extra($label_extra)
	{
		$this->label_extra = $label_extra;
		return $this;
	}

	/*
	 *  set the type, default is input
	 *  set the options if this is a checkbox that is checked
	 */
	public function type($type)
	{
		$this->type = $type;

		if (($this->type == 'checkbox') && ($this->value != ''))
		{
			$this->options(TRUE);
		}

		return $this;
	}


	public function options($options)
	{
		$this->options = $options;
		return $this;
	}


	/*
	 *  set the div	class name, the default is row
	 */
	public function div($div)
	{
		$this->div = $div;
		return $this;
	}

	/*
	 * set the default, used for checkboxes and radio buttons
	 */

	public function def($default)
	{
		$this->default = $default;
		return $this;
	}

	public function value($value)
	{

	// if value is already set from $_POST, do not reset
		if ($this->value === '')
		{
			$this->value = $value;
		}

// do not display zero price - leave as blank
		if ($this->value == '0.00')
		{
			$this->value = '';
		}
		return $this;
	}

	public function extra($extra)
	{
		$this->extra = $extra;
		return $this;
	}

	/*
	 * set up the data based on type
	 */
	public function create()
	{

		if (($this->default) && ($this->type=='checkbox'))
		{
			$this->options(TRUE);
		}

		if ($this->type=='radio')
		{
			$this->create_radio();
		}
		else
		{
			$data = $this->set_data_array();
			$this->create_pair($data,$this->label);
		}

		return $this->pair;
	}

	/*
	 * create the div, label and input
	 * call the form helper based on type to create input
	 *
	 * the form element is given an id and the label uses that id with the for attribute
	 *
	 * Note: the form helper does not have all the parameters in the same order for all
	 * its functions that create form fields. The My_form helper extension is needed to
	 * rearrange the parameters into the correct order.
	 */
	private function create_pair($data,$label)
	{
		if ($this->div !== false)
		{
			$this->pair .= "<div class=\"{$this->div} \">";
		}

			$this->create_label($label);
			$field = $this->type;
			$this->pair .=  form::$field($data,$this->value, $this->extra,$this->options);
		if ($this->div !== false)
		{
			$this->pair .=  "</div>";
		}

	}

	/*
	 * create a set of radio buttons. Wrap all button in div rrow
	 */
	private function create_radio()
	{
		$this->default = ($this->value != '') ? $this->value : $this->default;
		$this->pair .=  "<div class=\"rrow\">";
			foreach ($this->label as $label=>$value)
			{
				$data = $this->set_data_radio($value);
				$this->value = $value;
				$this->create_pair($data,$label);
			}
		$this->pair .= "</div>";
	}

	/*
	 *	this takes the name and value and sets them in an array to send to the super form_helper
	 *  this also sets the id to be the name so the label will be associated with this field
	 */
	private function set_data_array()
	{
		if ($this->type != 'hidden') {

			$name = str_replace(' ','_',$this->name);
//			$name = str_replace('/','',$name);
			$this->id = $this->form_id.$name;
//			$this->id = $this->form_id.$this->name;

			$this->data['id'] = $this->id;

			if ($this->type == 'textarea')
			{
				$this->checkRowCol();
			}
		}
		else
		{
			$this->data[$this->name] = $this->value;
			unset($this->data['name']);
		}
		return $this->data;
	}

	public function checkRowCol()
	{
		if (! isset ($this->data['rows'] ))
		{
			$this->data['rows'] = 10;
		}
		if (! isset ($this->data['cols'] ))
		{
			$this->data['cols'] = 15;
		}
		return $this;
	}

	/*
	 * set the radio button ids based off the of common name
	 */
	private function set_data_radio($value)
	{
		$data['name'] = $this->name;
		$data['id'] = $this->name.'_'.$value;
		$this->id = $data['id'];
		$this->options = ($value == $this->default);

		return $data;
	}

	private function set_value($data, $value)
	{
		$val = ($value == '' && isset($this->values[$data])) ? $this->values[$data] : $value;
		return $val;
	}

	/*
	 * create the label if it is not set to false
	 */
	private function create_label($label)
	{
		if ($mylabel = $this->get_label($label))
		{
			$this->pair .=  form::label($this->id, $mylabel, $this->label_extra);
		}

	}

	public function get_label($label)
	{
		$alabel = ($label === '') ? $this->get_default_label() : $label;
		return $alabel;
	}

	/*
	 * the default label is the field name with spaces replaced by '_'
	 */
	private function get_default_label()
	{
		return ucfirst(str_replace('_', ' ', $this->name));
	}

}

?>