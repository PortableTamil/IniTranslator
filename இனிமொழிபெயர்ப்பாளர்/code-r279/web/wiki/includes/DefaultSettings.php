<?php
/**
 *
 *                 DO NOT EVER EDIT THIS FILE!
 *
 *
 * To customize your installation, edit "LocalSettings.php". If you make
 * changes here, they will be lost on next upgrade of MediaWiki!
 *
 * Note that since all these string interpolations are expanded
 * before LocalSettings is included, if you localize something
 * like $wgScriptPath, you must also localize everything that
 * depends on it.
 *
 * Documentation is in the source and on:
 * http://www.mediawiki.org/wiki/Help:Configuration_settings
 *
 * @package MediaWiki
 */

# This is not a valid entry point, perform no further processing unless MEDIAWIKI is defined
if( !defined( 'MEDIAWIKI' ) ) {
	echo "This file is part of MediaWiki and is not a valid entry point\n";
	die( -1 );
}

/**
 * Create a site configuration object
 * Not used for much in a default install
 */
require_once( 'includes/SiteConfiguration.php' );
$wgConf = new SiteConfiguration;

/** MediaWiki version number */
$wgVersion			= '1.6.3';

/** Name of the site. It must be changed in LocalSettings.php */
$wgSitename         = 'MediaWiki';

/** Will be same as you set @see $wgSitename */
$wgMetaNamespace    = FALSE;


/** URL of the server. It will be automatically built including https mode */
$wgServer = '';

if( isset( $_SERVER['SERVER_NAME'] ) ) {
	$wgServerName = $_SERVER['SERVER_NAME'];
} elseif( isset( $_SERVER['HOSTNAME'] ) ) {
	$wgServerName = $_SERVER['HOSTNAME'];
} elseif( isset( $_SERVER['HTTP_HOST'] ) ) {
	$wgServerName = $_SERVER['HTTP_HOST'];
} elseif( isset( $_SERVER['SERVER_ADDR'] ) ) {
	$wgServerName = $_SERVER['SERVER_ADDR'];
} else {
	$wgServerName = 'localhost';
}

# check if server use https:
$wgProto = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';

$wgServer = $wgProto.'://' . $wgServerName;
# If the port is a non-standard one, add it to the URL
if(    isset( $_SERVER['SERVER_PORT'] )
    && (    ( $wgProto == 'http' && $_SERVER['SERVER_PORT'] != 80 )
	 || ( $wgProto == 'https' && $_SERVER['SERVER_PORT'] != 443 ) ) ) {

	$wgServer .= ":" . $_SERVER['SERVER_PORT'];
}


/**
 * The path we should point to.
 * It might be a virtual path in case with use apache mod_rewrite for example
 */
$wgScriptPath	    = '/wiki';

/**
 * Whether to support URLs like index.php/Page_title
 * @global bool $wgUsePathInfo
 */
$wgUsePathInfo		= ( strpos( php_sapi_name(), 'cgi' ) === false );


/**#@+
 * Script users will request to get articles
 * ATTN: Old installations used wiki.phtml and redirect.phtml -
 * make sure that LocalSettings.php is correctly set!
 * @deprecated
 */
/**
 *	@global string $wgScript
 */
$wgScript           = "{$wgScriptPath}/index.php";
/**
 *	@global string $wgRedirectScript
 */
$wgRedirectScript   = "{$wgScriptPath}/redirect.php";
/**#@-*/


/**#@+
 * @global string
 */
/**
 * style path as seen by users
 * @global string $wgStylePath
 */
$wgStylePath   = "{$wgScriptPath}/skins";
/**
 * filesystem stylesheets directory
 * @global string $wgStyleDirectory
 */
$wgStyleDirectory = "{$IP}/skins";
$wgStyleSheetPath = &$wgStylePath;
$wgArticlePath      = "{$wgScript}?title=$1";
$wgUploadPath       = "{$wgScriptPath}/upload";
$wgUploadDirectory	= "{$IP}/upload";
$wgHashedUploadDirectory	= true;
$wgLogo				= "{$wgUploadPath}/wiki.png";
$wgFavicon			= '/favicon.ico';
$wgMathPath         = "{$wgUploadPath}/math";
$wgMathDirectory    = "{$wgUploadDirectory}/math";
$wgTmpDirectory     = "{$wgUploadDirectory}/tmp";
$wgUploadBaseUrl    = "";
/**#@-*/

/**
 * Allowed title characters -- regex character class
 * Don't change this unless you know what you're doing
 *
 * Problematic punctuation:
 *  []{}|#    Are needed for link syntax, never enable these
 *  %         Enabled by default, minor problems with path to query rewrite rules, see below
 *  +         Doesn't work with path to query rewrite rules, corrupted by apache
 *  ?         Enabled by default, but doesn't work with path to PATH_INFO rewrites
 *
 * All three of these punctuation problems can be avoided by using an alias, instead of a
 * rewrite rule of either variety.
 *
 * The problem with % is that when using a path to query rewrite rule, URLs are
 * double-unescaped: once by Apache's path conversion code, and again by PHP. So
 * %253F, for example, becomes "?". Our code does not double-escape to compensate
 * for this, indeed double escaping would break if the double-escaped title was
 * passed in the query string rather than the path. This is a minor security issue
 * because articles can be created such that they are hard to view or edit.
 *
 * Theoretically 0x80-0x9F of ISO 8859-1 should be disallowed, but
 * this breaks interlanguage links
 */
$wgLegalTitleChars = " %!\"$&'()*,\\-.\\/0-9:;=?@A-Z\\\\^_`a-z~\\x80-\\xFF";


/**
 * The external URL protocols
 */
$wgUrlProtocols = array(
	'http://',
	'https://',
	'ftp://',
	'irc://',
	'gopher://',
	'telnet://', // Well if we're going to support the above.. -??var
	'nntp://', // @bug 3808 RFC 1738
	'worldwind://',
	'mailto:',
	'news:'
);

/** internal name of virus scanner. This servers as a key to the $wgAntivirusSetup array.
 * Set this to NULL to disable virus scanning. If not null, every file uploaded will be scanned for viruses.
 * @global string $wgAntivirus
 */
$wgAntivirus= NULL;

/** Configuration for different virus scanners. This an associative array of associative arrays:
 * it contains on setup array per known scanner type. The entry is selected by $wgAntivirus, i.e.
 * valid values for $wgAntivirus are the keys defined in this array.
 *
 * The configuration array for each scanner contains the following keys: "command", "codemap", "messagepattern";
 *
 * "command" is the full command to call the virus scanner - %f will be replaced with the name of the
 * file to scan. If not present, the filename will be appended to the command. Note that this must be
 * overwritten if the scanner is not in the system path; in that case, plase set
 * $wgAntivirusSetup[$wgAntivirus]['command'] to the desired command with full path.
 *
 * "codemap" is a mapping of exit code to return codes of the detectVirus function in SpecialUpload.
 * An exit code mapped to AV_SCAN_FAILED causes the function to consider the scan to be failed. This will pass
 * the file if $wgAntivirusRequired is not set.
 * An exit code mapped to AV_SCAN_ABORTED causes the function to consider the file to have an usupported format,
 * which is probably imune to virusses. This causes the file to pass.
 * An exit code mapped to AV_NO_VIRUS will cause the file to pass, meaning no virus was found.
 * All other codes (like AV_VIRUS_FOUND) will cause the function to report a virus.
 * You may use "*" as a key in the array to catch all exit codes not mapped otherwise.
 *
 * "messagepattern" is a perl regular expression to extract the meaningful part of the scanners
 * output. The relevant part should be matched as group one (\1).
 * If not defined or the pattern does not match, the full message is shown to the user.
 *
 * @global array $wgAntivirusSetup
 */
$wgAntivirusSetup= array(

	#setup for clamav
	'clamav' => array (
		'command' => "clamscan --no-summary ",

		'codemap'=> array (
			"0"=>  AV_NO_VIRUS, #no virus
			"1"=>  AV_VIRUS_FOUND, #virus found
			"52"=> AV_SCAN_ABORTED, #unsupported file format (probably imune)
			"*"=>  AV_SCAN_FAILED, #else scan failed
		),

		'messagepattern'=> '/.*?:(.*)/sim',
	),

	#setup for f-prot
	'f-prot' => array (
		'command' => "f-prot ",

		'codemap'=> array (
			"0"=> AV_NO_VIRUS, #no virus
			"3"=> AV_VIRUS_FOUND, #virus found
			"6"=> AV_VIRUS_FOUND, #virus found
			"*"=> AV_SCAN_FAILED, #else scan failed
		),

		'messagepattern'=> '/.*?Infection:(.*)$/m',
	),
);


