<?php
/*  Poweradmin, a friendly web-based admin tool for PowerDNS.
 *  See <http://www.poweradmin.org> for more details.
 *
 *  Copyright 2007-2009  Rejo Zenger <rejo@zenger.nl>
 *  Copyright 2010-2014  Poweradmin Development Team
 *      <http://www.poweradmin.org/credits.html>
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *  Toolkit functions
 *
 * @package     Poweradmin
 * @copyright   2007-2010 Rejo Zenger <rejo@zenger.nl>
 * @copyright   2010-2014 Poweradmin Development Team
 * @license     http://opensource.org/licenses/GPL-3.0 GPL
 */
// TODO: display elapsed time and memory consumption,
// used to check improvements in refactored version
$display_stats = false;
if ($display_stats)
    include('inc/benchmark.php');

ob_start();

require_once("error.inc.php");

if (!function_exists('session_start'))
    die(error('You have to install PHP session extension!'));
if (!function_exists('_'))
    die(error('You have to install PHP gettext extension!'));
if (!function_exists('mcrypt_encrypt'))
    die(error('You have to install PHP mcrypt extension!'));

session_start();

include_once("config-me.inc.php");

if (!@include_once("config.inc.php")) {
    error(_('You have to create a config.inc.php!'));
}

/* * ***********
 * Constants *
 * *********** */

if (isset($_GET["start"])) {
    define('ROWSTART', (($_GET["start"] - 1) * $iface_rowamount));
} else {
    /** Starting row
     */
    define('ROWSTART', 0);
}

if (isset($_GET["letter"])) {
    define('LETTERSTART', $_GET["letter"]);
    $_SESSION["letter"] = $_GET["letter"];
} elseif (isset($_SESSION["letter"])) {
    define('LETTERSTART', $_SESSION["letter"]);
} else {
    /** Starting letter
     */
    define('LETTERSTART', "a");
}

if (isset($_GET["zone_sort_by"]) && preg_match("/^[a-z_]+$/", $_GET["zone_sort_by"])) {
    define('ZONE_SORT_BY', $_GET["zone_sort_by"]);
    $_SESSION["zone_sort_by"] = $_GET["zone_sort_by"];
} elseif (isset($_POST["zone_sort_by"]) && preg_match("/^[a-z_]+$/", $_POST["zone_sort_by"])) {
    define('ZONE_SORT_BY', $_POST["zone_sort_by"]);
    $_SESSION["zone_sort_by"] = $_POST["zone_sort_by"];
} elseif (isset($_SESSION["zone_sort_by"])) {
    define('ZONE_SORT_BY', $_SESSION["zone_sort_by"]);
} else {
    /** Field to sort zone by
     */
    define('ZONE_SORT_BY', "name");
}

if (isset($_SESSION["userlang"])) {
    $iface_lang = $_SESSION["userlang"];
}

if (isset($_GET["record_sort_by"]) && preg_match("/^[a-z_]+$/", $_GET["record_sort_by"])) {
    define('RECORD_SORT_BY', $_GET["record_sort_by"]);
    $_SESSION["record_sort_by"] = $_GET["record_sort_by"];
} elseif (isset($_POST["record_sort_by"]) && preg_match("/^[a-z_]+$/", $_POST["record_sort_by"])) {
    define('RECORD_SORT_BY', $_POST["record_sort_by"]);
    $_SESSION["record_sort_by"] = $_POST["record_sort_by"];
} elseif (isset($_SESSION["record_sort_by"])) {
    define('RECORD_SORT_BY', $_SESSION["record_sort_by"]);
} else {
    /** Record to sort zone by
     */
    define('RECORD_SORT_BY', "name");
}

