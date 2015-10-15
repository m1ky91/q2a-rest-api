<?php

require_once QA_INCLUDE_DIR.'app/posts.php';

class qa_rest_api_response_page {
	
	var $userslimit;
	var $questionslimit;
	var $answerslimit;
	
	function match_request($request) {
		$parts = explode ( '/', $request );
		
		return $parts [0] == 'api' && $parts [1] == 'v1'; //&& sizeof ( $parts ) > 1;
	}
	
	function process_request($request) {
		header ( 'Content-Type: application/json' );
		
		$this->userslimit = qa_opt ( 'plugin_rest_api_max_users' );
		$this->questionslimit = qa_opt ( 'plugin_rest_api_max_questions' );
		$this->answerslimit = qa_opt ( 'plugin_rest_api_max_users' );
		
		$parts = explode ( '/', $request );
		$resource = $parts [2];
		$id = null;
		$range = false;
		$from = null;
		$to = null;
		if (sizeof ( $parts ) == 4) {
			if (is_numeric($parts [3]) && intval($parts [3]) > 0)
				$id = $parts [3];
			else 
				$resource = 'invalid';
		} else if (sizeof ( $parts ) == 6) {
			if (strcmp($parts [3], 'range') == 0 && 
				is_numeric($parts [4]) &&
				intval($parts [4]) > 0 &&
				is_numeric($parts [5]) &&
				intval($parts [5]) > 0 &&
				intval($parts [5]) > intval($parts [4])) {
					$range = true;
					$from = $parts [4];
					$to = $parts [5];
					if (strcmp($resource, 'users') != 0 &&
						strcmp($resource, 'questions') != 0 &&
						strcmp($resource, 'answers') != 0)
						$resource = 'invalid';
						
			} else {
				$resource = 'invalid';
			}
		} else if (sizeof($parts) == 5 || sizeof($parts) > 6) {
			$resource = 'invalid';	
		}
		
		/* 
		 * Internal security (non for third-party applications)
		 * 
		 * if (qa_user_permit_error ( 'plugin_rest_api_permit' )) {
			http_response_code ( 401 );
			
			$ret_val = array ();
			
			$json_object = array ();
			
			$json_object ['statuscode'] = '401';
			$json_object ['message'] = 'Unauthorized';
			$json_object ['details'] = 'The user is not authorized to use the API.';
			
			array_push ( $ret_val, $json_object );
			echo json_encode ( $ret_val, JSON_PRETTY_PRINT );
			
			return;
		} */
		
		$method = $_SERVER['REQUEST_METHOD'];
		
		switch ($resource) {
			case 'users' :
				if ($id == null) {
					if ($range) 
						echo $this->get_range_of_users ($from, $to);
					else
						echo $this->get_users ();
				} else {
					echo $this->get_user ( $id );
				}
				break;
			
			case 'questions' :
				if ($id == null) {
					if (strcmp($method, 'POST') == 0) {						
						$inputJSON = file_get_contents('php://input');
						$content = json_decode( $inputJSON, TRUE );
						
						echo $this->post_question ($content);
					} else {
						if ($range)
							echo $this->get_range_of_questions ($from, $to);
						else
							echo $this->get_questions ();
					}
				} else
					echo $this->get_question ( $id );
				break;
			
			case 'answers' :
				if ($id == null) {
					if (strcmp($method, 'POST') == 0) {
						$inputJSON = file_get_contents('php://input');
						$content = json_decode( $inputJSON, TRUE );
					
						echo $this->post_answer ($content);
					} else {
						if ($range)
							echo $this->get_range_of_answers ($from, $to);
						else
							echo $this->get_answers ();
					}
				} else
					echo $this->get_answer ( $id );
				break;
			
			case 'categories' :
				if ($id == null)
					echo $this->get_categories ();
				else
					echo $this->get_category ( $id );
				break;
			
			case 'tags' :
				if ($id == null)
					echo $this->get_tags ();
				else
					echo $this->get_tag ( $id );
				break;
			
			default :
				http_response_code ( 400 );
				
				$ret_val = array ();
				
				$json_object = array ();
				
				$json_object ['statuscode'] = '400';
				$json_object ['message'] = 'Bad Request';
				$json_object ['details'] = 'The request URI does not match the API in the system, or the operation failed for unknown reasons.';
				
				array_push ( $ret_val, $json_object );
				echo json_encode ( $ret_val, JSON_PRETTY_PRINT );
		}
	}
	