/** Determines if a failed virus scan (AV_SCAN_FAILED) will cause the file to be rejected.
 * @global boolean $wgAntivirusRequired
*/
$wgAntivirusRequired= true;

/** Determines if the mime type of uploaded files should be checked
 * @global boolean $wgVerifyMimeType
*/
$wgVerifyMimeType= true;

/** Sets the mime type definition file to use by MimeMagic.php.
* @global string $wgMimeTypeFile
*/
#$wgMimeTypeFile= "/etc/mime.types";
$wgMimeTypeFile= "includes/mime.types";
#$wgMimeTypeFile= NULL; #use built-in defaults only.

/** Sets the mime type info file to use by MimeMagic.php.
* @global string $wgMimeInfoFile
*/
$wgMimeInfoFile= "includes/mime.info";
#$wgMimeInfoFile= NULL; #use built-in defaults only.

/** Switch for loading the FileInfo extension by PECL at runtime.
* This should be used only if fileinfo is installed as a shared object / dynamic libary
* @global string $wgLoadFileinfoExtension
*/
$wgLoadFileinfoExtension= false;

/** Sets an external mime detector program. The command must print only the mime type to standard output.
* the name of the file to process will be appended to the command given here.
* If not set or NULL, mime_content_type will be used if available.
*/
$wgMimeDetectorCommand= NULL; # use internal mime_content_type function, available since php 4.3.0
#$wgMimeDetectorCommand= "file -bi"; #use external mime detector (Linux)

/** Switch for trivial mime detection. Used by thumb.php to disable all fance things,
* because only a few types of images are needed and file extensions can be trusted.
*/
$wgTrivialMimeDetection= false;

/**
 * To set 'pretty' URL paths for actions other than
 * plain page views, add to this array. For instance:
 *   'edit' => "$wgScriptPath/edit/$1"
 *
 * There must be an appropriate script or rewrite rule
 * in place to handle these URLs.
 */
$wgActionPaths = array();

/**
 * If you operate multiple wikis, you can define a shared upload path here.
 * Uploads to this wiki will NOT be put there - they will be put into
 * $wgUploadDirectory.
 * If $wgUseSharedUploads is set, the wiki will look in the shared repository if
 * no file of the given name is found in the local repository (for [[Image:..]],
 * [[Media:..]] links). Thumbnails will also be looked for and generated in this
 * directory.
 */
$wgUseSharedUploads = false;
/** Full path on the web server where shared uploads can be found */
$wgSharedUploadPath = "http://commons.wikimedia.org/shared/images";
/** Fetch commons image description pages and display them on the local wiki? */
$wgFetchCommonsDescriptions = false;
/** Path on the file system where shared uploads can be found. */
$wgSharedUploadDirectory = "/var/www/wiki3/images";
/** DB name with metadata about shared directory. Set this to false if the uploads do not come from a wiki. */
$wgSharedUploadDBname = false;
/** Optional table prefix used in database. */
$wgSharedUploadDBprefix = '';
/** Cache shared metadata in memcached. Don't do this if the commons wiki is in a different memcached domain */
$wgCacheSharedUploads = true;

/**
 * Point the upload navigation link to an external URL
 * Useful if you want to use a shared repository by default
 * without disabling local uploads (use $wgEnableUploads = false for that)
 * e.g. $wgUploadNavigationUrl = 'http://commons.wikimedia.org/wiki/Special:Upload';
*/
$wgUploadNavigationUrl = false;

/**
 * Give a path here to use thumb.php for thumbnail generation on client request, instead of
 * generating them on render and outputting a static URL. This is necessary if some of your
 * apache servers don't have read/write access to the thumbnail path.
 *
 * Example:
 *   $wgThumbnailScriptPath = "{$wgScriptPath}/thumb.php";
 */
$wgThumbnailScriptPath = false;
$wgSharedThumbnailScriptPath = false;

/**
 * Set the following to false especially if you have a set of files that need to
 * be accessible by all wikis, and you do not want to use the hash (path/a/aa/)
 * directory layout.
 */
$wgHashedSharedUploadDirectory = true;

/**
 * Base URL for a repository wiki. Leave this blank if uploads are just stored
 * in a shared directory and not meant to be accessible through a separate wiki.
 * Otherwise the image description pages on the local wiki will link to the
 * image description page on this wiki.
 *
 * Please specify the namespace, as in the example below.
 */
$wgRepositoryBaseUrl="http://commons.wikimedia.org/wiki/Image:";


#
# Email settings
#

/**
 * Site admin email address
 * Default to wikiadmin@SERVER_NAME
 * @global string $wgEmergencyContact
 */
$wgEmergencyContact = 'wikiadmin@' . $wgServerName;

/**
 * Password reminder email address
 * The address we should use as sender when a user is requesting his password
 * Default to apache@SERVER_NAME
 * @global string $wgPasswordSender
 */
$wgPasswordSender	= 'MediaWiki Mail <apache@' . $wgServerName . '>';

/**
 * dummy address which should be accepted during mail send action
 * It might be necessay to adapt the address or to set it equal
 * to the $wgEmergencyContact address
 */
#$wgNoReplyAddress	= $wgEmergencyContact;
$wgNoReplyAddress	= 'reply@not.possible';

/**
 * Set to true to enable the e-mail basic features:
 * Password reminders, etc. If sending e-mail on your
 * server doesn't work, you might want to disable this.
 * @global bool $wgEnableEmail
 */
$wgEnableEmail = true;

/**
 * Set to true to enable user-to-user e-mail.
 * This can potentially be abused, as it's hard to track.
 * @global bool $wgEnableUserEmail
 */
$wgEnableUserEmail = true;

/**
 * SMTP Mode
 * For using a direct (authenticated) SMTP server connection.
 * Default to false or fill an array :
 * <code>
 * "host" => 'SMTP domain',
 * "IDHost" => 'domain for MessageID',
 * "port" => "25",
 * "auth" => true/false,
 * "username" => user,
 * "password" => password
 * </code>
 *
 * @global mixed $wgSMTP
 */
$wgSMTP				= false;


/**#@+
 * Database settings
 */
/** database host name or ip address */
$wgDBserver         = 'localhost';
/** name of the database */
$wgDBname           = 'wikidb';
/** */
$wgDBconnection     = '';
/** Database username */
$wgDBuser           = 'wikiuser';
/** Database type
 * "mysql" for working code and "PostgreSQL" for development/broken code
 */
$wgDBtype           = "mysql";
/** Search type
 * Leave as null to select the default search engine for the
 * selected database type (eg SearchMySQL4), or set to a class
 * name to override to a custom search engine.
 */
$wgSearchType	    = null;
/** Table name prefix */
$wgDBprefix         = '';
/** Database schema
 * on some databases this allows separate
 * logical namespace for application data
 */
$wgDBschema	    = 'mediawiki';
/**#@-*/

/** Live high performance sites should disable this - some checks acquire giant mysql locks */
$wgCheckDBSchema = true;


/**
 * Shared database for multiple wikis. Presently used for storing a user table
 * for single sign-on. The server for this database must be the same as for the
 * main database.
 * EXPERIMENTAL
 */
$wgSharedDB = null;

# Database load balancer
# This is a two-dimensional array, an array of server info structures
# Fields are:
#   host:        Host name
#   dbname:      Default database name
#   user:        DB user
#   password:    DB password
#   type:        "mysql" or "pgsql"
#   load:        ratio of DB_SLAVE load, must be >=0, the sum of all loads must be >0
#   groupLoads:  array of load ratios, the key is the query group name. A query may belong
#                to several groups, the most specific group defined here is used.
#
#   flags:       bit field
#                   DBO_DEFAULT -- turns on DBO_TRX only if !$wgCommandLineMode (recommended)
#                   DBO_DEBUG -- equivalent of $wgDebugDumpSql
#                   DBO_TRX -- wrap entire request in a transaction
#                   DBO_IGNORE -- ignore errors (not useful in LocalSettings.php)
#                   DBO_NOBUFFER -- turn off buffering (not useful in LocalSettings.php)
#
#   max lag:     (optional) Maximum replication lag before a slave will taken out of rotation
#   max threads: (optional) Maximum number of running threads
#
#   These and any other user-defined properties will be assigned to the mLBInfo member
#   variable of the Database object.
#
# Leave at false to use the single-server variables above
$wgDBservers		= false;

