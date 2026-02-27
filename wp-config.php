<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'custom_template' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
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
define( 'AUTH_KEY',         'gq$:vSDH,C~5MZ)]@.8[K)lN3+!L^CpKkLD{cwpiQ7/]gAA7u_aV7zlYT(GmRw$}' );
define( 'SECURE_AUTH_KEY',  'M1jkg]@#)jg2y:?sEB~7SAl-QG!$_s}d3mW^*7SMqmI3@H&S}zh.gV9W.?GF^]cs' );
define( 'LOGGED_IN_KEY',    'qkmt gX.vpADmk(Q$90[4{b!TPZ.|HY^c(S_,)=Rtip;wsTYTrA15Hii$Z bx6B+' );
define( 'NONCE_KEY',        'P!?iZP$k_fS!}IW*2$>v>abbVy&0mGi>d1~*{zL3bqEz{j&U_A!g)0{C?w%+eFC%' );
define( 'AUTH_SALT',        ' K{}t47*RnD3BRO bN~*YO5)9A7Ey.B;H=3nC!w DLe>@-#PE^bG?%mCr{}Kj_~A' );
define( 'SECURE_AUTH_SALT', '97/b[!OO;(05GOF0ey:MH~IV~)Gy}^! t  H.euxwcN5>|TxP9sc`a[8Z 1P0VTN' );
define( 'LOGGED_IN_SALT',   'lf^Eci)KS#GkaV_mO[_U5w@zA%(KUV^A0Tt/xe#_KbJZW?h}w@7p5*~]20NX%)D]' );
define( 'NONCE_SALT',       'av)*j,D+s%kiTWnK?J8gZ46#+WjZWdUK@EI1*PuW^d+i}-rw@mew,ebf`F>#?EO&' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'ct_wp_';

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
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
