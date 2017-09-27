# TestCrlf
PHP tool to test Carriage Return Line Feed aka CRLF.  
Note that this is an automated tool, manual check is still required.  

```
Usage: php testcrlf.php [OPTIONS] -o <host>

Options:
	-h	print this help
	-l	display payloads, do not perform any test
	-o	single host to test or source file
	-r	follow redirection
	-s	force https

Examples:
	php testcrlf.php -o www.example.com
	php testcrlf.php -r -s -o domains.txt
```

I don't believe in license.  
You can do want you want with this program.  
