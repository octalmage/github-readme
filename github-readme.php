<?php
/*
Plugin Name: GitHub README
*/

require_once 'Michelf/Markdown.inc.php';

use \Michelf\Markdown;

add_shortcode( 'github_readme', 'github_readme_func' );

function github_readme_func( $atts ) 
{
	extract( shortcode_atts( array(
		'repo' => 'octalmage/GitHub Shortcode'
	), $atts ) );
	
	$url="https://api.github.com/repos/" . $repo . "/readme";
	
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_USERAGENT,'WordPress');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$data = curl_exec($ch);
	curl_close($ch);

	$json=json_decode($data);
 	$markdown=base64_decode($json->content);
	
	$html = Markdown::defaultTransform($markdown);
	return $html;	
}
?>
