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
 * Script that displays list of permission templates
 *
 * @package     Poweradmin
 * @copyright   2007-2010 Rejo Zenger <rejo@zenger.nl>
 * @copyright   2010-2014 Poweradmin Development Team
 * @license     http://opensource.org/licenses/GPL-3.0 GPL
 */
require_once("inc/toolkit.inc.php");
include_once("inc/header.inc.php");
do_hook('verify_permission', 'templ_perm_edit') ? $perm_templ_perm_edit = "1" : $perm_templ_perm_edit = "0";

$permission_templates = do_hook('list_permission_templates');

if ($perm_templ_perm_edit == "0") {
    error(ERR_PERM_EDIT_PERM_TEMPL);
} else {
    echo "    <h1 class=\"page-header\">" . _('Permission templates') . "</h1>\n";
    echo "     <table class=\"table table-hover\">\n";
    echo "      <thead>\n";
    echo "      <tr>\n";
    echo "       <th>&nbsp;</th>\n";
    echo "       <th>" . _('Name') . "</th>\n";
    echo "       <th>" . _('Description') . "</th>\n";
    echo "      </tr>\n";
    echo "      </thead>\n";
    echo "      <tbody>\n";

    foreach ($permission_templates as $template) {

        $perm_item_list = do_hook('get_permissions_by_template_id', $template['id'], true);
        $perm_items = implode(', ', $perm_item_list);

        echo "      <tr>\n";
        if ($perm_templ_perm_edit == "1") {
            echo "       <td>\n";
            echo "        <a class=\"btn btn-warning btn-sm\" role=\"button\" href=\"edit_perm_templ.php?id=" . $template["id"] . "\"><span class=\"glyphicon glyphicon-edit\" aria-hidden=\"true\" alt=\"[ " . _('Edit template') . " ]\"></span></a>\n";
            echo "        <a class=\"btn btn-danger btn-sm\" role=\"button\" href=\"delete_perm_templ.php?id=" . $template["id"] . "\"><span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\" alt=\"[ " . _('Delete template') . " ]\"></span></a>\n";
            echo "       </td>\n";
        } else {
            echo "       <td>&nbsp;</td>\n";
        }
        echo "       <td>" . $template['name'] . "</td>\n";
        echo "       <td>" . $template['descr'] . "</td>\n";
        echo "      </tr>\n";
    }
    echo "      </tbody>\n";
    echo "     </table>\n";
    // echo "     <ul>\n";
    echo "      <a class=\"btn btn-default btn-sm\" role=\"button\" href=\"add_perm_templ.php\"><span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\">&nbsp;</span>" . _('Add permission template') . "</a>\n";
    // echo "     </ul>\n";
}

include_once("inc/footer.inc.php");