	function get_range_of_users($from, $to) {
		$rows = qa_db_query_sub ( "SELECT ^users.userid, ^users.handle, ^userpoints.points, ^userpoints.qposts, ^userpoints.aposts FROM ^users INNER JOIN ^userpoints ON ^users.userid=^userpoints.userid WHERE ^users.userid>=$from && ^users.userid<=$to ORDER BY ^users.userid ASC;" );
		
		$ret_val = array ();
	
		while ( ($row = qa_db_read_one_assoc ( $rows, true )) !== null) {
			$json_object = array ();
				
			$json_object ['userid'] = intval ( $row ['userid'] );
			$json_object ['handle'] = $row ['handle'];
			$json_object ['points'] = intval ( $row ['points'] );
			$json_object ['qcount'] = intval ( $row ['qposts'] );
			$json_object ['acount'] = intval ( $row ['aposts'] );
				
			array_push ( $ret_val, $json_object );
		}
	
		if ($this->userslimit == 0) { // Maximum number of users in response is 10
			if (count ( $ret_val ) > 10) {
				$random_keys = array_rand ( $ret_val, 10 );
	
				$random_rows = array ();
	
				for($i = 0; $i < count ( $random_keys ); ++ $i)
					array_push ( $random_rows, $ret_val [$random_keys [$i]] );
	
				$ret_val = $random_rows;
			}
		} else if ($this->userslimit == 1) { // Maximum number of users in response is 100
			if (count ( $ret_val ) > 100) {
				$random_keys = array_rand ( $ret_val, 100 );
	
				$random_rows = array ();
	
				for($i = 0; $i < count ( $random_keys ); ++ $i)
					array_push ( $random_rows, $ret_val [$random_keys [$i]] );
	
				$ret_val = $random_rows;
			}
		}
	
		if ($ret_val == null) {
			http_response_code ( 404 );
			
			$json_object = array ();
			
			$json_object ['statuscode'] = '404';
			$json_object ['message'] = 'Not found';
			$json_object ['details'] = 'The requested resource was not found.';
			
			array_push ( $ret_val, $json_object );
		} else
			http_response_code ( 200 );
	
		return json_encode ( $ret_val, JSON_PRETTY_PRINT );
	}
	
	function get_users() {
		$rows = qa_db_query_sub ( "SELECT ^users.userid, ^users.handle, ^userpoints.points, ^userpoints.qposts, ^userpoints.aposts FROM ^users INNER JOIN ^userpoints ON ^users.userid=^userpoints.userid;" );
		
		$ret_val = array ();
		
		while ( ($row = qa_db_read_one_assoc ( $rows, true )) !== null ) {
			$json_object = array ();
			
			$json_object ['userid'] = intval ( $row ['userid'] );
			$json_object ['handle'] = $row ['handle'];
			$json_object ['points'] = intval ( $row ['points'] );
			$json_object ['qcount'] = intval ( $row ['qposts'] );
			$json_object ['acount'] = intval ( $row ['aposts'] );
			
			array_push ( $ret_val, $json_object );
		}
		
		if ($this->userslimit == 0) { // Maximum number of users in response is 10
			if (count ( $ret_val ) > 10) {
				$random_keys = array_rand ( $ret_val, 10 );
				
				$random_rows = array ();
				
				for($i = 0; $i < count ( $random_keys ); ++ $i)
					array_push ( $random_rows, $ret_val [$random_keys [$i]] );
				
				$ret_val = $random_rows;
			}
		} else if ($this->userslimit == 1) { // Maximum number of users in response is 100
			if (count ( $ret_val ) > 100) {
				$random_keys = array_rand ( $ret_val, 100 );
				
				$random_rows = array ();
				
				for($i = 0; $i < count ( $random_keys ); ++ $i)
					array_push ( $random_rows, $ret_val [$random_keys [$i]] );
				
				$ret_val = $random_rows;
			}
		}
		
		if ($ret_val == null) {
			http_response_code ( 404 );
			
			$json_object = array ();
			
			$json_object ['statuscode'] = '404';
			$json_object ['message'] = 'Not found';
			$json_object ['details'] = 'The requested resource was not found.';
			
			array_push ( $ret_val, $json_object );
		} else
			http_response_code ( 200 );
		
		return json_encode ( $ret_val, JSON_PRETTY_PRINT );
	}
	
