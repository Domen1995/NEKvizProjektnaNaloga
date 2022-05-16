<div>

<!--<h2>Pazite! Ob zvitem poskusu vrnitve na predhodno vprašanje se bodo vaše skupne točke sfižile!</h2> -->

<?php
//$data['vprasanjeInOdg'] = "bla";
echo validation_errors();
echo form_open('vprasanja/fetch_answer/'.$vprasanjeIdOdg['sifra']);
echo "<div>";
echo $vprasanjeIdOdg['Vprasanje']; 
echo "</div>";
echo "<p>";
echo form_input('answer');
echo "</p>";
echo form_submit('submit', 'Oddaj odgovor!');
echo form_close();
?>
</div>