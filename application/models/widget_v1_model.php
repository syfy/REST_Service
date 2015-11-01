<?php

include_once(APPPATH."dtos/widget_v1_service.php");

class Widget_v1_model extends CI_Model {
	private $widget_id;

	function __construct()
	{
		parent::__construct();
		$widget_id = '';
		$widget_details = null;
	}

	function initialize($widget_id)
	{
		$this->widget_id = $widget_id;		
	}
	
	function get() {
						
		if ($this->isValidWidget()) {
			$widget_v1_service = $this->_getWidget();
			if ($widget_v1_service->isValid()) {	
				return $widget_v1_service;
			}
		}	
		return null;
	}

	function isValidWidget() {
		if (is_numeric($this->widget_id) && $this->widget_id >= 1) {
			return true;
		}
		return false;
	}
	
	private function _getWidget()
	{		
		$widget_v1_service = new widget_v1_service();
		$widget_v1_service->addRequestParam('id', $this->widget_id);

		$widget_v1_dto = null;
		$widget_v1_dto = new widget_v1_dto();
		$widget_v1_dto->id = $this->widget_id;
		$widget_v1_dto->name = "sprocket";
		$widget_v1_dto->type = "part";
		$widget_v1_dto->price = "41.99";

		$widget_v1_service->widget = $widget_v1_dto;
				
		return $widget_v1_service;
	}		
}

/* End of file widget_v1_model.php */
/* Location: ./system/application/models/widget_v1_model.php */