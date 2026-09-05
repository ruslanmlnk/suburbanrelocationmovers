<?php
/** Build the production legacy-content dataset from fetched Wayback HTML. */

$root       = dirname( __DIR__ );
$source_dir = $argv[1] ?? $root . '/.wayback-cache';
$theme_dir  = $root . '/wp-content/themes/suburban-relocation';
$output     = $theme_dir . '/inc/legacy-content.json';
$image_dir  = $theme_dir . '/assets/images/legacy';
$manifest   = json_decode( file_get_contents( rtrim( $source_dir, '/\\' ) . '/manifest.json' ), true );

if ( ! is_array( $manifest ) ) throw new RuntimeException( 'Invalid Wayback manifest.' );
if ( ! is_dir( $image_dir ) ) mkdir( $image_dir, 0775, true );

function srs_dom_inner_html( DOMNode $node ): string {
	$html = '';
	foreach ( $node->childNodes as $child ) $html .= $node->ownerDocument->saveHTML( $child );
	return $html;
}

function srs_clean_legacy_node( DOMElement $node, string $slug, array &$images, string $image_dir ): string {
	$remove = array();
	foreach ( $node->getElementsByTagName( '*' ) as $element ) {
		if ( in_array( strtolower( $element->nodeName ), array( 'script', 'style', 'form', 'iframe', 'noscript' ), true ) ) $remove[] = $element;
	}
	foreach ( $remove as $element ) $element->parentNode?->removeChild( $element );

	$all = array( $node );
	foreach ( $node->getElementsByTagName( '*' ) as $element ) $all[] = $element;
	foreach ( $all as $element ) {
		if ( ! $element instanceof DOMElement ) continue;
		foreach ( iterator_to_array( $element->attributes ?? array() ) as $attribute ) {
			if ( ! in_array( strtolower( $attribute->name ), array( 'href', 'src', 'alt', 'title' ), true ) ) $element->removeAttribute( $attribute->name );
		}
		if ( 'a' === strtolower( $element->nodeName ) && $element->hasAttribute( 'href' ) ) {
			$href = $element->getAttribute( 'href' );
			$href = preg_replace( '~^https?://(?:www\.)?suburbanrelocationmovers\.com~i', '', $href );
			$element->setAttribute( 'href', $href ?: '/' );
		}
	}

	foreach ( iterator_to_array( $node->getElementsByTagName( 'img' ) ) as $index => $image ) {
		$src = html_entity_decode( $image->getAttribute( 'src' ), ENT_QUOTES | ENT_HTML5 );
		if ( ! $src ) continue;
		$path = parse_url( $src, PHP_URL_PATH ) ?: '';
		$name = strtolower( rawurldecode( basename( $path ) ) );
		$name = trim( preg_replace( '/[^a-z0-9._-]+/i', '-', $name ), '-' );
		if ( ! $name ) $name = $slug . '-image-' . ( $index + 1 ) . '.jpg';
		$name = $slug . '-' . $name;
		$target = $image_dir . '/' . $name;
		if ( ! is_file( $target ) ) {
			foreach ( array( '20231004090206', '20221203000000', '20200101000000', '20180101000000', '20160101000000' ) as $stamp ) {
				$archive_url = 'https://web.archive.org/web/' . $stamp . 'id_/' . $src;
				$curl = curl_init( $archive_url );
				curl_setopt_array( $curl, array( CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true, CURLOPT_TIMEOUT => 45, CURLOPT_USERAGENT => 'SuburbanRelocationMigration/1.0' ) );
				$binary = curl_exec( $curl );
				$status = (int) curl_getinfo( $curl, CURLINFO_RESPONSE_CODE );
				$type = (string) curl_getinfo( $curl, CURLINFO_CONTENT_TYPE );
				curl_close( $curl );
				if ( $status >= 200 && $status < 300 && is_string( $binary ) && strlen( $binary ) > 500 && false === stripos( $type, 'text/html' ) ) {
					file_put_contents( $target, $binary );
					break;
				}
			}
		}
		if ( is_file( $target ) ) {
			$image->setAttribute( 'src', '{{theme_uri}}/assets/images/legacy/' . $name );
			$images[] = $name;
		} else {
			$image->parentNode?->removeChild( $image );
		}
	}

	$html = $node->ownerDocument->saveHTML( $node );
	// DOMDocument URL-encodes braces inside src attributes.
	$html = str_ireplace( '%7B%7Btheme_uri%7D%7D', '{{theme_uri}}', $html );
	return trim( preg_replace( '/\s+$/m', '', $html ) );
}

$dataset = array();
$allowed = array( 'h1', 'h2', 'h3', 'h4', 'p', 'ul', 'ol', 'blockquote', 'figure', 'img' );

foreach ( $manifest as $page ) {
	if ( empty( $page['ok'] ) || empty( $page['file'] ) ) continue;
	$file = rtrim( $source_dir, '/\\' ) . '/' . $page['slug'] . '.html';
	$dom = new DOMDocument( '1.0', 'UTF-8' );
	libxml_use_internal_errors( true );
	$dom->loadHTML( '<?xml encoding="utf-8" ?>' . file_get_contents( $file ), LIBXML_NOWARNING | LIBXML_NOERROR );
	$xpath = new DOMXPath( $dom );
	$roots = $xpath->query( "//*[contains(concat(' ', normalize-space(@class), ' '), ' about_page_left ')]" );
	if ( ! $roots->length ) { echo "EMPTY ROOT {$page['slug']}\n"; continue; }

	$parts = array();
	$images = array();
	foreach ( iterator_to_array( $roots->item( 0 )->childNodes ) as $child ) {
		if ( ! $child instanceof DOMElement || ! in_array( strtolower( $child->nodeName ), $allowed, true ) ) continue;
		if ( preg_match( '/(^|\s)posttitle(\s|$)/', $child->getAttribute( 'class' ) ) ) continue;
		$text = trim( preg_replace( '/\s+/u', ' ', $child->textContent ) );
		if ( '' === $text && 0 === $child->getElementsByTagName( 'img' )->length && 'img' !== strtolower( $child->nodeName ) ) continue;
		$clean = srs_clean_legacy_node( $child, $page['slug'], $images, $image_dir );
		if ( $clean ) $parts[] = $clean;
	}

	$content = implode( "\n", $parts );
	$words = str_word_count( html_entity_decode( strip_tags( $content ), ENT_QUOTES | ENT_HTML5 ) );
	$description_node = $xpath->query( "//meta[translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='description']/@content" );
	$meta_description = $description_node->length ? trim( $description_node->item( 0 )->nodeValue ) : '';
	$title_node = $xpath->query( '//title' );
	$seo_title = $title_node->length ? trim( preg_replace( '/\s+/u', ' ', $title_node->item( 0 )->textContent ) ) : '';
	$dataset[ $page['slug'] ] = array(
		'type'       => $page['type'],
		'legacy_url' => $page['legacyUrl'],
		'source'     => $page['source'],
		'seo_title'  => $seo_title,
		'meta_description' => $meta_description,
		'word_count' => $words,
		'images'     => array_values( array_unique( $images ) ),
		'content'    => $content,
	);
	printf( "%4d words %2d images %s\n", $words, count( $images ), $page['slug'] );
}

file_put_contents( $output, json_encode( $dataset, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
echo 'DATASET=' . count( $dataset ) . ' OUTPUT=' . $output . PHP_EOL;
