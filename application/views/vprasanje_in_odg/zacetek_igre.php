<h3>S klikom na gumb se začne čas, ki odbija točke posameznega odgovora, odštevati. V primeru zvitega poskusa vrnitve na predhodno vprašanje z uporabo funkcije brskalnika se bodo vaše SKUPNE točke sfižile! Če igro zapustite, preden podate odgovor, bo točkovanje temu primerno.</h3>

<?php
echo form_open('Vprasanja/random_question');
echo form_submit('submit', 'Serviraj mi eno vprašanje!');
?>