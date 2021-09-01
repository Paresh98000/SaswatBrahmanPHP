<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'paresh_saswatbrahman' );

/** MySQL database username */
define( 'DB_USER', 'Paresh' );

/** MySQL database password */
define( 'DB_PASSWORD', 'paresh@123' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'KUf5xiWq/N +@Q?[2+t:^X~5-cxzvxs%:<;/jx7 T[$z|y5a!w/!0%Ek9Q@=Y;`f' );
define( 'SECURE_AUTH_KEY',  '^>6&^2%~@cY8w 4+!%,:?CIb7/?MQ6V@6Y-m/B(4DF9oW2E^nLzuN:W0]l<F4y?.' );
define( 'LOGGED_IN_KEY',    '!]UHU9CM,9^jIc[*^ZAiX$z b]S!#K&^eVlSw1#qT[2MSJ&bk_NcK+WIywG*u[f]' );
define( 'NONCE_KEY',        'o`B-|iO/VRx ,4>h*_B0O<A6d&]*1& )r;r4c>2KK2&)&9lCX9#ye%^ZuAi7siNQ' );
define( 'AUTH_SALT',        'M7<]lY]-8|<rMV?n3kW5338m4_iBV#X#-<@FQ#va;6x.K#3u$zi;;Q(]rDPDjm|E' );
define( 'SECURE_AUTH_SALT', 'm%--:x73D,2:EvoLuzE}JBw!vVfU)t$+!.pl]34w{k~b*h@DT2Vs*Gfb2Pqqb]b.' );
define( 'LOGGED_IN_SALT',   'Gxm]tnkX1x:m#B7NoD9IcKR7Be5@@7V*>VKP/7sWN8DcJxX<KEM!J=76jkQu5(;;' );
define( 'NONCE_SALT',       '#g,STLiG3,ECE;. !rr~AM*Sh7_[ZlaMk9fs,3&>#TEd|SY37<5}CSq+S6zCm{qr' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
