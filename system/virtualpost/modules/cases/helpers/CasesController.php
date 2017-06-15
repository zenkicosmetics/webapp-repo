<?php

class CasesController {
	protected $parent;

	protected $case_data;

	public function __construct($parent, $case_data) {
		$this->parent = $parent;
		$this->case_data = $case_data;
	}
}