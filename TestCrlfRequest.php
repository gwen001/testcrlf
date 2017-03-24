<?php

/**
 * I don't believe in license
 * You can do want you want with this program
 * - gwen -
 */

class TestCrlfRequest extends HttpRequest
{
	private $result_header = null;
	
	private $result_body = null;
	

	public function request()
	{
		$c = parent::request();

		$header_size = curl_getinfo( $c, CURLINFO_HEADER_SIZE );
		$this->result_header = trim( substr($this->result, 0, $header_size) );
		$this->result_body = trim( substr($this->result, $header_size) );
		//var_dump( $this->result_header );
		
		return $this->result_code;
	}
	

	public function extractCookies()
	{
		preg_match_all( '/^Set-Cookie:\s*([^;]*)/mi', $this->result_header, $matches );
		
		$t_cookies = array();
		
		foreach( $matches[1] as $item ) {
		    parse_str( $item, $cookie );
		    $t_cookies = array_merge( $t_cookies, $cookie );
		}
		
		return $t_cookies;
	}
}

?>
