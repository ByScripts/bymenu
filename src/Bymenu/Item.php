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

	public function __construct(Menu $menu, $id, $label, $url = null) {

		$this->menu = $menu;
		$this->id = $id;
		$this->label = $label;
		$this->url = $url;

		$menu->addItem($this);
	}

	public function menu($id = null) {
		
		$id = is_null($id) ? $this->id : $id;

		$this->setSubMenu(new Menu($id));
		
		return $this->subMenu;
	}

	public function getMenu() {
		return $this->menu;
	}

	public function setSubMenu(Menu $subMenu) {
		$this->subMenu = $subMenu;
		$this->subMenu->setParentItem($this);
	}

	public function hasSubMenu() {
		return $this->subMenu instanceof Menu;
	}

	public function item($id, $label, $url = null) {
		return $this->menu->item($id, $label, $url);
	}

	public function end() {
		return $this->menu->end();
	}

	public function getId($recursive = false) {
		return !$recursive ? $this->id : $this->getMenu()->getId(true) . '-' . $this->id;
	}

	public function setHtmlId($id) {
		$this->htmlId = $id;
		return $this;
	}

	public function getHtmlId() {
		return !is_null($this->htmlId) ? $this->htmlId : $this->getId(true) . '-item';
	}

	public function addHtmlClass($class) {
		$this->htmlClass[$class] = true;
		return $this;
	}

	public function getHtmlClass() {
		return implode(' ', array_keys($this->htmlClass));
	}

	public function setLabelHtmlId($id) {
		$this->labelHtmlId = $id;
		return $this;
	}

	public function getLabelHtmlId() {
		return !is_null($this->labelHtmlId) ? $this->labelHtmlId : $this->getId(true) . '-label';
	}

	public function addLabelHtmlClass($class) {
		$this->labelHtmlClass[$class] = true;
		return $this;
	}

	public function getLabelHtmlClass() {
		return implode(' ', array_keys($this->labelHtmlClass));
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
		$labelTo = array($this->getLabelHtmlId(true), $this->getLabelHtmlClass(), $this->getUrl(), $this->label);

		$label = str_replace($labelFrom, $labelTo, $labelTemplate);

		//
		// ITEM
		//
		$itemTemplate = $this->getMenu()->getItemTemplate();

		$itemFrom = array('%id%', '%class%', '%label%', '%url%', '%submenu%');
		$itemTo = array($this->getHtmlId(true), $this->getHtmlClass(), $label, $this->getUrl(), $this->renderSubMenu());

		return str_replace($itemFrom, $itemTo, $itemTemplate);
	}
}