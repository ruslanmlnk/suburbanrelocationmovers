<?php
/** Docker/Dokploy values come from the environment; local OSPanel defaults remain usable. */
function srs_config_env(array $names, $default = '') {
    foreach ($names as $name) {
        $value = getenv($name);
        if ($value !== false && $value !== '') return $value;
    }
    return $default;
}

define('DB_NAME', srs_config_env(array('WORDPRESS_DB_NAME', 'DB_NAME', 'MYSQL_DATABASE', 'MARIADB_DATABASE'), 'suburbanrelocationmovers_clean'));
define('DB_USER', srs_config_env(array('WORDPRESS_DB_USER', 'DB_USER', 'MYSQL_USER', 'MARIADB_USER'), 'root'));
define('DB_PASSWORD', srs_config_env(array('WORDPRESS_DB_PASSWORD', 'DB_PASSWORD', 'MYSQL_PASSWORD', 'MARIADB_PASSWORD'), ''));
define('DB_HOST', srs_config_env(array('WORDPRESS_DB_HOST', 'DB_HOST', 'MYSQL_HOST', 'MARIADB_HOST'), '127.0.0.1'));
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');

define('AUTH_KEY',         'S4^xp1!Vk8@Hm3#Qz7&Lr9*Bn2%Wd6');
define('SECURE_AUTH_KEY',  'D8@qw5#Mt2!Kc7%Yr1^Gp4&Nx9*Lj3');
define('LOGGED_IN_KEY',    'B2!nz6@Rf9#Wp3%Hv8^Xm1&Qk7*Tc4');
define('NONCE_KEY',        'J7%lg2!Cs5@Vx9#Pd4^Nr8&Yw1*Km6');
define('AUTH_SALT',        'E3#vh8@Qm1!Tx6%Kp9^Wc2&Rs7*Ln5');
define('SECURE_AUTH_SALT', 'U9!db4#Yk7@Gx2%Nw6^Pt1&Vm8*Hr3');
define('LOGGED_IN_SALT',   'M5@cj9!Ls3#Rq8%Xv2^Bn6&Wk1*Pd7');
define('NONCE_SALT',       'Z1%tr6@Hp4!Nm9#Kx3^Gv7&Qc2*Wj8');

$table_prefix = 'wp_';
define('WP_DEBUG', false);
define('WP_DEBUG_DISPLAY', false);
define('WP_DEBUG_LOG', true);
define('DISALLOW_FILE_EDIT', true);

$wordpress_url = getenv('WORDPRESS_URL');
if ($wordpress_url) {
    define('WP_HOME', rtrim($wordpress_url, '/'));
    define('WP_SITEURL', rtrim($wordpress_url, '/'));
}

/* Dokploy terminates HTTPS at its reverse proxy. */
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strpos($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') !== false) {
    $_SERVER['HTTPS'] = 'on';
}

if (!defined('ABSPATH')) define('ABSPATH', __DIR__ . '/');
require_once ABSPATH . 'wp-settings.php';