/** How long to wait for a slave to catch up to the master */
$wgMasterWaitTimeout = 10;

/** File to log MySQL errors to */
$wgDBerrorLog		= false;

/** When to give an error message */
$wgDBClusterTimeout = 10;

/**
 * wgDBminWordLen :
 * MySQL 3.x : used to discard words that MySQL will not return any results for
 * shorter values configure mysql directly.
 * MySQL 4.x : ignore it and configure mySQL
 * See: http://dev.mysql.com/doc/mysql/en/Fulltext_Fine-tuning.html
 */
$wgDBminWordLen     = 4;
/** Set to true if using InnoDB tables */
$wgDBtransactions	= false;
/** Set to true for compatibility with extensions that might be checking.
 * MySQL 3.23.x is no longer supported. */
$wgDBmysql4			= true;

/**
 * Set to true to engage MySQL 4.1/5.0 charset-related features;
 * for now will just cause sending of 'SET NAMES=utf8' on connect.
 *
 * WARNING: THIS IS EXPERIMENTAL!
 *
 * May break if you're not using the table defs from mysql5/tables.sql.
 * May break if you're upgrading an existing wiki if set differently.
 * Broken symptoms likely to include incorrect behavior with page titles,
 * usernames, comments etc containing non-ASCII characters.
 * Might also cause failures on the object cache and other things.
 *
 * Even correct usage may cause failures with Unicode supplementary
 * characters (those not in the Basic Multilingual Plane) unless MySQL
 * has enhanced their Unicode support.
 */
$wgDBmysql5			= false;

/**
 * Other wikis on this site, can be administered from a single developer
 * account.
 * Array numeric key => database name
 */
$wgLocalDatabases   = array();

/**
 * Object cache settings
 * See Defines.php for types
 */
$wgMainCacheType = CACHE_NONE;
$wgMessageCacheType = CACHE_ANYTHING;
$wgParserCacheType = CACHE_ANYTHING;

$wgParserCacheExpireTime = 86400;

$wgSessionsInMemcached = false;
$wgLinkCacheMemcached = false; # Not fully tested

/**
 * Memcached-specific settings
 * See docs/memcached.txt
 */
$wgUseMemCached     = false;
$wgMemCachedDebug   = false; # Will be set to false in Setup.php, if the server isn't working
$wgMemCachedServers = array( '127.0.0.1:11000' );
$wgMemCachedDebug   = false;
$wgMemCachedPersistent = false;

/**
 * Directory for local copy of message cache, for use in addition to memcached
 */
$wgLocalMessageCache = false;

/**
 * Directory for compiled constant message array databases
 * WARNING: turning anything on will just break things, aaaaaah!!!!
 */
$wgCachedMessageArrays = false;

# Language settings
#
/** Site language code, should be one of ./languages/Language(.*).php */
$wgLanguageCode     = 'en';

/** Treat language links as magic connectors, not inline links */
$wgInterwikiMagic	= true;

/** Hide interlanguage links from the sidebar */
$wgHideInterlanguageLinks = false;


/** We speak UTF-8 all the time now, unless some oddities happen */
$wgInputEncoding	= 'UTF-8';
$wgOutputEncoding	= 'UTF-8';
$wgEditEncoding		= '';

# Set this to eg 'ISO-8859-1' to perform character set
# conversion when loading old revisions not marked with
# "utf-8" flag. Use this when converting wiki to UTF-8
# without the burdensome mass conversion of old text data.
#
# NOTE! This DOES NOT touch any fields other than old_text.
# Titles, comments, user names, etc still must be converted
# en masse in the database before continuing as a UTF-8 wiki.
$wgLegacyEncoding   = false;

/**
 * If set to true, the MediaWiki 1.4 to 1.5 schema conversion will
 * create stub reference rows in the text table instead of copying
 * the full text of all current entries from 'cur' to 'text'.
 *
 * This will speed up the conversion step for large sites, but
 * requires that the cur table be kept around for those revisions
 * to remain viewable.
 *
 * maintenance/migrateCurStubs.php can be used to complete the
 * migration in the background once the wiki is back online.
 *
 * This option affects the updaters *only*. Any present cur stub
 * revisions will be readable at runtime regardless of this setting.
 */
$wgLegacySchemaConversion = false;

$wgMimeType			= 'text/html';
$wgJsMimeType			= 'text/javascript';
$wgDocType			= '-//W3C//DTD XHTML 1.0 Transitional//EN';
$wgDTD				= 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd';

/** Enable to allow rewriting dates in page text.
 * DOES NOT FORMAT CORRECTLY FOR MOST LANGUAGES */
$wgUseDynamicDates  = false;
/** Enable dates like 'May 12' instead of '12 May', this only takes effect if
 * the interface is set to English
 */
$wgAmericanDates    = false;
/**
 * For Hindi and Arabic use local numerals instead of Western style (0-9)
 * numerals in interface.
 */
$wgTranslateNumerals = true;


# Translation using MediaWiki: namespace
# This will increase load times by 25-60% unless memcached is installed
# Interface messages will be loaded from the database.
$wgUseDatabaseMessages = true;
$wgMsgCacheExpiry	= 86400;

# Whether to enable language variant conversion.
$wgDisableLangConversion = false;

# Whether to use zhdaemon to perform Chinese text processing
# zhdaemon is under developement, so normally you don't want to
# use it unless for testing
$wgUseZhdaemon = false;
$wgZhdaemonHost="localhost";
$wgZhdaemonPort=2004;

/** Normally you can ignore this and it will be something
    like $wgMetaNamespace . "_talk". In some languages, you
    may want to set this manually for grammatical reasons.
    It is currently only respected by those languages
    where it might be relevant and where no automatic
    grammar converter exists.
*/
$wgMetaNamespaceTalk = false;

# Miscellaneous configuration settings
#

$wgLocalInterwiki   = 'w';
$wgInterwikiExpiry = 10800; # Expiry time for cache of interwiki table

/** Interwiki caching settings.
	$wgInterwikiCache specifies path to constant database file
		This cdb database is generated by dumpInterwiki from maintenance
		and has such key formats:
			dbname:key - a simple key (e.g. enwiki:meta)
			_sitename:key - site-scope key (e.g. wiktionary:meta)
			__global:key - global-scope key (e.g. __global:meta)
			__sites:dbname - site mapping (e.g. __sites:enwiki)
		Sites mapping just specifies site name, other keys provide
			"local url" data layout.
	$wgInterwikiScopes specify number of domains to check for messages:
		1 - Just wiki(db)-level
		2 - wiki and global levels
		3 - site levels
	$wgInterwikiFallbackSite - if unable to resolve from cache
*/
$wgInterwikiCache = false;
$wgInterwikiScopes = 3;
$wgInterwikiFallbackSite = 'wiki';

/**
 * If local interwikis are set up which allow redirects,
 * set this regexp to restrict URLs which will be displayed
 * as 'redirected from' links.
 *
 * It might look something like this:
 * $wgRedirectSources = '!^https?://[a-z-]+\.wikipedia\.org/!';
 *
 * Leave at false to avoid displaying any incoming redirect markers.
 * This does not affect intra-wiki redirects, which don't change
 * the URL.
 */
$wgRedirectSources = false;


$wgShowIPinHeader	= true; # For non-logged in users
$wgMaxNameChars		= 255;  # Maximum number of bytes in username
$wgMaxArticleSize	= 2048; # Maximum article size in kilobytes

$wgExtraSubtitle	= '';
$wgSiteSupportPage	= ''; # A page where you users can receive donations

$wgReadOnlyFile         = "{$wgUploadDirectory}/lock_yBgMBwiR";

/**
 * The debug log file should be not be publicly accessible if it is used, as it
 * may contain private data. */
$wgDebugLogFile         = '';

/**#@+
 * @global bool
 */