	function get_user($userid) {
		$rows = qa_db_query_sub ( "SELECT ^users.userid, ^users.handle, ^userpoints.points, ^userpoints.qposts, ^userpoints.aposts FROM ^users INNER JOIN ^userpoints ON ^users.userid=^userpoints.userid WHERE ^users.userid=$userid;" );
		
		$ret_val = array ();
		
		while ( ($row = qa_db_read_one_assoc ( $rows, true )) !== null ) {
			$json_object = array ();
			
			$json_object ['userid'] = intval ( $row ['userid'] );
			$json_object ['handle'] = $row ['handle'];
			$json_object ['points'] = intval ( $row ['points'] );
			$json_object ['qcount'] = intval ( $row ['qposts'] );
			$json_object ['acount'] = intval ( $row ['aposts'] );
			
			array_push ( $ret_val, $json_object );
		}
		
		if ($ret_val == null) {
			http_response_code ( 404 );
			
			$json_object = array ();
			
			$json_object ['statuscode'] = '404';
			$json_object ['message'] = 'Not found';
			$json_object ['details'] = 'The requested resource was not found.';
			
			array_push ( $ret_val, $json_object );
		} else
			http_response_code ( 200 );
		
		return json_encode ( $ret_val, JSON_PRETTY_PRINT );
	}
	
	function get_range_of_questions($from, $to) {
		$rows = qa_db_query_sub ( "SELECT postid, title, content, categoryid, tags, userid, created, acount FROM ^posts WHERE type='Q' && postid>=$from && postid<=$to ORDER BY postid ASC;" );
	
		$ret_val = array ();
	
		while ( ($row = qa_db_read_one_assoc ( $rows, true )) !== null ) {
			$json_object = array ();
				
			$json_object ['questionid'] = intval ( $row ['postid'] );
			$json_object ['title'] = $row ['title'];
			$json_object ['content'] = $row ['content'];
			$json_object ['categoryid'] = intval ( $row ['categoryid'] );
			$tags = explode ( ',', $row ['tags'] );
			$json_object ['tags'] = $tags;
			$json_object ['userid'] = intval ( $row ['userid'] );
			$json_object ['creationdate'] = $row ['created'];
			$json_object ['acount'] = intval ( $row ['acount'] );
				
			array_push ( $ret_val, $json_object );
		}
	
		if ($this->questionslimit == 0) { // Maximum number of questions in response is 10
			if (count ( $ret_val ) > 10) {
				$random_keys = array_rand ( $ret_val, 10 );
	
				$random_rows = array ();
	
				for($i = 0; $i < count ( $random_keys ); ++ $i)
					array_push ( $random_rows, $ret_val [$random_keys [$i]] );
	
				$ret_val = $random_rows;
			}
		} else if ($this->questionslimit == 1) { // Maximum number of questions in response is 100
			if (count ( $ret_val ) > 100) {
				$random_keys = array_rand ( $ret_val, 100 );
	
				$random_rows = array ();
	
				for($i = 0; $i < count ( $random_keys ); ++ $i)
					array_push ( $random_rows, $ret_val [$random_keys [$i]] );
	
				$ret_val = $random_rows;
			}
		}
	
		if ($ret_val == null) {
			http_response_code ( 404 );
			
			$json_object = array ();
			
			$json_object ['statuscode'] = '404';
			$json_object ['message'] = 'Not found';
			$json_object ['details'] = 'The requested resource was not found.';
			
			array_push ( $ret_val, $json_object );
		} else
			http_response_code ( 200 );
	
		return json_encode ( $ret_val, JSON_PRETTY_PRINT );
	}
	
