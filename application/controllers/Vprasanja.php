<?php

class Vprasanja extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('Baza_vprasanj');
		$this->load->helper('url_helper');
		$this->load->helper('form');
		$this->load->library('session');
		$this->load->library("form_validation");
	}

	function testek()
	{
		$tab = array("bla", "ble", "bli");
		unset($tab[1]);
		echo $tab[1];
		//$this->load->view('header');
		//$this->load->view('footer');
		//$_SESSION['item'] = 'bla';
		//echo $_SESSION['vzdevek'];
	}

	function viewHeader()
	{
		$t0 = microtime(true);
		usleep(2000000);
		$t1 = microtime(true);
		$casovnaRazlika = $t1-$t0;
		echo $casovnaRazlika;
		//$this->testek();
	}

	public function random_question()
	{
		//ZADNJE: IGRALCU točke na 0 v vseh vprašanjih; ne izbris vse njegove odg, sicer bi golfal in si resetiral vsa vpr, ko bi kakega falil.
		//if(!$this->preveriZadnjeVprasanje())
		$indeksPredzadnjegaVprasanja = $this->preveriZadnjeVprasanje();
		if($indeksPredzadnjegaVprasanja != 0)
		{
			$data['goljufija'] = "Hja, niste bili pridni ... Fiženje vaših skupnih točk je izvedeno. Nadaljujete, kot bi se znova registrirali.";
			//echo "Hja, niste bili pridni ... Fiženje vaših skupnih točk je izvedeno ...";
			//$this->Baza_vprasanj->izbrisiOdgovor($indeksPredzadnjegaVprasanja);
			$this->Baza_vprasanj->izbrisiVseOdgUporabnika();
			$this->load->view('header');
			$this->load->view('footer', $data);
			// izbrisali bomo predzadnje vprasanje z odgovorom. To je edino vprašanje z goljufivim odg. se bo dalo iti nazaj na še prejšnja? Dalo se bo. Samo da ni deljenja z 0 pri zgolfanem odg.
			// 

			//$_SESSION['nazajkliknjeno'] = !
		}else
		{
			$podrocje = $this->nakljucno_podrocje();
			$data['vprasanjeIdOdg'] = $this->Baza_vprasanj->random_question_from_database($podrocje);
			//$this->form_validation->set_rules('answer', 'Answer', 'required');
			//$this->form_validation->run();
			$this->Baza_vprasanj->shrani_vpr_brez_odg($data['vprasanjeIdOdg']['Vprasanje'], $data['vprasanjeIdOdg']['Odgovor']);
			$_SESSION['t0'] = microtime(true);
			$this->load->view('header');
			$this->load->view('vprasanje_in_odg/vprasanje_in_odgovor', $data);
			$this->load->view('footer');
		}
	}


	public function fetch_answer($sifra)
	{
		// AVTOMATSKO BO DOBIL DRUGO VPRAŠANJE, KO KLIKNE GUMB NAZAJ. ČE PA PRILEPI LINK
		// https://www.studenti.famnit.upr.si/~89181150/Kviz/CodeIgniter/index.php/vprasanja/fetch_answer/knj-txt2, BO DOBIL 0 TOČK.
		// če je prejšnji link vseboval fetch_answer, uporabnik goljufa. V sessionu je naveden ta link. Funkcija fetch_answer se izvede, ko gre za 1 puščico nazaj. Ne more pa preskočiti več kot 1, ker se bo pojavilo drugo vprašanje.
		// če je settan session v vprasanje_in_odg.php, uporabnik golfa. session unsetamo tik pred naslednjim vpr. v sessionu so podatki uporabnika. če uporabnik ne odgovori na naslednje vpr in daje puščice za nazaj, 
		// beleži se, katera vprašanja je uporabnik videl. 
		// če na novo vprašanje ni odgovora IN je v bazi kot (pred)zadnje vprašanje tisto, ki ga je spet dobil, ko poklikal nazaj, goljufa. v igralčevo bazo se vprašanje vpiše takoj ko se pojavi, pred odgovorom. Če je vprašanje brez odgovora in je potem dobil ravno istega, goljufa.


		// v igralčevo bazo se vprašanje vpiše takoj ko se pojavi, pred odgovorom, z oceno 0. ocena se popravi, ko odda odg.

		// Igralec loh še enkrat naloži stran. Piše "dokument je potekel", in ko klikne "poskusi znova", pišejo točke 100%. Ne more priti do vprašanja pred tem zadnjim, ne da bi sprožil napako pri zadnjem. Če je zadnje shranjeno vprašanje isto in ima isti čas kot predzadnje, diskvalificiramo igralca. V bazo so shranjeni vsi odgovori in njihovi časi. Ni pa sploh nobenega problema, ker se ne shrani vpr in odgovora v bazo, če uporabnik dobi ponovno dokument je potekel, ker se odpre le view in ne controller (fetch_answer se ne zažene še enkrat. SE ZAŽENE ŠE 1KRAT. Če bi se, bi morali pogledati, ali je odg enak zadnjemu pred tem in ima do desettisočinke enak čas), ki shrani odgovor. V vsakem primeru se odpre le view in ne controller, tudi če uporabnik kopira in prilepi link, ne bo niti shranilo njegovih 0 točk.

		//že v random_questionu daj v databazo vprašanje.

		// Če vprašanje, ki zapisano v bazo v random_question, že vsebuje odgovor, se novi ne vpiše. To velja za vsa vprašanja v zgodovini igralca. Le opozorjen je, če klika na "ponovno pošlji", ker ne more golfati. Ni treba preverjati, ali je bilo zadnje vpr isto kot trenutno ter čas isti. Kaj pa če igralec nalašč ne odgovori? Potem bo v odg. pisalo 0%. In s tem je odg. že zabeležen in ne bo mogel še enkrat odgovoriti. Če bi loh golfal tako da bi šel za več vprašanj nazaj - a ne more -, bi ga diskvalificirali že po prehodu za 1 nazaj. V random_question se vprašanje s praznim odgovorom vpiše v bazo. Samo za vprašanje z največjim indeksom v bazi uporabnika preverimo, če ima odg. Če ga ima, uporabnik goljufa. Preverimo na začetku fetch_answer.
		// po id-ju najdeš zadnje vprašanje, ki ga je uporabnik imel pred trenutnim.
		// vsa vprasanja do zdaj bodo imela neprazen odg. Ne more posodobiti nobenega odg iz preteklosti.
		// Takoj po potrjenju, da je odg prazen, se vpiše nekaj drugega v odg, še preden tekmovalec odda svoj odg.
		// če vpr isto kot prejšnje in čas do desettisočinke sekunde isti, diskval.
		/*
		if(!($this->Baza_vprasanj->zadnji_odgovor_igralca_prazen()))
		{
			echo "Goljufija!";
		}else
		{
			*/
			// če odgovor na zadnje vpr ni !?!Prazno!?!, uporabnik goljufa. če bo vprašanje videl prvič, bo zadnji odgovor zagotovo Prazno, ker se odgovor še ni shranil. Če vprašanja ne bo videl prvič, zagotovo ne bo v bazi odg Prazno, ker se novo vprašanje še ni izžrebalo. Ne, novo vprašanje se je izžrebalo, ko je kliknil "Naslednje vprašanje", in se (brez odgovora) dodalo zgodovini uporabnikovih vprašanj. Torej ne pomaga, če preveriš, ali je zadnje vpr brez odgovora, ker bo v vsakem primeru brez. Istočasno pa predzadnje vprašanje ne bo brez odg v nobenem primeru. Torej primerjaj, ali je vpr isto kot prejšnje in je odg. isti in nad 10% pravilen in v istem času v tisočinkah. Ko igralec ponovno zahteva prejšnjo stran, se izvede fetch_answer, ne le view, saj se ponovni odgovor shrani.
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
			$this->Baza_vprasanj->shrani_odg_k_vprasanju($odgovorIgralca, $pravilnostTekstovnegaOdg, $hitrostOdgovora, $pravilnostTeksUpostevajocCas);
			$this->load->view('header');
			$this->load->view('vprasanje_in_odg/po_odgovoru', $data);
			$this->load->view('footer');
		//}
		/*
		$data = array(
		 	'odgovorIgralca' => $this->input->post('answer'),
		 	//'vprasanje' => $VprasanjeIzolirano
		 	'sifra' => $sifra
		);
		/*
		$this->load->view('header');
		$this->load->view('testni', $data);
		$this->load->view('footer');
		*/
	}

	/*
	public function pravilnost_tekstovnega_odgovora($steviloEnakihCrk, $dolzinaPravilnegaOdg)
	{
		if($dolzinaPravilnegaOdg == 0)
		{
			return 0;
		}
		return round($steviloEnakihCrk/$dolzinaPravilnegaOdg*100, 2);
	}

	public function stevilo_enakih_crk($odgovorIgralca, $odgovorPravilni)
	{
		if($odgovorIgralca=="" || $odgovorPravilni==""){
	    	return 0;
	    }
	    $odgIgralcaBrezPrve = substr($odgovorIgralca, 1, strlen($odgovorIgralca)-1);
	    $odgPravilniBrezPrve = substr($odgovorPravilni, 1, strlen($odgovorPravilni)-1);

	    if(substr($odgovorIgralca, 0, 1) == substr($odgovorPravilni, 0, 1)){
	    	return 1 + $this->stevilo_enakih_crk($odgIgralcaBrezPrve, $odgPravilniBrezPrve);
	    }
	    return $this->stevilo_enakih_crk($odgIgralcaBrezPrve, $odgPravilniBrezPrve);
	}
	*/

	public function preveriZadnjeVprasanje() // PREDZADNJE!
		// Če bodo na novo izračunani procenti različni,
		// obvesti uporabnika o goljufiji. In nastavi se na 0 točk zadnje vprašanje.
		// Če se po naključju ponovi vprašanje, bo imel možnost pogoljufati z 1 klikom nazaj, 
		// a mu ne bo koristilo, ker bo odgovor tak, kot ga je že sam podal in bi istega ali boljšega
		//loh še enkrat podal.
		// predzadnje vprašanje preveri. Če igralec še nima za sabo 2 vprašanj, preskoči.
	{
		//$indeksZadnjegaVprasanja = $this->Baza_vprasanj->maksIdVprasanjaUporabnika();
		//$zadnjeVprasanjeTekmovalca = $this->Baza_vprasanj->zadnjeVprasanje($indeksZadnjegaVprasanja);
		$indeksPredzadnjegaVprasanja = $this->Baza_vprasanj->maksIdVprasanjaUporabnika() -1;
		$predzadnjeVprasanjeTekmovalca = $this->Baza_vprasanj->zadnjeVprasanje($indeksPredzadnjegaVprasanja);
		$odgovorTekmovalca = $predzadnjeVprasanjeTekmovalca['Odgovor'];
		$odgovorPravilni = $predzadnjeVprasanjeTekmovalca['pravilni_odgovor'];
		$steviloEnakihCrk = $this->Baza_vprasanj->stevilo_enakih_crk($odgovorTekmovalca, $odgovorPravilni);
		$dolzinaPravilnegaOdg = strlen($odgovorPravilni);
		$pravilnostTekstovnegaOdg = $this->Baza_vprasanj->pravilnost_tekstovnega_odgovora($steviloEnakihCrk, $dolzinaPravilnegaOdg);
		$prejZapisanaPravilnostOdgovora = $predzadnjeVprasanjeTekmovalca['procenti'];
		//return ($pravilnostTekstovnegaOdg == $prejZapisanaPravilnostOdgovora);
		if($pravilnostTekstovnegaOdg == $prejZapisanaPravilnostOdgovora)
		{
			return 0;
		}else
		{
			return $indeksPredzadnjegaVprasanja;
		}
	}

