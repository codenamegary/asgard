<?php
namespace Asgard\Form\Widgets;

class TextareaWidget extends \Asgard\Form\Widget {
	public function render(array $options=[]) {
		$options = $this->options+$options;
		
		$attrs = [];
		if(isset($options['attrs']))
			$attrs = $options['attrs'];
		return \Asgard\Form\HTMLHelper::tag('textarea', [
			'name'	=>	$this->name,
			'id'	=>	isset($options['id']) ? $options['id']:null,
		]+$attrs,
		$this->value ? \Asgard\Http\Utils\HTML::sanitize($this->value):'');
	}
}