	function get_questions() {
		$rows = qa_db_query_sub ( "SELECT postid, title, content, categoryid, tags, userid, created, acount FROM ^posts WHERE type='Q';" );
		
		$ret_val = array ();
		
		while ( ($row = qa_db_read_one_assoc ( $rows, true )) !== null ) {
			$json_object = array ();
			
			$json_object ['questionid'] = intval ( $row ['postid'] );
			$json_object ['title'] = $row ['title'];
			$json_object ['content'] = $row ['content'];
			$json_object ['categoryid'] = intval ( $row ['categoryid'] );
			$tags = explode ( ',', $row ['tags'] );
			$json_object ['tags'] = $tags;
			$json_object ['userid'] = intval ( $row ['userid'] );
			$json_object ['creationdate'] = $row ['created'];
			$json_object ['acount'] = intval ( $row ['acount'] );
			
			array_push ( $ret_val, $json_object );
		}
		
		if ($this->questionslimit == 0) { // Maximum number of questions in response is 10
			if (count ( $ret_val ) > 10) {
				$random_keys = array_rand ( $ret_val, 10 );
				
				$random_rows = array ();
				
				for($i = 0; $i < count ( $random_keys ); ++ $i)
					array_push ( $random_rows, $ret_val [$random_keys [$i]] );
				
				$ret_val = $random_rows;
			}
		} else if ($this->questionslimit == 1) { // Maximum number of questions in response is 100
			if (count ( $ret_val ) > 100) {
				$random_keys = array_rand ( $ret_val, 100 );
				
				$random_rows = array ();
				
				for($i = 0; $i < count ( $random_keys ); ++ $i)
					array_push ( $random_rows, $ret_val [$random_keys [$i]] );
				
				$ret_val = $random_rows;
			}
		}
		
		if ($ret_val == null) {
			http_response_code ( 404 );
			
			$json_object = array ();
			
			$json_object ['statuscode'] = '404';
			$json_object ['message'] = 'Not found';
			$json_object ['details'] = 'The requested resource was not found.';
			
			array_push ( $ret_val, $json_object );
		} else
			http_response_code ( 200 );
		
		return json_encode ( $ret_val, JSON_PRETTY_PRINT );
	}
	
	function get_question($questionid) {
		$rows = qa_db_query_sub ( "SELECT postid, title, content, categoryid, tags, userid, created, acount FROM ^posts WHERE type='Q' && postid=$questionid;" );
		
		$ret_val = array ();
		
		while ( ($row = qa_db_read_one_assoc ( $rows, true )) !== null ) {
			$json_object = array ();
			
			$json_object ['questionid'] = intval ( $row ['postid'] );
			$json_object ['title'] = $row ['title'];
			$json_object ['content'] = $row ['content'];
			$json_object ['categoryid'] = intval ( $row ['categoryid'] );
			$tags = explode ( ',', $row ['tags'] );
			$json_object ['tags'] = $tags;
			$json_object ['userid'] = intval ( $row ['userid'] );
			$json_object ['creationdate'] = $row ['created'];
			$json_object ['acount'] = intval ( $row ['acount'] );
			
			array_push ( $ret_val, $json_object );
		}
		
		if ($ret_val == null) {
			http_response_code ( 404 );
			
			$json_object = array ();
			
			$json_object ['statuscode'] = '404';
			$json_object ['message'] = 'Not found';
			$json_object ['details'] = 'The requested resource was not found.';
			
			array_push ( $ret_val, $json_object );
		} else
			http_response_code ( 200 );
		
		return json_encode ( $ret_val, JSON_PRETTY_PRINT );
	}
	
