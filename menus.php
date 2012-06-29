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
			header('Allow: GET, POST, PUT, DELETE, HEAD, OPTIONS');
		break;
		case 'PUT':
		
			// Get the data of the PUT-request
			/*$putSocket = fopen('php://input', 'r');
			$putData = '';
		
			// Save all the data of the PUT-request in a variable
			while($data = fread($putSocket, 1024)) {
				$putData .= $data;
			}		
			fclose($putSocket);
			*/
			
			$putData = file_get_contents("php://input");
			$putXML = new SimpleXMLElement($putData);

			$name = $mysqli->real_escape_string($putXML->name);
			$description = $mysqli->real_escape_string($putXML->description);

			if($mysqli->query("UPDATE menus SET name = '".$name."', description = '".$description."' WHERE id = '".$_GET['id']."'")) {
				header('http/1.1 204 No Content');
			} else {
				header('http/1.1 500 Internal Server Error');	
			}
			
		break;
			
		case 'GET':
		
			if(isset($_GET['format'])) {
				// Get all menu ordered by name
				$result = $mysqli->query("SELECT * FROM menus ORDER BY name");

				while($menu = $result->fetch_assoc()) {
					$resultsArray[] = array(
						'id' 			=> $menu['id'], 
						'name' 			=> $menu['name'], 
						'description' 	=> $menu['description']
					);
				}
														
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
						if(is_array($resultsArray)) {
							foreach($resultsArray as $menu) {
								$xmlMenu = $xml->addChild('menu');
								$xmlMenu->addAttribute('id', $menu['id']);
								$xmlMenu->addChild('name', $menu['name']);
								$xmlMenu->addChild('description', $menu['description']);
							}
						}
						
						echo $xml->asXML();
					break;
					default:
						header('http/1.1 400 Bad Request');
					break;
				}
			} else {
				header('http/1.1 404 Not Found');
			}
		break;
		
		case 'POST':
						
			$postName = $mysqli->real_escape_string($_POST['name']);
			$postDescription = $mysqli->real_escape_string($_POST['description']);
		
			if($mysqli->query("INSERT INTO menus (name, description) VALUES ('".$postName."', '".$postDescription."')")) {
				header('http/1.1 201 Created'); // succeed
			} else {
				header('http/1.1 500 Internal Server Error'); // Failed
			}
		break;
		
		case 'DELETE':
			//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
			
			/*$deleteData = file_get_contents("php://input");
			
			$deleteXML = new SimpleXMLElement($deleteData);
			
			$id = $mysqli->real_escape_string($deleteXML->attributes()->id);*/
			
			if($mysqli->query("DELETE FROM menus WHERE id = '".$_GET['id']."'")) {
		
				header('http/1.1 200 OK'); // succeed
		
			} else {
				header('http/1.1 500 Internal Server Error'); // Failed
			}
		break;
		
		default:
			header('http/1.1 405 Method Not Allowed');
		break;
		
		$mysqli->close();
	}
?>