// Updated on 2015092200 - 1061 TLDs
// http://data.iana.org/TLD/tlds-alpha-by-domain.txt
$valid_tlds = array('aaa', 'abb', 'abbott', 'abogado', 'ac', 'academy', 'accenture', 'accountant', 'accountants',
'aco', 'active', 'actor', 'ad', 'ads', 'adult', 'ae', 'aeg', 'aero', 'af', 'afl', 'ag', 'agency', 'ai', 'aig',
'airforce', 'airtel', 'al', 'allfinanz', 'alsace', 'am', 'amica', 'amsterdam', 'android', 'ao', 'apartments',
'app', 'aq', 'aquarelle', 'ar', 'archi', 'army', 'arpa', 'as', 'asia', 'associates', 'at', 'attorney', 'au',
'auction', 'audio', 'auto', 'autos', 'aw', 'ax', 'axa', 'az', 'azure', 'ba', 'band', 'bank', 'bar', 'barcelona',
'barclaycard', 'barclays', 'bargains', 'bauhaus', 'bayern', 'bb', 'bbc', 'bbva', 'bcn', 'bd', 'be', 'beer',
'bentley', 'berlin', 'best', 'bet', 'bf', 'bg', 'bh', 'bharti', 'bi', 'bible', 'bid', 'bike', 'bing', 'bingo',
'bio', 'biz', 'bj', 'black', 'blackfriday', 'bloomberg', 'blue', 'bm', 'bmw', 'bn', 'bnl', 'bnpparibas', 'bo',
'boats', 'bond', 'boo', 'boots', 'boutique', 'br', 'bradesco', 'bridgestone', 'broker', 'brother', 'brussels',
'bs', 'bt', 'budapest', 'build', 'builders', 'business', 'buzz', 'bv', 'bw', 'by', 'bz', 'bzh', 'ca', 'cab',
'cafe', 'cal', 'camera', 'camp', 'cancerresearch', 'canon', 'capetown', 'capital', 'car', 'caravan', 'cards',
'care', 'career', 'careers', 'cars', 'cartier', 'casa', 'cash', 'casino', 'cat', 'catering', 'cba', 'cbn', 'cc',
'cd', 'ceb', 'center', 'ceo', 'cern', 'cf', 'cfa', 'cfd', 'cg', 'ch', 'chanel', 'channel', 'chat', 'cheap',
'chloe', 'christmas', 'chrome', 'church', 'ci', 'cisco', 'citic', 'city', 'ck', 'cl', 'claims', 'cleaning',
'click', 'clinic', 'clothing', 'cloud', 'club', 'cm', 'cn', 'co', 'coach', 'codes', 'coffee', 'college',
'cologne', 'com', 'commbank', 'community', 'company', 'computer', 'condos', 'construction', 'consulting',
'contractors', 'cooking', 'cool', 'coop', 'corsica', 'country', 'coupons', 'courses', 'cr', 'credit',
'creditcard', 'cricket', 'crown', 'crs', 'cruises', 'csc', 'cu', 'cuisinella', 'cv', 'cw', 'cx', 'cy',
'cymru', 'cyou', 'cz', 'dabur', 'dad', 'dance', 'date', 'dating', 'datsun', 'day', 'dclk', 'de', 'deals',
'degree', 'delivery', 'delta', 'democrat', 'dental', 'dentist', 'desi', 'design', 'dev', 'diamonds', 'diet',
'digital', 'direct', 'directory', 'discount', 'dj', 'dk', 'dm', 'dnp', 'do', 'docs', 'dog', 'doha', 'domains',
'doosan', 'download', 'drive', 'durban', 'dvag', 'dz', 'earth', 'eat', 'ec', 'edu', 'education', 'ee', 'eg',
'email', 'emerck', 'energy', 'engineer', 'engineering', 'enterprises', 'epson', 'equipment', 'er', 'erni',
'es', 'esq', 'estate', 'et', 'eu', 'eurovision', 'eus', 'events', 'everbank', 'exchange', 'expert', 'exposed',
'express', 'fage', 'fail', 'faith', 'family', 'fan', 'fans', 'farm', 'fashion', 'feedback', 'fi', 'film',
'finance', 'financial', 'firmdale', 'fish', 'fishing', 'fit', 'fitness', 'fj', 'fk', 'flights', 'florist',
'flowers', 'flsmidth', 'fly', 'fm', 'fo', 'foo', 'football', 'forex', 'forsale', 'forum', 'foundation', 'fr',
'frl', 'frogans', 'fund', 'furniture', 'futbol', 'fyi', 'ga', 'gal', 'gallery', 'game', 'garden', 'gb', 'gbiz',
'gd', 'gdn', 'ge', 'gea', 'gent', 'genting', 'gf', 'gg', 'ggee', 'gh', 'gi', 'gift', 'gifts', 'gives', 'giving',
'gl', 'glass', 'gle', 'global', 'globo', 'gm', 'gmail', 'gmo', 'gmx', 'gn', 'gold', 'goldpoint', 'golf', 'goo',
'goog', 'google', 'gop', 'gov', 'gp', 'gq', 'gr', 'graphics', 'gratis', 'green', 'gripe', 'group', 'gs', 'gt',
'gu', 'guge', 'guide', 'guitars', 'guru', 'gw', 'gy', 'hamburg', 'hangout', 'haus', 'healthcare', 'help', 'here',
'hermes', 'hiphop', 'hitachi', 'hiv', 'hk', 'hm', 'hn', 'hockey', 'holdings', 'holiday', 'homedepot', 'homes',
'honda', 'horse', 'host', 'hosting', 'hoteles', 'hotmail', 'house', 'how', 'hr', 'hsbc', 'ht', 'hu', 'ibm',
'icbc', 'ice', 'icu', 'id', 'ie', 'ifm', 'iinet', 'il', 'im', 'immo', 'immobilien', 'in', 'industries',
'infiniti', 'info', 'ing', 'ink', 'institute', 'insure', 'int', 'international', 'investments', 'io',
'ipiranga', 'iq', 'ir', 'irish', 'is', 'ist', 'istanbul', 'it', 'itau', 'iwc', 'java', 'jcb', 'je',
'jetzt', 'jewelry', 'jlc', 'jll', 'jm', 'jo', 'jobs', 'joburg', 'jp', 'jprs', 'juegos', 'kaufen', 'kddi',
'ke', 'kg', 'kh', 'ki', 'kim', 'kitchen', 'kiwi', 'km', 'kn', 'koeln', 'komatsu', 'kp', 'kr', 'krd', 'kred',
'kw', 'ky', 'kyoto', 'kz', 'la', 'lacaixa', 'lancaster', 'land', 'lasalle', 'lat', 'latrobe', 'law', 'lawyer',
'lb', 'lc', 'lds', 'lease', 'leclerc', 'legal', 'lexus', 'lgbt', 'li', 'liaison', 'lidl', 'life', 'lighting',
'limited', 'limo', 'linde', 'link', 'live', 'lixil', 'lk', 'loan', 'loans', 'lol', 'london', 'lotte', 'lotto',
'love', 'lr', 'ls', 'lt', 'ltda', 'lu', 'lupin', 'luxe', 'luxury', 'lv', 'ly', 'ma', 'madrid', 'maif', 'maison',
'man', 'management', 'mango', 'market', 'marketing', 'markets', 'marriott', 'mba', 'mc', 'md', 'me', 'media',
'meet', 'melbourne', 'meme', 'memorial', 'men', 'menu', 'mg', 'mh', 'miami', 'microsoft', 'mil', 'mini', 'mk',
'ml', 'mm', 'mma', 'mn', 'mo', 'mobi', 'moda', 'moe', 'mom', 'monash', 'money', 'montblanc', 'mormon', 'mortgage',
'moscow', 'motorcycles', 'mov', 'movie', 'movistar', 'mp', 'mq', 'mr', 'ms', 'mt', 'mtn', 'mtpc', 'mu', 'museum',
'mv', 'mw', 'mx', 'my', 'mz', 'na', 'nadex', 'nagoya', 'name', 'navy', 'nc', 'ne', 'nec', 'net', 'netbank',
'network', 'neustar', 'new', 'news', 'nexus', 'nf', 'ng', 'ngo', 'nhk', 'ni', 'nico', 'ninja', 'nissan', 'nl',
'no', 'nokia', 'np', 'nr', 'nra', 'nrw', 'ntt', 'nu', 'nyc', 'nz', 'office', 'okinawa', 'om', 'omega', 'one',
'ong', 'onl', 'online', 'ooo', 'oracle', 'orange', 'org', 'organic', 'osaka', 'otsuka', 'ovh', 'pa', 'page',
'panerai', 'paris', 'partners', 'parts', 'party', 'pe', 'pet', 'pf', 'pg', 'ph', 'pharmacy', 'philips', 'photo',
'photography', 'photos', 'physio', 'piaget', 'pics', 'pictet', 'pictures', 'pink', 'pizza', 'pk', 'pl', 'place',
'play', 'plumbing', 'plus', 'pm', 'pn', 'pohl', 'poker', 'porn', 'post', 'pr', 'praxi', 'press', 'pro', 'prod',
'productions', 'prof', 'properties', 'property', 'protection', 'ps', 'pt', 'pub', 'pw', 'py', 'qa', 'qpon',
'quebec', 'racing', 're', 'realtor', 'realty', 'recipes', 'red', 'redstone', 'rehab', 'reise', 'reisen', 'reit',
'ren', 'rent', 'rentals', 'repair', 'report', 'republican', 'rest', 'restaurant', 'review', 'reviews', 'rich',
'ricoh', 'rio', 'rip', 'ro', 'rocks', 'rodeo', 'rs', 'rsvp', 'ru', 'ruhr', 'run', 'rw', 'ryukyu', 'sa', 'saarland',
'sakura', 'sale', 'samsung', 'sandvik', 'sandvikcoromant', 'sanofi', 'sap', 'sarl', 'saxo', 'sb', 'sc', 'sca',
'scb', 'schmidt', 'scholarships', 'school', 'schule', 'schwarz', 'science', 'scor', 'scot', 'sd', 'se', 'seat',
'security', 'seek', 'sener', 'services', 'sew', 'sex', 'sexy', 'sg', 'sh', 'shiksha', 'shoes', 'show', 'shriram',
'si', 'singles', 'site', 'sj', 'sk', 'ski', 'sky', 'skype', 'sl', 'sm', 'sn', 'sncf', 'so', 'soccer', 'social',
'software', 'sohu', 'solar', 'solutions', 'sony', 'soy', 'space', 'spiegel', 'spreadbetting', 'sr', 'srl', 'st',
'stada', 'starhub', 'statoil', 'stc', 'stcgroup', 'studio', 'study', 'style', 'su', 'sucks', 'supplies', 'supply',
'support', 'surf', 'surgery', 'suzuki', 'sv', 'swatch', 'swiss', 'sx', 'sy', 'sydney', 'systems', 'sz', 'taipei',
'tatamotors', 'tatar', 'tattoo', 'tax', 'taxi', 'tc', 'td', 'team', 'tech', 'technology', 'tel', 'telefonica',
'temasek', 'tennis', 'tf', 'tg', 'th', 'thd', 'theater', 'theatre', 'tickets', 'tienda', 'tips', 'tires', 'tirol',
'tj', 'tk', 'tl', 'tm', 'tn', 'to', 'today', 'tokyo', 'tools', 'top', 'toray', 'toshiba', 'tours', 'town', 'toyota',
'toys', 'tr', 'trade', 'trading', 'training', 'travel', 'trust', 'tt', 'tui', 'tv', 'tw', 'tz', 'ua', 'ubs', 'ug',
'uk', 'university', 'uno', 'uol', 'us', 'uy', 'uz', 'va', 'vacations', 'vc', 've', 'vegas', 'ventures',
'versicherung', 'vet', 'vg', 'vi', 'viajes', 'video', 'villas', 'vin', 'vision', 'vista', 'vistaprint', 'viva',
'vlaanderen', 'vn', 'vodka', 'vote', 'voting', 'voto', 'voyage', 'vu', 'wales', 'walter', 'wang', 'watch',
'webcam', 'website', 'wed', 'wedding', 'weir', 'wf', 'whoswho', 'wien', 'wiki', 'williamhill', 'win', 'windows',
'wine', 'wme', 'work', 'works', 'world', 'ws', 'wtc', 'wtf', 'xbox', 'xerox', 'xin', 'xn--11b4c3d', 'xn--1qqw23a',
'xn--30rr7y', 'xn--3bst00m', 'xn--3ds443g', 'xn--3e0b707e', 'xn--3pxu8k', 'xn--42c2d9a', 'xn--45brj9c',
'xn--45q11c', 'xn--4gbrim', 'xn--55qw42g', 'xn--55qx5d', 'xn--6frz82g', 'xn--6qq986b3xl', 'xn--80adxhks',
'xn--80ao21a', 'xn--80asehdb', 'xn--80aswg', 'xn--90a3ac', 'xn--90ais', 'xn--9dbq2a', 'xn--9et52u',
'xn--b4w605ferd', 'xn--c1avg', 'xn--c2br7g', 'xn--cg4bki', 'xn--clchc0ea0b2g2a9gcd', 'xn--czr694b',
'xn--czrs0t', 'xn--czru2d', 'xn--d1acj3b', 'xn--d1alf', 'xn--efvy88h', 'xn--estv75g', 'xn--fhbei',
'xn--fiq228c5hs', 'xn--fiq64b', 'xn--fiqs8s', 'xn--fiqz9s', 'xn--fjq720a', 'xn--flw351e', 'xn--fpcrj9c3d',
'xn--fzc2c9e2c', 'xn--gecrj9c', 'xn--h2brj9c', 'xn--hxt814e', 'xn--i1b6b1a6a2e', 'xn--imr513n', 'xn--io0a7i',
'xn--j1aef', 'xn--j1amh', 'xn--j6w193g', 'xn--kcrx77d1x4a', 'xn--kprw13d', 'xn--kpry57d', 'xn--kput3i',
'xn--l1acc', 'xn--lgbbat1ad8j', 'xn--mgb9awbf', 'xn--mgba3a4f16a', 'xn--mgbaam7a8h', 'xn--mgbab2bd',
'xn--mgbayh7gpa', 'xn--mgbbh1a71e', 'xn--mgbc0a9azcg', 'xn--mgberp4a5d4ar', 'xn--mgbpl2fh', 'xn--mgbx4cd0ab',
'xn--mk1bu44c', 'xn--mxtq1m', 'xn--ngbc5azd', 'xn--node', 'xn--nqv7f', 'xn--nqv7fs00ema', 'xn--nyqy26a',
'xn--o3cw4h', 'xn--ogbpf8fl', 'xn--p1acf', 'xn--p1ai', 'xn--pgbs0dh', 'xn--pssy2u', 'xn--q9jyb4c',
'xn--qcka1pmc', 'xn--rhqv96g', 'xn--s9brj9c', 'xn--ses554g', 'xn--t60b56a', 'xn--tckwe', 'xn--unup4y',
'xn--vermgensberater-ctb', 'xn--vermgensberatung-pwb', 'xn--vhquv', 'xn--vuq861b', 'xn--wgbh1c',
'xn--wgbl6a', 'xn--xhq521b', 'xn--xkc2al3hye2a', 'xn--xkc2dl3a5ee0h', 'xn--y9a3aq', 'xn--yfro4i67o',
'xn--ygbi2ammx', 'xn--zfr164b', 'xperia', 'xxx', 'xyz', 'yachts', 'yandex', 'ye', 'yodobashi', 'yoga',
'yokohama', 'youtube', 'yt', 'za', 'zip', 'zm', 'zone', 'zuerich', 'zw');

