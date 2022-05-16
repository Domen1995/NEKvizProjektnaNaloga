<p>
<h3>Na listo so uvrščeni tekmovalci, ki so podali najmanj 40 odgovorov.</h3>
<?php 
if(isset($this->session->userdata['prijavljen']) && isset($manjkajocihVprasanj))
{?>
	<p>Vam jih manjka še <?php echo $manjkajocihVprasanj?>.</p>
<?php }?>

<table>
	<tr>
		<th>Uvrstitev</th>
		<th>Tekmovalec</th>
		<th>Skupne točke</th>
	</tr>
	<?php
	foreach($tekmovalciProcenti as $tekmovalecProcenti)
	{
		echo "<tr>";
		echo "<td>";
		echo $tekmovalecProcenti[2];
		echo "</td>";
		echo "<td>";
		echo $tekmovalecProcenti[0];
		echo "</td>";
		echo "<td>";
		echo $tekmovalecProcenti[1];
		echo "</td>";
		echo "</tr>";
	}
	?>
</table>
</p>