<?php
/**
 * Google XML Sitemap
 *
 * @author Cor van Noorloos
 * @license http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link https://github.com/corvannoorloos/google-xml-sitemap
 *
 * @wordpress
 * Plugin Name: Google XML Sitemap
 * Plugin URI: https://github.com/corvannoorloos/google-xml-sitemap
 * Description: Sitemaps are a way to tell Google about pages on your site we might not otherwise discover. In its simplest terms, a XML Sitemap&mdash;usually called Sitemap, with a capital S&mdash;is a list of the pages on your website. Creating and submitting a Sitemap helps make sure that Google knows about all the pages on your site, including URLs that may not be discoverable by Google's normal crawling process.
 * Author: Cor van Noorloos
 * Version: 0.1.1
 * Author URI: http://corvannoorloos.com/
 */

add_action( 'template_redirect', 'google_sitemap' );
/**
 * Google XML Sitemap
 *
 * @since 0.1.1
 *
 * @global type $wpdb
 *
 * @return type
 */
function google_sitemap() {
  if ( ! preg_match( '/sitemap\.xml$/', $_SERVER['REQUEST_URI'] ) ) {
    return;
  }
  global $wpdb;
  $posts = $wpdb->get_results( "SELECT ID, post_title, post_modified_gmt
    FROM $wpdb->posts
    WHERE post_status = 'publish'
    AND post_password = ''
    ORDER BY post_type DESC, post_modified DESC
    LIMIT 50000" );
  header( "HTTP/1.1 200 OK" );
  header( 'X-Robots-Tag: noindex, follow', true );
  header( 'Content-Type: text/xml' );
  echo '<?xml version="1.0" encoding="' . get_bloginfo( 'charset' ) . '"?>' . "\n";
  echo '<!-- generator="' . home_url( '/' ) . '" -->' . "\n";
  $xml  = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\n";
  $xml .= "\t<url>" . "\n";
  $xml .= "\t\t<loc>" . home_url( '/' ) . "</loc>\n";
  $xml .= "\t\t<lastmod>" . mysql2date( 'Y-m-d\TH:i:s+00:00', get_lastpostmodified( 'GMT' ), false ) . "</lastmod>\n";
  $xml .= "\t\t<changefreq>" . 'daily' . "</changefreq>\n";
  $xml .= "\t\t<priority>" . '1' . "</priority>\n";
  $xml .= "\t</url>" . "\n";
  foreach ( $posts as $post ) {
    if ( $post->ID == get_option( 'page_on_front' ) )
      continue;
    if ( ! empty( $post->post_title ) ) {
      $xml .= "\t<url>\n";
      $xml .= "\t\t<loc>" . get_permalink( $post->ID ) . "</loc>\n";
      $xml .= "\t\t<lastmod>" . mysql2date( 'Y-m-d\TH:i:s+00:00', $post->post_modified_gmt, false ) . "</lastmod>\n";
      $xml .= "\t\t<changefreq>" . 'weekly' . "</changefreq>\n";
      $xml .= "\t\t<priority>" . '0.8' . "</priority>\n";
      $xml .= "\t</url>\n";
    }
  }
  $xml .= '</urlset>';
  echo ( "$xml" );
  exit();
}