	function post_question( $content ) {
		require_once QA_INCLUDE_DIR.'qa-app-posts.php';
		require_once QA_INCLUDE_DIR.'qa-app-post-create.php';
		
		$id = qa_post_create('Q', null, $content['title'], $content['content'], 'html', $content['categoryid'], $content['tags']);
				
		$rows = qa_db_query_sub ( "SELECT postid, title, content, categoryid, tags, userid, created, acount FROM ^posts WHERE type='Q' && postid=$id;" );
		
		$ret_val = array ();
		
		while ( ($row = qa_db_read_one_assoc ( $rows, true )) !== null ) {
			$json_object = array ();
				
			$json_object ['questionid'] = intval ( $row ['postid'] );
			$json_object ['title'] = $row ['title'];
			$json_object ['content'] = $row ['content'];
			$json_object ['categoryid'] = intval ( $row ['categoryid'] );
			$tags = explode ( ',', $row ['tags'] );
			$json_object ['tags'] = $tags;
			$json_object ['userid'] = intval ( $row ['userid'] );
			$json_object ['creationdate'] = $row ['created'];
			$json_object ['acount'] = intval ( $row ['acount'] );
				
			array_push ( $ret_val, $json_object );
		}
		
		if ($ret_val == null) {
			http_response_code ( 404 );
				
			$json_object = array ();
				
			$json_object ['statuscode'] = '404';
			$json_object ['message'] = 'Not found';
			$json_object ['details'] = 'The requested resource was not found.';
				
			array_push ( $ret_val, $json_object );
		} else
			http_response_code ( 200 );
		
		return json_encode ( $ret_val, JSON_PRETTY_PRINT );
	}
	
	function get_range_of_answers($from, $to) {
		$rows = qa_db_query_sub ( "SELECT postid, parentid, content FROM ^posts WHERE type='A' && postid>=$from && postid<=$to ORDER BY postid ASC;" );
	
		$ret_val = array ();
	
		while ( ($row = qa_db_read_one_assoc ( $rows, true )) !== null ) {
			$json_object = array ();
				
			$json_object ['answerid'] = intval ( $row ['postid'] );
			$json_object ['questionid'] = intval ( $row ['parentid'] );
			$json_object ['content'] = $row ['content'];
				
			array_push ( $ret_val, $json_object );
		}
	
		if ($this->answerslimit == 0) { // Maximum number of answers in response is 10
			if (count ( $ret_val ) > 10) {
				$random_keys = array_rand ( $ret_val, 10 );
	
				$random_rows = array ();
	
				for($i = 0; $i < count ( $random_keys ); ++ $i)
					array_push ( $random_rows, $ret_val [$random_keys [$i]] );
	
				$ret_val = $random_rows;
			}
		} else if ($this->answerslimit == 1) { // Maximum number of answers in response is 100
			if (count ( $ret_val ) > 100) {
				$random_keys = array_rand ( $ret_val, 100 );
	
				$random_rows = array ();
	
				for($i = 0; $i < count ( $random_keys ); ++ $i)
					array_push ( $random_rows, $ret_val [$random_keys [$i]] );
	
				$ret_val = $random_rows;
			}
		}
	
		if ($ret_val == null) {
			http_response_code ( 404 );
				
			$json_object = array ();
				
			$json_object ['statuscode'] = '404';
			$json_object ['message'] = 'Not found';
			$json_object ['details'] = 'The requested resource was not found.';
				
			array_push ( $ret_val, $json_object );
		} else
			http_response_code ( 200 );
	
		return json_encode ( $ret_val, JSON_PRETTY_PRINT );
	}
	
