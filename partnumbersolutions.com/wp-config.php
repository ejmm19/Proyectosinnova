<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'parts_number_solutions');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'SGvaPZ&_lYQ]<c#E;TQ&:PC h{[iRX*4PV.dM87*9MJZcBG<0Fr`H1huiH*J Yxr');
define('SECURE_AUTH_KEY',  'Z3v `ShYg $15Uv1fzv3F<T-`TOE(4d=0~-s~w&,09+L@Z1l^i,gt2MnslW[X>.C');
define('LOGGED_IN_KEY',    'F6;sG1OHON-8k/X_xkG.A;*qOHf/49+a$R&82wF<P^BMkhp:C|AM?J+PIEFSf5^)');
define('NONCE_KEY',        'B?x/z1+5W+OPZ*eQ0TVLCi}i+g(+VNHkvgL*C%%zn?pjs%}g|o?)nFl}9Y(]H_ho');
define('AUTH_SALT',        '7IfIjW6hQT[[{4gX@ ,]xvl.sNO3]u4czL:K1Pv1v,=Wt|fF.}&;>Xr@ =)_|$xf');
define('SECURE_AUTH_SALT', 'L`C?toa$ts K,p+mA,*@R0L?mZcb[Gg>7b;l}MZZwCwc!~BpW:C|4dV53ImCCdU&');
define('LOGGED_IN_SALT',   '|Mpc6HfAph [X6{wVk~E?#]fWFUk)0zsl`=8ArjY;R~W.jt~ys:yy?a0r%%sM&.c');
define('NONCE_SALT',       'wBqq#&ITyGisXDGMber,@wG4:+HWR+0^tD#K23HHmIpz5hL1dnfU)nn,z,qRF$|E');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
