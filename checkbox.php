<?php

class acf_field_checkbox extends acf_field
{

	/*
	*  __construct
	*
	*  Set name / label needed for actions / filters
	*
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function __construct()
	{
		// vars
		$this->name = 'checkbox';
		$this->label = __("Checkbox",'acf');
		$this->category = __("Choice",'acf');
		$this->defaults = array(
			'layout'		=>	'vertical',
			'choices'		=>	array(),
			'default_value'	=>	'',
			'other_choice'		=>	0,
			'save_other_choice'	=>	0,
		);
		
		
		// do not delete!
    	parent::__construct();
	}
		
	
	/*
	*  create_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function create_field( $field )
	{
		// value must be array
		if( !is_array($field['value']) )
		{
			// perhaps this is a default value with new lines in it?
			if( strpos($field['value'], "\n") !== false )
			{
				// found multiple lines, explode it
				$field['value'] = explode("\n", $field['value']);
			}
			else
			{
				$field['value'] = array( $field['value'] );
			}
		}
		
		//only one other value.
		$end_option = '';
		foreach ($field['value'] as $key => $option_field) {
			if($option_field){
				if(!array_key_exists($option_field,$field['choices'])){
					$end_option = $option_field;
					unset($field['value'][$key]);
				}
			}
			
		}
		if($end_option!=''){
			array_push($field['value'],$end_option);
		}
		
		// trim value
		$field['value'] = array_map('trim', $field['value']);

		
		
		// vars
		$i = 0;
		$e = '<input type="hidden" name="' .  esc_attr($field['name']) . '" value="" />';
		$e .= '<ul class="acf-checkbox-list ' . esc_attr($field['class']) . ' ' . esc_attr($field['layout']) . '">';
		
		
		// checkbox saves an array
		$field['name'] .= '[]';
		
		if( is_array($field['choices']) )
		{
			// foreach choices
			foreach( $field['choices'] as $key => $value )
			{
				// vars
				$i++;
				$atts = '';
				
				
				if( in_array($key, $field['value']) )
				{
					$atts = 'checked="yes"';
				}
				if( isset($field['disabled']) && in_array($key, $field['disabled']) )
				{
					$atts .= ' disabled="true"';
				}
				
				
				// each checkbox ID is generated with the $key, however, the first checkbox must not use $key so that it matches the field's label for attribute
				$id = $field['id'];
				
				if( $i > 1 )
				{
					$id .= '-' . $key;
				}
				
				$e .= '<li><label><input id="' . esc_attr($id) . '" type="checkbox" class="' . esc_attr($field['class']) . '" name="' . esc_attr($field['name']) . '" value="' . esc_attr($key) . '" ' . $atts . ' />' . $value . '</label></li>';
			}
		}

		// other choice
		if( $field['other_choice'] )
		{
			// vars
			$atts = '';
			$atts2 = 'name="' . esc_attr($field['name']) .' value="" ';
			if( end($field['value']) !== '')
			{
				if( !isset($field['choices'][ end($field['value']) ]) )
				{
					$atts = 'checked="checked" data-checked="checked"';
					$atts2 = 'name="' . esc_attr($field['name']) . '" value="' . esc_attr(end($field['value'])) . '"' ;
				}
			}
			
			
			$e .= '<li><label><input id="' . esc_attr($field['id']) . '-other" type="checkbox" name="' . esc_attr($field['name']) . '" value="other" ' . $atts . ' />' . __("Otros", 'acf') . '</label> <input type="text" ' . $atts2 . ' /></li>';
		}

		$e .= '</ul>';
		
		
		// return
		echo $e;
	}
	
	
	/*
	*  create_options()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/
	
	function create_options( $field )
	{
		// vars
		$key = $field['name'];
		
		
		// implode checkboxes so they work in a textarea
		if( is_array($field['choices']) )
		{		
			foreach( $field['choices'] as $k => $v )
			{
				$field['choices'][ $k ] = $k . ' : ' . $v;
			}
			$field['choices'] = implode("\n", $field['choices']);
		}
		
		?>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label for=""><?php _e("Choices",'acf'); ?></label>
		<p><?php _e("Enter each choice on a new line.",'acf'); ?></p>
		<p><?php _e("For more control, you may specify both a value and label like this:",'acf'); ?></p>
		<p><?php _e("red : Red",'acf'); ?><br /><?php _e("blue : Blue",'acf'); ?></p>
	</td>
	<td>
		<?php
		
		do_action('acf/create_field', array(
			'type'	=>	'textarea',
			'class' => 	'textarea field_option-choices',
			'name'	=>	'fields['.$key.'][choices]',
			'value'	=>	$field['choices'],
		));
		
		?>
		<div class="radio-option-other_choice">
		<?php
		
		do_action('acf/create_field', array(
			'type'		=>	'true_false',
			'name'		=>	'fields['.$key.'][other_choice]',
			'value'		=>	$field['other_choice'],
			'message'	=>	__("Add 'other' choice to allow for custom values", 'acf')
		));
		
		?>
		</div>
		<div class="radio-option-save_other_choice" <?php if( !$field['other_choice'] ): ?>style="display:none"<?php endif; ?>>
		<?php
		
		do_action('acf/create_field', array(
			'type'		=>	'true_false',
			'name'		=>	'fields['.$key.'][save_other_choice]',
			'value'		=>	$field['save_other_choice'],
			'message'	=>	__("Save 'other' values to the field's choices", 'acf')
		));
		
		?>
		</div>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Default Value",'acf'); ?></label>
		<p class="description"><?php _e("Enter each default value on a new line",'acf'); ?></p>
	</td>
	<td>
		<?php
		
		do_action('acf/create_field', array(
			'type'	=>	'textarea',
			'name'	=>	'fields['.$key.'][default_value]',
			'value'	=>	$field['default_value'],
		));
		
		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label for=""><?php _e("Layout",'acf'); ?></label>
	</td>
	<td>
		<?php
		
		do_action('acf/create_field', array(
			'type'	=>	'radio',
			'name'	=>	'fields['.$key.'][layout]',
			'value'	=>	$field['layout'],
			'layout' => 'horizontal', 
			'choices' => array(
				'vertical' => __("Vertical",'acf'), 
				'horizontal' => __("Horizontal",'acf')
			)
		));
		
		?>
	</td>
</tr>
		<?php
		
	}

	/*
	*  update_value()
	*
	*  This filter is appied to the $value before it is updated in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value which will be saved in the database
	*  @param	$post_id - the $post_id of which the value will be saved
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the modified value
	*/
	
	function update_value( $value, $post_id, $field )
	{
		// validate
		if( $field['save_other_choice'] )
		{	
			/*
			// value isn't in choices yet
			if( !isset($field['choices'][ $value ]) )
			{
				// update $field
				$field['choices'][ $value ] = $value;
				
				
				// can save
				if( isset($field['field_group']) )
				{
					do_action('acf/update_field', $field, $field['field_group']);
				}
				
			}
			*/
		}		
		
		return $value;
	}
	
}

new acf_field_checkbox();

?>