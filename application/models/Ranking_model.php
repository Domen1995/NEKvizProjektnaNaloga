<?php

class Ranking_model extends CI_Model{

	public function __construct(){
		$this->load->database();
	}

	public function vsiRazlicniTekmovalci()
	{
		$this->db->select('vzdevek');
		$this->db->from('Tekmovalec');
		$tabVzdevkov = $this->db->get()->result_array();
		$tabVzdevkovStripped = array();
		foreach ($tabVzdevkov as $vzdevek)
		{
			array_push($tabVzdevkovStripped, $vzdevek['vzdevek']);
		}
		return $tabVzdevkovStripped;
	}

	public function skupneTockeEnegaTekmovalcaCeVecKot40($vzdevek)
	{
		$maksIndeks = $this->maxIndex($vzdevek);
		if($maksIndeks < 40)
		{
			return -1;
		}
		return $this->skupneTockeEnegaTekmovalca($vzdevek);
	}

	public function skupneTockeEnegaTekmovalca($vzdevek)
	{
		$maksIndeks = $this->maxIndex($vzdevek);
		$odVprasanjaNaprej = $maksIndeks;
		if($odVprasanjaNaprej > 40)
		{
			$odVprasanjaNaprej = $odVprasanjaNaprej - 40;
		}else
		{
			$odVprasanjaNaprej = 1;
		}
		$pogoj = "tekmovalec = '".$vzdevek."' AND id >= '".$odVprasanjaNaprej."'";
		//$this->db->select('sum(procenti) as sumProcenti');
		$this->db->select('sum(skupni_procenti) as sumProcenti');
		$this->db->from('Odgovori_test');
		$this->db->where($pogoj);
		$sumProcenti = $this->db->get()->row_array();
		$sumProcenti = intval($sumProcenti['sumProcenti']);
		if($maksIndeks > 40)
		{
			$procenti = $sumProcenti/40;
		}else{
			if($maksIndeks == 0)
			{
				$procenti = 0;
			}else
			{
				$procenti = $sumProcenti/$maksIndeks;
			}
		}
		$procenti = (round($procenti, 2));
		return $procenti;
	}

	public function maxIndex($vzdevek)
	{
		$pogoj = "tekmovalec = '".$vzdevek."'";
		$this->db->select('max(id) AS maksid');
		$this->db->from('Odgovori_test');
		$this->db->where($pogoj);
		$maksIndeks = $this->db->get()->row_array();
		$maksIndeks = intval($maksIndeks['maksid']);
		/*
		if($maksIndeks>100)
		{
			$maksIndeks = $maksIndeks-100;
		}
		*/
		return $maksIndeks;
	}
}

?>