<?php
	require_once('config.php');

	$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DB);
	
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
			$result = $mysqli->query("SELECT * FROM categories ORDER BY category");

			while($r = $result->fetch_assoc()) {
				$categories['categories'][] = array(
					'id' 		=> $r['id'], 
					'category'	=> $r['category']
				);
			}

			// Check for data type in the URI.
			switch($_GET['format']) {
				case 'json':
					header('Content-Type: text/json');
					
					echo json_encode($categories);
				break;
				case 'xml':
					header('Content-Type: text/xml');
					
					$xml = new SimpleXMLElement('<categories/>');

					foreach($categories['categories'] as $cat) {
						$xmlDish = $xml->addChild('category');
						$xmlDish->addAttribute('id', $cat['id']);
						$xmlDish->addChild('category', $cat['category']);
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