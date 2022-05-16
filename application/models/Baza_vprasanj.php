<?php

class Baza_vprasanj extends CI_Model{

	public function __construct()
	{
		$this->load->database();
	}

	public function random_question_from_database($podrocje)
	{
		$this->db->select('max(id) as "stev"');
		$this->db->from($podrocje);  // tu je bilo Knjizevnost_tekstovni
		$steviloVprasanj = $this->db->get()->row_array();// row_array je 1D tabela, result_array je 2D
		$steviloVpr = intval($steviloVprasanj['stev']);
		$nakljucniIndeksVprasanja = rand(1, $steviloVpr);

		$pogoj = "id = '".$nakljucniIndeksVprasanja."'";
		//$pogoj = "id = ".$steviloVpr;
		//var_dump(intval($steviloVprasanj['stev']));
		//$query = $this->db->get_where('Knjizevnost_tekstovni', array('id' => 1));
		$this->db->select('*');
		$this->db->from($podrocje);
		$this->db->where($pogoj);
		$vprasanje = $this->db->get();
		return $vprasanje->row_array();
		/*
		//$query = $this->db->get();
		$data = array()
		$query = $this->db->get_where('Knjizevnost_tekstovni', array('id' => 1));
		return $query->result_array();
		*/
	}

	public function question_from_database_from_sifra($sifra)
	{
		$prve3CrkePodrocja = substr($sifra, 0, 3);
		switch($prve3CrkePodrocja)
		{
			case "knj":
				$podrocje = "Knjizevnost_tekstovni";
				break;
			case "zgo":
				$podrocje = "Zgodovina_tekstovni";
				break;
			case "zab":
				$podrocje = "Zabava_tekstovni";
				break;
			case "geo":
				$podrocje = "Geografija_tekstovni";
		}
		$pogoj = "sifra= '".$sifra."'";
		$this->db->select('*');
		//$this->db->from('Knjizevnost_tekstovni');
		$this->db->from($podrocje);
		$this->db->where($pogoj);
		$vprasanje = $this->db->get();
		return $vprasanje->row_array();
	}

	public function shrani_odgovor($odgovor)
	{
		$data = array(
			'Odgovor' => $odgovor,
			'Vprasanje' => 'ni vazno',
			'id' => 2
		);
		$this->db->insert('Odgovori_test', $data);
	}

	public function shrani_vpr_brez_odg($vprasanje, $pravilniOdgovor)
	{
		$pogoj = "tekmovalec = '".$_SESSION['vzdevek']."'";
		$this->db->select('max(id) as "stev"');
		$this->db->from('Odgovori_test');
		$this->db->where($pogoj);
		$indeksZadnjegaVprasanja = $this->db->get()->row_array();// row_array je 1D tabela, result_array je 2D
		$indeksZadnjegaVprasanja = intval($indeksZadnjegaVprasanja['stev']);
		//$nakljucnaSifra = rand(0, 1000000);
		$data = array(
			'Odgovor' => '',
			'Vprasanje' => $vprasanje,
			'id' => $indeksZadnjegaVprasanja + 1,
			'tekmovalec' => $_SESSION['vzdevek'],
			'procenti' => 0,
			'pravilni_odgovor' => $pravilniOdgovor
			//'nakljucnaSifra' => $nakljucnaSifra
		);
		$this->db->insert('Odgovori_test', $data);
	}

	public function shrani_odg_k_vprasanju($odgovor, $procenti, $hitrost, $skupni_procenti)  //tudi procente shrani
	{
		/*
		$pogoj = "tekmovalec = '".$_SESSION['vzdevek']."'";
		$this->db->select('max(id) as "stev"');
		$this->db->from('Odgovori_test');
		$this->db->where($pogoj);
		$indeksZadnjegaVprasanja = $this->db->get()->row_array();
		$indeksZadnjegaVprasanja = intval($indeksZadnjegaVprasanja['stev']);
		*/
		$pogoj = "tekmovalec = '".$_SESSION['vzdevek']."'";
		$indeksZadnjegaVprasanja = $this->maksIdVprasanjaUporabnika();
		$pogoj = $pogoj." AND id = '".$indeksZadnjegaVprasanja."'";
		$this->db->select('*');
		$this->db->from('Odgovori_test');
		$this->db->where($pogoj);
		$zadnjeVprasanje = $this->db->get()->row_array();

		//$nakljucnaSifra = rand(0, 1000000);
		$data['Odgovor'] = $odgovor;
		$data['procenti'] = $procenti;
		$data['hitrost'] = $hitrost;
		$data['skupni_procenti'] = $skupni_procenti;
		//$data['nakljucnaSifra'] = $nakljucnaSifra;
		//$this->db->set('Odgovor', $odgovor);
		$this->db->set($data);
		$this->db->where($pogoj);
		$this->db->update('Odgovori_test');
	}

