<?php
	if ( current_user_can( 'activate_plugins' ) ) {
		?>
		<div class="sv_section_description"><?php echo $module->get_section_desc(); ?></div>
		<div class="sv_setting_flex">
			<?php
				echo $module->get_setting('posts')->form();
				echo $module->get_setting('comments')->form();
				echo $module->get_setting('date_after')->form();
			?>
		</div>
		<?php
	}
?>