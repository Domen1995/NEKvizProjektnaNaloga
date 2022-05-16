<div>
<?php
echo validation_errors();
echo form_open('Uporabniki/registriraj');
echo form_label('Vaš bodoči vzdevek: ');
echo "<br>";
echo form_input('vzdevek');
echo "<br>";
echo form_label('Domislite se še gesla: ');
echo "<br>";
echo form_password('geslo');
echo "<br>";
echo form_label('Pa nam zaupajte še svoj e-naslov: ');
echo form_input('enaslov');
echo "<br>";
if(isset($neveljavenEnaslov))
{
	echo $neveljavenEnaslov;
}elseif(isset($neveljavniPodatki))
{
	echo $neveljavniPodatki;
}
echo form_submit('submit', 'Registracija!');
echo form_close();
?>
</div>