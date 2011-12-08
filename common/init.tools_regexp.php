<?php
// häufige benutzte Regular Expressions

// zB "127.0.0.1"
	@define('REGEXP_PATTERN_IP', '[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}');
// zB "www" in "www.domain.de"
	@define('REGEXP_PATTERN_DOMAIN', '[a-zA-Z0-9-]+');
// zB "de" in "www.domain.de"
	@define('REGEXP_PATTERN_TOPLEVELDOMAIN', '[a-zA-Z]{2,10}');
// zB "www.domain.de" oder "127.0.0.1"
	@define('REGEXP_PATTERN_HOSTNAME', '((' . REGEXP_PATTERN_DOMAIN . '\.)+' . REGEXP_PATTERN_TOPLEVELDOMAIN . ')|' . REGEXP_PATTERN_IP . '');
// zB "peter@domain.de" oder "peter@127.0.0.1"
	@define('REGEXP_PATTERN_EMAIL', '[a-zA-Z0-9\._-]+@' . REGEXP_PATTERN_HOSTNAME . '');


	@define('REGEXP_PATTERN_PHONE', '(0|\+?49)([0-9]+)');
	
	@define('REGEXP_PATTERN_DATE', '[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,4}');
?>