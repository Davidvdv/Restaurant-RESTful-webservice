<?php

	class API {
		
		private $_theInstace;
		
		private __construct() {
			
		}
		
		public static getInstance() {
			if ($this->_theInstance == null) {
				$this->_theInstance = new API();
			} 

			return $this->_theInstance;
		}
	}

?>