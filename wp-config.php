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
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          ':T@R4Bqedn<y+OY>5/cV& 8l)L^tNY/RH.mzHKPBOoI-.&NPz] ?xKJ#&0f85Q<:' );
define( 'SECURE_AUTH_KEY',   'uA<9UD~i<1CL-{e0n4@YnQmIBzJBpeIt1p>y;&Q[ljVp^P5b)qz}+y2LqlTY$O4x' );
define( 'LOGGED_IN_KEY',     '~,H&eh:BKoU-fe.vlNe)R<::{`xQL@Lri?hq$-puy&&m3yf).Gd=f9L-Z0im9sKi' );
define( 'NONCE_KEY',         'aw5Kz35vdUZN&d+jn8EX;`Ncs-R^B/DSsykQ`y4ZtK/~@T4pWN1gi(9Lj5#KxE:d' );
define( 'AUTH_SALT',         'V.,dDx-Eh=#IU1J6Y2S__[@Qw#Y/nP`|zX7Ysc-c3xdo]-#<<`(v=P@ wa^C{JBk' );
define( 'SECURE_AUTH_SALT',  'K6kE.NaI)Oko6X{ I?Rm=;I3*;Uj09?|WFcI|$H[eN.+F9XMv#R]iHMBD!+G9q?_' );
define( 'LOGGED_IN_SALT',    'de~C?NoNPFsKY?S>4*eFOy.WYK4Zy/!okP_yy$4`23dFCy9#>XqjBot5m2}<}$Z2' );
define( 'NONCE_SALT',        ':}Jz*HJPd]-#R@e|Nhdz|pAe97_X}-SYdi4Xc~{Vi27Y2p#ce9kh1$b&IV#_@lE6' );
define( 'WP_CACHE_KEY_SALT', ' w <.L0pu,s$aSfnogp=ehIBArz>!w|<RJ`rubj2|_^V2gc[r..G4A`ejWmf2Vw+' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );

if ( 'local' === WP_ENVIRONMENT_TYPE ) {
	define( 'DISABLE_WP_CRON', true );
	define( 'WP_MEMORY_LIMIT', '256M' );
	define( 'WP_MAX_MEMORY_LIMIT', '512M' );
	define( 'AUTOSAVE_INTERVAL', 120 );
	define( 'WP_POST_REVISIONS', 10 );
}
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
