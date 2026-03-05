<?php

trait JE_Permissions_Module {
	function can_upload() {
		if ( ! is_user_logged_in() ) {
			return false;
		}

		if ( current_user_can( 'upload_files' ) ) {
			return true;
		}

		$allowed = $this->settings()->allow_attachment;
		if ( ! is_array( $allowed ) ) {
			$allowed = array();
		}
		$allowed = array_filter( $allowed );
		$user    = new WP_User( get_current_user_id() );
		foreach ( $user->roles as $role ) {
			if ( in_array( $role, $allowed ) ) {
				return true;
			}
		}

		return false;
	}

	function can_upload_avatar() {
		if ( ! is_user_logged_in() ) {
			return false;
		}

		if ( current_user_can( 'upload_files' ) ) {
			return true;
		}

		$allowed = $this->settings()->allow_avatar;
		if ( ! is_array( $allowed ) ) {
			$allowed = array();
		}
		$allowed = array_filter( $allowed );
		$user    = new WP_User( get_current_user_id() );
		foreach ( $user->roles as $role ) {
			if ( in_array( $role, $allowed ) ) {
				return true;
			}
		}

		return false;
	}
}
