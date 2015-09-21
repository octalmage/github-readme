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

add_shortcode( 'github_readme', 'github_readme_default' );
add_shortcode( 'github_markdown', 'github_readme_markdown' );
add_shortcode( 'github_wikipage', 'github_readme_wikipage' );

/**
 * Handler for github_readme shortcode.
 *
 * @param array $atts
 *
 * @return string
 */
function github_readme_default( $atts ) {
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
		$markdown = github_readme_trim_markdown( $markdown, $trim );

		$html = Markdown::defaultTransform( $markdown );
		set_transient( $transient, $html, 12 * HOUR_IN_SECONDS );
	}

	return $html;
}

/**
 * Handler for github_markdown shortcode.
 *
 * @param array $atts
 *
 * @return string
 */
function github_readme_markdown( $atts ) {
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
		$markdown = github_readme_trim_markdown( $markdown, $trim );

		$html = Markdown::defaultTransform( $markdown );
		set_transient( $transient, $html, $cache );
	}

	return $html;
}

/**
 * Handler for github_wikipage shortcode.
 *
 * @param array $atts
 *
 * @return string
 */
function github_readme_wikipage( $atts ) {
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
		$markdown = github_readme_trim_markdown( $markdown, $trim );

		$html = Markdown::defaultTransform( $markdown );
		set_transient( $transient, $html, $cache );
	}

	return $html;
}

/**
 * Get data from URL.
 *
 * @param string $url
 *
 * @return string
 */
function github_readme_get_url( $url ) {
	$data = '';

	$response = wp_remote_get( $url );

	if ( ! empty( $response['response']['code'] ) && 200 === $response['response']['code'] && ! empty( $response['body'] ) ) {
		$data = $response['body'];
	}

	return $data;
}

/**
 * Trim lines from begining of markdown text.
 *
 * @param string  $markdown
 * @param integer $lines Optional number of lines to trim from beginning of supplied markdown.
 *
 * @return string
 */
function github_readme_trim_markdown( $markdown, $lines = 0 ) {
	if ( 0 < $lines ) {
		$markdown = implode( "\n", array_slice( explode( "\n", $markdown ), $lines ) );

		return $markdown;
	}

	return $markdown;
}