// Special TLDs for testing and documentation purposes
// http://tools.ietf.org/html/rfc2606#section-2
array_push($valid_tlds, 'test', 'example', 'invalid', 'localhost');

/* Database connection */
require_once("database.inc.php");
// Generates $db variable to access database.
// Array of the available zone types
$server_types = array("MASTER", "SLAVE", "NATIVE");

// $rtypes - array of possible record types
$rtypes = array(
    'A',
    'AAAA',
    'AFSDB',
    'CERT',
    'CNAME',
    'DHCID',
    'DLV',
    'DNSKEY',
    'DS',
    'EUI48',
    'EUI64',
    'HINFO',
    'IPSECKEY',
    'KEY',
    'KX',
    'LOC',
    'MINFO',
    'MR',
    'MX',
    'NAPTR',
    'NS',
    'NSEC',
    'NSEC3',
    'NSEC3PARAM',
    'OPT',
    'PTR',
    'RKEY',
    'RP',
    'RRSIG',
    'SOA',
    'SPF',
    'SRV',
    'SSHFP',
    'TLSA',
    'TSIG',
    'TXT',
    'WKS',
);

// If fancy records is enabled, extend this field.
if ($dns_fancy) {
    $rtypes[] = 'URL';
    $rtypes[] = 'MBOXFW';
    $rtypes[] = 'CURL';
}


