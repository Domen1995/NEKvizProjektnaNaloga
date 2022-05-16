<?php

class Uporabniki extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('uporabniki_model');
		$this->load->model('Baza_vprasanj');  // Samo za izbris vseh odg na uporabnikovo željo
		$this->load->helper('url_helper');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->library('session');
	}

	public function pokazi_obrazec_registracija()
	{
		$this->load->view('header');
		$this->load->view('uporabniski_obrazci/registracija');
		$this->load->view('footer');
	}

	public function registriraj()
	{
		$this->form_validation->set_rules('vzdevek', 'Vzdevek', 'required');
		$this->form_validation->set_rules('geslo', 'Geslo', 'required');
		$this->form_validation->set_rules('enaslov', 'Enaslov', 'required');
		if($this->form_validation->run() == FALSE)
		{
			$data['neveljavniPodatki'] = "Neveljavni podatki! Poskusite ponovno ...";
			$this->load->view('header');
			$this->load->view('uporabniski_obrazci/registracija', $data);
			$this->load->view('footer');
		}else
		{
			$data = array(
				'vzdevek' => $this->input->post('vzdevek'),
				'geslo' => $this->input->post('geslo'),
				'eposta' => $this->input->post('enaslov')
			);
			if(!$this->eNaslovVeljaven($data['eposta']))
			{
				$data['neveljavenEnaslov'] = "Vnesite SVOJ e-naslov!!!";
				$this->load->view('header');
				$this->load->view('uporabniski_obrazci/registracija', $data);
				$this->load->view('footer');
			}else{
				$registracijaUspela = $this->uporabniki_model->vnesi_registracijo($data);
				if($registracijaUspela)
				{
					//echo "registracija uspela";
					$ravnoRegistriran['ravnokarRegistriran'] = "S temi podatki se odslej, začenši zdaj, prijavljajte: ";
					$this->load->view('header');
					$this->load->view('uporabniski_obrazci/prijava', $ravnoRegistriran);
					$this->load->view('footer');
				}else{
					$data['neveljavniPodatki'] = "Vnesli ste podatke že obstoječega uporabnika!";
					$this->load->view('header');
					$this->load->view('uporabniski_obrazci/registracija', $data);
					$this->load->view('footer');
				}
			}
		}
	}

	public function eNaslovVeljaven($eNaslov)
	{
		$i=0;
		for(; $i<strlen($eNaslov); $i++){
			if(substr($eNaslov, $i, 1)=="@"){
		    	break;
		    }
		}
		if($i>strlen($eNaslov)-6)
		{
			return false;
		}
		return true;
	}

	public function pokazi_obrazec_prijava()
	{
		$this->load->view('header');
		$this->load->view('uporabniski_obrazci/prijava');
		$this->load->view('footer');
	}

	public function prijavi()
	{
		$this->form_validation->set_rules('vzdevek', 'Vzdevek', 'required');
		$this->form_validation->set_rules('geslo', 'Geslo', 'required');
		if($this->form_validation->run() == FALSE)
		{
			$data['neveljavniPodatki'] = "Neveljavni podatki! Poskusite ponovno ...";
			$this->load->view('header');
			$this->load->view('uporabniski_obrazci/prijava', $data);
			$this->load->view('footer');
		}else{
			$data = array(
				'vzdevek' => $this->input->post('vzdevek'),
				'geslo' => $this->input->post('geslo')
			);
			if($this->uporabniki_model->preveri_podatke_prijave($data))
			{
				$this->session->set_userdata('prijavljen', $data);
				//$_SESSION['itemm'] = 'blo';
				$_SESSION['vzdevek'] = $data['vzdevek'];
				$this->zacetek_igre();
				/*
				$this->load->view('header');
				$this->load->view('vprasanje_in_odg/zacetek_igre');
				$this->load->view('footer');
				*/
			}else
			{
				$data['neveljavniPodatki'] = "Neveljavni podatki! Poskusite ponovno ...";
				$this->load->view('header');
				$this->load->view('uporabniski_obrazci/prijava', $data);
				$this->load->view('footer');
			}
		}
	}

	public function odjavi()
	{
		$userdata = array(
			'vzdevek' => ''
		);
		$this->session->unset_userdata('prijavljen', $userdata);
		$this->load->view('header');
		$this->load->view('footer');
	}

	public function zacetek_igre()
	{
		$this->load->view('header');
		$this->load->view('vprasanje_in_odg/zacetek_igre');
		$this->load->view('footer');
	}

	public function resetirajMiTockeSvarilo()
	{
		$this->load->view('header');
		$this->load->view('uporabniski_obrazci/resetiranje_tock_obrazec');
		$this->load->view('footer');
	}

	public function resetTock()
	{
		$this->Baza_vprasanj->izbrisiVseOdgUporabnika();
		$data['vsiOdgIzbrisani'] = "Vaši dosedanji odgovori so izbrisani. V vnovičnem poskusu pa gremo do perfekcije!";
		$this->load->view('header');
		$this->load->view('pages/home', $data);
		$this->load->view('footer');
	}
}

?>