$wgDebugRedirects		= false;
$wgDebugRawPage         = false; # Avoid overlapping debug entries by leaving out CSS

$wgDebugComments        = false;
$wgReadOnly             = null;
$wgLogQueries           = false;

/**
 * Write SQL queries to the debug log
 */
$wgDebugDumpSql         = false;

/**
 * Set to an array of log group keys to filenames.
 * If set, wfDebugLog() output for that group will go to that file instead
 * of the regular $wgDebugLogFile. Useful for enabling selective logging
 * in production.
 */
$wgDebugLogGroups       = array();

/**
 * Whether to show "we're sorry, but there has been a database error" pages.
 * Displaying errors aids in debugging, but may display information useful
 * to an attacker.
 */
$wgShowSQLErrors        = false;

/**
 * If true, some error messages will be colorized when running scripts on the
 * command line; this can aid picking important things out when debugging.
 * Ignored when running on Windows or when output is redirected to a file.
 */
$wgColorErrors          = true;

/**
 * disable experimental dmoz-like category browsing. Output things like:
 * Encyclopedia > Music > Style of Music > Jazz
 */
$wgUseCategoryBrowser   = false;

/**
 * Keep parsed pages in a cache (objectcache table, turck, or memcached)
 * to speed up output of the same page viewed by another user with the
 * same options.
 *
 * This can provide a significant speedup for medium to large pages,
 * so you probably want to keep it on.
 */
$wgEnableParserCache = true;

/**
 * Under which condition should a page in the main namespace be counted
 * as a valid article? If $wgUseCommaCount is set to true, it will be
 * counted if it contains at least one comma. If it is set to false
 * (default), it will only be counted if it contains at least one [[wiki
 * link]]. See http://meta.wikimedia.org/wiki/Help:Article_count
 *
 * Retroactively changing this variable will not affect
 * the existing count (cf. maintenance/recount.sql).
*/
$wgUseCommaCount = false;

/**#@-*/

/**
 * wgHitcounterUpdateFreq sets how often page counters should be updated, higher
 * values are easier on the database. A value of 1 causes the counters to be
 * updated on every hit, any higher value n cause them to update *on average*
 * every n hits. Should be set to either 1 or something largish, eg 1000, for
 * maximum efficiency.
*/
$wgHitcounterUpdateFreq = 1;

# Basic user rights and block settings
$wgSysopUserBans        = true; # Allow sysops to ban logged-in users
$wgSysopRangeBans		= true; # Allow sysops to ban IP ranges
$wgAutoblockExpiry		= 86400; # Number of seconds before autoblock entries expire
$wgBlockAllowsUTEdit    = false; # Blocks allow users to edit their own user talk page

# Pages anonymous user may see as an array, e.g.:
# array ( "Main Page", "Special:Userlogin", "Wikipedia:Help");
# NOTE: This will only work if $wgGroupPermissions['*']['read']
# is false -- see below. Otherwise, ALL pages are accessible,
# regardless of this setting.
# Also note that this will only protect _pages in the wiki_.
# Uploaded files will remain readable. Make your upload
# directory name unguessable, or use .htaccess to protect it.
$wgWhitelistRead = false;

/** 
 * Should editors be required to have a validated e-mail
 * address before being allowed to edit?
 */
$wgEmailConfirmToEdit=false;

/**
 * Permission keys given to users in each group.
 * All users are implicitly in the '*' group including anonymous visitors;
 * logged-in users are all implicitly in the 'user' group. These will be
 * combined with the permissions of all groups that a given user is listed
 * in in the user_groups table.
 *
 * Functionality to make pages inaccessible has not been extensively tested
 * for security. Use at your own risk!
 *
 * This replaces wgWhitelistAccount and wgWhitelistEdit
 */
$wgGroupPermissions = array();

// Implicit group for all visitors
$wgGroupPermissions['*'    ]['createaccount']   = true;
$wgGroupPermissions['*'    ]['read']            = true;
$wgGroupPermissions['*'    ]['edit']            = true;
$wgGroupPermissions['*'    ]['createpage']      = true;
$wgGroupPermissions['*'    ]['createtalk']      = true;

// Implicit group for all logged-in accounts
$wgGroupPermissions['user' ]['move']            = true;
$wgGroupPermissions['user' ]['read']            = true;
$wgGroupPermissions['user' ]['edit']            = true;
$wgGroupPermissions['user' ]['createpage']      = true;
$wgGroupPermissions['user' ]['createtalk']      = true;
$wgGroupPermissions['user' ]['upload']          = true;
$wgGroupPermissions['user' ]['reupload']        = true;
$wgGroupPermissions['user' ]['reupload-shared'] = true;
$wgGroupPermissions['user' ]['minoredit']       = true;

// Implicit group for accounts that pass $wgAutoConfirmAge
$wgGroupPermissions['autoconfirmed']['autoconfirmed'] = true;

// Users with bot privilege can have their edits hidden
// from various log pages by default
$wgGroupPermissions['bot'  ]['bot']             = true;
$wgGroupPermissions['bot'  ]['autoconfirmed']   = true;

// Most extra permission abilities go to this group
$wgGroupPermissions['sysop']['block']           = true;
$wgGroupPermissions['sysop']['createaccount']   = true;
$wgGroupPermissions['sysop']['delete']          = true;
$wgGroupPermissions['sysop']['deletedhistory']  = true; // can view deleted history entries, but not see or restore the text
$wgGroupPermissions['sysop']['editinterface']   = true;
$wgGroupPermissions['sysop']['import']          = true;
$wgGroupPermissions['sysop']['importupload']    = true;
$wgGroupPermissions['sysop']['move']            = true;
$wgGroupPermissions['sysop']['patrol']          = true;
$wgGroupPermissions['sysop']['protect']         = true;
$wgGroupPermissions['sysop']['rollback']        = true;
$wgGroupPermissions['sysop']['upload']          = true;
$wgGroupPermissions['sysop']['reupload']        = true;
$wgGroupPermissions['sysop']['reupload-shared'] = true;
$wgGroupPermissions['sysop']['unwatchedpages']	= true;
$wgGroupPermissions['sysop']['autoconfirmed']   = true;

// Permission to change users' group assignments
$wgGroupPermissions['bureaucrat']['userrights'] = true;

// Experimental permissions, not ready for production use
//$wgGroupPermissions['sysop']['deleterevision'] = true;
//$wgGroupPermissions['bureaucrat']['hiderevision'] = true;

/**
 * The developer group is deprecated, but can be activated if need be
 * to use the 'lockdb' and 'unlockdb' special pages. Those require
 * that a lock file be defined and creatable/removable by the web
 * server.
 */
# $wgGroupPermissions['developer']['siteadmin'] = true;

/**
 * Set of available actions that can be restricted via Special:Protect
 * You probably shouldn't change this.
 * Translated trough restriction-* messages.
 */
$wgRestrictionTypes = array( 'edit', 'move' );

/**
 * Set of permission keys that can be selected via Special:Protect.
 * 'autoconfirm' allows all registerd users if $wgAutoConfirmAge is 0.
 */
$wgRestrictionLevels = array( '', 'autoconfirmed', 'sysop' );


/**
 * Number of seconds an account is required to age before
 * it's given the implicit 'autoconfirm' group membership.
 * This can be used to limit privileges of new accounts.
 *
 * Accounts created by earlier versions of the software
 * may not have a recorded creation date, and will always
 * be considered to pass the age test.
 *
 * When left at 0, all registered accounts will pass.
 */
$wgAutoConfirmAge = 0;
//$wgAutoConfirmAge = 600;     // ten minutes
//$wgAutoConfirmAge = 3600*24; // one day



# Proxy scanner settings
#

/**
 * If you enable this, every editor's IP address will be scanned for open HTTP
 * proxies.
 *
 * Don't enable this. Many sysops will report "hostile TCP port scans" to your
 * ISP and ask for your server to be shut down.
 *
 * You have been warned.
 */
$wgBlockOpenProxies = false;
/** Port we want to scan for a proxy */
$wgProxyPorts = array( 80, 81, 1080, 3128, 6588, 8000, 8080, 8888, 65506 );
/** Script used to scan */
$wgProxyScriptPath = "$IP/proxy_check.php";
/** */
$wgProxyMemcExpiry = 86400;
/** This should always be customised in LocalSettings.php */
$wgSecretKey = false;
/** big list of banned IP addresses, in the keys not the values */
$wgProxyList = array();
/** deprecated */
$wgProxyKey = false;

