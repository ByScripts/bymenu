<?php
namespace Bymenu;

use Slug\Slugifier;

class Menu
{
	private $id;
	private $items = array();
	private $parentItem;
	private $slugifier;
	private $htmlId;
	private $htmlClass = array();

	// private $menuTemplate;
	// private $itemTemplate;
	// private $labelUrlTemplate;
	// private $labelNoUrlTemplate;

	private $template;
	private $templates = array(
		'default' => array(
			'menu' => '<ul id="%id%" class="%class%">%items%</ul>',
			'item' => '<li class="%class%">%label%%submenu%</li>',
			'labelUrl' => '<a class="%class%" href="%url%">%label%</a>',
			'labelNoUrl' => '<span class="%class%">%label%</span>'
			),
		'complex' => array(
			'menu' => '<ul id="%id%" class="%class%">%items%</ul>',
			'item' => '<li id="%id%" class="%class%">%label%%submenu%</li>',
			'labelUrl' => '<a id="%id%" class="%class%" href="%url%">%label%</a>',
			'labelNoUrl' => '<span id="%id%" class="%class%">%label%</span>'
			),
		'div' => array(
			'menu' => '<div id="%id%" class="%class%">%items%</div>',
			'item' => '<div id="%id%" class="%class%">%label%%submenu%</div>',
			'labelUrl' => '<a id="%id%" class="%class%" href="%url%">%label%</a>',
			'labelNoUrl' => '<span id="%id%" class="%class%">%label%</span>'
			),
		'simple' => array(
			'menu' => '<ul>%items%</ul>',
			'item' => '<li>%label%%submenu%</li>',
			'labelUrl' => '<a href="%url%">%label%</a>',
			'labelNoUrl' => '<span>%label%</span>'
			)
		);

	/**
	 * Create a new Menu
	 *
	 * @param string $id The menu id
	 */
	public function __construct($id) {
		$this->id = $id;
	}

	public function addTemplate($name, array $template, $extend = 'default') {
		if($this->hasParentItem()) {
			throw new \Exception('addTemplate must be called on a root menu only.');
		}

		if(array_key_exists($name, $this->templates)) {
			throw new \Exception(sprintf('A template name %s already exists.', $name));
		}

		if(!array_key_exists($extend, $this->templates)) {
			throw new \Exception(sprintf('The template you are trying to extend (%s) don\'t exists.', $extend));
		}

		$this->templates[$name] = array_merge($this->templates[$extend], $template);

		return $this;
	}

	public function setTemplate($name) {
		$this->template = $name;
		return $this;
	}

	public function getTemplate($name, $element) {
		return $this->templates[$name][$element];
	}

	/** 
	 * Get the template for menu
	 *
	 * @return string The menu template
	 */
	public function getMenuTemplate() {
		if(!is_null($this->template)) {
			return $this->getRootMenu()->getTemplate($this->template, 'menu');
		}
		elseif($this->hasParentItem()) {
			return $this->getParentMenu()->getMenuTemplate();
		}
		else {
			return $this->getTemplate('default', 'menu');
		}
	}

	/**
	 * Get the template for item
	 *
	 * @return string The item template
	 */
	public function getItemTemplate() {
		if(!is_null($this->template)) {
			return $this->getRootMenu()->getTemplate($this->template, 'item');
		}
		elseif($this->hasParentItem()) {
			return $this->getParentMenu()->getItemTemplate();
		}
		else {
			return $this->getTemplate('default', 'item');
		}
	}

	/**
	 * Get the template for label with link
	 *
	 * @return string The label template
	 */
	public function getLabelUrlTemplate() {
		if(!is_null($this->template)) {
			return $this->getRootMenu()->getTemplate($this->template, 'labelUrl');
		}
		elseif($this->hasParentItem()) {
			return $this->getParentMenu()->getLabelUrlTemplate();
		}
		else {
			return $this->getTemplate('default', 'labelUrl');
		}
	}

