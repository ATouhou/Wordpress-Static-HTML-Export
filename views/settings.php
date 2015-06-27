<div class="wrap">
<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
 
<form method="POST">
	<table class="form-table"><tbody>
		<tr>
			<th scope="row">
				<label for="new_site_url">Neue Adresse (URL)</label>
			</td>
			<td>
				<input name="new_site_url" type="url" id="new_site_url" value="<?php echo htmlspecialchars($options['new_site_url']); ?>" class="regular-text code">
				<p class="description">Die neue Adresse (URL) unter der die statische Webseite erreichbar sein wird.</p>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="output_dir">Ziel-Ordner</label>
			</td>
			<td>
				<input name="output_dir" type="text" id="output_dir" value="<?php echo htmlspecialchars($options['output_dir']); ?>" class="regular-text code">
				<p class="description">Der Ordner, indem die statische Webseite gespeichert werden soll.</p>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="crawl_rounds">Crawling-Tiefe</label>
			</td>
			<td>
				<input name="crawl_rounds" type="number" id="crawl_rounds" value="<?php echo (int)$options['crawl_rounds']; ?>" class="regular-text code">
				<p class="description">Standardmäßig liegt dieser Wert bei 3, was in den meisten Fällen reichen sollte.</p>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="submit" name="submit" id="submit" class="button button-primary" value="Änderungen übernehmen">
			</td>
		</tr>
	</tbody></table>

	<?php wp_nonce_field(); ?>
</form>
<hr>
<form method="POST">

<table class="form-table"><tbody>
	<tr>
		<td colspan="2">
		<?php if(isset($run_status) && $run_status == 'done'){ ?>
			<div class="notice-success notice is-dismissible">
				Die Seite wurde nach <strong><?php echo htmlspecialchars($options['output_dir']); ?></strong> exportiert.
			</div>
		<?php } ?>
		</td>
	</tr>
	<tr>
		<th scope="row">
			Webseite exportieren
		</th>
		<td>
			<input type="submit" name="run" class="button button-primary button-hero" value="Export starten">
			<p class="description">Exportiert die Webseite als statisches HTML.</p>
		</td>
	</tr>
</tbody></table>

</form>
</div>