	function get_answers() {
		$rows = qa_db_query_sub ( "SELECT postid, parentid, content FROM ^posts WHERE type='A';" );
		
		$ret_val = array ();
		
		while ( ($row = qa_db_read_one_assoc ( $rows, true )) !== null ) {
			$json_object = array ();
			
			$json_object ['answerid'] = intval ( $row ['postid'] );
			$json_object ['questionid'] = intval ( $row ['parentid'] );
			$json_object ['content'] = $row ['content'];
			
			array_push ( $ret_val, $json_object );
		}
		
		if ($this->answerslimit == 0) { // Maximum number of answers in response is 10
			if (count ( $ret_val ) > 10) {
				$random_keys = array_rand ( $ret_val, 10 );
				
				$random_rows = array ();
				
				for($i = 0; $i < count ( $random_keys ); ++ $i)
					array_push ( $random_rows, $ret_val [$random_keys [$i]] );
				
				$ret_val = $random_rows;
			}
		} else if ($this->answerslimit == 1) { // Maximum number of answers in response is 100
			if (count ( $ret_val ) > 100) {
				$random_keys = array_rand ( $ret_val, 100 );
				
				$random_rows = array ();
				
				for($i = 0; $i < count ( $random_keys ); ++ $i)
					array_push ( $random_rows, $ret_val [$random_keys [$i]] );
				
				$ret_val = $random_rows;
			}
		}
		
		if ($ret_val == null) {
			http_response_code ( 404 );
			
			$json_object = array ();
			
			$json_object ['statuscode'] = '404';
			$json_object ['message'] = 'Not found';
			$json_object ['details'] = 'The requested resource was not found.';
			
			array_push ( $ret_val, $json_object );
		} else
			http_response_code ( 200 );
		
		return json_encode ( $ret_val, JSON_PRETTY_PRINT );
	}
	
	function get_answer($answerid) {
		$rows = qa_db_query_sub ( "SELECT postid, parentid, content FROM ^posts WHERE type='A' && postid=$answerid;" );
		
		$ret_val = array ();
		
		while ( ($row = qa_db_read_one_assoc ( $rows, true )) !== null ) {
			$json_object = array ();
			
			$json_object ['answerid'] = intval ( $row ['postid'] );
			$json_object ['questionid'] = intval ( $row ['parentid'] );
			$json_object ['content'] = $row ['content'];
			
			array_push ( $ret_val, $json_object );
		}
		
		if ($ret_val == null) {
			http_response_code ( 404 );
			
			$json_object = array ();
			
			$json_object ['statuscode'] = '404';
			$json_object ['message'] = 'Not found';
			$json_object ['details'] = 'The requested resource was not found.';
			
			array_push ( $ret_val, $json_object );
		} else
			http_response_code ( 200 );
		
		return json_encode ( $ret_val, JSON_PRETTY_PRINT );
	}
	
	function post_answer( $content ) {
		require_once QA_INCLUDE_DIR.'qa-app-posts.php';
		require_once QA_INCLUDE_DIR.'qa-app-post-create.php';
	
		$id = qa_post_create('A', $content['questionid'], null, $content['content'], 'html', null, null);
	
		$rows = qa_db_query_sub ( "SELECT postid, parentid, content FROM ^posts WHERE type='A' && postid=$id;" );
		
		$ret_val = array ();
		
		while ( ($row = qa_db_read_one_assoc ( $rows, true )) !== null ) {
			$json_object = array ();
				
			$json_object ['answerid'] = intval ( $row ['postid'] );
			$json_object ['questionid'] = intval ( $row ['parentid'] );
			$json_object ['content'] = $row ['content'];
				
			array_push ( $ret_val, $json_object );
		}
		
		if ($ret_val == null) {
			http_response_code ( 404 );
				
			$json_object = array ();
				
			$json_object ['statuscode'] = '404';
			$json_object ['message'] = 'Not found';
			$json_object ['details'] = 'The requested resource was not found.';
				
			array_push ( $ret_val, $json_object );
		} else
			http_response_code ( 200 );
		
		return json_encode ( $ret_val, JSON_PRETTY_PRINT );
	}
	
