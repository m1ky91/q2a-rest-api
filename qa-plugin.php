<?php

/*
 * Plugin Name: REST API
 * Plugin URI: 
 * Plugin Description: Gives your Q2A site a REST API
 * Plugin Version: 1.0
 * Plugin Date: 2015-08-16
 * Plugin Author: Michele Di Chio
 * Plugin Author URI:
 * Plugin License: GPLv2
 * Plugin Minimum Question2Answer Version: 1.5
 * Plugin Update Check URI:
 */

if (! defined ( 'QA_VERSION' )) {
	header ( 'Location: ../../' );
	exit ();
}

qa_register_plugin_module(
		'page',
		'qa-rest-api-page.php',
		'qa_rest_api_presentation_page',
		'REST API'
);

qa_register_plugin_module(
		'page',
		'qa-rest-api-response.php',
		'qa_rest_api_response_page',
		'REST API response'
);

qa_register_plugin_module(
		'module', 
		'qa-rest-api-options.php', 
		'qa_rest_api_options_admin', 
		'REST API option admin'
);

qa_register_plugin_phrases(
		'qa-rest-api-lang-*.php',
		'plugin_rest_api'
);