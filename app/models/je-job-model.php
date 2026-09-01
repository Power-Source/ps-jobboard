<?php

/**
 * @author:DerN3rd
 */
class JE_Job_Model extends IG_Post_Model {
	//model own property
	public $id;
	public $job_title;
	public $categories;
	public $skills;
	public $description;
	public $budget;
	public $contact_email;
	public $dead_line;
	public $job_img;
	public $open_for;
	public $portfolios;
	public $status;
	public $min_budget;
	public $max_budget;
	public $owner;
	public $engagement_type;
	public $compensation_period;
	public $schedule_mode;
	public $schedule_text;
	public $external_url_type;
	public $external_url;

	public $text_domain = 'jbp';

	protected $table = 'jbp_job';
	protected $defaults = array(
		'ping_status'    => 'closed',
		'comment_status' => 'closed'
	);

	protected $mapped = array(
		'id'          => 'ID',
		'job_title'   => 'post_title',
		'owner'       => 'post_author',
		'description' => 'post_content',
		'status'      => 'post_status'
	);

	protected $relations = array(
		array(
			'type' => 'meta',
			'key'  => '_ct_jbp_job_Budget',
			'map'  => 'budget'
		),
		array(
			'type' => 'meta',
			'key'  => '_ct_jbp_job_Contact_Email',
			'map'  => 'contact_email'
		),
		array(
			'type' => 'meta',
			'key'  => '_ct_jbp_job_Due',
			'map'  => 'dead_line'
		),
		array(
			'type' => 'meta',
			'key'  => '_ct_jbp_job_img',
			'map'  => 'job_img'
		),
		array(
			'type' => 'meta',
			'key'  => '_jbp_job_expires',
			'map'  => 'open_for'
		),
		array(
			'type' => 'meta',
			'key'  => '_jbp_job_portfolios',
			'map'  => 'portfolios'
		),
		array(
			'type' => 'meta',
			'key'  => '_jbp_job_budget_min',
			'map'  => 'min_budget'
		),
		array(
			'type' => 'meta',
			'key'  => '_jbp_job_budget_max',
			'map'  => 'max_budget'
		),
		array(
			'type' => 'meta',
			'key'  => '_jbp_job_engagement_type',
			'map'  => 'engagement_type'
		),
		array(
			'type' => 'meta',
			'key'  => '_jbp_job_compensation_period',
			'map'  => 'compensation_period'
		),
		array(
			'type' => 'meta',
			'key'  => '_jbp_job_schedule_mode',
			'map'  => 'schedule_mode'
		),
		array(
			'type' => 'meta',
			'key'  => '_jbp_job_schedule_text',
			'map'  => 'schedule_text'
		),
		array(
			'type' => 'meta',
			'key'  => '_jbp_job_external_url_type',
			'map'  => 'external_url_type'
		),
		array(
			'type' => 'meta',
			'key'  => '_jbp_job_external_url',
			'map'  => 'external_url'
		),
		array(
			'type' => 'taxonomy',
			'key'  => 'jbp_category',
			'map'  => 'categories'
		),
		array(
			'type' => 'taxonomy',
			'key'  => 'jbp_skills_tag',
			'map'  => 'skills'
		),
	);

	public function __construct() {
		$this->virtual_attributes = apply_filters( 'je_job_additions_field', $this->virtual_attributes );
		$this->relations          = apply_filters( 'je_job_relations', $this->relations );
		$this->mapped             = apply_filters( 'je_job_fields_mapped', $this->mapped );
		$this->defaults           = apply_filters( 'je_job_default_fields', $this->defaults );
	}

	public function before_validate() {
		$this->normalize_employment_fields();
		$rules = array(
			'job_title'     => 'required',
			'contact_email' => 'required|valid_email',
			'engagement_type' => 'required',
			'schedule_mode' => 'required',
			'open_for'      => 'required',
			'description'   => 'required',
		);
		if ( 'date' === $this->schedule_mode ) {
			$rules['dead_line'] = 'required';
		} elseif ( 'custom' === $this->schedule_mode ) {
			$rules['schedule_text'] = 'required|max_len,25';
		}
		if ( 'freelance' === $this->engagement_type && je()->settings()->job_budget_range == 1 ) {
			$rules['min_budget'] = 'required|numeric|min_numeric,0';
			$rules['max_budget'] = 'required|numeric';
		} elseif ( 'freelance' === $this->engagement_type ) {
			$rules['budget'] = 'required|numeric';
		} elseif ( je()->settings()->job_budget_range == 1 ) {
			if ( strlen( (string) $this->min_budget ) ) {
				$rules['min_budget'] = 'numeric|min_numeric,0';
			}
			if ( strlen( (string) $this->max_budget ) ) {
				$rules['max_budget'] = 'numeric';
			}
		} elseif ( strlen( (string) $this->budget ) ) {
			$rules['budget'] = 'numeric';
		}
		if ( strlen( (string) $this->external_url ) ) {
			$rules['external_url'] = 'valid_url';
		}

		$rules       = apply_filters( 'je_job_validation_rules', $rules );
		$this->rules = $rules;

		$fields_text = array(
			'dead_line'     => $this->get_schedule_label(),
			'schedule_text' => __( 'Alternative Terminangabe', 'psjb' ),
			'open_for'      => __( 'Anzeige veröffentlichen für', 'psjb' )
		);
		$fields_text = apply_filters( 'je_job_field_name', $fields_text );
		foreach ( $fields_text as $key => $text ) {
			GUMP::set_field_name( $key, $text );
		}
	}

