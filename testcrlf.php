#!/usr/bin/php
<?php

/**
 * I don't believe in license
 * You can do want you want with this program
 * - gwen -
 */

function __autoload( $c ) {
	include( $c.'.php' );
}


// parse command line
{
	$testcrlf = new TestCrlf();
	//$reference = new TestCrlfRequest();

	$argc = $_SERVER['argc'] - 1;

	for ($i = 1; $i <= $argc; $i++) {
		switch ($_SERVER['argv'][$i]) {
			case '-f':
				$testcrlf->setHost( $_SERVER['argv'][$i + 1] );
				$i++;
				break;

			case '-h':
				Utils::help();
				break;

			case '-o':
				$testcrlf->setHost($_SERVER['argv'][$i + 1]);
				$i++;
				break;

			case '-p':
				$testcrlf->setProtocol($_SERVER['argv'][$i + 1]);
				$i++;
				break;

			case '-r':
				$testcrlf->setRedirect( true );
				break;

			case '-s':
				$testcrlf->setSsl( true );
				break;

			default:
				Utils::help('Unknown option: '.$_SERVER['argv'][$i]);
		}
	}

	if( !$testcrlf->getHost() ) {
		Utils::help('Host not found!');
	}
}
// ---


// main loop
{
	$testcrlf->run();
}
// ---


exit();

?>
