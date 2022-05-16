<?php

class Ranking extends CI_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->model('ranking_model');
		$this->load->model('Baza_vprasanj');
		$this->load->helper('url_helper');
		$this->load->library('session');
	}

	public function vsiRazlicniVzdevki()
		// ustvari tabelo uporabnik + tocke + mesto
	{
		//$procenti = $this->ranking_model->skupneTockeEnegaTekmovalca($vzdev);
		$tekmovalciRowArray = $this->ranking_model->vsiRazlicniTekmovalci();
		$tekmovalciProcenti = array();
		$procenti = array();
		foreach($tekmovalciRowArray as $tekmovalec)
		{
			$procentiTrenutnegaTekmovalca = $this->procentiEnegaTekmovalca($tekmovalec);
			if($procentiTrenutnegaTekmovalca == -1)
			{
				continue;
			}
			$tekmovalecProcenti = array($tekmovalec, $procentiTrenutnegaTekmovalca);
			array_push($tekmovalciProcenti, $tekmovalecProcenti);
			array_push($procenti, $procentiTrenutnegaTekmovalca);
		}
		$sortiraniProcenti = $this->procentiPadajoce($procenti);
		$tekmovalciProcentiPadaj = $this->tekmovalciProcentiPadajoce($tekmovalciProcenti, $sortiraniProcenti);
		$data['tekmovalciProcenti'] = $tekmovalciProcentiPadaj;
		$maksIdVprasanjaUporabnika = $this->Baza_vprasanj->maksIdVprasanjaUporabnika();
		if($maksIdVprasanjaUporabnika < 40)
		{
			$manjkajocihVprasanj = 40-$maksIdVprasanjaUporabnika;
			$data['manjkajocihVprasanj'] = $manjkajocihVprasanj;
		}
		$this->load->view('header');
		$this->load->view('rang lista/ranklist', $data);
		$this->load->view('footer');
	}

	public function procentiEnegaTekmovalca($vzdev)
	{
		$procenti = $this->ranking_model->skupneTockeEnegaTekmovalcaCeVecKot40($vzdev);
		return $procenti;
	}

	public function procentiPadajoce($procenti)
	{
		sort($procenti);
		$procenti = $this->obrniPolje($procenti);
		return $procenti;
	}

	public function tekmovalciProcentiPadajoce($tekmovalciProcenti, $sortiraniProcenti)
		// 3. komponenta je mesto na ranglisti
	{
		$tekmProcPadajoce = array();
		for($i=0; $i<count($sortiraniProcenti); $i++)
		{
			for($j=0; $j<count($tekmovalciProcenti); $j++)
			{
				if($sortiraniProcenti[$i] == $tekmovalciProcenti[$j][1])
				{
					$tekmovalecProcent = array($tekmovalciProcenti[$j][0], $sortiraniProcenti[$i], $i+1);
					array_push($tekmProcPadajoce, $tekmovalecProcent);
					$tekmovalciProcenti[$j][1] = -1;
					break;
				}
			}
		}
		return $tekmProcPadajoce;
	}

	public function obrniPolje($polje)
	{
		$obrnjeno = array();
		for($i = count($polje)-1; $i>=0; $i--)
		{
			array_push($obrnjeno, $polje[$i]);
		}
		return $obrnjeno;
	}

	public function moje_tocke()
	{
		$mojVzdevek = $_SESSION['vzdevek'];
		//$procenti = $this->procentiEnegaTekmovalca($mojVzdevek);
		$procenti = $this->ranking_model->skupneTockeEnegaTekmovalca($mojVzdevek);
		$data['mojVzdevek'] = $mojVzdevek;
		$data['mojeTocke'] = $procenti;
		$this->load->view('header');
		$this->load->view('rang lista/moje_tocke', $data);
		$this->load->view('footer');
	}
}

?>