/** Number of accounts each IP address may create, 0 to disable.
 * Requires memcached */
$wgAccountCreationThrottle = 0;

# Client-side caching:

/** Allow client-side caching of pages */
$wgCachePages       = true;

/**
 * Set this to current time to invalidate all prior cached pages. Affects both
 * client- and server-side caching.
 * You can get the current date on your server by using the command:
 *   date +%Y%m%d%H%M%S
 */
$wgCacheEpoch = '20030516000000';


# Server-side caching:

/**
 * This will cache static pages for non-logged-in users to reduce
 * database traffic on public sites.
 * Must set $wgShowIPinHeader = false
 */
$wgUseFileCache = false;
/** Directory where the cached page will be saved */
$wgFileCacheDirectory = "{$wgUploadDirectory}/cache";

/**
 * When using the file cache, we can store the cached HTML gzipped to save disk
 * space. Pages will then also be served compressed to clients that support it.
 * THIS IS NOT COMPATIBLE with ob_gzhandler which is now enabled if supported in
 * the default LocalSettings.php! If you enable this, remove that setting first.
 *
 * Requires zlib support enabled in PHP.
 */
$wgUseGzip = false;

# Email notification settings
#

/** For email notification on page changes */
$wgPasswordSender = $wgEmergencyContact;

# true: from page editor if s/he opted-in
# false: Enotif mails appear to come from $wgEmergencyContact
$wgEnotifFromEditor	= false;

// TODO move UPO to preferences probably ?
# If set to true, users get a corresponding option in their preferences and can choose to enable or disable at their discretion
# If set to false, the corresponding input form on the user preference page is suppressed
# It call this to be a "user-preferences-option (UPO)"
$wgEmailAuthentication				= true; # UPO (if this is set to false, texts referring to authentication are suppressed)
$wgEnotifWatchlist		= false; # UPO
$wgEnotifUserTalk		= false;	# UPO
$wgEnotifRevealEditorAddress	= false;	# UPO; reply-to address may be filled with page editor's address (if user allowed this in the preferences)
$wgEnotifMinorEdits		= true;	# UPO; false: "minor edits" on pages do not trigger notification mails.
#							# Attention: _every_ change on a user_talk page trigger a notification mail (if the user is not yet notified)


/** Show watching users in recent changes, watchlist and page history views */
$wgRCShowWatchingUsers 				= false; # UPO
/** Show watching users in Page views */
$wgPageShowWatchingUsers 			= false;
/**
 * Show "Updated (since my last visit)" marker in RC view, watchlist and history
 * view for watched pages with new changes */
$wgShowUpdatedMarker 				= true;

$wgCookieExpiration = 2592000;

/** Clock skew or the one-second resolution of time() can occasionally cause cache
 * problems when the user requests two pages within a short period of time. This
 * variable adds a given number of seconds to vulnerable timestamps, thereby giving
 * a grace period.
 */
$wgClockSkewFudge = 5;

# Squid-related settings
#

/** Enable/disable Squid */
$wgUseSquid = false;

/** If you run Squid3 with ESI support, enable this (default:false): */
$wgUseESI = false;

/** Internal server name as known to Squid, if different */
# $wgInternalServer = 'http://yourinternal.tld:8000';
$wgInternalServer = $wgServer;

/**
 * Cache timeout for the squid, will be sent as s-maxage (without ESI) or
 * Surrogate-Control (with ESI). Without ESI, you should strip out s-maxage in
 * the Squid config. 18000 seconds = 5 hours, more cache hits with 2678400 = 31
 * days
 */
$wgSquidMaxage = 18000;

/**
 * A list of proxy servers (ips if possible) to purge on changes don't specify
 * ports here (80 is default)
 */
# $wgSquidServers = array('127.0.0.1');
$wgSquidServers = array();
$wgSquidServersNoPurge = array();

/** Maximum number of titles to purge in any one client operation */
$wgMaxSquidPurgeTitles = 400;

/** HTCP multicast purging */
$wgHTCPPort = 4827;
$wgHTCPMulticastTTL = 1;
# $wgHTCPMulticastAddress = "224.0.0.85";

# Cookie settings:
#
/**
 * Set to set an explicit domain on the login cookies eg, "justthis.domain. org"
 * or ".any.subdomain.net"
 */
$wgCookieDomain = '';
$wgCookiePath = '/';
$wgCookieSecure = ($wgProto == 'https');
$wgDisableCookieCheck = false;

/**  Whether to allow inline image pointing to other websites */
$wgAllowExternalImages = true;

/** If the above is false, you can specify an exception here. Image URLs
  * that start with this string are then rendered, while all others are not.
  * You can use this to set up a trusted, simple repository of images.
  *
  * Example:
  * $wgAllowExternalImagesFrom = 'http://127.0.0.1/';
  */
$wgAllowExternalImagesFrom = '';

/** Disable database-intensive features */
$wgMiserMode = false;
/** Disable all query pages if miser mode is on, not just some */
$wgDisableQueryPages = false;
/** Generate a watchlist once every hour or so */
$wgUseWatchlistCache = false;
/** The hour or so mentioned above */
$wgWLCacheTimeout = 3600;
/** Number of links to a page required before it is deemed "wanted" */
$wgWantedPagesThreshold = 1;

/**
 * To use inline TeX, you need to compile 'texvc' (in the 'math' subdirectory of
 * the MediaWiki package and have latex, dvips, gs (ghostscript), andconvert
 * (ImageMagick) installed and available in the PATH.
 * Please see math/README for more information.
 */
$wgUseTeX = false;
/** Location of the texvc binary */
$wgTexvc = './math/texvc';

#
# Profiling / debugging
#

/** Enable for more detailed by-function times in debug log */
$wgProfiling = false;
/** Only record profiling info for pages that took longer than this */
$wgProfileLimit = 0.0;
/** Don't put non-profiling info into log file */
$wgProfileOnly = false;
/** Log sums from profiling into "profiling" table in db. */
$wgProfileToDatabase = false;
/** Only profile every n requests when profiling is turned on */
$wgProfileSampleRate = 1;
/** If true, print a raw call tree instead of per-function report */
$wgProfileCallTree = false;
/** If not empty, specifies profiler type to load */
$wgProfilerType = '';

/** Settings for UDP profiler */
$wgUDPProfilerHost = '127.0.0.1';
$wgUDPProfilerPort = '3811';

/** Detects non-matching wfProfileIn/wfProfileOut calls */
$wgDebugProfiling = false;
/** Output debug message on every wfProfileIn/wfProfileOut */
$wgDebugFunctionEntry = 0;
/** Lots of debugging output from SquidUpdate.php */
$wgDebugSquid = false;

$wgDisableCounters = false;
$wgDisableTextSearch = false;
$wgDisableSearchContext = false;
/**
 * If you've disabled search semi-permanently, this also disables updates to the
 * table. If you ever re-enable, be sure to rebuild the search table.
 */
$wgDisableSearchUpdate = false;
/** Uploads have to be specially set up to be secure */
$wgEnableUploads = false;
/**
 * Show EXIF data, on by default if available.
 * Requires PHP's EXIF extension: http://www.php.net/manual/en/ref.exif.php
 */
$wgShowEXIF = function_exists( 'exif_read_data' );

/**
 * Set to true to enable the upload _link_ while local uploads are disabled.
 * Assumes that the special page link will be bounced to another server where
 * uploads do work.
 */
$wgRemoteUploads = false;
$wgDisableAnonTalk = false;
/**
 * Do DELETE/INSERT for link updates instead of incremental
 */
$wgUseDumbLinkUpdate = false;

/**
 * Anti-lock flags - bitfield
 *   ALF_PRELOAD_LINKS
 *       Preload links during link update for save
 *   ALF_PRELOAD_EXISTENCE
 *       Preload cur_id during replaceLinkHolders
 *   ALF_NO_LINK_LOCK
 *       Don't use locking reads when updating the link table. This is
 *       necessary for wikis with a high edit rate for performance
 *       reasons, but may cause link table inconsistency
 *   ALF_NO_BLOCK_LOCK
 *       As for ALF_LINK_LOCK, this flag is a necessity for high-traffic
 *       wikis.
 */
