<!-- NE TEGA. UPORABNIK MORDA NE SME VEDETI, DA SE JIH TOČKUJE LE ZADNJIH X. -->
<h3>Vsi odgovori, ki ste jih podali, bodo izbrisani. Nič drastičnega, saj se jih pri točkovanju upošteva le zadnjih 40.</h3>

<?php
echo form_open('Uporabniki/resetTock');
echo form_submit('submit', 'Resetiraj mi točke.');
echo form_close();
?>