	/*
	public function preveriZadnjeVprasanje()  
		// Če bodo na novo izračunani procenti različni,
		// obvesti uporabnika o goljufiji. In nastavi se na 0 točk zadnje vprašanje.
		// Če se po naključju ponovi vprašanje, bo imel možnost pogoljufati z 1 klikom nazaj, 
		// a mu ne bo koristilo, ker bo odgovor tak, kot ga je že sam podal in bi istega ali boljšega
		//loh še enkrat podal.
	{
		$indeksZadnjegaVprasanja = $this->maksIdVprasanjaUporabnika();
		$zadnjeVprasanjeTekmovalca = $this->zadnjeVprasanje($indeksZadnjegaVprasanja);
	}
	*/

	public function maksIdVprasanjaUporabnika()
	{
		$pogoj = "tekmovalec = '".$_SESSION['vzdevek']."'";
		$this->db->select('max(id) as "stev"');
		$this->db->from('Odgovori_test');
		$this->db->where($pogoj);
		$indeksZadnjegaVprasanja = $this->db->get()->row_array();
		$indeksZadnjegaVprasanja = intval($indeksZadnjegaVprasanja['stev']);
		return $indeksZadnjegaVprasanja;
	}

	public function zadnjeVprasanje($indeksZadnjegaVprasanja)
	{
		$pogoj = "tekmovalec = '".$_SESSION['vzdevek']."'";
		$pogoj = $pogoj." AND id = '".$indeksZadnjegaVprasanja."'";
		$this->db->select('*');
		$this->db->from('Odgovori_test');
		$this->db->where($pogoj);
		$zadnjeVprasanje = $this->db->get()->row_array();
		return $zadnjeVprasanje;
	}

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

	public function oceniUpostevajocCas($pravilnostTekstovnegaOdg, $hitrostOdgovora)
	{
		$ocena = $pravilnostTekstovnegaOdg;
		$i = 0;
		while($ocena-$hitrostOdgovora>20 && $i<5)
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
		*/
	}

	public function izbrisiOdgovor($id)
	{
		/*
		$pogoj = "tekmovalec = '".$_SESSION['vzdevek']."' AND id = '".$id."'";
		$data['tekmovalec'] = $_SESSION['vzdevek'];
		$data['id'] = $id;
		*/
		$data = array('tekmovalec' => $_SESSION['vzdevek'], 'id' => $id);
		$this->db->where($data);
		$this->db->delete("Odgovori_test");
	}

	public function izbrisiVseOdgUporabnika()
	{
		$data = array('tekmovalec' => $_SESSION['vzdevek']);
		$this->db->where($data);
		$this->db->delete('Odgovori_test');
	}

	/*
	public function nedovoljena_ponovitev_odgovora()
	{

	}
	*/

	/*
	public function zadnji_odgovor_igralca_prazen()
	{
		$this->db->select('max(id) as "stev"');
		$this->db->from('Odgovori_test');
		$indeksZadnjegaVprasanja = $this->db->get()->row_array();// row_array je 1D tabela, result_array je 2D
		$indeksZadnjegaVprasanja = intval($indeksZadnjegaVprasanja['stev']);
		$pogoj = "id = '".$indeksZadnjegaVprasanja."'";
		$this->db->select('Odgovor');
		$this->db->from('Odgovori_test');
		$this->db->where($pogoj);
		$odgovor = $this->db->get()->row_array();
		$odgovor = $odgovor['Odgovor'];
		if($odgovor == "!?!Prazno!?!")
		{
			return true;
		}
		return false;
	}
	*/
}

?>