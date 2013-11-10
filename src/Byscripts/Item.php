<?php

namespace Byscripts;

class Item
{
	private $id;
	private $menu;
	private $label;
	private $subMenu;

	public function __construct($label, $id) {
		$this->id = $id;
		$this->label = $label;
	}

	public function setMenu(Menu $menu) {
		$this->menu = $menu;
	}

	public function menu($id) {
		$this->subMenu = new Menu($id);
		$this->subMenu->setParentItem($this);
		return $this->subMenu;
	}

	public function hasSubMenu() {
		return $this->subMenu instanceof Menu;
	}

	public function item($label, $id) {
		return $this->menu->item($label, $id);
	}

	public function end() {
		return $this->menu->end();
	}

	public function render() {
		$output = '<li>';
		$output .= $this->label;
		
		if($this->hasSubMenu()) {
			$output .= $this->subMenu->render();
		}
		
		$output .= '</li>';

		return $output;
	}
}