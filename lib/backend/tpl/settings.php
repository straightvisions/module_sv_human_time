<?php
	if ( current_user_can( 'activate_plugins' ) ) {
		?>
		<div class="sv_section_description"><?php echo $module->get_section_desc(); ?></div>
		<div class="sv_setting_flex">
			<?php
				echo $module->get_settings()['posts']->run_type()->form();
				echo $module->get_settings()['comments']->run_type()->form();
				echo $module->get_settings()['date_after']->run_type()->form();
			?>
		</div>
		<?php
	}
?>