/* * ***********
 * Includes  *
 * *********** */
$db = dbConnect();
require_once("plugin.inc.php");

require_once("i18n.inc.php");
require_once("auth.inc.php");
require_once("users.inc.php");
require_once("dns.inc.php");
require_once("record.inc.php");
require_once("dnssec.inc.php");
require_once("templates.inc.php");

//do_hook('hook_post_includes');
do_hook('authenticate');


/* * ***********
 * Functions *
 * *********** */

/** Print paging menu
 *
 * Display the page option: [ < ][ 1 ] .. [ 8 ][ 9 ][ 10 ][ 11 ][ 12 ][ 13 ][ 14 ][ 15 ][ 16 ] .. [ 34 ][ > ]
 *
 * @param int $amount Total number of items
 * @param int $rowamount Per page number of items
 * @param int $id Page specific ID (Zone ID, Template ID, etc)
 *
 * @return null
 */
function show_pages($amount, $rowamount, $id = '') {
    if ($amount > $rowamount) {
        $num = 8;
        $poutput = '';
        $lastpage = ceil($amount / $rowamount);
        $startpage = 1;

        if (!isset($_GET["start"]))
            $_GET["start"] = 1;
        $start = $_GET["start"];

        if ($lastpage > $num & $start > ($num / 2)) {
            $startpage = ($start - ($num / 2));
        }

        echo _('Show page') . ":<br>";

        if ($lastpage > $num & $start > 1) {
            $poutput .= '<a href=" ' . htmlentities($_SERVER["PHP_SELF"], ENT_QUOTES);
            $poutput .= '?start=' . ($start - 1);
            if ($id != '')
                $poutput .= '&id=' . $id;
            $poutput .= '">';
            $poutput .= '[ < ]';
            $poutput .= '</a>';
        }
        if ($start != 1) {
            $poutput .= '<a href=" ' . htmlentities($_SERVER["PHP_SELF"], ENT_QUOTES);
            $poutput .= '?start=1';
            if ($id != '')
                $poutput .= '&id=' . $id;
            $poutput .= '">';
            $poutput .= '[ 1 ]';
            $poutput .= '</a>';
            if ($startpage > 2)
                $poutput .= ' .. ';
        }

        for ($i = $startpage; $i <= min(($startpage + $num), $lastpage); $i++) {
            if ($start == $i) {
                $poutput .= '[ <b>' . $i . '</b> ]';
            } elseif ($i != $lastpage & $i != 1) {
                $poutput .= '<a href=" ' . htmlentities($_SERVER["PHP_SELF"], ENT_QUOTES);
                $poutput .= '?start=' . $i;
                if ($id != '')
                    $poutput .= '&id=' . $id;
                $poutput .= '">';
                $poutput .= '[ ' . $i . ' ]';
                $poutput .= '</a>';
            }
        }

        if ($start != $lastpage) {
            if (min(($startpage + $num), $lastpage) < ($lastpage - 1))
                $poutput .= ' .. ';
            $poutput .= '<a href=" ' . htmlentities($_SERVER["PHP_SELF"], ENT_QUOTES);
            $poutput .= '?start=' . $lastpage;
            if ($id != '')
                $poutput .= '&id=' . $id;
            $poutput .= '">';
            $poutput .= '[ ' . $lastpage . ' ]';
            $poutput .= '</a>';
        }

        if ($lastpage > $num & $start < $lastpage) {
            $poutput .= '<a href=" ' . htmlentities($_SERVER["PHP_SELF"], ENT_QUOTES);
            $poutput .= '?start=' . ($start + 1);
            if ($id != '')
                $poutput .= '&id=' . $id;
            $poutput .= '">';
            $poutput .= '[ > ]';
            $poutput .= '</a>';
        }

        echo $poutput;
    }
}

