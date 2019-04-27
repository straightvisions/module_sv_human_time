<?php
namespace sv_100;

/**
 * @version         1.00
 * @author			straightvisions GmbH
 * @package			sv_100
 * @copyright		2017 straightvisions GmbH
 * @link			https://straightvisions.com
 * @since			1.0
 * @license			See license.txt or https://straightvisions.com
 */

class sv_human_time_diff extends init {
	public function __construct() {

	}

	public function init() {
		// Translates the module
		load_theme_textdomain( $this->get_module_name(), $this->get_path( 'languages' ) );

		// Module Info
		$this->set_module_title( 'SV Human Time Diff' );
		$this->set_module_desc( __( 'This module converts dates and times to a human readable text, via the "[sv_human_time_diff]" shortcode.', $this->get_module_name() ) );

		// Section Info
		$this->set_section_title( 'Human Time Diff' );
		$this->set_section_desc( __( 'Settings', $this->get_module_name() ) );
		$this->set_section_type( 'settings' );
		$this->get_root()->add_section( $this );

		// Load settings, register scripts and sidebars
		$this->load_settings();

		// Shortcodes
		add_shortcode( $this->get_module_name(), array( $this, 'shortcode' ) );
	}

	protected function load_settings(): sv_human_time_diff {
		$this->s['date_after'] =
			static::$settings
				->create( $this )
				->set_ID( 'date_after' )
				->set_title( 'Show Date Format' )
				->set_description( __( 'Shows the date and time in the default WordPress format, when the time difference is higher than the set days.', $this->get_module_name() ) )
				->set_default_value( 7 )
				->set_min( 1 )
				->load_type( 'number' );

		return $this;
	}

	public function shortcode( $settings, $content = '' ): string {
		$settings								= shortcode_atts(
			array(
				'date_start'				=> false,
				'date_end'                  => false,
				'date_after'                => false,
			),
			$settings,
			$this->get_module_name()
		);

		return $this->router( $settings );
	}

	protected function router( array $settings ): string {
		if ( $settings['date_start'] ) {
			$date_start = strtotime( $settings['date_start'] );
		} else {
			$date_start = get_post_time();
		}

		if ( $settings['date_end'] ) {
			$date_end   = strtotime( $settings['date_end'] );
		} else {
			$date_end   = current_time( 'timestamp' );
		}

		$date_after = $settings['date_after'] ? $settings['date_after'] : $this->s['date_after']->run_type()->get_data();

		// Time difference between post date and current date, in days
		$time_diff = round( ( $date_end - $date_start ) / ( 60 * 60 * 24 ) );

		if ( $time_diff > $date_after ) {
			$date = get_the_date();
		} else {
			$date = human_time_diff( $date_start, $date_end );

			switch ( get_locale() ) {
				case 'de_DE':
					$date = 'vor ' . $date;
					break;
				case 'en_US':
					$date = $date . ' ago';
					break;
			}
		}

		return $date;
	}
}