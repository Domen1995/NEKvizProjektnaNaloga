<?php

class Vadbena extends CI_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->model('Baza_vprasanj');
		$this->load->helper('url_helper');
		$this->load->helper('form');
		$this->load->library("form_validation");
		$this->load->library('session');
	}

	public function zacetek()
	{
		$this->load->view('header');
		$this->load->view('vprasanje_in_odg/zacetek_vadbene_igre');
		$this->load->view('footer');
	}

	public function random_question_podrocja($izbranoPodrocje)
	{
		$data['vprasanjeIdOdg'] = $this->Baza_vprasanj->random_question_from_database($izbranoPodrocje);
		$_SESSION['t0'] = microtime(true);
		$this->load->view('header');
		$this->load->view('vprasanje_in_odg/vpr_in_odg_vadbena', $data);
		$this->load->view('footer');
	}

	public function fetch_answer($sifra)
	{
		$t1 = microtime(true);
		$hitrostOdgovora = round(($t1 - $_SESSION['t0']), 2);
		$odgovorIgralca = $this->input->post('answer');
		$vprasanjeIdOdg = $this->Baza_vprasanj->question_from_database_from_sifra($sifra);
		$odgovorPravilni = $vprasanjeIdOdg['Odgovor'];
		$steviloEnakihCrk = $this->Baza_vprasanj->stevilo_enakih_crk($odgovorIgralca, $odgovorPravilni);
		$dolzinaPravilnegaOdg = strlen($odgovorPravilni);
		$pravilnostTekstovnegaOdg = $this->Baza_vprasanj->pravilnost_tekstovnega_odgovora($steviloEnakihCrk, $dolzinaPravilnegaOdg);
		$pravilnostTeksUpostevajocCas = $this->Baza_vprasanj->oceniUpostevajocCas($pravilnostTekstovnegaOdg, $hitrostOdgovora);
		$data['pravilnostTekstovnegaOdg'] = $pravilnostTekstovnegaOdg;
		$data['odgovorPravilni'] = $odgovorPravilni;
		$data['hitrostOdgovora'] = $hitrostOdgovora;
		$data['pravilnostTeksUpostevajocCas'] = $pravilnostTeksUpostevajocCas;
		$data['odgovorIgralca'] = $odgovorIgralca;
		//$this->Baza_vprasanj->shrani_odgovor($odgovorIgralca);
		$this->load->view('header');
		$this->load->view('vprasanje_in_odg/po_odgovoru_vadbenem', $data);
		$this->load->view('footer');
	}
}

?>