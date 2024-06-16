<?php

class Http {


	public static function status($code = null) {
		if (empty($code)){ // "" retorna status atual
			return http_response_code();
		}else {
			http_response_code($code); // header("HTTP/1.1 204 No Content");
		}
	}

	public static function toJSON($data) {
		header('Content-type: application/json; charset=utf-8');
		return json_encode($data);
	}

	public static function method():string {
		return strtolower($_SERVER['REQUEST_METHOD']);
	}

	public static function requestURI($type = "path"):string {
		return strtolower(parse_url($_SERVER['REQUEST_URI'])[$type]); // remover Barra inicial => return trim($uri, '/');
	} // $URL = str_replace( "?" . $_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI'] );
}
