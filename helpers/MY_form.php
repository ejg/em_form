<?php

/*
 * form extension
 *
 * MY_form
 *
 * This extends the form_Core because form_Core does not have all parameters in the same order
 *
 * ie. input has the order ($data, $value, $extra, $options) while
 * dropdown has the order ($data, $options, $value, $extra)
 *
 * This is needed because the Form_Pair expects every form function to have the parameters in the same order
 *
 */
class form extends form_Core {

public static function dropdown($data = '', $value = '', $extra = '', $options = array())
{
	return parent::dropdown($data, $options, $value, $extra);

}

public static function checkbox($data = '', $value = '', $extra = '', $options = FALSE)
{
	$my_value = ($value == '') ? $data : $value;
	return parent::checkbox($data, $my_value, $options, $extra);

}

public static function radio($data = '', $value = '', $extra = '', $options = FALSE)
{
	return parent::radio($data, $value, $options, $extra);

}

public static function hidden($data = '', $value = '', $extra = '', $options = FALSE)
{
		return '<div class="hidden_row">'.parent::hidden($data,$value).'</div>';
}

}




?>