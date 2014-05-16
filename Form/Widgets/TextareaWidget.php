<?php
namespace Asgard\Form\Widgets;

class TextareaWidget extends \Asgard\Form\Widgets\HTMLWidget {
	public function render(array $options=array()) {
		$options = $this->options+$options;
		
		$attrs = array();
		if(isset($options['attrs']))
			$attrs = $options['attrs'];
		return \Asgard\Form\HTMLHelper::tag('textarea', array(
			'name'	=>	$this->name,
			'id'	=>	isset($options['id']) ? $options['id']:null,
		)+$attrs,
		$this->value ? \Asgard\Utils\HTML::sanitize($this->value):'');
	}
}