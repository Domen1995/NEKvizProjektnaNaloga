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
		  echo "Sedaj lahko igro zapustite brez izgube točk!";
		  ?></p>

<?php
echo form_open('vprasanja/random_question');
echo form_submit('submit', "Naslednje vprašanje.");
echo form_close();
?>