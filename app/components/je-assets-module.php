<?php

trait JE_Assets_Module {
	function load_script( $scenario = '' ) {
		switch ( $scenario ) {
			case 'buttons':
				wp_enqueue_style( 'jobs-buttons-shortcode' );
				break;
			case 'jobs':
				wp_enqueue_script( 'jobs-main' );
				wp_enqueue_style( 'jobs-list-shortcode' );
				break;
			case 'job':
				wp_enqueue_style( 'jobs-single-shortcode' );
				break;
			case 'job-form':
				wp_enqueue_script( 'jobs-main' );
				wp_enqueue_style( 'jobs-form-shortcode' );
				wp_enqueue_script( 'jobs-select2' );
				wp_enqueue_style( 'jobs-select2' );
				wp_enqueue_script( 'jbp-flatpickr' );
				wp_enqueue_script( 'jbp-flatpickr-de' );
				wp_enqueue_style( 'jbp-flatpickr' );
				break;
			case 'contact':
				wp_enqueue_style( 'jobs-contact' );
				break;
			case 'experts':
				wp_enqueue_script( 'jobs-main' );
				wp_enqueue_style( 'expert-list-shortcode' );
				wp_enqueue_script( 'jobs-main' );
				break;
			case 'expert':
				wp_enqueue_style( 'expert-single-shortcode' );

				wp_enqueue_script( 'jobs-main' );
				break;
			case 'expert-form':
				wp_enqueue_style( 'expert-form-shortcode' );
				wp_enqueue_script( 'jobs-main' );
				wp_enqueue_script( 'jquery-frame-transport' );
				wp_enqueue_style( 'jobs-form-validation' );
				wp_enqueue_script( 'jobs-form-validation' );
				wp_enqueue_script( 'jobs-form-init' );
				break;
			case 'landing':
				wp_enqueue_style( 'jobs-list-shortcode' );
				wp_enqueue_style( 'expert-list-shortcode' );
				wp_enqueue_style( 'jobs-landing-shortcode' );
				wp_enqueue_script( 'jobs-main' );
				break;
			case 'widget':
				wp_enqueue_style( 'job-plus-widgets' );
				break;
		}
	}

	function scripts() {
		wp_enqueue_script( 'jquery' );
		wp_register_script( 'jobs-uploader', $this->plugin_url . 'assets/uploader.js', array( 'jquery' ), $this->version );
		wp_enqueue_script( 'jobs-uploader' );

		wp_register_script( 'jbp-flatpickr', $this->plugin_url . 'assets/vendors/flatpickr/flatpickr.min.js', array(), $this->version, true );
		wp_register_script( 'jbp-flatpickr-de', $this->plugin_url . 'assets/vendors/flatpickr/l10n/de.js', array( 'jbp-flatpickr' ), $this->version, true );
		wp_register_style( 'jbp-flatpickr', $this->plugin_url . 'assets/vendors/flatpickr/flatpickr.min.css', array(), $this->version );

		$min = $this->dev == true ? null : '.min';
		wp_register_style( 'jobs-form-validation', $this->plugin_url . 'assets/form-validation.css', array(), $this->version );
		wp_register_script( 'jobs-form-validation', $this->plugin_url . 'assets/form-validation.js', array(), $this->version, true );
		wp_register_script( 'jobs-form-init', $this->plugin_url . 'assets/form-init.js', array( 'jquery', 'jobs-form-validation' ), $this->version, true );

		if ( is_admin() ) {
			wp_enqueue_style( 'jbp_admin', $this->plugin_url . 'assets/css/admin.css', array( 'ig-packed' ), $this->version );
			wp_register_style( 'jbp_select2', $this->plugin_url . 'assets/select2/select2.css', array( 'ig-packed' ), $this->version );
			wp_register_script( 'jbp_select2', $this->plugin_url . 'assets/select2/select2.min.js', array( 'jquery' ), $this->version );
		} else {

			global $wp_locale;
			$aryArgs = array(
				'closeText'       => __( 'Erledigt', 'psjb' ),
				'currentText'     => __( 'Heute', 'psjb' ),
				'monthNames'      => $this->strip_array_indices( $wp_locale->month ),
				'monthNamesShort' => $this->strip_array_indices( $wp_locale->month_abbrev ),
				'monthStatus'     => __( 'Zeige einen anderen Monat', 'psjb' ),
				'dayNames'        => $this->strip_array_indices( $wp_locale->weekday ),
				'dayNamesShort'   => $this->strip_array_indices( $wp_locale->weekday_abbrev ),
				'dayNamesMin'     => $this->strip_array_indices( $wp_locale->weekday_initial ),
				'dateFormat'      => $this->date_format_php_to_js( get_option( 'date_format' ) ),
				'firstDay'        => get_option( 'start_of_week' ),
				'isRTL'           => false,
			);

			$min = $this->dev == true ? null : '.min';
			wp_register_style( 'jobs-main', $this->plugin_url . 'assets/main' . $min . '.css', array( 'ig-packed' ), $this->version );
			wp_register_style( 'jobs-buttons-shortcode', $this->plugin_url . 'assets/buttons' . $min . '.css', array( 'jobs-main' ), $this->version );
			wp_register_style( 'jobs-single-shortcode', $this->plugin_url . 'assets/jobs-single' . $min . '.css', array( 'jobs-main' ), $this->version );
			wp_register_style( 'jobs-form-shortcode', $this->plugin_url . 'assets/jobs-form' . $min . '.css', array( 'jobs-main' ), $this->version );
			wp_register_style( 'expert-form-shortcode', $this->plugin_url . 'assets/expert-form' . $min . '.css', array( 'jobs-main' ), $this->version );
			wp_register_style( 'expert-single-shortcode', $this->plugin_url . 'assets/expert-single' . $min . '.css', array( 'jobs-main' ), $this->version );
			wp_register_style( 'jobs-list-shortcode', $this->plugin_url . 'assets/jobs-list' . $min . '.css', array( 'jobs-main' ), $this->version );
			wp_register_style( 'expert-list-shortcode', $this->plugin_url . 'assets/expert-list' . $min . '.css', array( 'jobs-main' ), $this->version );
			wp_register_style( 'jobs-contact', $this->plugin_url . 'assets/contact' . $min . '.css', array( 'jobs-main' ), $this->version );
			wp_register_style( 'jobs-landing-shortcode', $this->plugin_url . 'assets/landing' . $min . '.css', array( 'jobs-main' ), $this->version );
			wp_register_style( 'job-plus-widgets', $this->plugin_url . 'assets/widget' . $min . '.css', array( 'jobs-main' ), $this->version );

			wp_register_script( 'jobs-main', $this->plugin_url . 'assets/main.js', array(
				'jquery',
				'ig-packed'
			), $this->version );
			wp_localize_script( 'jobs-main', 'jeL10n', $aryArgs );

			wp_register_script( 'jobs-select2', $this->plugin_url . 'assets/select2/select2.min.js' );
			wp_register_style( 'jobs-select2', $this->plugin_url . 'assets/select2/select2.css' );

			wp_register_script( 'jobs-noty', $this->plugin_url . 'assets/vendors/noty/packaged/jquery.noty.packaged.min.js', array(), $this->version, true );
		}
	}
}