	/**
	 * Get the template for label with no link
	 *
	 * @return string The label template
	 */ 
	public function getLabelNoUrlTemplate() {
		if(!is_null($this->template)) {
			return $this->getRootMenu()->getTemplate($this->template, 'labelNoUrl');
		}
		elseif($this->hasParentItem()) {
			return $this->getParentMenu()->getLabelNoUrlTemplate();
		}
		else {
			return $this->getTemplate('default', 'labelNoUrl');
		}
	}

	public function getRootMenu() {
		return $this->hasParentItem() ? $this->getParentMenu()->getRootMenu() : $this;
	}

	/**
	 * Get the Menu instance of the parent Item
	 *
	 * @return Menu The Menu instance of the parent Item
	 */
	public function getParentMenu() {
		return $this->hasParentItem() ? $this->getParentItem()->getMenu() : null;
	}

	/**
	 * Set the parent item
	 *
	 * @param Item $item The parent item
	 */
	public function setParentItem(Item $item) {
		$this->parentItem = $item;
	}

	/**
	 * @return Item The parent item
	 */
	public function getParentItem() {
		return $this->parentItem;
	}

	/**
	 * Alias of getParentItem
	 *
	 * @see Menu::getParentItem for the aliased method 
	 *
	 * @return Item The parent item
	 */
	public function end() {
		return $this->getParentItem();
	}

	/**
	 * Return true if this menu has a parent item
	 *
	 * @return bool Does this menu have a parent item
	 */
	public function hasParentItem() {
		return $this->parentItem instanceof Item;
	}

	/**
	 * Add an item to the menu
	 *
	 * @param string $label The item label
	 * @param string $url The item URL
	 * @param string $id The item id
	 *
	 * @return Item The item
	 */
	public function item($id, $label, $url = null) {
		$item = new Item($this, $id, $label, $url);
		$this->addItem($item);
		return $item;
	}

	public function addItem(Item $item) {
		$this->items[$item->getId()] = $item;
	}

	/**
	 * Returns the depth of this menu
	 *
	 * @return int Depth of the menu
	 */
	public function getDepth() {
		return $this->hasParentItem() ? $this->getParentMenu()->getDepth() + 1 : 0;
	}

	/**
	 * Returns the id of this menu
	 *
	 * @return string Id of the menu
	 */
	public function getId($recursive = false) {
		if(!$recursive || !$this->hasParentItem()) {
			return $this->id;
		}
		else {
			return $this->getParentMenu()->getId(true) . '-' . $this->id;
		}
	}

	/**
	 * Set the HTML id
	 *
	 * @param string $id HTML id
	 * @return Menu Return self instance
	 */
	public function setHtmlId($id) {
		$this->htmlId = $id;
		return $this;
	}

	/**
	 * Returns the HTML id of the menu
	 *
	 * @return string HTML id of the menu
	 */
	public function getHtmlId() {
		return !is_null($this->htmlId) ? $this->htmlId : $this->getId(true) . '-menu';
	}

	public function addHtmlClass($class) {
		$this->htmlClass[$class] = true;
		return $this;
	}

	/**
	 * Returns the HTML class of the menu
	 *
	 * @return string HTML class of the menu
	 */
	public function getHtmlClass() {
		return implode(' ', array_keys($this->htmlClass) + array('depth-' . $this->getDepth()));
	}

	/**
	 * Renders each items
	 *
	 * @return string The concatenation of all rendered items
	 */
	public function renderItems() {
		
		$output = null;

		foreach($this->items as $item) {
			$output .= $item->render();
		}

		return $output;
	}

	/**
	 * Renders the menu
	 *
	 * @return string The rendered menu
	 */
	public function render() {

		$template = $this->getMenuTemplate();

		$from = array('%id%', '%class%', '%items%', 'class=""');
		$to = array($this->getHtmlId(), $this->getHtmlClass(), $this->renderItems(), '');
		
		return str_replace($from, $to, $template);
	}
}
