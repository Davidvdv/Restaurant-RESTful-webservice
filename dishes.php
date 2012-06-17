<?php

	if(isset($_GET['menuid']) && ctype_digit($_GET['menuid']) && isset($_GET['format'])) {
		
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
				header('Allow: GET, HEAD, OPTIONS, PUT');
			break;
			case 'PUT':
				
			case 'GET':
				$result = $mysqli->query("SELECT * FROM menus WHERE id = '".$_GET['menuid']."'");
				
				$r = $result->fetch_object();
				
				$resultsArray['menu'] = array(
					'id' => $r->id,
					'name' => $r->name
				);
			
				$result = $mysqli->query(
					"SELECT dishes.id, dishes.name, dishes.price, courses.course, categories.category FROM dishes
					INNER JOIN dishes_menus AS dm ON dm.dish_id = dishes.id
					INNER JOIN courses ON courses.id = dishes.course_id 
					INNER JOIN categories ON categories.id = dishes.category_id WHERE dm.menu_id = '".$_GET['menuid']."'");
				
				while($dish = $result->fetch_assoc()) {
					$tmpArray[] = array(
						'id' 		=> $dish['id'], 
						'name' 		=> $dish['name'], 
						'price' 	=> $dish['price'], 
						'course' 	=> $dish['course'],
						'category'	=> $dish['category']
					);
				}
				
				$resultsArray['menu']['dishes'] = $tmpArray;
				
				// Check for data type in the URI.
				switch($_GET['format']) {
					case 'json':
						header('Content-Type: text/json');
						
						$json = $resultsArray;
						
						echo json_encode($json);
					break;
					case 'xml':
						header('Content-Type: text/xml');
						
						$xml = new SimpleXMLElement('<menu/>');
						$xml->addAttribute('id', $resultsArray['menu']['id']);
						$xml->addChild('name', $resultsArray['menu']['name']);
						
						$xmlDishes = $xml->addChild('dishes');
						
						foreach($resultsArray['menu']['dishes'] as $dish) {
							$xmlDishes->addChild('id', $dish['id']);
							$xmlDishes->addChild('name', $dish['name']);
							$xmlDishes->addChild('price', $dish['price']);
							$xmlDishes->addChild('course', $dish['course']);
							$xmlDishes->addChild('category', $dish['category']);
						}
						
						echo $xml->asXML();
					break;
				}
				
			break;
			
			$mysqli->close();
		}
	}
?>