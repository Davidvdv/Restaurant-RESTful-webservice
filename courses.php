<?php
		
	$mysqli = new mysqli('localhost', 'root', 'root', 'STR6');
	
	// Save request method in $requestMethod.
	$requestMethod = $_SERVER['REQUEST_METHOD'];	

	// Check for the request method
	switch($requestMethod) {
		case 'HEAD':
			// Sends headers back to client.
			header("Cache-Control: no-cache, must-revalidate");
		break;
		case 'OPTIONS':
			// Sends the HTTP-request methods back which are allowed.
			header('Allow: GET, HEAD, OPTIONS');
		break;

		case 'GET':
			$result = $mysqli->query("SELECT * FROM courses ORDER BY course");

			while($r = $result->fetch_assoc()) {
				$courses['courses'][] = array(
					'id' 		=> $r['id'], 
					'course'	=> $r['course']
				);
			}
														
			// Check for data type in the URI.
			switch($_GET['format']) {
				case 'json':
					header('Content-Type: text/json');
					
					echo json_encode($courses);
				break;
				case 'xml':
					header('Content-Type: text/xml');
					
					$xml = new SimpleXMLElement('<categories/>');

					foreach($categories['categories'] as $cat) {
						$xmlDish = $xml->addChild('course');
						$xmlDish->addAttribute('id', $cat['id']);
						$xmlDish->addChild('course', $cat['course']);
					}
					
					echo $xml->asXML();
				break;
				default:
					header('http/1.1 405 Method Not Allowed');
				break;
			}
			
		break;
		
		$mysqli->close();
	}
?>