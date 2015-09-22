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
 * Web interface footer
 *
 * @package     Poweradmin
 * @copyright   2007-2010 Rejo Zenger <rejo@zenger.nl>
 * @copyright   2010-2014 Poweradmin Development Team
 * @license     http://opensource.org/licenses/GPL-3.0 GPL
 */

global $db;
if (is_object($db)) {
    $db->disconnect();
}

if (isset($_SESSION ["userid"])) {
    echo " </div> <!-- /content --> </div>\n";
} else {
    echo "</div>\n";
    echo "<div class=\"footer\">\n";
    echo "  <a href=\"http://www.poweradmin.org\">a complete(r) <strong>poweradmin</strong></a> - <a href=\"http://www.poweradmin.org/credits.html\">credits</a>";
    echo "</div>\n";
}

if (file_exists('inc/custom_footer.inc.php')) {
    include('inc/custom_footer.inc.php');
}
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<![endif]-->
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="../../style/js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>

<?php
if (isset($db_debug) && $db_debug == true) {
    $debug = $db->getDebugOutput();
    $debug = str_replace("query(1)", "", $debug);
    $lines = explode(":", $debug);

    foreach ($lines as $line) {
        echo "$line<br>";
    }
}
?>

<?php
global $display_stats;
if ($display_stats)
    display_current_stats();