/*
	public function oceniUpostevajocCas($pravilnostTekstovnegaOdg, $hitrostOdgovora)
	{
		$ocena = $pravilnostTekstovnegaOdg;
		$i = 0;
		while($ocena>20 && $i<5)
		{
			$ocena = $ocena - $hitrostOdgovora;
			$i++;
		}
		return $ocena;
		/*
		if($ocena<)
		if($pravilnostTekstovnegaOdg > 50 && $pravilnostTekstovnegaOdg - 5*$hitrostOdgovora > 20)
		{
			$pravilnostTekstovnegaOdg = $pravilnostTekstovnegaOdg - 5*$hitrostOdgovora;
		}
		return $pravilnostTekstovnegaOdg;
		
	}
	*/

	public function nakljucno_podrocje()
	{
		$enaDo4 = rand(1, 4);
		switch($enaDo4)
		{
			case 1:
				return "Knjizevnost_tekstovni";
			case 2:
				return "Zgodovina_tekstovni";
			case 3:
				return "Zabava_tekstovni";
			case 4:
				return "Geografija_tekstovni";
		}
	}

/*
	public function random_question()
	{
		$data['vprasanjeIdOdg'] = $this->Baza_vprasanj->random_question_from_database();
		$this->form_validation->set_rules('answer', 'Answer', 'required');
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('header');
			$this->load->view('vprasanje_in_odg/vprasanje_in_odgovor', $data);
			$this->load->view('footer');
		}else
		{
			//$answer = $this->input->post('answer');
			$data['vprasanjeInOdg'] = "bla";
			$this->load->view('header');
			$this->load->view('testni', $data);
			$this->load->view('footer');
		}
	}
/*
	public function fetch_answer()
	{
		$answer = $this->input->post('answer');
		$data['vprasanjeInOdg'] = "bla";
		$this->load->view('header');
		$this->load->view('testni', $data);
		$this->load->view('footer');
	}
	*/
}

?>