<?php
/** Regression test for domain replacement. No WordPress database or network needed. */
if ( PHP_SAPI !== 'cli' ) { exit; }

function home_url() { return 'https://suburbanrelocationmovers.com'; }

// Load just the real helper without bootstrapping WordPress and its migrations.
$source = file_get_contents( __DIR__ . '/../wp-content/themes/suburban-relocation/functions.php' );
$start = strpos( $source, 'function srs_replace_local_origin(' );
$end = strpos( $source, "add_filter( 'the_content'", $start );
if ( false === $start || false === $end ) { throw new RuntimeException( 'Origin helper not found.' ); }
eval( substr( $source, $start, $end - $start ) );

$cases = array(
	array( 'http://suburbanrelocationmovers', home_url() ),
	array( 'https://suburbanrelocationmovers/', home_url() . '/' ),
	array( 'http://suburbanrelocationmovers/local.html', home_url() . '/local.html' ),
	array( 'HTTPS://SUBURBANRELOCATIONMOVERS/services.html', home_url() . '/services.html' ),
	array( 'https://suburbanrelocationmovers?x=1#quote', home_url() . '?x=1#quote' ),
	array( home_url() . '/', home_url() . '/' ),
	array( home_url() . '/residential.html', home_url() . '/residential.html' ),
	array( 'http://suburbanrelocationmovers.com/local.html', 'http://suburbanrelocationmovers.com/local.html' ),
	array( 'https://suburbanrelocationmovers.com.com/', 'https://suburbanrelocationmovers.com.com/' ),
	array( 'https://suburbanrelocationmovers.example.org/', 'https://suburbanrelocationmovers.example.org/' ),
	array( 'https://suburbanrelocationmovers-other.com/', 'https://suburbanrelocationmovers-other.com/' ),
	array( 'https://suburbanrelocationmovers@example.org/', 'https://suburbanrelocationmovers@example.org/' ),
	array( '/local.html#quote', '/local.html#quote' ),
	array( '<a href="http://suburbanrelocationmovers">Home</a><img src="https://suburbanrelocationmovers/image.png">', '<a href="' . home_url() . '">Home</a><img src="' . home_url() . '/image.png">' ),
	array( "<a href='https://suburbanrelocationmovers'>Home</a>", "<a href='" . home_url() . "'>Home</a>" ),
	array( null, null ),
	array( array( 'untouched' ), array( 'untouched' ) ),
);
foreach ( $cases as $index => $case ) {
	$actual = srs_replace_local_origin( $case[0] );
	if ( $actual !== $case[1] ) { throw new RuntimeException( 'Failed case ' . $index . ': ' . var_export( $actual, true ) ); }
	if ( srs_replace_local_origin( $actual ) !== $actual ) { throw new RuntimeException( 'Repeated replacement changed case ' . $index ); }
}
echo 'PASS: ' . count( $cases ) . " cases; local origins replaced, production/external URLs preserved, repeated calls stable.\n";
