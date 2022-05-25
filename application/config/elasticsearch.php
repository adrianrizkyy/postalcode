<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
if (ENVIRONMENT == "development" || ENVIRONMENT == "staging") {
    $config['es_server'] = 'http://localhost:9200'; // 'https://search-tribunnews-es-mtkqj2e5hs2t3m4px7ikrnx2ry.ap-southeast-1.es.amazonaws.com';
    $config['index'] = ''; // 'web-post';
    $config['auth'] = false;
    $config['username'] = '';
    $config['password'] = '';
} else {
    /* $config['es_server'] = 'https://vpc-tjb-es-gdrocx7lt6fwfv4ymvhwzycciu.ap-southeast-1.es.amazonaws.com';
	$config['index'] = 'web-post';
	$config['auth'] = true;
	$config['username'] = 'tribun-jualbeli';
	$config['password'] = 'u6ysLhEUA#Fer4vh7DBxRSte3G9gdHC8HyM++Bfk4RU#P8Rv9g'; */
    $config['es_server'] = 'http://localhost:9200'; // 'https://search-tribunnews-es-mtkqj2e5hs2t3m4px7ikrnx2ry.ap-southeast-1.es.amazonaws.com';
    $config['index'] = ''; // 'web-post';
    $config['auth'] = false;
    $config['username'] = '';
    $config['password'] = '';
}