/** Print alphanumeric paging menu
 *
 * Display the alphabetic option: [0-9] [a] [b] .. [z]
 *
 * @param string $letterstart Starting letter/number or 'all'
 * @param boolean $userid unknown usage
 *
 * @return null
 */
function show_letters($letterstart, $userid = true) {
    echo _('Show zones beginning with') . ":<br>";

    $letter = "[[:digit:]]";
    if ($letterstart == "1") {
        echo "<span class=\"lettertaken\">[ 0-9 ]</span> ";
    } elseif (zone_letter_start($letter, $userid)) {
        echo "<a href=\"" . htmlentities($_SERVER["PHP_SELF"], ENT_QUOTES) . "?letter=1\">[ 0-9 ]</a> ";
    } else {
        echo "[ <span class=\"letternotavailable\">0-9</span> ] ";
    }

    foreach (range('a', 'z') as $letter) {
        if ($letter == $letterstart) {
            echo "<span class=\"lettertaken\">[ " . $letter . " ]</span> ";
        } elseif (zone_letter_start($letter, $userid)) {
            echo "<a href=\"" . htmlentities($_SERVER["PHP_SELF"], ENT_QUOTES) . "?letter=" . $letter . "\">[ " . $letter . " ]</a> ";
        } else {
            echo "[ <span class=\"letternotavailable\">" . $letter . "</span> ] ";
        }
    }

    if ($letterstart == '_') {
        echo "<span class=\"lettertaken\">[ _ ]</span> ";
    } elseif (zone_letter_start('_', $userid)) {
        echo "<a href=\"" . htmlentities($_SERVER["PHP_SELF"], ENT_QUOTES) . "?letter=_\">[ _ ]</a> ";
    } else {
        echo "[ <span class=\"letternotavailable\">_</span> ] ";
    }

    if ($letterstart == 'all') {
        echo "<span class=\"lettertaken\">[ Show all ]</span>";
    } else {
        echo "<a href=\"" . htmlentities($_SERVER["PHP_SELF"], ENT_QUOTES) . "?letter=all\">[ Show all ]</a> ";
    }
}

