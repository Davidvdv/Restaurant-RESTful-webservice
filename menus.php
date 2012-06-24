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
			
			$putXML = new SimpleXMLElement($putData);

			$id = $putXML[0]->attributes()->id;
			$name = $putXML->name;
			$description = $putXML->description;
			*/
			
			parse_str(file_get_contents("php://input"), $putData);
			
			$id = $mysqli->real_escape_string($putData['id']);
			$name = $mysqli->real_escape_string($putData['name']);
			$description = $mysqli->real_escape_string($putData['description']);
			
			if($mysqli->query("UPDATE menus SET name = '".$name."', description = '".$description."' WHERE id = '".$id."'")) {
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
						foreach($resultsArray as $menu) {
							$xmlMenu = $xml->addChild('menu');
							$xmlMenu->addAttribute('id', $menu['id']);
							$xmlMenu->addChild('name', $menu['name']);
							$xmlMenu->addChild('description', $menu['description']);
						}
					
						echo $xml->asXML();
					break;
					default:
						header('http/1.1 400 Bad Request');
					break;
				}
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
			
			parse_str(file_get_contents("php://input"), $putData);
			
			$id = $mysqli->real_escape_string($putData['id']);
			
			if($mysqli->query("DELETE FROM menus WHERE id = '".$id."'")) {
		
				// Delete the dishes that belongs to the menu
				if($mysqli->query("DELETE FROM dishes WHERE menu_id = '".$id."'")) {
					header('http/1.1 200 OK'); // succeed
				} else {
					header('http/1.1 500 Internal Server Error'); // Failed
				}
		
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