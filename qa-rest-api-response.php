<?php
class qa_rest_api_response_page {
	
	var $userslimit;
	var $questionslimit;
	var $answerslimit;
	
	function match_request($request) {
		$parts = explode ( '/', $request );
		
		return $parts [0] == 'api' && sizeof ( $parts ) > 1;
	}
	
	function process_request($request) {
		header ( 'Content-Type: application/json' );
		
		$this->userslimit = qa_opt ( 'plugin_rest_api_max_users' );
		$this->questionslimit = qa_opt ( 'plugin_rest_api_max_questions' );
		$this->answerslimit = qa_opt ( 'plugin_rest_api_max_users' );
		
		$parts = explode ( '/', $request );
		$resource = $parts [1];
		$id = null;
		if (sizeof ( $parts ) == 3)
			$id = $parts [2];
		else if (sizeof ( $parts ) > 3)
			$resource = 'invalid';
		
		if (qa_user_permit_error ( 'plugin_rest_api_permit' )) {
			http_response_code ( 401 );
			
			$ret_val = array ();
			
			$json_object = array ();
			
			$json_object ['statuscode'] = '401';
			$json_object ['message'] = 'Unauthorized';
			$json_object ['details'] = 'The user is not authorized to use the API.';
			
			array_push ( $ret_val, $json_object );
			echo json_encode ( $ret_val, JSON_PRETTY_PRINT );
			
			return;
		}
		
		switch ($resource) {
			case 'users' :
				if ($id == null)
					echo $this->get_users ();
				else
					echo $this->get_user ( $id );
				break;
			
			case 'questions' :
				if ($id == null)
					echo $this->get_questions ();
				else
					echo $this->get_question ( $id );
				break;
			
			case 'answers' :
				if ($id == null)
					echo $this->get_answers ();
				else
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
	
	function get_users() {
		$rows = qa_db_query_sub ( "SELECT ^users.userid, ^users.handle, ^userpoints.points, ^userpoints.qposts, ^userpoints.aposts FROM ^users INNER JOIN ^userpoints ON ^users.userid=^userpoints.userid;" );
		
		$ret_val = array ();
		
		while ( ($row = qa_db_read_one_assoc ( $rows, true )) !== null ) {
			$json_object = array ();
			
			$json_object ['userid'] = $row ['userid'];
			$json_object ['handle'] = $row ['handle'];
			$json_object ['points'] = $row ['points'];
			$json_object ['qcount'] = $row ['qposts'];
			$json_object ['acount'] = $row ['aposts'];
			
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
			http_response_code ( 204 );
			
			$json_object = array ();
			
			$json_object ['statuscode'] = '204';
			$json_object ['message'] = 'Success';
			$json_object ['details'] = 'Success with no response body.';
			
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
			
			$json_object ['userid'] = $row ['userid'];
			$json_object ['handle'] = $row ['handle'];
			$json_object ['points'] = $row ['points'];
			$json_object ['qcount'] = $row ['qposts'];
			$json_object ['acount'] = $row ['aposts'];
			
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
	
	function get_questions() {
		$rows = qa_db_query_sub ( "SELECT postid, title, content, categoryid, tags, userid, created, acount FROM ^posts WHERE type='Q';" );
		
		$ret_val = array ();
		
		while ( ($row = qa_db_read_one_assoc ( $rows, true )) !== null ) {
			$json_object = array ();
			
			$json_object ['questionid'] = $row ['postid'];
			$json_object ['title'] = $row ['title'];
			$json_object ['content'] = $row ['content'];
			$json_object ['categoryid'] = $row ['categoryid'];
			$tags = explode ( ',', $row ['tags'] );
			$json_object ['tags'] = $tags;
			$json_object ['userid'] = $row ['userid'];
			$json_object ['creationdate'] = $row ['created'];
			$json_object ['acount'] = $row ['acount'];
			
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
			http_response_code ( 204 );
			
			$json_object = array ();
			
			$json_object ['statuscode'] = '204';
			$json_object ['message'] = 'Success';
			$json_object ['details'] = 'Success with no response body.';
			
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
			
			$json_object ['questionid'] = $row ['postid'];
			$json_object ['title'] = $row ['title'];
			$json_object ['content'] = $row ['content'];
			$json_object ['categoryid'] = $row ['categoryid'];
			$tags = explode ( ',', $row ['tags'] );
			$json_object ['tags'] = $tags;
			$json_object ['userid'] = $row ['userid'];
			$json_object ['creationdate'] = $row ['created'];
			$json_object ['acount'] = $row ['acount'];
			
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
	
	function get_answers() {
		$rows = qa_db_query_sub ( "SELECT postid, parentid, content FROM ^posts WHERE type='A';" );
		
		$ret_val = array ();
		
		while ( ($row = qa_db_read_one_assoc ( $rows, true )) !== null ) {
			$json_object = array ();
			
			$json_object ['answerid'] = $row ['postid'];
			$json_object ['questionid'] = $row ['parentid'];
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
			http_response_code ( 204 );
			
			$json_object = array ();
			
			$json_object ['statuscode'] = '204';
			$json_object ['message'] = 'Success';
			$json_object ['details'] = 'Success with no response body.';
			
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
			
			$json_object ['answerid'] = $row ['postid'];
			$json_object ['questionid'] = $row ['parentid'];
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
			
			$json_object ['categoryid'] = $row ['categoryid'];
			$json_object ['title'] = $row ['title'];
			$json_object ['qcount'] = $row ['qcount'];
			
			array_push ( $ret_val, $json_object );
		}
		
		if ($ret_val == null) {
			http_response_code ( 204 );
			
			$json_object = array ();
			
			$json_object ['statuscode'] = '204';
			$json_object ['message'] = 'Success';
			$json_object ['details'] = 'Success with no response body.';
			
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
			
			$json_object ['categoryid'] = $row ['categoryid'];
			$json_object ['title'] = $row ['title'];
			$json_object ['qcount'] = $row ['qcount'];
			
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
			
			$json_object ['tagid'] = $row ['wordid'];
			$json_object ['title'] = $row ['word'];
			$json_object ['tagcount'] = $row ['tagcount'];
			
			array_push ( $ret_val, $json_object );
		}
		
		if ($ret_val == null) {
			http_response_code ( 204 );
			
			$json_object = array ();
			
			$json_object ['statuscode'] = '204';
			$json_object ['message'] = 'Success';
			$json_object ['details'] = 'Success with no response body.';
			
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
			
			$json_object ['tagid'] = $row ['wordid'];
			$json_object ['title'] = $row ['word'];
			$json_object ['tagcount'] = $row ['tagcount'];
			
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