$wgAntiLockFlags = 0;

/**
 * Path to the GNU diff3 utility. If the file doesn't exist, edit conflicts will
 * fall back to the old behaviour (no merging).
 */
$wgDiff3 = '/usr/bin/diff3';

/**
 * We can also compress text in the old revisions table. If this is set on, old
 * revisions will be compressed on page save if zlib support is available. Any
 * compressed revisions will be decompressed on load regardless of this setting
 * *but will not be readable at all* if zlib support is not available.
 */
$wgCompressRevisions = false;

/**
 * This is the list of preferred extensions for uploading files. Uploading files
 * with extensions not in this list will trigger a warning.
 */
$wgFileExtensions = array( 'png', 'gif', 'jpg', 'jpeg' );

/** Files with these extensions will never be allowed as uploads. */
$wgFileBlacklist = array(
	# HTML may contain cookie-stealing JavaScript and web bugs
	'html', 'htm', 'js', 'jsb',
	# PHP scripts may execute arbitrary code on the server
	'php', 'phtml', 'php3', 'php4', 'phps',
	# Other types that may be interpreted by some servers
	'shtml', 'jhtml', 'pl', 'py', 'cgi',
	# May contain harmful executables for Windows victims
	'exe', 'scr', 'dll', 'msi', 'vbs', 'bat', 'com', 'pif', 'cmd', 'vxd', 'cpl' );

/** Files with these mime types will never be allowed as uploads
 * if $wgVerifyMimeType is enabled.
 */
$wgMimeTypeBlacklist= array(
	# HTML may contain cookie-stealing JavaScript and web bugs
	'text/html', 'text/javascript', 'text/x-javascript',  'application/x-shellscript',
	# PHP scripts may execute arbitrary code on the server
	'application/x-php', 'text/x-php',
	# Other types that may be interpreted by some servers
	'text/x-python', 'text/x-perl', 'text/x-bash', 'text/x-sh', 'text/x-csh',
	# Windows metafile, client-side vulnerability on some systems
	'application/x-msmetafile'
);

/** This is a flag to determine whether or not to check file extensions on upload. */
$wgCheckFileExtensions = true;

/**
 * If this is turned off, users may override the warning for files not covered
 * by $wgFileExtensions.
 */
$wgStrictFileExtensions = true;

/** Warn if uploaded files are larger than this */
$wgUploadSizeWarning = 150 * 1024;

/** For compatibility with old installations set to false */
$wgPasswordSalt = true;

/** Which namespaces should support subpages?
 * See Language.php for a list of namespaces.
 */
$wgNamespacesWithSubpages = array(
	NS_TALK           => true,
 	NS_USER           => true,
 	NS_USER_TALK      => true,
 	NS_PROJECT_TALK   => true,
 	NS_IMAGE_TALK     => true,
 	NS_MEDIAWIKI_TALK => true,
 	NS_TEMPLATE_TALK  => true,
 	NS_HELP_TALK      => true,
 	NS_CATEGORY_TALK  => true
 );

$wgNamespacesToBeSearchedDefault = array(
	NS_MAIN           => true,
);

/** If set, a bold ugly notice will show up at the top of every page. */
$wgSiteNotice = '';


#
# Images settings
#

/** dynamic server side image resizing ("Thumbnails") */
$wgUseImageResize		= false;

/**
 * Resizing can be done using PHP's internal image libraries or using
 * ImageMagick or another third-party converter, e.g. GraphicMagick.
 * These support more file formats than PHP, which only supports PNG,
 * GIF, JPG, XBM and WBMP.
 *
 * Use Image Magick instead of PHP builtin functions.
 */
$wgUseImageMagick		= false;
/** The convert command shipped with ImageMagick */
$wgImageMagickConvertCommand    = '/usr/bin/convert';

/**
 * Use another resizing converter, e.g. GraphicMagick
 * %s will be replaced with the source path, %d with the destination
 * %w and %h will be replaced with the width and height
 *
 * An example is provided for GraphicMagick
 * Leave as false to skip this
 */
#$wgCustomConvertCommand = "gm convert %s -resize %wx%h %d"
$wgCustomConvertCommand = false;

# Scalable Vector Graphics (SVG) may be uploaded as images.
# Since SVG support is not yet standard in browsers, it is
# necessary to rasterize SVGs to PNG as a fallback format.
#
# An external program is required to perform this conversion:
$wgSVGConverters = array(
	'ImageMagick' => '$path/convert -background white -geometry $width $input $output',
	'sodipodi' => '$path/sodipodi -z -w $width -f $input -e $output',
	'inkscape' => '$path/inkscape -z -w $width -f $input -e $output',
	'batik' => 'java -Djava.awt.headless=true -jar $path/batik-rasterizer.jar -w $width -d $output $input',
	'rsvg' => '$path/rsvg -w$width -h$height $input $output',
	);
/** Pick one of the above */
$wgSVGConverter = 'ImageMagick';
/** If not in the executable PATH, specify */
$wgSVGConverterPath = '';
/** Don't scale a SVG larger than this */
$wgSVGMaxSize = 1024;
/**
 * Don't thumbnail an image if it will use too much working memory
 * Default is 50 MB if decompressed to RGBA form, which corresponds to
 * 12.5 million pixels or 3500x3500
 */
$wgMaxImageArea = 1.25e7;
/**
 * If rendered thumbnail files are older than this timestamp, they
 * will be rerendered on demand as if the file didn't already exist.
 * Update if there is some need to force thumbs and SVG rasterizations
 * to rerender, such as fixes to rendering bugs.
 */
$wgThumbnailEpoch = '20030516000000';



/** Set $wgCommandLineMode if it's not set already, to avoid notices */
if( !isset( $wgCommandLineMode ) ) {
	$wgCommandLineMode = false;
}


#
# Recent changes settings
#

/** Log IP addresses in the recentchanges table */
$wgPutIPinRC = true;

/**
 * Recentchanges items are periodically purged; entries older than this many
 * seconds will go.
 * For one week : 7 * 24 * 3600
 */
$wgRCMaxAge = 7 * 24 * 3600;


# Send RC updates via UDP
$wgRC2UDPAddress = false;
$wgRC2UDPPort = false;
$wgRC2UDPPrefix = '';

#
# Copyright and credits settings
#

/** RDF metadata toggles */
$wgEnableDublinCoreRdf = false;
$wgEnableCreativeCommonsRdf = false;

/** Override for copyright metadata.
 * TODO: these options need documentation
 */
$wgRightsPage = NULL;
$wgRightsUrl = NULL;
$wgRightsText = NULL;
$wgRightsIcon = NULL;

/** Set this to some HTML to override the rights icon with an arbitrary logo */
$wgCopyrightIcon = NULL;

/** Set this to true if you want detailed copyright information forms on Upload. */
$wgUseCopyrightUpload = false;

/** Set this to false if you want to disable checking that detailed copyright
 * information values are not empty. */
$wgCheckCopyrightUpload = true;

/**
 * Set this to the number of authors that you want to be credited below an
 * article text. Set it to zero to hide the attribution block, and a negative
 * number (like -1) to show all authors. Note that this will require 2-3 extra
 * database hits, which can have a not insignificant impact on performance for
 * large wikis.
 */
$wgMaxCredits = 0;

/** If there are more than $wgMaxCredits authors, show $wgMaxCredits of them.
 * Otherwise, link to a separate credits page. */
$wgShowCreditsIfMax = true;



/**
 * Set this to false to avoid forcing the first letter of links to capitals.
 * WARNING: may break links! This makes links COMPLETELY case-sensitive. Links
 * appearing with a capital at the beginning of a sentence will *not* go to the
 * same place as links in the middle of a sentence using a lowercase initial.
 */
$wgCapitalLinks = true;

/**
 * List of interwiki prefixes for wikis we'll accept as sources for
 * Special:Import (for sysops). Since complete page history can be imported,
 * these should be 'trusted'.
 *
 * If a user has the 'import' permission but not the 'importupload' permission,
 * they will only be able to run imports through this transwiki interface.
 */
$wgImportSources = array();

/**
 * If set to false, disables the full-history option on Special:Export.
 * This is currently poorly optimized for long edit histories, so is
 * disabled on Wikimedia's sites.
 */
