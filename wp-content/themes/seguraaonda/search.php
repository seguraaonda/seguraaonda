<?php


	if(isset($_GET['search-type'])) {

		$type = $_GET['search-type'];

		if($type == 'map') {

			load_template( get_stylesheet_directory() . '/mapa.php' );
		}

	} else {

		load_template( get_template_directory() . '/index.php' );
	}



?>