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
define('DB_NAME', 'zboom');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'ladygaga123');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         '4|6t+&PIEhRm .VRq-]?Co@IKmpyYu|B+9}%v|.Yx^<`mQ<9m{a|r7p+-zlP=(o|');
define('SECURE_AUTH_KEY',  'L`Juq&Mz(;lCu3Z%,tn~22oN>X+mAxXyPWYTa:nonE/4*xpXgrzPs>w1EX-5`1g*');
define('LOGGED_IN_KEY',    'L?FuZyN;*|,zArPV1|M+pJEEW)o)F64;uLJ0FmC@-cHOES7wXpsaE0:=eEG{WFh&');
define('NONCE_KEY',        '5)1,-GI)SYHM`3_FL1ho2kXcPBZH>D}^e(P5;Hfm7Jb_8Y7<)2U[s!nH5Ow-1iT:');
define('AUTH_SALT',        '}FT6>=%UIio^OX?goW3aIim5ueWB@a@MSDPG|TM.8ug4v(:fry]idea=v|TYxa+^');
define('SECURE_AUTH_SALT', 'ZTN!iZ[#&Yo~|J4Pp&~Y|c*,S3Jj @=FJ+c0HC_SUS2PU<n&Hg4eS`i~P_6+?ML&');
define('LOGGED_IN_SALT',   'PnUvxWee:g+(l|2+U(T.M$Z4&fk4<WLLDe]eX:&!OD#qpI[=;)X%[l%U;61:l24^');
define('NONCE_SALT',       'LOwADA8w|9|}wLjj^-Y<+^p- M+@=X;#0|sP=yo3bdPO4h!>Zz,Ky3WnZ>,BYf+F');

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