	function get_categories() {
		$rows = qa_db_query_sub ( "SELECT categoryid, title, qcount FROM ^categories;" );
		
		$ret_val = array ();
		
		while ( ($row = qa_db_read_one_assoc ( $rows, true )) !== null ) {
			$json_object = array ();
			
			$json_object ['categoryid'] = intval ( $row ['categoryid'] );
			$json_object ['title'] = $row ['title'];
			$json_object ['qcount'] = intval ( $row ['qcount'] );
			
			array_push ( $ret_val, $json_object );
		}
		
		if ($ret_val == null) {
			http_response_code ( 404 );
			
			$json_object = array ();
			
			$json_object ['statuscode'] = '404';
			$json_object ['message'] = 'Not found';
			$json_object ['details'] = 'The requested resource was not found.';
			
			array_push ( $ret_val, $json_object );
		} else
			http_response_code ( 200 );
		
		return json_encode ( $ret_val, JSON_PRETTY_PRINT );
	}
	
	function get_category($categoryid) {
		$rows = qa_db_query_sub ( "SELECT categoryid, title, qcount FROM ^categories WHERE categoryid=$categoryid;" );
		
		$ret_val = array ();
		
		while ( ($row = qa_db_read_one_assoc ( $rows, true )) !== null ) {
			$json_object = array ();
			
			$json_object ['categoryid'] = intval ( $row ['categoryid'] );
			$json_object ['title'] = $row ['title'];
			$json_object ['qcount'] = intval ( $row ['qcount'] );
			
			array_push ( $ret_val, $json_object );
		}
		
		if ($ret_val == null) {
			http_response_code ( 404 );
			
			$json_object = array ();
			
			$json_object ['statuscode'] = '404';
			$json_object ['message'] = 'Not found';
			$json_object ['details'] = 'The requested resource was not found.';
			
			array_push ( $ret_val, $json_object );
		} else
			http_response_code ( 200 );
		
		return json_encode ( $ret_val, JSON_PRETTY_PRINT );
	}
	
	function get_tags() {
		$rows = qa_db_query_sub ( "SELECT ^words.wordid, ^words.word, ^words.tagcount FROM ^words INNER JOIN ^posttags ON ^words.wordid=^posttags.wordid;" );
		
		$ret_val = array ();
		
		while ( ($row = qa_db_read_one_assoc ( $rows, true )) !== null ) {
			$json_object = array ();
			
			$json_object ['tagid'] = intval ( $row ['wordid'] );
			$json_object ['title'] = $row ['word'];
			$json_object ['tagcount'] = intval ( $row ['tagcount'] );
			
			array_push ( $ret_val, $json_object );
		}
		
		if ($ret_val == null) {
			http_response_code ( 404 );
			
			$json_object = array ();
			
			$json_object ['statuscode'] = '404';
			$json_object ['message'] = 'Not found';
			$json_object ['details'] = 'The requested resource was not found.';
			
			array_push ( $ret_val, $json_object );
		} else
			http_response_code ( 200 );
		
		return json_encode ( $ret_val, JSON_PRETTY_PRINT );
	}
	
	function get_tag($tagid) {
		$rows = qa_db_query_sub ( "SELECT ^words.wordid, ^words.word, ^words.tagcount FROM ^words INNER JOIN ^posttags ON ^words.wordid=^posttags.wordid WHERE ^words.wordid=$tagid;" );
		
		$ret_val = array ();
		
		while ( ($row = qa_db_read_one_assoc ( $rows, true )) !== null ) {
			$json_object = array ();
			
			$json_object ['tagid'] = intval ( $row ['wordid'] );
			$json_object ['title'] = $row ['word'];
			$json_object ['tagcount'] = intval ( $row ['tagcount'] );
			
			array_push ( $ret_val, $json_object );
		}
		
		if ($ret_val == null) {
			http_response_code ( 404 );
			
			$json_object = array ();
			
			$json_object ['statuscode'] = '404';
			$json_object ['message'] = 'Not found';
			$json_object ['details'] = 'The requested resource was not found.';
			
			array_push ( $ret_val, $json_object );
		} else
			http_response_code ( 200 );
		
		return json_encode ( $ret_val, JSON_PRETTY_PRINT );
	}
	
}