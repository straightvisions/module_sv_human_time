<?php
	namespace sv100;
	
	/**
	 * @version         4.000
	 * @author			straightvisions GmbH
	 * @package			sv100
	 * @copyright		2019 straightvisions GmbH
	 * @link			https://straightvisions.com
	 * @since			1.000
	 * @license			See license.txt or https://straightvisions.com
	 */
	
	class sv_human_time extends init {
		public function init() {
			// Module Info
			$this->set_module_title( 'SV Human Time' );
			$this->set_module_desc( __( 'This module converts dates and times to a human readable text, via the "[sv_human_time]" shortcode.', 'sv100' ) );
	
			// Section Info
			$this->set_section_title( 'Human Time' );
			$this->set_section_desc( __( 'Settings', 'sv100' ) );
			$this->set_section_type( 'settings' );
			$this->get_root()->add_section( $this );
	
			// Load settings, register scripts and sidebars
			$this->load_settings();
			
			// Actions Hooks & Filter
			add_filter( 'get_comment_date', array( $this, 'get_comment_date' ), 10, 3 );
			add_filter( 'get_the_date', array( $this, 'get_the_date' ), 10, 3 );
		}
	
		protected function load_settings(): sv_human_time {
			$this->s['posts'] =
				static::$settings
					->create( $this )
					->set_ID( 'posts' )
					->set_title( __( 'Enables relative date format for all posts', 'sv100' ) )
					->set_default_value( 1 )
					->load_type( 'checkbox' );
			
			$this->s['comments'] =
				static::$settings
					->create( $this )
					->set_ID( 'comments' )
					->set_title( __( 'Enables relative date format for all comments', 'sv100' ) )
					->set_default_value( 1 )
					->load_type( 'checkbox' );
			
			$this->s['date_after'] =
				static::$settings
					->create( $this )
					->set_ID( 'date_after' )
					->set_title( __( 'Show Date Format', 'sv100' ) )
					->set_description( __( 'Shows the date and time in the default WordPress format, when the time difference is higher than the set days.<br>0 = never', 'sv100' ) )
					->set_default_value( 0 )
					->set_min( 0 )
					->load_type( 'number' );
	
			return $this;
		}
	
		public function load( $settings = array() ): string {
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
				$date_start = $settings['date_start'];
			} else {
				return __( 'Starting date is needed!', 'sv100' );
			}
	
			if ( isset($settings['date_end'] ) && $settings['date_end'] ) {
				$date_end   = strtotime( $settings['date_end'] );
			} else {
				$date_end   = current_time( 'timestamp' );
			}
			
			$date_after = ( isset( $settings['date_after'] ) && $settings['date_after'] )
				? $settings['date_after']
				: $this->get_setting( 'date_after' )->run_type()->get_data();
	
			// Time difference between post date and current date, in days
			$time_diff = round( ( $date_end - $date_start ) / ( 60 * 60 * 24 ) );
	
			if ( $date_after > 0 && $time_diff > $date_after ) {
				$date = $settings['date_start'];
			} else {
				$date = human_time_diff( $date_start, $date_end );
				$date = sprintf( __( '%s ago', 'sv100' ), $date );
			}
	
			return $date;
		}
		
		public function get_comment_date( $date, $d, $comment ) {
			if ( $this->get_setting( 'comments' )->run_type()->get_data() ) {
				$formatted_date = $this->router( array( 'date_start' => mysql2date( 'U', $comment->comment_date ) ) );
				
				return ( $formatted_date == mysql2date( 'U', $comment->comment_date ) ) ? $date : $formatted_date;
			} else {
				return $date;
			}
		}
		
		public function get_the_date( $date, $d, $post ) {
			if ( $this->get_setting( 'posts' )->run_type()->get_data() ) {
				$formatted_date = $this->router( array( 'date_start' => mysql2date( 'U', $post->post_date ) ) );
				
				return ( $formatted_date == mysql2date( 'U', $post->post_date ) ) ? $date : $formatted_date;
			} else {
				return $date;
			}
		}
	}