	public function before_save() {
		$this->normalize_employment_fields();
		$this->external_url = esc_url_raw( (string) $this->external_url );
		if ( $this->is_expired() ) {
			update_post_meta( $this->id, 'jbp_job_post_day', date( 'Y-m-d H:i:s' ) );
		}

		$this->defaults = array_merge( $this->defaults, array(
			'post_name' => sanitize_title( $this->job_title )
		) );
	}

	public function after_validate() {
		if ( strlen( (string) $this->external_url ) ) {
			$scheme = strtolower( (string) wp_parse_url( $this->external_url, PHP_URL_SCHEME ) );
			if ( ! filter_var( $this->external_url, FILTER_VALIDATE_URL ) || ! in_array( $scheme, array( 'http', 'https' ), true ) ) {
				$this->set_error( 'external_url', __( 'Bitte gib eine vollständige URL mit http:// oder https:// ein.', 'psjb' ) );

				return false;
			}
		}
		if ( 'employment' === $this->engagement_type && je()->settings()->job_budget_range == 1 && ( strlen( (string) $this->min_budget ) xor strlen( (string) $this->max_budget ) ) ) {
			$this->set_error( 'min_budget', __( 'Bitte gib sowohl das minimale als auch das maximale Gehalt an oder lasse beide Felder leer.', 'psjb' ) );

			return false;
		}
		if ( je()->settings()->job_budget_range == 1 && strlen( (string) $this->min_budget ) && strlen( (string) $this->max_budget ) && $this->min_budget > $this->max_budget ) {
			$message = 'employment' === $this->engagement_type
				? __( 'Das Mindestgehalt sollte unter dem Maximalgehalt liegen.', 'psjb' )
				: __( 'Das Mindestbudget sollte unter dem Maximalbudget liegen.', 'psjb' );
			$this->set_error( 'min_budget', $message );

			return false;
		}

		return true;
	}

	public function get_price() {
		if ( je()->settings()->job_budget_range == 1 ) {
			//use range
			if ( strlen( $this->min_budget ) && strlen( $this->max_budget ) ) {
				return array( $this->min_budget, $this->max_budget );
			} else {
				//fallback to normal budget
				return $this->budget;
			}
		} else {
			return $this->budget;
		}
	}

	public function render_prices( $return = '' ) {
		$this->normalize_employment_fields();
		$prices   = $this->get_price();
		$currency = je()->settings()->currency;
		ob_start();
		if ( ( is_array( $prices ) && ! strlen( (string) $this->min_budget ) && ! strlen( (string) $this->max_budget ) ) || ( ! is_array( $prices ) && ! strlen( (string) $prices ) ) ) {
			_e( 'Nicht angegeben', 'psjb' );
		} elseif ( is_array( $prices ) ) {
			?>
			<?php if ( empty( $return ) ): ?>
				<?php echo JobsExperts_Helper::format_currency( $currency, $this->min_budget ) ?> -
				<?php echo JobsExperts_Helper::format_currency( $currency, $this->max_budget ) ?>
			<?php else: ?>
				<?php echo JobsExperts_Helper::format_currency( $currency, $this->max_budget ) ?>
			<?php endif; ?>
			<?php
		} else {
			?>
			<?php echo JobsExperts_Helper::format_currency( $currency, $this->budget ) ?>
			<?php
		}
		if ( 'employment' === $this->engagement_type && $this->has_compensation() ) {
			echo ' ' . esc_html( $this->get_compensation_period_label() );
		}
		$content = ob_get_clean();
		if ( $return == true ) {
			return $content;
		}
		echo $content;
	}

	public function get_engagement_label() {
		$this->normalize_employment_fields();

		return 'employment' === $this->engagement_type ? __( 'Festanstellung', 'psjb' ) : __( 'Freelance/Projektarbeit', 'psjb' );
	}

	public function get_compensation_label() {
		$this->normalize_employment_fields();

		return 'employment' === $this->engagement_type ? __( 'Gehalt', 'psjb' ) : __( 'Budget', 'psjb' );
	}

	public function get_schedule_label() {
		$this->normalize_employment_fields();

		return 'employment' === $this->engagement_type ? __( 'Einstellung ab', 'psjb' ) : __( 'Fertigstellung bis', 'psjb' );
	}

