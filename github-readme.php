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
	$defaults = array(
		'repo' => 'octalmage/GitHub Shortcode',
		'trim' => 0,
	);

	$atts = shortcode_atts(
		$defaults,
		$atts,
		'github_readme'
	);

	$repo = empty( $atts['repo'] ) ? $defaults['repo'] : $atts['repo'];
	$trim = empty( $atts['trim'] ) ? $defaults['trim'] : abs( (int) $atts['trim'] );

	$transient = github_readme_transient_name( 'github_readme_' . $repo . '_' . $trim );

	$html = get_transient( $transient );

	if ( false === $html ) {
		$url = 'https://api.github.com/repos/' . $repo . '/readme';

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
	$defaults = array(
		'repo'   => 'octalmage/GitHub Shortcode',
		'trim'   => 0,
		'cache'  => 60,
		'file'   => '/readme',
		'branch' => 'master',
	);

	$atts = shortcode_atts(
		$defaults,
		$atts,
		'github_markdown'
	);

	$repo   = empty( $atts['repo'] ) ? $defaults['repo'] : $atts['repo'];
	$trim   = empty( $atts['trim'] ) ? $defaults['trim'] : abs( (int) $atts['trim'] );
	$cache  = empty( $atts['cache'] ) ? $defaults['cache'] : abs( (int) $atts['cache'] );
	$file   = empty( $atts['file'] ) ? $defaults['file'] : $atts['file'];
	$branch = empty( $atts['branch'] ) ? $defaults['branch'] : $atts['branch'];

	$transient = github_readme_transient_name( 'github_markdown_' . $repo . '_' . $branch . '_' . $file . '_' . $trim . '_' . $cache );

	$html = get_transient( $transient );

	if ( false === $html ) {
		$url = 'https://raw.githubusercontent.com/' . $repo . '/' . $branch . '/' . $file;

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
	$defaults = array(
		'repo'  => 'octalmage/GitHub Shortcode',
		'trim'  => 0,
		'cache' => 60,
		'page'  => '',
	);

	shortcode_atts(
		$defaults,
		$atts,
		'github_wikipage'
	);

	$repo  = empty( $atts['repo'] ) ? $defaults['repo'] : $atts['repo'];
	$trim  = empty( $atts['trim'] ) ? $defaults['trim'] : abs( (int) $atts['trim'] );
	$cache = empty( $atts['cache'] ) ? $defaults['cache'] : abs( (int) $atts['cache'] );
	$page  = empty( $atts['page'] ) ? $defaults['page'] : $atts['page'];

	$transient = github_readme_transient_name( 'github_wikipage_' . $repo . '_' . $page . '_' . $trim . '_' . $cache );

	$html = get_transient( $transient );

	if ( false === $html ) {
		$url = 'https://raw.githubusercontent.com/wiki/' . $repo . '/' . $page . '.md';

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
 * Trim lines from beginning of markdown text.
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

/**
 * Returns a string that can be used as a transient name without possible truncation or invalid character problems.
 *
 * @param string $key
 *
 * @return string
 */
function github_readme_transient_name( $key ) {
	return 'github_readme_' . md5( $key );
}