/** Check if any zones start with letter
 *
 * @param string $letter Starting Letter
 * @param boolean $userid unknown usage
 *
 * @return int 1 if rows found, 0 otherwise
 */
function zone_letter_start($letter, $userid = true) {
    global $db;
    global $sql_regexp;
    $query = "SELECT
			domains.id AS domain_id,
			zones.owner,
			domains.name AS domainname
			FROM domains
			LEFT JOIN zones ON domains.id=zones.domain_id
			WHERE substring(domains.name,1,1) " . $sql_regexp . " " . $db->quote("^" . $letter, 'text');
    $db->setLimit(1);
    $result = $db->queryOne($query);
    return ($result ? 1 : 0);
}

/** Print success message (toolkit.inc)
 *
 * @param string $msg Success message
 *
 * @return null
 */
function success($msg) {
    if ($msg) {
        echo "     <div class=\"alert alert-success\">" . $msg . "</div>\n";
    } else {
        echo "     <div class=\"alert alert-success\">" . _('Something has been successfully performed. What exactly, however, will remain a mystery.') . "</div>\n";
    }
}

/** Print message
 *
 * Something has been done nicely, display a message and a back button.
 *
 * @param string $msg Message
 *
 * @return null
 */
function message($msg) {
    include_once("header.inc.php");
    ?>
    <P><TABLE CLASS="messagetable"><TR><TD CLASS="message"><H2><?php echo _('Success!'); ?></H2>
                <BR>
                <FONT STYLE="font-weight: Bold">
                <P>
                    <?php
                    if ($msg) {
                        echo nl2br($msg);
                    } else {
                        echo _('Successful!');
                    }
                    ?>
                </P>
                <BR>
                <P>
                    <a href="javascript:history.go(-1)">&lt;&lt; <?php echo _('back'); ?></a></FONT>
                </P>
            </TD></TR></TABLE></P>
    <?php
    include_once("footer.inc.php");
}

