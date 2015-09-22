<?php

/*  Poweradmin, a friendly web-based admin tool for PowerDNS.
 *  See <http://www.poweradmin.org> for more details.
 *
 *  Copyright 2007-2010  Rejo Zenger <rejo@zenger.nl>
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
 * Script that displays supermasters list
 *
 * @package     Poweradmin
 * @copyright   2007-2010 Rejo Zenger <rejo@zenger.nl>
 * @copyright   2010-2014 Poweradmin Development Team
 * @license     http://opensource.org/licenses/GPL-3.0 GPL
 */
require_once("inc/toolkit.inc.php");
include_once("inc/header.inc.php");

(do_hook('verify_permission', 'supermaster_view')) ? $perm_sm_view = "1" : $perm_sm_view = "0";
(do_hook('verify_permission', 'supermaster_edit')) ? $perm_sm_edit = "1" : $perm_sm_edit = "0";

$supermasters = get_supermasters();
$num_supermasters = ($supermasters == -1) ? 0 : count($supermasters);

echo "    <h1 class=\"page-header\">" . _('List supermasters') . "</h1>\n";
echo "     <div class=\"table-responsive\">\n";
echo "     <table class=\"table table-hover table-condensed\">\n";
echo "      <thead>\n";
echo "      <tr>\n";
echo "       <th>&nbsp;</th>\n";
echo "       <th>" . _('IP address of supermaster') . "</th>\n";
echo "       <th>" . _('Hostname in NS record') . "</th>\n";
echo "       <th>" . _('Account') . "</th>\n";
echo "      </tr>\n";
echo "      </thead>\n";
echo "      <tbody>\n";
if ($num_supermasters == "0") {
    echo "      <tr>\n";
    echo "       <td>&nbsp;</td>\n";
    echo "       <td colspan=\"3\">\n";
    echo "        " . _('There are no zones to show in this listing.') . "\n";
    echo "       </td>\n";
    echo "      </tr>\n";
} else {
    foreach ($supermasters as $c) {
        echo "      <tr>\n";
        if ($perm_sm_edit == "1") {
            echo "        <td><a class=\"btn btn-danger btn-sm\" role=\"button\" href=\"delete_supermaster.php?master_ip=" . $c['master_ip'] . "&amp;ns_name=" . $c['ns_name'] . "\"><span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\" title=\"" . _('Delete supermaster') . ' ' . $c['master_ip'] . "\" alt=\"[  " . _('Delete supermaster') . " ]\"></span></a></td>\n";
        } else {
            echo "<td>&nbsp;</td>\n";
        }
        echo "       <td>" . $c['master_ip'] . "</td>\n";
        echo "       <td>" . $c['ns_name'] . "</td>\n";
        echo "       <td>" . $c['account'] . "</td>\n";
        echo "      </tr>\n";
    }
}
echo "      </tbody>\n";
echo "     </table>\n";
echo "     </div>\n";
include_once("inc/footer.inc.php");
