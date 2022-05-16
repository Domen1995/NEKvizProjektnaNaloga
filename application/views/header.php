<!DOCTYPE html>
<html>
<head>
	<title>Kviz</title>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/css/style.css">
</head>
	<body>
		<ul>
		<?php if(isset($this->session->userdata['prijavljen']))	
				{ ?>
					<li><a href="<?php echo site_url('Uporabniki/zacetek_igre'); ?>">Točkovana vprašanja</a></li>
					<li><a href="<?php echo site_url('Ranking/moje_tocke'); ?>">Moje točke</a></li>
					<li><a href="<?php echo site_url('Uporabniki/resetirajMiTockeSvarilo'); ?>">Resetiranje točk</a></li>
					<li><a href="<?php echo site_url('Uporabniki/odjavi'); ?>">Odjava</a></li>
		<?php } else{ ?>
					<li><a id ="meniLevo" href="<?php echo site_url('Uporabniki/pokazi_obrazec_registracija'); ?>">Registracija</a></li>
				<li><a href="<?php echo site_url('Uporabniki/pokazi_obrazec_prijava'); ?>">Prijava</a></li>
		<?php } ?>
		<li><a href="<?php echo site_url('Ranking/vsiRazlicniVzdevki'); ?>">Rang lista</a></li>
		<li><a id="meniDesno" href="<?php echo site_url('Vadbena/zacetek') ?>">Vadbena igra</a></li>
		</ul>
		<br><br><br><br><br>