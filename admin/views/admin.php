<?php
/**
 * The view for the administration dashboard.
 *
 * @package   Divi Page Builder Integration
 * @author    Pete Molinero <pete@laternastudio.com>
 * @license   GPL-2.0+
 * @link      http://www.laternastudio.com
 * @copyright 2014 Laterna Studio
 */
?>

<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<form method="post" action="options.php" enctype="multipart/form-data">
		<?php settings_fields('dbi_settings') ?>
		<?php do_settings_sections($this->plugin_slug) ?>

		<p class="submit">
			<input name="submit" type="submit" class="button-primary" value="Save Changes" />
		</p>
	</form>


</div>