	public function get_schedule_value() {
		$this->normalize_employment_fields();
		if ( 'immediately' === $this->schedule_mode ) {
			return __( 'Ab sofort', 'psjb' );
		}
		if ( 'arrangement' === $this->schedule_mode ) {
			return __( 'Nach Absprache', 'psjb' );
		}
		if ( 'custom' === $this->schedule_mode ) {
			return $this->schedule_text;
		}

		return strtotime( $this->dead_line ) ? date_i18n( get_option( 'date_format' ), strtotime( $this->dead_line ) ) : __( 'Nicht angegeben', 'psjb' );
	}

	public function get_external_url_label() {
		$this->normalize_employment_fields();

		return 'application' === $this->external_url_type ? __( 'Zum externen Bewerbungsformular', 'psjb' ) : __( 'Zur Firmenwebseite', 'psjb' );
	}

	private function has_compensation() {
		if ( je()->settings()->job_budget_range == 1 ) {
			return strlen( (string) $this->min_budget ) || strlen( (string) $this->max_budget );
		}

		return strlen( (string) $this->budget );
	}

	private function get_compensation_period_label() {
		$labels = array(
			'year'  => __( 'pro Jahr', 'psjb' ),
			'month' => __( 'pro Monat', 'psjb' ),
			'hour'  => __( 'pro Stunde', 'psjb' ),
		);

		return isset( $labels[ $this->compensation_period ] ) ? $labels[ $this->compensation_period ] : $labels['year'];
	}

	private function normalize_employment_fields() {
		$this->engagement_type = in_array( $this->engagement_type, array( 'freelance', 'employment' ), true ) ? $this->engagement_type : 'freelance';
		$this->compensation_period = in_array( $this->compensation_period, array( 'year', 'month', 'hour' ), true ) ? $this->compensation_period : 'year';
		$this->schedule_mode = in_array( $this->schedule_mode, array( 'date', 'immediately', 'arrangement', 'custom' ), true ) ? $this->schedule_mode : 'date';
		$this->schedule_text = function_exists( 'mb_substr' ) ? mb_substr( sanitize_text_field( (string) $this->schedule_text ), 0, 25 ) : substr( sanitize_text_field( (string) $this->schedule_text ), 0, 25 );
		$this->external_url_type = in_array( $this->external_url_type, array( 'company', 'application' ), true ) ? $this->external_url_type : 'company';
		$this->external_url = sanitize_text_field( (string) $this->external_url );
	}

	public function get_due_day() {
		$post = get_post( $this->id );
		if ( $post ) {
			$created_date = get_post_meta( $post->ID, 'jbp_job_post_day', true );
			if ( ! $created_date ) {
				$created_date = $post->post_date;
			}
			$expire_date = strtotime( '+ ' . $this->open_for . ' days', strtotime( $created_date ) );

			return $this->days_hours( $expire_date );
		}
	}

	function count() {
		global $wpdb;
		$sql    = "SELECT count(ID) FROM " . $wpdb->posts . " WHERE post_type=%s AND post_status IN (%s,%s) AND post_author=%d";
		$result = $wpdb->get_var( $wpdb->prepare( $sql, 'jbp_job', 'publish', 'draft', get_current_user_id() ) );

		return $result;
	}

	private function days_hours( $expires ) {
		$date = intval( $expires );
		$secs = $date - time();
		if ( $secs > 0 ) {
			$days  = floor( $secs / ( 60 * 60 * 24 ) );
			$hours = round( ( $secs - $days * 60 * 60 * 24 ) / ( 60 * 60 ) );

			return sprintf( __( '%d Tage %dhrs', 'psjb' ), $days, $hours );
		} else {
			return __( 'Abgelaufen', 'psjb' );
		}
	}

	function get_end_date() {
		return date_i18n( get_option( 'date_format' ), strtotime( $this->dead_line ) );
	}

	protected function count_user_posts_by_type( $user_id = 0, $post_type = 'post' ) {
		global $wpdb;

		$where = get_posts_by_author_sql( $post_type, true, $user_id );

		if ( in_array( $post_type, array( 'jbp_pro', 'jbp_job' ) ) ) {
			$where = str_replace( "post_status = 'publish'", "post_status = 'publish' OR post_status = 'draft'", $where );
		}
		$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts $where" );

		return apply_filters( 'get_usernumposts', $count, $user_id );
	}

	function is_expired() {
		if ( $this->get_due_day() == __( 'Abgelaufen', 'psjb' ) ) {
			return true;
		}

		return false;
	}

	function is_current_owner() {
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}

		if ( get_current_user_id() == $this->owner ) {
			return true;
		}

		return false;
	}

	function get_status() {
		$status = $this->status;
		if ( $status == 'publish' ) {
			$status = __( 'veröffentlicht', 'psjb' );
		} elseif ( $status == 'pending' ) {
			$status = __( 'ausstehend', 'psjb' );
		} elseif ( $status == 'draft' ) {
			$status = __( "Entwurf", 'psjb' );
		}

		return $status;
	}

	public static function model( $class_name = __CLASS__ ) {
		return parent::model( $class_name );
	}
}