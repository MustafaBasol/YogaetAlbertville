<?php
/**
 * Router for PHP's built-in web server (local preview only): serves
 * existing files directly and sends pretty permalinks to WordPress.
 *
 * @package YogaEtVie
 */

$path = (string) parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ); // phpcs:ignore

if ( '/' !== $path && is_file( $_SERVER['DOCUMENT_ROOT'] . $path ) ) { // phpcs:ignore
	return false;
}

if ( is_dir( $_SERVER['DOCUMENT_ROOT'] . $path ) && is_file( $_SERVER['DOCUMENT_ROOT'] . rtrim( $path, '/' ) . '/index.php' ) ) { // phpcs:ignore
	$_SERVER['SCRIPT_NAME'] = rtrim( $path, '/' ) . '/index.php';
	require $_SERVER['DOCUMENT_ROOT'] . rtrim( $path, '/' ) . '/index.php'; // phpcs:ignore
	return true;
}

$_SERVER['SCRIPT_NAME'] = '/index.php';
require $_SERVER['DOCUMENT_ROOT'] . '/index.php'; // phpcs:ignore