$wgExportAllowHistory = true;
$wgExportAllowListContributors = false ;


/** Text matching this regular expression will be recognised as spam
 * See http://en.wikipedia.org/wiki/Regular_expression */
$wgSpamRegex = false;
/** Similarly if this function returns true */
$wgFilterCallback = false;

/** Go button goes straight to the edit screen if the article doesn't exist. */
$wgGoToEdit = false;

/** Allow limited user-specified HTML in wiki pages?
 * It  will be run through a whitelist for security. Set this to false if you
 * want wiki pages to consist only of wiki markup. Note that replacements do not
 * yet exist for all HTML constructs.*/
$wgUserHtml = true;

/** Allow raw, unchecked HTML in <html>...</html> sections.
 * THIS IS VERY DANGEROUS on a publically editable site, so USE wgGroupPermissions
 * TO RESTRICT EDITING to only those that you trust
 */
$wgRawHtml = false;

/**
 * $wgUseTidy: use tidy to make sure HTML output is sane.
 * This should only be enabled if $wgUserHtml is true.
 * tidy is a free tool that fixes broken HTML.
 * See http://www.w3.org/People/Raggett/tidy/
 * $wgTidyBin should be set to the path of the binary and
 * $wgTidyConf to the path of the configuration file.
 * $wgTidyOpts can include any number of parameters.
 *
 * $wgTidyInternal controls the use of the PECL extension to use an in-
 *   process tidy library instead of spawning a separate program.
 *   Normally you shouldn't need to override the setting except for
 *   debugging. To install, use 'pear install tidy' and add a line
 *   'extension=tidy.so' to php.ini.
 */
$wgUseTidy = false;
$wgAlwaysUseTidy = false;
$wgTidyBin = 'tidy';
$wgTidyConf = $IP.'/extensions/tidy/tidy.conf';
$wgTidyOpts = '';
$wgTidyInternal = function_exists( 'tidy_load_config' );

/** See list of skins and their symbolic names in languages/Language.php */
$wgDefaultSkin = 'monobook';

/**
 * Settings added to this array will override the language globals for the user
 * preferences used by anonymous visitors and newly created accounts. (See names
 * and sample values in languages/Language.php)
 * For instance, to disable section editing links:
 *  $wgDefaultUserOptions ['editsection'] = 0;
 *
 */
$wgDefaultUserOptions = array();

/** Whether or not to allow and use real name fields. Defaults to true. */
$wgAllowRealName = true;

/** Use XML parser? */
$wgUseXMLparser = false ;

/** Extensions */
$wgSkinExtensionFunctions = array();
$wgExtensionFunctions = array();
/**
 * An array of extension types and inside that their names, versions, authors
 * and urls, note that the version and url key can be omitted.
 *
 * <code>
 * $wgExtensionCredits[$type][] = array(
 * 	'name' => 'Example extension',
 *      'version' => 1.9,
 *	'author' => 'Foo Barstein',
 *	'url' => 'http://wwww.example.com/Example%20Extension/',
 * );
 * </code>
 *
 * Where $type is 'specialpage', 'parserhook', or 'other'.
 */
$wgExtensionCredits = array();

/**
 * Allow user Javascript page?
 * This enables a lot of neat customizations, but may
 * increase security risk to users and server load.
 */
$wgAllowUserJs = false;

/**
 * Allow user Cascading Style Sheets (CSS)?
 * This enables a lot of neat customizations, but may
 * increase security risk to users and server load.
 */
$wgAllowUserCss = false;

/** Use the site's Javascript page? */
$wgUseSiteJs = true;

/** Use the site's Cascading Style Sheets (CSS)? */
$wgUseSiteCss = true;

/** Filter for Special:Randompage. Part of a WHERE clause */
$wgExtraRandompageSQL = false;

/** Allow the "info" action, very inefficient at the moment */
$wgAllowPageInfo = false;

/** Maximum indent level of toc. */
$wgMaxTocLevel = 999;

/** Name of the external diff engine to use */
$wgExternalDiffEngine = false;

/** Use RC Patrolling to check for vandalism */
$wgUseRCPatrol = true;

/** Set maximum number of results to return in syndication feeds (RSS, Atom) for
 * eg Recentchanges, Newpages. */
$wgFeedLimit = 50;

/** _Minimum_ timeout for cached Recentchanges feed, in seconds.
 * A cached version will continue to be served out even if changes
 * are made, until this many seconds runs out since the last render.
 *
 * If set to 0, feed caching is disabled. Use this for debugging only;
 * feed generation can be pretty slow with diffs.
 */
$wgFeedCacheTimeout = 60;

/** When generating Recentchanges RSS/Atom feed, diffs will not be generated for
 * pages larger than this size. */
$wgFeedDiffCutoff = 32768;


/**
 * Additional namespaces. If the namespaces defined in Language.php and
 * Namespace.php are insufficient, you can create new ones here, for example,
 * to import Help files in other languages.
 * PLEASE  NOTE: Once you delete a namespace, the pages in that namespace will
 * no longer be accessible. If you rename it, then you can access them through
 * the new namespace name.
 *
 * Custom namespaces should start at 100 to avoid conflicting with standard
 * namespaces, and should always follow the even/odd main/talk pattern.
 */
#$wgExtraNamespaces =
#	array(100 => "Hilfe",
#	      101 => "Hilfe_Diskussion",
#	      102 => "Aide",
#	      103 => "Discussion_Aide"
#	      );
$wgExtraNamespaces = NULL;

/**
 * Limit images on image description pages to a user-selectable limit. In order
 * to reduce disk usage, limits can only be selected from a list. This is the
 * list of settings the user can choose from:
 */
$wgImageLimits = array (
	array(320,240),
	array(640,480),
	array(800,600),
	array(1024,768),
	array(1280,1024),
	array(10000,10000) );

/**
 * Adjust thumbnails on image pages according to a user setting. In order to
 * reduce disk usage, the values can only be selected from a list. This is the
 * list of settings the user can choose from:
 */
$wgThumbLimits = array(
	120,
	150,
	180,
	200,
	250,
	300
);

/**
 *  On  category pages, show thumbnail gallery for images belonging to that
 * category instead of listing them as articles.
 */
$wgCategoryMagicGallery = true;

/**
 * Paging limit for categories
 */
$wgCategoryPagingLimit = 200;

/**
 * Browser Blacklist for unicode non compliant browsers
 * Contains a list of regexps : "/regexp/"  matching problematic browsers
 */
$wgBrowserBlackList = array(
	"/Mozilla\/4\.78 \[en\] \(X11; U; Linux/",
	/**
	 * MSIE on Mac OS 9 is teh sux0r, converts ?? to <thorn>, ?? to <eth>, ?? to <THORN> and ?? to <ETH>
	 *
	 * Known useragents:
	 * - Mozilla/4.0 (compatible; MSIE 5.0; Mac_PowerPC)
	 * - Mozilla/4.0 (compatible; MSIE 5.15; Mac_PowerPC)
	 * - Mozilla/4.0 (compatible; MSIE 5.23; Mac_PowerPC)
	 * - [...]
	 *
	 * @link http://en.wikipedia.org/w/index.php?title=User%3A%C6var_Arnfj%F6r%F0_Bjarmason%2Ftestme&diff=12356041&oldid=12355864
	 * @link http://en.wikipedia.org/wiki/Template%3AOS9
	 */
	"/Mozilla\/4\.0 \(compatible; MSIE \d+\.\d+; Mac_PowerPC\)/"
);

/**
 * Fake out the timezone that the server thinks it's in. This will be used for
 * date display and not for what's stored in the DB. Leave to null to retain
 * your server's OS-based timezone value. This is the same as the timezone.
 *
 * This variable is currently used ONLY for signature formatting, not for
 * anything else.
 */
# $wgLocaltimezone = 'GMT';
# $wgLocaltimezone = 'PST8PDT';
# $wgLocaltimezone = 'Europe/Sweden';
# $wgLocaltimezone = 'CET';
$wgLocaltimezone = null;

