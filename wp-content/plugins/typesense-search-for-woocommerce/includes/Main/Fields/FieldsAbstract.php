<?php

namespace Codemanas\Typesense\WooCommerce\Main\Fields;

abstract class FieldsAbstract implements FieldsInterface {
	
	public function __construct() {
	}

	/**
	 * @var Fields 
	 */
	protected Fields $fields;
}