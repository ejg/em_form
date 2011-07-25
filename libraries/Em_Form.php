<?php

/*
 * Em_Form
 *
 * Author - Emily Gillcoat
 *
 * creates a label/form element pair wrapped in a div
 *
 */
class Em_Form_Core {

	private $id = '';
	private $values = array();
	private $decimals = array();

public function __construct($values=array())
{
	$this->values = $values;
}

public function open($action = NULL, $attr = array(), $hidden = NULL)
{
	if (array_key_exists('id', $attr))
	{
		$this->id = $attr['id'];
	}
	echo form::open($action, $attr, $hidden);
}

public function close()
{
	echo form::close();
}

public function set_decimals($decimals)
{
	$this->decimals = $decimals;
}
/*
 * set the values after a post
 */
public function set_values($values)
{
	$this->values = $values;
	foreach ($this->decimals as $field=>$place)
	{
		if (isset($values[$field]) && (is_numeric($values[$field])))
		{
			$this->values[$field] = number_format($values[$field], $place);
		}
	}
}

/*
 * creates a form pair
 *
 * if there is a value for this data (from a $_POST), set it
 *
 * if there are options defined in the form_options_model, it is a select and set the options.
 */
public function pair($data)
{
	$pair = new Form_Pair($data, $this->id);
	$name = $pair->get_name();

/*	if (isset($this->values[$name]))
	{
		$pair->value( $this->values[$name]);
	}
	else
	{
		if (preg_match('/[][]+/', $name))
		{
			$pair->value($this->get_value($this->values,$name));
		}
		else
		{
			$pair->value('');
		}
	}

*/
	if (!isset($this->values[$name]))
	{
		$pair->value($this->get_value($this->values,$name));
	}
	$pair->value( (isset($this->values[$name])) ? $this->values[$name] : '');

	if (method_exists('Form_options_Model', $name))
	{
		$pair->options(Form_options_Model::$name());
	}
	return $pair;
}

function get_value($array, $string)
{
	$val = '';
    $i = preg_split('/[][]+/', $string, -1, PREG_SPLIT_NO_EMPTY);

	if (count($i) > 1)
	{
		$first = $i[0];
		$second = $i[1];
		if (isset($array[$first]))
		{
			$val = $array[$first][$second];
		}
	}
    return $val;
}

function xxget_value($array, $string)
{

    $indices = preg_split('/[][]+/', $string, -1, PREG_SPLIT_NO_EMPTY);
	if (count($indices) > 1)
	{
	    foreach ($indices as $index)
	    {
			if (isset($array[$index]))
			{
	 	       $array = $array[$index];
	 	    }
	 	    else
	 	    {
	 	    	echo '<br>break';
	 	    	break;
	 	    }
	    }
	}
var_dump($array);
    return $array;
}






public function get_form_fields()
{
	return $this->form_fields;
}

/*
 * return true if this option is the first option
 */
public function is_first_option($opt)
{
	return (Form_options_Model::is_first_option($opt));
}

/*
 * return true if this option is the last option
 */
public function is_last_option($opt)
{
	return (Form_options_Model::is_last_option($opt));
}

}




?>