/**
 * Set an offset from UTC in hours to use for the default timezone setting
 * for anonymous users and new user accounts.
 *
 * This setting is used for most date/time displays in the software, and is
 * overrideable in user preferences. It is *not* used for signature timestamps.
 *
 * You can set it to match the configured server timezone like this:
 *   $wgLocalTZoffset = date("Z") / 3600;
 *
 * If your server is not configured for the timezone you want, you can set
 * this in conjunction with the signature timezone and override the TZ
 * environment variable like so:
 *   $wgLocaltimezone="Europe/Berlin";
 *   putenv("TZ=$wgLocaltimezone");
 *   $wgLocalTZoffset = date("Z") / 3600;
 *
 * Leave at NULL to show times in universal time (UTC/GMT).
 */
$wgLocalTZoffset = null;


/**
 * When translating messages with wfMsg(), it is not always clear what should be
 * considered UI messages and what shoud be content messages.
 *
 * For example, for regular wikipedia site like en, there should be only one
 * 'mainpage', therefore when getting the link of 'mainpage', we should treate
 * it as content of the site and call wfMsgForContent(), while for rendering the
 * text of the link, we call wfMsg(). The code in default behaves this way.
 * However, sites like common do offer different versions of 'mainpage' and the
 * like for different languages. This array provides a way to override the
 * default behavior. For example, to allow language specific mainpage and
 * community portal, set
 *
 * $wgForceUIMsgAsContentMsg = array( 'mainpage', 'portal-url' );
 */
$wgForceUIMsgAsContentMsg = array();


/**
 * Authentication plugin.
 */
$wgAuth = null;

/**
 * Global list of hooks.
 * Add a hook by doing:
 *     $wgHooks['event_name'][] = $function;
 * or:
 *     $wgHooks['event_name'][] = array($function, $data);
 * or:
 *     $wgHooks['event_name'][] = array($object, 'method');
 */
$wgHooks = array();

/**
 * Experimental preview feature to fetch rendered text
 * over an XMLHttpRequest from JavaScript instead of
 * forcing a submit and reload of the whole page.
 * Leave disabled unless you're testing it.
 */
$wgLivePreview = false;

/**
 * Disable the internal MySQL-based search, to allow it to be
 * implemented by an extension instead.
 */
$wgDisableInternalSearch = false;

/**
 * Set this to a URL to forward search requests to some external location.
 * If the URL includes '$1', this will be replaced with the URL-encoded
 * search term.
 *
 * For example, to forward to Google you'd have something like:
 * $wgSearchForwardUrl = 'http://www.google.com/search?q=$1' .
 *                       '&domains=http://example.com' .
 *                       '&sitesearch=http://example.com' .
 *                       '&ie=utf-8&oe=utf-8';
 */
$wgSearchForwardUrl = null;

/**
 * If true, external URL links in wiki text will be given the
 * rel="nofollow" attribute as a hint to search engines that
 * they should not be followed for ranking purposes as they
 * are user-supplied and thus subject to spamming.
 */
$wgNoFollowLinks = true;

/**
 * Specifies the minimal length of a user password. If set to
 * 0, empty passwords are allowed.
 */
$wgMinimalPasswordLength = 0;

/**
 * Activate external editor interface for files and pages
 * See http://meta.wikimedia.org/wiki/Help:External_editors
 */
$wgUseExternalEditor = true;

/** Whether or not to sort special pages in Special:Specialpages */

$wgSortSpecialPages = true;

/**
 * Specify the name of a skin that should not be presented in the
 * list of available skins.
 * Use for blacklisting a skin which you do not want to remove
 * from the .../skins/ directory
 */
$wgSkipSkin = '';
$wgSkipSkins = array(); # More of the same

/**
 * Array of disabled article actions, e.g. view, edit, dublincore, delete, etc.
 */
$wgDisabledActions = array();

/**
 * Disable redirects to special pages and interwiki redirects, which use a 302 and have no "redirected from" link
 */
$wgDisableHardRedirects = false;

/**
 * Use http.dnsbl.sorbs.net to check for open proxies
 */
$wgEnableSorbs = false;

/**
 * Use opm.blitzed.org to check for open proxies.
 * Not yet actually used.
 */
$wgEnableOpm = false;

/**
 * Proxy whitelist, list of addresses that are assumed to be non-proxy despite what the other
 * methods might say
 */
$wgProxyWhitelist = array();

/**
 * Simple rate limiter options to brake edit floods.
 * Maximum number actions allowed in the given number of seconds;
 * after that the violating client receives HTTP 500 error pages
 * until the period elapses.
 *
 * array( 4, 60 ) for a maximum of 4 hits in 60 seconds.
 *
 * This option set is experimental and likely to change.
 * Requires memcached.
 */
$wgRateLimits = array(
	'edit' => array(
		'anon'   => null, // for any and all anonymous edits (aggregate)
		'user'   => null, // for each logged-in user
		'newbie' => null, // for each recent account; overrides 'user'
		'ip'     => null, // for each anon and recent account
		'subnet' => null, // ... with final octet removed
		),
	'move' => array(
		'user'   => null,
		'newbie' => null,
		'ip'     => null,
		'subnet' => null,
		),
	);

/**
 * Set to a filename to log rate limiter hits.
 */
$wgRateLimitLog = null;

/**
 * On Special:Unusedimages, consider images "used", if they are put
 * into a category. Default (false) is not to count those as used.
 */
$wgCountCategorizedImagesAsUsed = false;

/**
 * External stores allow including content
 * from non database sources following URL links
 *
 * Short names of ExternalStore classes may be specified in an array here:
 * $wgExternalStores = array("http","file","custom")...
 *
 * CAUTION: Access to database might lead to code execution
 */
$wgExternalStores = false;

/**
 * An array of external mysql servers, e.g.
 * $wgExternalServers = array( 'cluster1' => array( 'srv28', 'srv29', 'srv30' ) );
 */
$wgExternalServers = array();

/**
 * The place to put new revisions, false to put them in the local text table.
 * Part of a URL, e.g. DB://cluster1
 *
 * Can be an array instead of a single string, to enable data distribution. Keys 
 * must be consecutive integers, starting at zero. Example:
 *
 * $wgDefaultExternalStore = array( 'DB://cluster1', 'DB://cluster2' );
 *
 */
$wgDefaultExternalStore = false;

/**
* list of trusted media-types and mime types.
* Use the MEDIATYPE_xxx constants to represent media types.
* This list is used by Image::isSafeFile
*
* Types not listed here will have a warning about unsafe content
* displayed on the images description page. It would also be possible
* to use this for further restrictions, like disabling direct
* [[media:...]] links for non-trusted formats.
*/
$wgTrustedMediaFormats= array(
	MEDIATYPE_BITMAP, //all bitmap formats
	MEDIATYPE_AUDIO,  //all audio formats
	MEDIATYPE_VIDEO,  //all plain video formats
	"image/svg",  //svg (only needed if inline rendering of svg is not supported)
	"application/pdf",  //PDF files
	#"application/x-shockwafe-flash", //flash/shockwave movie
);

/**
 * Allow special page inclusions such as {{Special:Allpages}}
 */
$wgAllowSpecialInclusion = true;

/**
 * Timeout for HTTP requests done via CURL
 */
$wgHTTPTimeout = 3;

/**
 * Proxy to use for CURL requests.
 */
$wgHTTPProxy = false;

/**
 * Enable interwiki transcluding.  Only when iw_trans=1.
 */
$wgEnableScaryTranscluding = false;
/**
 * Expiry time for interwiki transclusion
 */
$wgTranscludeCacheExpiry = 3600;

/**
 * Support blog-style "trackbacks" for articles.  See
 * http://www.sixapart.com/pronet/docs/trackback_spec for details.
 */
$wgUseTrackbacks = false;

/**
 * Enable filtering of robots in Special:Watchlist
 */

$wgFilterRobotsWL = false;

/**
 * Enable filtering of categories in Recentchanges
 */
$wgAllowCategorizedRecentChanges = false ;

/**
 * Number of jobs to perform per request. May be less than one in which case
 * jobs are performed probabalistically. If this is zero, jobs will not be done
 * during ordinary apache requests. In this case, maintenance/doJobs.php should
 * be run periodically.
 */
$wgJobRunRate = 1;

/**
 * Log file for job execution
 */
$wgJobLogFile = false;

/**
 * Enable use of AJAX features, currently auto suggestion for the search bar
 */
$wgUseAjax = false;

/**
 * List of Ajax-callable functions
 */
$wgAjaxExportList = array( 'wfSajaxSearch' );


?>
