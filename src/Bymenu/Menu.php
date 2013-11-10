<?php

namespace Bymenu;

class Menu
{
	private $id;
	private $items = array();
	private $parentItem;

	public function __construct($id) {
		$this->id = $id;
	}

	public function setParentItem(Item $item) {
		$this->parentItem = $item;
	}

	public function item($label, $id) {
		$item = new Item($label, $id);
		$item->setMenu($this);
		$this->items[] = $item;
		return $item;
	}

	public function end() {
		return $this->parentItem;
	}

	public function render() {
		
		$output = '<ul>';
		
		foreach($this->items as $item) {
			$output .= $item->render();
		}
		
		$output .= '</ul>';

		return $output;
	}
}


/*
$nav = new Menu('top');
$nav
	->item('accueil')
	->item('le-siell')
		->menu('le-siell')
			->item('notre-histoire')
			->item('notre-territoire')
			->item('les-instances')
				->menu('les-instances')
					->item('le-bureau')
					->item('comite')
				->end()
			->item('notre-organisation')
		->end()
	->item('developpement-durable')
		->menu('developpement-durable')
			->item('investissement')
			->item('protection')
			->item('renouvellement')
			->item('rendement')
			->item('traitement')
		->end()
	->item('notre-metier')
		->menu('notre-metier')
	 		->item('chiffres')
 			->item('origine')
 			->item('sites')
 				->menu('sites')
					->item('deuxnouds')
					->item('dompierre')
					->item('troyon')
				->end()
 			->item('transport')
 				->menu('transport')
					->item('chiffres')
					->item('stockage')
					->item('branchement')
				->end()
		->end()
	->item('infos')
		->menu('infos')
			->item('actualite')
			->item('documents')
			->item('observatoire')
				->menu('observatoire')
					->item('rapport')
					->item('qualite')
				->end()
			->item('faq')
			->item('deliberations')
			->item('marches')
			->item('presse')
			->item('textes')
		->end()
	->item('leau-chez-moi')
		->menu('leau-chez-moi')
			->item('quotidien')
			->item('robinet')
			->item('informations')
			->item('prix')
			->item('comprendre')
			->item('payer')
	;

$nav->render();

*/