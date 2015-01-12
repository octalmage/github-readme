<?php
/**
 * Plugin Name: GitHub README
 * Plugin URI: https://github.com/octalmage/github-readme
 * Description: Github README is a plugin that allows you to embed a GitHub README in a page or post using a simple shortcode.
 * Version: 0.0.3
 * Author: Jason Stallings
 * Author URI: http://jason.stallin.gs
 */

require_once 'Michelf/Markdown.inc.php';

use \Michelf\Markdown;

add_shortcode( 'github_readme', 'github_readme_func' );

function github_readme_func( $atts ) 
{
	extract( shortcode_atts( array(
		'repo' => 'octalmage/GitHub Shortcode',
	    'trim' => 0
	), $atts ) );
	
	$transient="github_readme_" . $repo . "_" . $trim;
	if ( false === ( $html = get_transient($transient))) 
	{
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
		if ($trim>0)
		{
			$markdown = implode("\n", array_slice(explode("\n", $markdown), $trim)); 
		}
	
		$html = Markdown::defaultTransform($markdown);
	 	set_transient($transient, $html , 12 * HOUR_IN_SECONDS);
	}
	return $html;	
}
?>
