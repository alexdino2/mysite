<?php
$memcached_servers = array(
  'default' => array(
    'soredis.udbhj2.cfg.use1.cache.amazonaws.com:11211'
                //We can add more nodes by using commas
  )
);
 
/* We have to check that WordPress is configured to use the cache
 * looking for the following line and assuring the value is 'true'. If there isn't
 * we must create it:
*/
//define('WP_HOME','http://siteoptimus.com');
//define('WP_SITEURL','http://siteoptimus.com');

define( 'DBI_AWS_ACCESS_KEY_ID', 'AKIAIQ42O2WD7K44CEWQ' );
define( 'DBI_AWS_SECRET_ACCESS_KEY', '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' );

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
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'alexdino1');

/** MySQL database password */
define('DB_PASSWORD', 'madison7');

/** MySQL hostname */
define('DB_HOST', 'wordpress.cdc9etng7jk2.us-east-1.rds.amazonaws.com');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define('WP_MEMORY_LIMIT', '96M');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '0;5No[uL23rv~Mbd9-KV3k:gMYy/iSJScgt3/DN4le0~H*v egGLN.,Vh-LvZY>a');
define('SECURE_AUTH_KEY',  'fZbq<wStAgE3crjW(~!lal.)[$kv-^6QC?{5>hXY+U-kBzrVwi~3T|(,*ja+ntp2');
define('LOGGED_IN_KEY',    'J8l~g##ZT:}tMb90XL2Q,2Gm!}6F/N2an!SZ8o| ,^nb0^d&9H[.JlRC&Rt`Oi@f');
define('NONCE_KEY',        'y+g.bNO)s}5?dD4I!N/aa<QG NY]/o>S@q!=HP=bD(IS!oX>;WPaxECidUwN8`#W');
define('AUTH_SALT',        ']m-hA-k@(QU1I:FbE},jbXyy%xXx]5g`,*1A0^Z?>#KQ%# Daj4ERxJj`=wXH()^');
define('SECURE_AUTH_SALT', 'a-Tm~tS. E(e{M*C;9/Nj+wGxvp8Ekto<gtdy1QK6.`d5g{0q%?mJY{e^R6dp!u-');
define('LOGGED_IN_SALT',   'O7[]nb3Nnc1f3.V:@Su7Nis<Dlrz$t^Ts-pb8UAa`Ec#HX.-}/Ri#WB[8C<WcRk)');
define('NONCE_SALT',       '--~rH#0F#&gc+&8oG<+7o&]H+;w-c0-|Z FRZH0XV|9+$+%3-V(F^u`[|PT,o<j$');

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
define('WP_DEBUG', true);
//define('WP_DEBUG_LOG', true);
//define('WP_ALLOW_REPAIR', true);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */define('WP_MEMORY_LIMIT', '96M');
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
?>

