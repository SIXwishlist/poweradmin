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
 * Script that displays list of zone templates
 *
 * @package     Poweradmin
 * @copyright   2007-2010 Rejo Zenger <rejo@zenger.nl>
 * @copyright   2010-2014 Poweradmin Development Team
 * @license     http://opensource.org/licenses/GPL-3.0 GPL
 */
require_once("inc/toolkit.inc.php");
include_once("inc/header.inc.php");

do_hook('verify_permission', 'zone_master_add') ? $perm_zone_master_add = "1" : $perm_zone_master_add = "0";

$zone_templates = get_list_zone_templ($_SESSION['userid']);
$username = do_hook('get_fullname_from_userid', $_SESSION['userid']);

if ($perm_zone_master_add == "0") {
    error(ERR_PERM_EDIT_ZONE_TEMPL);
} else {
    echo "    <h1 class=\"page-header\">" . _('Zone templates for') . " " . $username . "</h1>\n";
    echo "     <div class=\"table-responsive\">\n";
    echo "     <table class=\"table table-hover table-condensed\">\n";
    echo "      <thead>\n";
    echo "      <tr>\n";
    echo "       <th>&nbsp;</th>\n";
    echo "       <th>" . _('Name') . "</th>\n";
    echo "       <th>" . _('Description') . "</th>\n";
    echo "      </tr>\n";
    echo "      </thead>\n";
    echo "      <tbody>\n";
    foreach ($zone_templates as $template) {

        echo "      <tr>\n";
        if ($perm_zone_master_add == "1") {
            echo "       <td>\n";
            echo "        <a class=\"btn btn-warning btn-sm\" role=\"button\" href=\"edit_zone_templ.php?id=" . $template["id"] . "\"><span class=\"glyphicon glyphicon-edit\" aria-hidden=\"true\" alt=\"[ " . _('Edit template') . " ]\"></span></a>\n";
            echo "        <a class=\"btn btn-danger btn-sm\" role=\"button\" href=\"delete_zone_templ.php?id=" . $template["id"] . "\"><span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\" alt=\"[ " . _('Delete template') . " ]\"></span></a>\n";
            echo "       </td>\n";
        } else {
            echo "       <td>&nbsp;</td>\n";
        }
        echo "       <td class=\"y\">" . $template['name'] . "</td>\n";
        echo "       <td class=\"y\">" . $template['descr'] . "</td>\n";
        echo "      </tr>\n";
    }
    echo "      </tbody>\n";
    echo "     </table>\n";
    echo "     </div>\n";
    echo "      <a class=\"btn btn-default\" role=\"button\" href=\"add_zone_templ.php\">" . _('Add zone template') . "</a>\n";
}

include_once("inc/footer.inc.php");