/** Send 302 Redirect with optional argument
 *
 * Reroute a user to a cleanpage of (if passed) arg
 *
 * @param string $arg argument string to add to url
 *
 * @return null
 */
function clean_page($arg = '') {
    if (!$arg) {
        header("Location: " . htmlentities($_SERVER['SCRIPT_NAME'], ENT_QUOTES) . "?time=" . time());
        exit;
    } else {
        if (preg_match('!\?!si', $arg)) {
            $add = "&time=";
        } else {
            $add = "?time=";
        }
        header("Location: $arg$add" . time());
        exit;
    }
}

/** Print active status
 *
 * @param int $res status, 0 for inactive, 1 active
 *
 * @return string html containing status
 */
function get_status($res) {
    if ($res == '0') {
        return "<FONT CLASS=\"inactive\">" . _('Inactive') . "</FONT>";
    } elseif ($res == '1') {
        return "<FONT CLASS=\"active\">" . _('Active') . "</FONT>";
    }
}

/** Validate email address string
 *
 * @param string $address email address string
 *
 * @return boolean true if valid, false otherwise
 */
function is_valid_email($address) {
    $fields = preg_split("/@/", $address, 2);
    if ((!preg_match("/^[0-9a-z]([-_.]?[0-9a-z])*$/i", $fields[0])) || (!isset($fields[1]) || $fields[1] == '' || !is_valid_hostname_fqdn($fields[1], 0))) {
        return false;
    }
    return true;
}

/** Validate numeric string
 *
 * @param string $string number
 *
 * @return boolean true if number, false otherwise
 */
function v_num($string) {
    if (!preg_match("/^[0-9]+$/i", $string)) {
        return false;
    } else {
        return true;
    }
}

/** Debug print
 *
 * @param string $var debug statement
 *
 * @return null
 */
