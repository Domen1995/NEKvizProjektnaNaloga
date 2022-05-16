<p> <?php echo "Pravilni odgovor: ";
		  echo $odgovorPravilni."<br>";
		  echo "Vaš odgovor: ";
		  echo $odgovorIgralca;
?> 
</p>
<p> <?php echo "Pravilnost odgovora: "; 
		  echo $pravilnostTekstovnegaOdg; 
		  echo "%.<br>";
		  echo "Čas: ";
		  echo $hitrostOdgovora;
		  echo " sekund.<br>";
		  echo "Točke odgovora: ";
		  echo $pravilnostTeksUpostevajocCas."%.";
		  ?></p>

<p>Izberite želeno področje naslednjega vprašanja: </p>

<a href="<?php echo site_url('Vadbena/random_question_podrocja/Knjizevnost_tekstovni'); ?>">Književnost</a>
<a href="<?php echo site_url('Vadbena/random_question_podrocja/Zgodovina_tekstovni'); ?>">Zgodovina</a>
<a href="<?php echo site_url('Vadbena/random_question_podrocja/Zabava_tekstovni'); ?>">Zabava</a>
<a href="<?php echo site_url('Vadbena/random_question_podrocja/Geografija_tekstovni'); ?>">Geografija</a>