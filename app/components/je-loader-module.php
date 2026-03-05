<?php

trait JE_Loader_Module {
	function autoload( $class ) {
		$filename = str_replace( '_', '-', strtolower( $class ) ) . '.php';
		if ( strstr( $filename, '-controller.php' ) ) {
			$files = $this->listFolderFiles( $this->plugin_path . 'app/controllers' );
			foreach ( $files as $file ) {
				if ( strcmp( $filename, pathinfo( $file, PATHINFO_BASENAME ) ) === 0 ) {
					include_once $file;
					break;
				}
			}
		} elseif ( strstr( $filename, '-model.php' ) ) {
			$files = $this->listFolderFiles( $this->plugin_path . 'app/models' );

			foreach ( $files as $file ) {
				if ( strcmp( $filename, pathinfo( $file, PATHINFO_BASENAME ) ) === 0 ) {
					include_once $file;
					break;
				}
			}
		} elseif ( file_exists( $this->plugin_path . 'app/' . $filename ) ) {
			include_once $this->plugin_path . 'app/' . $filename;
		} elseif ( file_exists( $this->plugin_path . 'app/components/' . $filename ) ) {
			include_once $this->plugin_path . 'app/components/' . $filename;
		}
	}

	function listFolderFiles( $dir ) {
		$ffs  = scandir( $dir );
		$i    = 0;
		$list = array();
		foreach ( $ffs as $ff ) {
			if ( $ff != '.' && $ff != '..' ) {
				if ( strlen( $ff ) >= 5 ) {
					if ( substr( $ff, - 4 ) == '.php' ) {
						$list[] = $dir . '/' . $ff;
					}
				}
				if ( is_dir( $dir . '/' . $ff ) ) {
					$list = array_merge( $list, $this->listFolderFiles( $dir . '/' . $ff ) );
				}
			}
		}

		return $list;
	}
}
