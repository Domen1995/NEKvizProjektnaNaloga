<?php

class Uvodna_stran extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->helper('url_helper');
		$this->load->library('session');
	}

	public function uvodnaStran()
	{
		$this->load->view('header');
		$this->load->view('pages/home');
		$this->load->view('footer');
	}
}

?>