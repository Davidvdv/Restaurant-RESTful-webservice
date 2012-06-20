<?php
		
	$mysqli = new mysqli('localhost', 'root', 'root', 'STR6');
	
	// Save request method in $requestMethod.
	$requestMethod = $_SERVER['REQUEST_METHOD'];	

	// Check for the request method
	switch($requestMethod) {
		case 'HEAD':
			// Sends headers back to client.
		break;
		case 'OPTIONS':
			// Sends the HTTP-request methods back which are allowed.
			header('Allow: GET, POST, HEAD, OPTIONS, PUT');
		break;
		case 'PUT':

			
		case 'GET':
			$result = $mysqli->query("SELECT * FROM menus ORDER BY name");

			while($menu = $result->fetch_assoc()) {
				$tmpArray[] = array(
					'id' 		=> $menu['id'], 
					'name' 		=> $menu['name'], 
					'description' 	=> $menu['description']
				);
			}
			
			$resultsArray = $tmpArray;
											
			// Check for data type in the URI.
			switch($_GET['format']) {
				case 'json':
					header('Content-Type: text/json');
					
					$json['menus'] = $resultsArray;
					
					echo json_encode($json);
				break;
				case 'xml':
					header('Content-Type: text/xml');
					
					$xml = new SimpleXMLElement('<menus/>');
					foreach($resultsArray as $menu) {
						$xmlMenu = $xml->addChild('menu');
						$xmlMenu->addAttribute('id', $menu['id']);
						$xmlMenu->addAttribute('name', $menu['name']);
						$xmlMenu->addChild('description', $menu['description']);
					}
					
					echo $xml->asXML();
				break;
			}
			
		break;
		
		case 'POST':
		
			$postName = $mysqli->real_escape_string($_POST['name']);
			$postDescription = $mysqli->real_escape_string($_POST['description']);
		
			if($mysqli->query("INSERT INTO menus (name, description) VALUES ('".$postName."', '".$postDescription."')")) {
				header('http/1.0 204 '); // succeed
			} else {
				header('http/1.0 500 Internal Server Error'); // Failed
			}
		break;
		
		$mysqli->close();
	}
?>