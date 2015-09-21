<?php
/**
 * Plugin Name: GitHub README
 * Plugin URI: https://github.com/octalmage/github-readme
 * Description: Github README is a plugin that allows you to embed a GitHub README in a page or post using a simple shortcode.
 * Version: 0.1.0
 * Author: Jason Stallings
 * Author URI: http://jason.stallin.gs
 */

require_once 'Michelf/Markdown.inc.php';

use \Michelf\Markdown;

add_shortcode( 'github_readme', 'github_readme_func' );
add_shortcode( 'github_markdown', 'github_markdown_func' );
add_shortcode( 'github_wikipage', 'github_wikipage_func' );

function github_readme_func( $atts ) {
	extract(
		shortcode_atts(
			array(
				'repo' => 'octalmage/GitHub Shortcode',
				'trim' => 0,
			),
			$atts
		)
	);

	$transient = "github_readme_" . $repo . "_" . $trim;

	if ( false === ( $html = get_transient( $transient ) ) ) {
		$url = "https://api.github.com/repos/" . $repo . "/readme";

		$data = github_readme_get_url( $url );

		$json     = json_decode( $data );
		$markdown = base64_decode( $json->content );

		if ( $trim > 0 ) {
			$markdown = implode( "\n", array_slice( explode( "\n", $markdown ), $trim ) );
		}

		$html = Markdown::defaultTransform( $markdown );
		set_transient( $transient, $html, 12 * HOUR_IN_SECONDS );
	}

	return $html;
}

function github_markdown_func( $atts ) {
	extract(
		shortcode_atts(
			array(
				'repo'   => 'octalmage/GitHub Shortcode',
				'trim'   => 0,
				'cache'  => 60,
				'file'   => '/readme',
				'branch' => 'master',
			),
			$atts
		)
	);

	$transient = "github_markdown_" . $repo . "_" . $file . "_" . $trim;

	if ( false === ( $html = get_transient( $transient ) ) ) {
		$url = "https://raw.githubusercontent.com/" . $repo . "/" . $branch . "/" . $file;

		$markdown = github_readme_get_url( $url );

		if ( $trim > 0 ) {
			$markdown = implode( "\n", array_slice( explode( "\n", $markdown ), $trim ) );
		}

		$html = Markdown::defaultTransform( $markdown );
		set_transient( $transient, $html, $cache );
	}

	return $html;
}

function github_wikipage_func( $atts ) {
	extract(
		shortcode_atts(
			array(
				'repo'  => 'octalmage/GitHub Shortcode',
				'trim'  => 0,
				'cache' => 60,
				'page'  => '',
			),
			$atts
		)
	);

	$transient = "github_wikipage_" . $repo . "_" . $page;

	if ( false === ( $html = get_transient( $transient ) ) ) {
		$url = "https://raw.githubusercontent.com/wiki/" . $repo . "/" . $page . ".md";

		$markdown = github_readme_get_url( $url );

		if ( $trim > 0 ) {
			$markdown = implode( "\n", array_slice( explode( "\n", $markdown ), $trim ) );
		}

		$html = Markdown::defaultTransform( $markdown );
		set_transient( $transient, $html, $cache );
	}

	return $html;
}

/**
 * Get data from URL.
 *
 * @param $url
 *
 * @return mixed
 */
function github_readme_get_url( $url ) {
	$data = '';

	$response = wp_remote_get( $url );

	if ( ! empty( $response['response']['code'] ) && 200 === $response['response']['code'] && ! empty( $response['body'] ) ) {
		$data = $response['body'];
	}

	return $data;
}
