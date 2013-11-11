<?php

namespace Bymenu;

class Item
{
	private $id;
	private $menu;
	private $label;
	private $subMenu;
	private $htmlId;
	private $htmlClass = array();
	private $labelHtmlId;
	private $labelHtmlClass = array();
	private $url;

	public function __construct(Menu $menu, $label, $url = null, $id = null) {

		$this->menu = $menu;

		if(is_null($id)) {
			$id = $this->getmenu()->getSlugifier()->slugify($label);
		}

		$this->id = $id;
		$this->label = $label;
		$this->url = $url;
	}

	public function menu($id = null) {
		
		if(is_null($id)) {
			$id = $this->id;
		}

		$this->subMenu = new Menu($id);
		$this->subMenu->setParentItem($this);
		
		return $this->subMenu;
	}

	public function getMenu() {
		return $this->menu;
	}

	public function hasSubMenu() {
		return $this->subMenu instanceof Menu;
	}

	public function item($label, $url = null, $id = null) {
		return $this->menu->item($label, $url, $id);
	}

	public function end() {
		return $this->menu->end();
	}

	public function getId() {
		return $this->id;
	}

	public function setHtmlId($id) {
		$this->htmlId = $id;
		return $this;
	}

	public function getHtmlId() {
		if(!is_null($this->htmlId)) {
			return $this->htmlId;
		}
		else {
			return $this->getId() . '-item';
		}
	}

	public function setLabelHtmlId($id) {
		$this->labelHtmlId = $id;
		return $this;
	}

	public function getLabelHtmlId() {
		if(!is_null($this->labelHtmlId)) {
			return $this->labelHtmlId;
		}
		else {
			return $this->getId() . '-label';
		}
	}

	public function addLabelHtmlClass($class) {
		$this->labelHtmlClass[$class] = true;
	}

	public function getLabelHtmlClass() {
		return implode(' ', array_keys($this->labelHtmlClass));
	}

	public function addHtmlClass($class) {
		$this->htmlClass[$class] = true;
	}

	public function getHtmlClass() {
		return implode(' ', array_keys($this->htmlClass));
	}

	public function setUrl($url) {
		$this->url = $url;
		return $this;
	}

	public function getUrl() {
		return $this->url;
	}

	public function hasUrl() {
		return !is_null($this->url);
	}

	public function renderSubMenu() {
		return $this->hasSubMenu() ? $this->subMenu->render() : null;
	}

	public function render() {

		//
		// LABEL
		//
		$labelTemplate = $this->hasUrl() ? $this->getMenu()->getLabelUrlTemplate() : $this->getMenu()->getLabelNoUrlTemplate();

		$labelFrom = array('%id%', '%class%', '%url%', '%label%');
		$labelTo = array($this->getLabelHtmlId(), $this->getLabelHtmlClass(), $this->getUrl(), $this->label);

		$label = str_replace($labelFrom, $labelTo, $labelTemplate);

		//
		// ITEM
		//
		$itemTemplate = $this->getMenu()->getItemTemplate();

		$itemFrom = array('%id%', '%class%', '%label%', '%url%', '%submenu%');
		$itemTo = array($this->getHtmlId(), $this->getHtmlClass(), $label, $this->getUrl(), $this->renderSubMenu());

		return str_replace($itemFrom, $itemTo, $itemTemplate);
		

		

		
		// $output = '<li id="' . $this->getHtmlId() . '">';
		
		// $output .= '<' . $this->getLabelHtmlTag();
		// $output .= ' class="item-label"';
		// $output .= ' id="' . $this->getLabelHtmlId() . '"';
		
		// if($this->getLabelHtmlTag() == 'a') {
		// 	$output .= ' href="' . $this->url . '"';
		// }

		// $output .= '>' . $this->label;

		// $output .= '</' . $this->getLabelHtmlTag() . '>';

		// if($this->hasSubMenu()) {
		// 	$output .= $this->subMenu->render();
		// }
		
		// $output .= '</li>';

		//return $output;
	}
}