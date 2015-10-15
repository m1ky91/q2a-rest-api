<?php

class qa_rest_api_presentation_page {
	
	function match_request($request) {
		$parts = explode ( '/', $request );
		
		return $parts [0] == 'api' && sizeof($parts) == 1;
	}
	
	function suggest_requests() {
		return array (
				array (
						'title' => 'REST API',
						'request' => 'api',
						'nav' => 'M' 
				) 
		);
	}
	
	function process_request($request) {
		require_once QA_INCLUDE_DIR . 'qa-app-admin.php';
		require_once QA_INCLUDE_DIR . 'qa-app-options.php';
		
		$permitoptions = qa_admin_permit_options ( QA_PERMIT_USERS, QA_PERMIT_SUPERS, false, false );
		
		$qa_content = qa_content_prepare ();
		
		$qa_content['head_lines'][] = 
			'<style>
				.api-resource-title {
					margin-top: 10px;
					font-size: 150%;
				}
				.api-endpoint-title {
					font-size: 125%;
					margin: 10px 0 5px;
					cursor: pointer;
				}
				.api-endpoint-title:hover {
					color: #666666;
				}
				.api-endpoint-method {
				    background: #DEEAFF none repeat scroll 0% 0%;
				    border: 1px solid #C1DAA6;
				    float: left;
				    width: 60px;
				    text-align: center;
				    margin-right: 5px;
				    padding: 0px;
				    border-radius: 3px;
				}
				.api-endpoint-info {
					display: none;
					margin: 0 0 10px 10px;
				}
				.api-endpoint-request {
					font-size: 95%;
				    background: #EEFFDA none repeat scroll 0% 0%;
				    border: 1px solid #E1E3E1;
				    float: left;
				    width: 95%;
				    text-align: left;
				    margin: 10px;
				    padding-left: 5px;
				    border-radius: 3px;
				}
				.api-endpoint-response {
					font-size: 95%;
				    background: #FEFFDE none repeat scroll 0% 0%;
				    border: 1px solid #FFC296;
				    float: left;
				    width: 95%;
				    text-align: left;
				    margin: 10px;
				    padding-left: 5px;
				    border-radius: 3px;
				}
			</style>';
		
		$qa_content ['title'] = qa_html ( qa_opt ( 'site_title' ) ) . ' REST API';
		
		$qa_content ['custom_introduction_1'] = qa_lang_html('plugin_rest_api/rest_api_intro_1');
		$qa_content ['custom_introduction_2'] = qa_lang_html('plugin_rest_api/rest_api_intro_2');
		$qa_content ['custom_introduction_3'] = qa_lang_html('plugin_rest_api/rest_api_intro_3');
		$qa_content ['custom_introduction_4'] = qa_lang_html_sub ( 'plugin_rest_api/rest_api_intro_4', qa_html ( $permitoptions[qa_opt ( 'plugin_rest_api_permit' )]  ) );
				
		$qa_content['custom_resource_users'] = 
			'<div class="api-resource-title">Users</div>';
		
		$qa_content['custom_resource_users_desc'] = qa_lang_html('plugin_rest_api/rest_api_users_desc');
		
		$qa_content['custom_users_endpoint_1_title'] = 
			'<div>
				<div id="custom_users_endpoint_1_title" onclick="jQuery(\'#custom_users_endpoint_1_text\').toggle(\'fast\')" class="api-endpoint-title">
					<span class="api-endpoint-method">GET</span>/v1/users/
				</div>';
		$qa_content['custom_users_endpoint_1_text'] = 
				'<div id="custom_users_endpoint_1_text" class="api-endpoint-info">'
					. qa_lang_html('plugin_rest_api/rest_api_users__users_') . 
					'<br><b>' . qa_lang_html('plugin_rest_api/rest_api_request') . '</b><br>
					<div class="api-endpoint-request">
						<pre>' . qa_opt ( 'site_url' ) . 'api/v1/users/</pre>
					</div>
					<b>' . qa_lang_html('plugin_rest_api/rest_api_response') . '</b>
					<div class="api-endpoint-response">
						<pre>[
    {
        "userid": 1,
        "handle": "m1ky91",
        "points": 200,
        "qcount": 1,
        "acount": 2
    },
    {
        "userid": 2,
        "handle": "test",
        "points": 100,
        "qcount": 0,
        "acount": 0
    },
    {
        "userid": 3,
        "handle": "testtest",
        "points": 100,
        "qcount": 0,
        "acount": 0
    }
]</pre>
					</div>
				</div>
			</div>';
		
		$qa_content['custom_users_endpoint_2_title'] =
		'<div>
				<div id="custom_users_endpoint_2_title" onclick="jQuery(\'#custom_users_endpoint_2_text\').toggle(\'fast\')" class="api-endpoint-title">
					<span class="api-endpoint-method">GET</span>/v1/users/{userid}
				</div>';
		$qa_content['custom_users_endpoint_2_text'] =
		'<div id="custom_users_endpoint_2_text" class="api-endpoint-info">'
					. qa_lang_html('plugin_rest_api/rest_api_users__users_userid') .
					'<br><b>' . qa_lang_html('plugin_rest_api/rest_api_request') . '</b><br>
					<div class="api-endpoint-request">
						<pre>' . qa_opt ( 'site_url' ) . 'api/v1/users/1</pre>
					</div>
					<b>' . qa_lang_html('plugin_rest_api/rest_api_response') . '</b>
					<div class="api-endpoint-response">
						<pre>[
    {
        "userid": 1,
        "handle": "m1ky91",
        "points": 200,
        "qcount": 1,
        "acount": 2
    }
]</pre>
					</div>
				</div>
			</div>';
		
		$qa_content['custom_users_endpoint_3_title'] =
		'<div>
				<div id="custom_users_endpoint_3_title" onclick="jQuery(\'#custom_users_endpoint_3_text\').toggle(\'fast\')" class="api-endpoint-title">
					<span class="api-endpoint-method">GET</span>/v1/users/range/{fromuserid}/{touserid}
				</div>';
		$qa_content['custom_users_endpoint_3_text'] =
		'<div id="custom_users_endpoint_3_text" class="api-endpoint-info">'
				. qa_lang_html('plugin_rest_api/rest_api_users__users_range') .
				'<br><b>' . qa_lang_html('plugin_rest_api/rest_api_request') . '</b><br>
					<div class="api-endpoint-request">
						<pre>' . qa_opt ( 'site_url' ) . 'api/v1/users/1/2</pre>
					</div>
					<b>' . qa_lang_html('plugin_rest_api/rest_api_response') . '</b>
					<div class="api-endpoint-response">
						<pre>[
    {
        "userid": 1,
        "handle": "m1ky91",
        "points": 200,
        "qcount": 1,
        "acount": 2
    },
    {
        "userid": 2,
        "handle": "test",
        "points": 100,
        "qcount": 0,
        "acount": 0
    }
]</pre>
					</div>
				</div>
			</div>';
		
		$qa_content['custom_resource_questions'] =
		'<div class="api-resource-title">Questions</div>';
		
		$qa_content['custom_resource_questions_desc'] = qa_lang_html('plugin_rest_api/rest_api_questions_desc');
		
		$qa_content['custom_questions_endpoint_1_title'] =
		'<div>
				<div id="custom_questions_endpoint_1_title" onclick="jQuery(\'#custom_questions_endpoint_1_text\').toggle(\'fast\')" class="api-endpoint-title">
					<span class="api-endpoint-method">GET</span>/v1/questions/
				</div>';
		$qa_content['custom_questions_endpoint_1_text'] =
		'<div id="custom_questions_endpoint_1_text" class="api-endpoint-info">'
					. qa_lang_html('plugin_rest_api/rest_api_questions__questions_') .
					'<br><b>' . qa_lang_html('plugin_rest_api/rest_api_request') . '</b><br>
					<div class="api-endpoint-request">
						<pre>' . qa_opt ( 'site_url' ) . 'api/v1/questions/</pre>
					</div>
					<b>' . qa_lang_html('plugin_rest_api/rest_api_response') . '</b>
					<div class="api-endpoint-response">
						<pre>[
    {
        "questionid": 1,
        "title": "Is this a question test 1?",
        "content": "test 1",
        "categoryid": 1,
        "tags": [
            "test1",
            "test2"
        ],
        "userid": 1,
        "creationdate": "2015-07-03 15:21:54",
        "acount": 2
    },
    {
        "questionid": 4,
        "title": "Is this a question test 2?",
        "content": "test 2",
        "categoryid": 1,
        "tags": [
            "test1",
            "test2",
            "test3"
        ],
        "userid": 4,
        "creationdate": "2015-08-17 10:41:57",
        "acount": 0
    }
]</pre>
					</div>
				</div>
			</div>';
		
		$qa_content['custom_questions_endpoint_2_title'] =
		'<div>
				<div id="custom_questions_endpoint_2_title" onclick="jQuery(\'#custom_questions_endpoint_2_text\').toggle(\'fast\')" class="api-endpoint-title">
					<span class="api-endpoint-method">GET</span>/v1/questions/{questionid}
				</div>';
		$qa_content['custom_questions_endpoint_2_text'] =
		'<div id="custom_questions_endpoint_2_text" class="api-endpoint-info">'
					. qa_lang_html('plugin_rest_api/rest_api_questions__questions_questionid') .
					'<br><b>' . qa_lang_html('plugin_rest_api/rest_api_request') . '</b><br>
					<div class="api-endpoint-request">
						<pre>' . qa_opt ( 'site_url' ) . 'api/v1/questions/1</pre>
					</div>
					<b>' . qa_lang_html('plugin_rest_api/rest_api_response') . '</b>
					<div class="api-endpoint-response">
						<pre>[
    {
        "questionid": 1,
        "title": "Is this a question test 1?",
        "content": "test 1",
        "categoryid": 1,
        "tags": [
            "test1",
            "test2",
            "test3"
        ],
        "userid": 1,
        "creationdate": "2015-07-03 15:21:54",
        "acount": 2
    }
]</pre>
					</div>
				</div>
			</div>';
		
		$qa_content['custom_questions_endpoint_3_title'] =
		'<div>
				<div id="custom_questions_endpoint_3_title" onclick="jQuery(\'#custom_questions_endpoint_3_text\').toggle(\'fast\')" class="api-endpoint-title">
					<span class="api-endpoint-method">GET</span>/v1/questions/range/{fromquestionid}/{toquestionid}
				</div>';
		$qa_content['custom_questions_endpoint_3_text'] =
		'<div id="custom_questions_endpoint_3_text" class="api-endpoint-info">'
				. qa_lang_html('plugin_rest_api/rest_api_questions__questions_range') .
				'<br><b>' . qa_lang_html('plugin_rest_api/rest_api_request') . '</b><br>
					<div class="api-endpoint-request">
						<pre>' . qa_opt ( 'site_url' ) . 'api/v1/questions/1/2</pre>
					</div>
					<b>' . qa_lang_html('plugin_rest_api/rest_api_response') . '</b>
					<div class="api-endpoint-response">
						<pre>[
    {
        "questionid": 1,
        "title": "Is this a question test 1?",
        "content": "test 1",
        "categoryid": 1,
        "tags": [
            "test1",
            "test2"
        ],
        "userid": 1,
        "creationdate": "2015-07-03 15:21:54",
        "acount": 2
    }
]</pre>
					</div>
				</div>
			</div>';
		
		/* 
		 * Documentation for method POST *** incomplete ***
		 * 
		$qa_content['custom_questions_endpoint_4_title'] =
		'<div>
				<div id="custom_users_endpoint_4_title" onclick="jQuery(\'#form1\').toggle(\'fast\')" class="api-endpoint-title">
					<span class="api-endpoint-method">POST</span>/v1/users/
				</div>';
			
		$qa_content ['form1'] = array (
				'tags' => 'METHOD="POST" ACTION="' . qa_self_html () . '/v1/questions"',
		
				'style' => 'tall',
		
				'fields' => array (
						array (
								'label' => 'ID category',
								'type' => 'number',
								'tags' => 'NAME="categoryid" ID="categoryid"',
								'value' => ''
						),
						array (
								'label' => 'Title',
								'type' => 'text',
								'rows' => 1,
								'tags' => 'NAME="titlequestion" ID="titlequestion"',
								'value' => ''
						),
						array (
								'label' => 'Question',
								'type' => 'text',
								'rows' => 4,
								'tags' => 'NAME="textquestion" ID="textquestion"',
								'value' => ''
						)
				),
		
				'buttons' => array (
						array (
								'tags' => 'NAME="dosavequestion"',
								'label' => 'send',
						)
				)
		);
										
		$qa_content['custom_questions_endpoint_4_cl'] = '</div>'; */
		
		$qa_content['custom_resource_answers'] =
		'<div class="api-resource-title">Answers</div>';
		
		$qa_content['custom_resource_answers_desc'] = qa_lang_html('plugin_rest_api/rest_api_answers_desc');
		
		$qa_content['custom_answers_endpoint_1_title'] =
		'<div>
				<div id="custom_answers_endpoint_1_title" onclick="jQuery(\'#custom_answers_endpoint_1_text\').toggle(\'fast\')" class="api-endpoint-title">
					<span class="api-endpoint-method">GET</span>/v1/answers/
				</div>';
		$qa_content['custom_answers_endpoint_1_text'] =
		'<div id="custom_answers_endpoint_1_text" class="api-endpoint-info">'
					. qa_lang_html('plugin_rest_api/rest_api_answers__answers_') .
					'<br><b>' . qa_lang_html('plugin_rest_api/rest_api_request') . '</b><br>
					<div class="api-endpoint-request">
						<pre>' . qa_opt ( 'site_url' ) . 'api/v1/answers/</pre>
					</div>
					<b>' . qa_lang_html('plugin_rest_api/rest_api_response') . '</b>
					<div class="api-endpoint-response">
						<pre>[
    {
        "answerid": 2,
        "questionid": 1,
        "content": "This is a answer test 1"
    },
    {
        "answerid": 3,
        "questionid": 1,
        "content": "This is a answer test 2"
    }
]</pre>
					</div>
				</div>
			</div>';
		
		$qa_content['custom_answers_endpoint_2_title'] =
		'<div>
				<div id="custom_answers_endpoint_2_title" onclick="jQuery(\'#custom_answers_endpoint_2_text\').toggle(\'fast\')" class="api-endpoint-title">
					<span class="api-endpoint-method">GET</span>/v1/answers/{answerid}
				</div>';
		$qa_content['custom_answers_endpoint_2_text'] =
		'<div id="custom_answers_endpoint_2_text" class="api-endpoint-info">'
					. qa_lang_html('plugin_rest_api/rest_api_answers__answers_answerid') .
					'<br><b>' . qa_lang_html('plugin_rest_api/rest_api_request') . '</b><br>
					<div class="api-endpoint-request">
						<pre>' . qa_opt ( 'site_url' ) . 'api/v1/answers/2</pre>
					</div>
					<b>' . qa_lang_html('plugin_rest_api/rest_api_response') . '</b>
					<div class="api-endpoint-response">
						<pre>[
    {
        "answerid": 2,
        "questionid": 1,
        "content": "This is a answer test 1"
    }
]</pre>
					</div>
				</div>
			</div>';
		
		$qa_content['custom_answers_endpoint_3_title'] =
		'<div>
				<div id="custom_answers_endpoint_3_title" onclick="jQuery(\'#custom_answers_endpoint_3_text\').toggle(\'fast\')" class="api-endpoint-title">
					<span class="api-endpoint-method">GET</span>/v1/answers/{fromanswerid}/{toanswerid}
				</div>';
		$qa_content['custom_answers_endpoint_3_text'] =
		'<div id="custom_answers_endpoint_3_text" class="api-endpoint-info">'
				. qa_lang_html('plugin_rest_api/rest_api_answers__answers_range') .
				'<br><b>' . qa_lang_html('plugin_rest_api/rest_api_request') . '</b><br>
					<div class="api-endpoint-request">
						<pre>' . qa_opt ( 'site_url' ) . 'api/v1/answers/1/2</pre>
					</div>
					<b>' . qa_lang_html('plugin_rest_api/rest_api_response') . '</b>
					<div class="api-endpoint-response">
						<pre>[
    {
        "answerid": 2,
        "questionid": 1,
        "content": "This is a answer test 1"
    }
]</pre>
					</div>
				</div>
			</div>';
		
		$qa_content['custom_resource_categories'] =
		'<div class="api-resource-title">Categories</div>';
		
		$qa_content['custom_resource_categories_desc'] = qa_lang_html('plugin_rest_api/rest_api_categories_desc');
		
		$qa_content['custom_categories_endpoint_1_title'] =
		'<div>
				<div id="custom_categories_endpoint_1_title" onclick="jQuery(\'#custom_categories_endpoint_1_text\').toggle(\'fast\')" class="api-endpoint-title">
					<span class="api-endpoint-method">GET</span>/v1/categories/
				</div>';
		$qa_content['custom_categories_endpoint_1_text'] =
		'<div id="custom_categories_endpoint_1_text" class="api-endpoint-info">'
					. qa_lang_html('plugin_rest_api/rest_api_categories__categories_') .
					'<br><b>' . qa_lang_html('plugin_rest_api/rest_api_request') . '</b><br>
					<div class="api-endpoint-request">
						<pre>' . qa_opt ( 'site_url' ) . 'api/v1/categories/</pre>
					</div>
					<b>' . qa_lang_html('plugin_rest_api/rest_api_response') . '</b>
					<div class="api-endpoint-response">
						<pre>[
    {
        "categoryid": 1,
        "title": "Test category 1",
        "qcount": 2
    },
    {
        "categoryid": 2,
        "title": "Test category 2",
        "qcount": 0
    }
]</pre>
					</div>
				</div>
			</div>';
		
		$qa_content['custom_categories_endpoint_2_title'] =
		'<div>
				<div id="custom_categories_endpoint_2_title" onclick="jQuery(\'#custom_categories_endpoint_2_text\').toggle(\'fast\')" class="api-endpoint-title">
					<span class="api-endpoint-method">GET</span>/v1/categories/{categoryid}
				</div>';
		$qa_content['custom_categories_endpoint_2_text'] =
		'<div id="custom_categories_endpoint_2_text" class="api-endpoint-info">'
					. qa_lang_html('plugin_rest_api/rest_api_categories__categories_categoryid') .
					'<br><b>' . qa_lang_html('plugin_rest_api/rest_api_request') . '</b><br>
					<div class="api-endpoint-request">
						<pre>' . qa_opt ( 'site_url' ) . 'api/v1/categories/1</pre>
					</div>
					<b>' . qa_lang_html('plugin_rest_api/rest_api_response') . '</b>
					<div class="api-endpoint-response">
						<pre>[
    {
        "categoryid": 1,
        "title": "Test category 1",
        "qcount": 2
    }
]</pre>
					</div>
				</div>
			</div>';
		
		$qa_content['custom_resource_tags'] =
		'<div class="api-resource-title">Tags</div>';
		
		$qa_content['custom_resource_tags_desc'] = qa_lang_html('plugin_rest_api/rest_api_tags_desc');
		
		$qa_content['custom_tags_endpoint_1_title'] =
		'<div>
				<div id="custom_tags_endpoint_1_title" onclick="jQuery(\'#custom_tags_endpoint_1_text\').toggle(\'fast\')" class="api-endpoint-title">
					<span class="api-endpoint-method">GET</span>/v1/tags/
				</div>';
		$qa_content['custom_tags_endpoint_1_text'] =
		'<div id="custom_tags_endpoint_1_text" class="api-endpoint-info">'
					. qa_lang_html('plugin_rest_api/rest_api_tags__tags_') .
					'<br><b>' . qa_lang_html('plugin_rest_api/rest_api_request') . '</b><br>
					<div class="api-endpoint-request">
						<pre>' . qa_opt ( 'site_url' ) . 'api/v1/tags/</pre>
					</div>
					<b>' . qa_lang_html('plugin_rest_api/rest_api_response') . '</b>
					<div class="api-endpoint-response">
						<pre>[
    {
        "tagid": 3,
        "title": "test1",
        "tagcount": 2
    },
    {
        "tagid": 5,
        "title": "test2",
        "tagcount": 2
    },
    {
        "tagid": 9,
        "title": "test3",
        "tagcount": 2
    }
]</pre>
					</div>
				</div>
			</div>';
		
		$qa_content['custom_tags_endpoint_2_title'] =
		'<div>
				<div id="custom_tags_endpoint_2_title" onclick="jQuery(\'#custom_tags_endpoint_2_text\').toggle(\'fast\')" class="api-endpoint-title">
					<span class="api-endpoint-method">GET</span>/v1/tags/{tagid}
				</div>';
		$qa_content['custom_tags_endpoint_2_text'] =
		'<div id="custom_tags_endpoint_2_text" class="api-endpoint-info">'
					. qa_lang_html('plugin_rest_api/rest_api_tags__tags_tagid') .
					'<br><b>' . qa_lang_html('plugin_rest_api/rest_api_request') . '</b><br>
					<div class="api-endpoint-request">
						<pre>' . qa_opt ( 'site_url' ) . 'api/v1/tags/3</pre>
					</div>
					<b>' . qa_lang_html('plugin_rest_api/rest_api_response') . '</b>
					<div class="api-endpoint-response">
						<pre>[
    {
        "tagid": 3,
        "title": "test1",
        "tagcount": 2
    }
]</pre>
					</div>
				</div>
			</div>';
		
		return $qa_content;
	}
	
}