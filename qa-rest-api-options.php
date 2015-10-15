<?php

class qa_rest_api_options_admin {

	function allow_template($template) {
		return ($template == 'admin');
	}

	function option_default($option) {
		/* 
		 * Internal security (non for third-party applications)
		 * 
		 * if ($option == 'plugin_rest_api_permit') {
			require_once QA_INCLUDE_DIR . 'qa-app-options.php';
			return QA_PERMIT_EXPERTS;
		} */

		if ($option == 'plugin_rest_api_max_users')
			return "10";
		
		if ($option == 'plugin_rest_api_max_questions')
			return "10";
		
		if ($option == 'plugin_rest_api_max_answers')
			return "10"; 

		return null;
	}

	function admin_form(&$qa_content) {
		require_once QA_INCLUDE_DIR . 'qa-app-admin.php';
		require_once QA_INCLUDE_DIR . 'qa-app-options.php';

		$permitoptions = qa_admin_permit_options ( QA_PERMIT_USERS, QA_PERMIT_SUPERS, false, false );
		$elementoptions = array("10", "100", qa_lang_html('plugin_rest_api/rest_api_all'));

		$saved = false;

		if (qa_clicked ( 'plugin_rest_api_save_button' )) {
			qa_opt ( 'plugin_rest_api_max_users', qa_post_text ( 'plugin_rest_api_mu_field' ) );
			qa_opt ( 'plugin_rest_api_max_questions', qa_post_text ( 'plugin_rest_api_mq_field' ) );
			qa_opt ( 'plugin_rest_api_max_answers', qa_post_text ( 'plugin_rest_api_ma_field' ) );
			qa_opt ( 'plugin_rest_api_permit', ( int ) qa_post_text ( 'plugin_rest_api_p_field' ) );
			$saved = true;
		}

		return array (
				'ok' => $saved ? 'REST API settings saved' : null,

				'fields' => array (
						/* 
						 * Internal security (non for third-party applications)
						 * 
						 * array (
								'label' => qa_lang_html('plugin_rest_api/rest_api_allow_use'),
								'type' => 'select',
								'value' => @$permitoptions [qa_opt ( 'plugin_rest_api_permit' )],
								'options' => $permitoptions,
								'tags' => 'NAME="plugin_rest_api_p_field"'
						), */

						array (
								'label' => qa_lang_html('plugin_rest_api/rest_api_max_users'),
								'type' => 'select',
								'value' => @$elementoptions [qa_opt ( 'plugin_rest_api_max_users' )],
								'options' => $elementoptions,
								'tags' => 'NAME="plugin_rest_api_mu_field"'
						),

						array (
								'label' => qa_lang_html('plugin_rest_api/rest_api_max_questions'),
								'type' => 'select',
								'value' => @$elementoptions [qa_opt ( 'plugin_rest_api_max_questions' )],
								'options' => $elementoptions,
								'tags' => 'NAME="plugin_rest_api_mq_field"'
						),

						array (
								'label' => qa_lang_html('plugin_rest_api/rest_api_max_answers'),
								'type' => 'select',
								'value' => @$elementoptions [qa_opt ( 'plugin_rest_api_max_answers' )],
								'options' => $elementoptions,
								'tags' => 'NAME="plugin_rest_api_ma_field"'
						)
				),

				'buttons' => array (
						array (
								'label' => 'Save Changes',
								'tags' => 'NAME="plugin_rest_api_save_button"'
						)
				)
		);
	}
	
}