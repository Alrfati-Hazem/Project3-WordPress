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
define( 'DB_NAME', 'finalproject' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         'Q+k{grS.V&[I+CY:)fs.98~r@Q:N^~?a7$ys|UF1.]NLpw-Lkm~63y{Sx_L91X^$' );
define( 'SECURE_AUTH_KEY',  '|YQGtV10]Sq$;V:llRIGW%XKn4zifvS@FEd}:.ri?g-|4]iKYbgW9Mov)*Lc;(<!' );
define( 'LOGGED_IN_KEY',    '}Sz)(e{g8u=`)X=fq^% FQ7*zrQtKBDh*]C)G`len<K7;@CJ~xEW/3Q{h/w*E!is' );
define( 'NONCE_KEY',        '8_A(cCW*M9o|wp8vkvXO+G+kjX9s$h$u`7hGqY[RG/5=&zd8K,qx `:)TTz.Z]cd' );
define( 'AUTH_SALT',        'd[syvMPyuG2VvD0c%iu9ZSa[+XL]u)?.*:zuq;yLd(}G>1j,>OO1=UAb)% NVY*`' );
define( 'SECURE_AUTH_SALT', '+o8.FWLLGqT_%}T3?^,MruTWX&[94NLXRMm4OeVY3l|CWF.j}z}GiY~0FZ@m$ aI' );
define( 'LOGGED_IN_SALT',   'RZ5>ez>uJ*ym?1}AB4WW}<S4Vka.<lfhjK3OaE<NH,e181X=xLe~/Q$5BOQquWMI' );
define( 'NONCE_SALT',       'BP1KIA~T7u5T4+YrN.3-}Z_1C?J/ v/dHrXQ1d.eV&wh!CMl7iXMkHv_(4=OT2Pi' );

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
