<?php
		
		$mysqli = new mysqli('localhost', 'root', 'root', 'STR6');
		$baseURL = 'http://'.$_SERVER['HTTP_HOST'].'/api/v1/';
		
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
			
				if(isset($_GET['format'])) {
					
					// Get the dishes of a menu
					if(isset($_GET['menuid']) && ctype_digit($_GET['menuid'])) {
					
						$menuId = $mysqli->real_escape_string($_GET['menuid']);
						$result = $mysqli->query("SELECT * FROM menus WHERE id = '".$menuId."'");

						$r = $result->fetch_assoc();

						$resultsArray['menu'] = array(
							'id'		=> $r['id'],
							'name' 		=> $r['name'],
						);

						$result = $mysqli->query(
							"SELECT dishes.id, dishes.name, dishes.price, courses.course, categories.category FROM dishes
							INNER JOIN dishes_menus AS dm ON dm.dish_id = dishes.id
							INNER JOIN courses ON courses.id = dishes.course_id 
							INNER JOIN categories ON categories.id = dishes.category_id WHERE dm.menu_id = '".$menuId."'");

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

								$resultsArray['menu']['resource'] = $baseURL.'menus/'.$menuId.'.json';

								echo json_encode($resultsArray);
							break;
							case 'xml':
								header('Content-Type: text/xml');

								$xml = new SimpleXMLElement('<menu/>');
								$xml->addAttribute('id', $resultsArray['menu']['id']);
								$xml->addChild('name', $resultsArray['menu']['name']);
								$xml->addChild('resource', $baseURL.'menus/'.$_GET['menuid'].'.xml');

								$xmlDishes = $xml->addChild('dishes');

								foreach($resultsArray['menu']['dishes'] as $dish) {
									$xmlDish = $xmlDishes->addChild('dish');
									$xmlDish->addAttribute('id', $dish['id']);
									$xmlDish->addChild('name', $dish['name']);
									$xmlDish->addChild('price', $dish['price']);
									$xmlDish->addChild('course', $dish['course']);
									$xmlDish->addChild('category', $dish['category']);
								}

								echo $xml->asXML();
							break;
						}
					}
					elseif(isset($_GET['cat'])) {
					
						// Get dishes of a category
					
						$category = $_GET['cat'];
					
						$result = $mysqli->query("SELECT dishes.id, dishes.name, dishes.price, courses.course, categories.category 
							FROM dishes
							INNER JOIN dishes_menus AS dm ON dm.dish_id = dishes.id
							INNER JOIN courses ON courses.id = dishes.course_id 
							INNER JOIN categories ON categories.id = dishes.category_id WHERE categories.category = '".$category."'");
					
						while($dish = $result->fetch_assoc()) {
							$dishes['dishes'][] = array(
								'id' 		=> $dish['id'], 
								'name' 		=> $dish['name'], 
								'price' 	=> $dish['price'], 
								'course' 	=> $dish['course'],
								'category'	=> $dish['category']
							);
						}
					
						// Check for data type in the URI.
						switch($_GET['format']) {
							case 'json':
								header('Content-Type: text/json');

								echo json_encode($dishes);
							break;
							case 'xml':
								header('Content-Type: text/xml');

								$xml = new SimpleXMLElement('<dishes/>');

								foreach($dishes['dishes'] as $dish) {
									$xmlDish = $xml->addChild('dish');
									$xmlDish->addAttribute('id', $dish['id']);
									$xmlDish->addChild('name', $dish['name']);
									$xmlDish->addChild('price', $dish['price']);
									$xmlDish->addChild('course', $dish['course']);
									$xmlDish->addChild('category', $dish['category']);
								}

								echo $xml->asXML();
							break;
						}
					}
					else {
						// Get all dishes
						$result = $mysqli->query("SELECT dishes.id, dishes.name, dishes.price, courses.course, categories.category 
							FROM dishes
							INNER JOIN dishes_menus AS dm ON dm.dish_id = dishes.id
							INNER JOIN courses ON courses.id = dishes.course_id 
							INNER JOIN categories ON categories.id = dishes.category_id");
					
						while($dish = $result->fetch_assoc()) {
							$dishes['dishes'][] = array(
								'id' 		=> $dish['id'], 
								'name' 		=> $dish['name'], 
								'price' 	=> $dish['price'], 
								'course' 	=> $dish['course'],
								'category'	=> $dish['category']
							);
						}
					
						// Check for data type in the URI.
						switch($_GET['format']) {
							case 'json':
								header('Content-Type: text/json');

								echo json_encode($dishes);
							break;
							case 'xml':
								header('Content-Type: text/xml');

								$xml = new SimpleXMLElement('<dishes/>');

								foreach($dishes['dishes'] as $dish) {
									$xmlDish = $xml->addChild('dish');
									$xmlDish->addAttribute('id', $dish['id']);
									$xmlDish->addChild('name', $dish['name']);
									$xmlDish->addChild('price', $dish['price']);
									$xmlDish->addChild('course', $dish['course']);
									$xmlDish->addChild('category', $dish['category']);
								}

								echo $xml->asXML();
							break;
						}
					}
				}
			
			break;
			
			$mysqli->close();
		}
?>