<?php

class Uporabniki_model extends CI_Model{

	public function __construct(){
		$this->load->database();
	}

	public function vnesi_registracijo($data)
	{
		$pogoj = "vzdevek = '".$data['vzdevek']."' OR eposta = '".$data['eposta']."'";
		$this->db->select('*');
		$this->db->from('Tekmovalec');
		$this->db->where($pogoj);
		$obstojeci = $this->db->get();
		if($obstojeci->num_rows() == 0)
		{
			$this->db->insert('Tekmovalec', $data);
			if($this->db->affected_rows() > 0)
			{
				return true;
			}
		}else
		{
			return false;
		}
	}

	public function preveri_podatke_prijave($data)
	{
		$pogoj = "vzdevek = '".$data['vzdevek']."' AND geslo = '".$data['geslo']."'";
		$this->db->select("*");
		$this->db->from('Tekmovalec');
		$this->db->where($pogoj);
		$tekmovalciSTemiPodatki = $this->db->get();
		if($tekmovalciSTemiPodatki->num_rows() == 1)
		{
			return true;
		}
		return false;
	}
}

?>