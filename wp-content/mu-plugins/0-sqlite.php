<?php
		if ( file_exists( WP_CONTENT_DIR . "/db.php" ) && file_exists( __DIR__ . "/sqlite-database-integration/load.php" ) ) {
			require_once __DIR__ . "/sqlite-database-integration/load.php";
		}