function debug_print($var) {
    echo "<pre style=\"border: 2px solid blue;\">\n";
    if (is_array($var)) {
        print_r($var);
    } else {
        echo $var;
    }
    echo "</pre>\n";
}


/** Generate random salt for encryption
 *
 * @param int $len salt length (default=5)
 *
 * @return string salt string
 */
function generate_salt($len = 5) {
    $valid_characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890@#$%^*()_-!';
    $valid_len = strlen($valid_characters) - 1;
    $salt = "";

    for ($i = 0; $i < $len; $i++) {
        $salt .= $valid_characters[rand(0, $valid_len)];
    }

    return $salt;
}

/** Extract salt from password
 *
 * @param string $password salted password
 *
 * @return string salt
 */
function extract_salt($password) {
    return substr(strchr($password, ':'), 1);
}

/** Generate salted password
 *
 * @param string $salt salt
 * @param string $pass password
 *
 * @return string salted password
 */
function mix_salt($salt, $pass) {
    return md5($salt . $pass) . ':' . $salt;
}

/** Generate random salt and salted password
 *
 * @param string $pass password
 *
 * @return salted password
 */
function gen_mix_salt($pass) {
    $salt = generate_salt();
    return mix_salt($salt, $pass);
}

function do_log($syslog_message, $priority) {
    global $syslog_use, $syslog_ident, $syslog_facility;
    if ($syslog_use) {
        openlog($syslog_ident, LOG_PERROR, $syslog_facility);
        syslog($priority, $syslog_message);
        closelog();
    }
}

function log_error($syslog_message) {
    do_log($syslog_message, LOG_ERR);
}

function log_warn($syslog_message) {
    do_log($syslog_message, LOG_WARNING);
}

function log_notice($syslog_message) {
    do_log($syslog_message, LOG_NOTICE);
}

function log_info($syslog_message) {
    do_log($syslog_message, LOG_INFO);
}

/** Print the login form
 *
 * @param string $msg Error Message
 * @param string $type Message type [default='success', 'error']
 *
 * @return null
 */
function auth($msg = "", $type = "success") {
    include_once('inc/header.inc.php');
    include('inc/config.inc.php');
    
    if ($msg) {
        print "<div class=\"$type\">$msg</div>\n";
    }
    ?>

    <form class="form-signin" method="post" action="<?php echo htmlentities($_SERVER["PHP_SELF"], ENT_QUOTES); ?>">
        <h2 class="form-signin-heading"><?php echo _('Log in'); ?></h2>
        <input type="hidden" name="query_string" value="<?php echo htmlentities($_SERVER["QUERY_STRING"]); ?>">
        
        <label for="username" class="sr-only"><?php echo _('Username'); ?>:</label>
        <input type="text" name="username" id="username" class="form-control" placeholder="Username" required autofocus>
        <label for="inputPassword" class="sr-only"><?php echo _('Password'); ?>:</label>
        <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required>
        <div class="form-group">
        <label for="userlang"><?php echo _('Language'); ?>:</label>
        
        <select class="form-control" name="userlang">
                        <?php
                        // List available languages (sorted alphabetically)
                        include_once('inc/countrycodes.inc.php');
                        $locales = scandir('locale/');
                        foreach ($locales as $locale) {
                            if (strlen($locale) == 5) {
                                $locales_fullname[$locale] = $countrycodes[substr($locale, 0, 2)];
                            }
                        }
                        asort($locales_fullname);
                        foreach ($locales_fullname as $locale => $language) {
                            if ($locale == $iface_lang) {
                                echo _('<option selected value="' . $locale . '">' . $language);
                            } else {
                                echo _('<option value="' . $locale . '">' . $language);
                            }
                        }
                        ?>
                    </select>
            
        </div>
        <button class="btn btn-lg btn-primary btn-block" name="authenticate" type="submit"><?php echo _('Go'); ?></button>
    </form>
    <script type="text/javascript">
        <!--
      document.getElementById('username').focus();
        //-->
    </script>
    <?php
    include_once('inc/footer.inc.php');
    exit;
}

/** Logout the user
 *
 * Logout the user and kickback to login form
 *
 * @param string $msg Error Message
 * @param string $type Message type [default='']
 *
 * @return null
 */
function logout($msg = "", $type = "") {
    session_unset();
    session_destroy();
    session_write_close();
    auth($msg, $type);
    exit;
}
