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
 * Script that handles search requests
 *
 * @package     Poweradmin
 * @copyright   2007-2010 Rejo Zenger <rejo@zenger.nl>
 * @copyright   2010-2014 Poweradmin Development Team
 * @license     http://opensource.org/licenses/GPL-3.0 GPL
 */
require_once('inc/toolkit.inc.php');
include_once('inc/header.inc.php');

if (!(do_hook('verify_permission', 'search'))) {
    error(ERR_PERM_SEARCH);
    include_once('inc/footer.inc.php');
    exit;
} else {
    echo "     <h1 class=\"page-header\">" . _('Search zones and records') . "</h1>\n";
    $holy_grail = '';
    if (isset($_POST['query'])) {

        if (do_hook('verify_permission', 'zone_content_view_others')) {
            $perm_view = "all";
        } elseif (do_hook('verify_permission', 'zone_content_view_own')) {
            $perm_view = "own";
        } else {
            $perm_view = "none";
        }

        if (do_hook('verify_permission', 'zone_content_edit_others')) {
            $perm_edit = "all";
        } elseif (do_hook('verify_permission', 'zone_content_edit_own')) {
            $perm_edit = "own";
        } else {
            $perm_edit = "none";
        }

        $holy_grail = $_POST['query'];
        $wildcards = ($_POST['wildcards'] == "true" ? true : false);
        $arpa = ($_POST['arpa'] == "true" ? true : false);

        $result = search_zone_and_record($holy_grail, $perm_view, ZONE_SORT_BY, RECORD_SORT_BY, $wildcards, $arpa);

        if (is_array($result['zones'])) {
            echo "     <script language=\"JavaScript\" type=\"text/javascript\">\n";
            echo "     <!--\n";
            echo "     function zone_sort_by ( sortbytype )\n";
            echo "     {\n";
            echo "       document.sortby_zone_form.zone_sort_by.value = sortbytype ;\n";
            echo "       document.sortby_zone_form.submit() ;\n";
            echo "     }\n";
            echo "     -->\n";
            echo "     </script>\n";
            echo "     <form class=\"form-inline\" name=\"sortby_zone_form\" method=\"post\" action=\"search.php\">\n";
            echo "     <input type=\"hidden\" name=\"query\" value=\"" . $_POST['query'] . "\" />\n";
            echo "     <input type=\"hidden\" name=\"zone_sort_by\" />\n";
            echo "     <h2 class=\"sub-header\">" . _('Zones found') . ":</h2>\n";
            echo "     <div class=\"table-responsive\">\n";
            echo "     <table class=\"table table-hover table-condensed\">\n";
            echo "      <thead>\n";
            echo "      <tr>\n";
            echo "       <th>&nbsp;</th>\n";
            echo "       <th><a href=\"javascript:zone_sort_by('name')\">" . _('Name') . "</a></th>\n";
            echo "       <th><a href=\"javascript:zone_sort_by('type')\">" . _('Type') . "</a></th>\n";
            echo "       <th><a href=\"javascript:zone_sort_by('master')\">" . _('Master') . "</a></th>\n";
            /* If user has all edit permissions show zone owners */
            if ($perm_edit == "all") {
                echo "	     <th><a href=\"javascript:zone_sort_by('owner')\">" . _('Owner') . "</a></th>\n";
            }

            echo "      </tr>\n";
            echo "      </thead>\n";
            echo "      </form>\n";
            echo "      <tbody>\n";
            foreach ($result['zones'] as $zone) {
                echo "      <tr>\n";
                echo "          <td>\n";
                echo "           <a class=\"btn btn-warning btn-sm\" role=\"button\" href=\"edit.php?name=" . $zone['name'] . "&id=" . $zone['zid'] . "\"><span class=\"glyphicon glyphicon-edit\" aria-hidden=\"true\" title=\"" . _('Edit zone') . " " . $zone['name'] . "\" alt=\"[ " . _('Edit zone') . " " . $zone['name'] . " ]\"></span></a>\n";
                if ($perm_edit != "all" || $perm_edit != "none") {
                    $user_is_zone_owner = do_hook('verify_user_is_owner_zoneid', $zone['zid']);
                }
                if ($perm_edit == "all" || ( $perm_edit == "own" && $user_is_zone_owner == "1")) {
                    echo "           <a class=\"btn btn-danger btn-sm\" role=\"button\" href=\"delete_domain.php?name=" . $zone['name'] . "&id=" . $zone['zid'] . "\"><span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\" title=\"" . _('Delete zone') . " " . $zone['name'] . "\" alt=\"[ " . _('Delete zone') . " " . $zone['name'] . " ]\"></span></a>\n";
                }
                echo "          </td>\n";
                echo "       <td>" . $zone['name'] . "</td>\n";
                echo "       <td>" . $zone['type'] . "</td>\n";
                if ($zone['type'] == "SLAVE") {
                    echo "       <td>" . $zone['master'] . "</td>\n";
                } else {
                    echo "       <td>&nbsp;</td>\n";
                }
                if ($perm_edit == "all") {
                    echo "          <td>" . $zone['owner'] . "</td>";
                }
                echo "      </tr>\n";
            }
            echo "      </tbody>\n";
            echo "     </table>\n";
            echo "     </div>\n";
        }

        if (is_array($result['records'])) {
            echo "     <script language=\"JavaScript\" type=\"text/javascript\">\n";
            echo "     <!--\n";
            echo "     function record_sort_by ( sortbytype )\n";
            echo "     {\n";
            echo "       document.sortby_record_form.record_sort_by.value = sortbytype ;\n";
            echo "       document.sortby_record_form.submit() ;\n";
            echo "     }\n";
            echo "     -->\n";
            echo "     </script>\n";
            echo "     <form class=\"form-inline\" name=\"sortby_record_form\" method=\"post\" action=\"search.php\">\n";
            echo "     <input type=\"hidden\" name=\"query\" value=\"" . $_POST['query'] . "\" />\n";
            echo "     <input type=\"hidden\" name=\"record_sort_by\" />\n";
            echo "     <h2 class=\"sub-header\">" . _('Records found') . ":</h2>\n";
            echo "     <div class=\"table-responsive\">\n";
            echo "     <table class=\"table table-hover table-condensed\">\n";
            echo "      <thead>\n";
            echo "      <tr>\n";
            echo "       <th>&nbsp;</th>\n";
            echo "       <th><a href=\"javascript:record_sort_by('name')\">" . _('Name') . "</a></th>\n";
            echo "       <th><a href=\"javascript:record_sort_by('type')\">" . _('Type') . "</a></th>\n";
            echo "       <th><a href=\"javascript:record_sort_by('content')\">" . _('Content') . "</a></th>\n";
            echo "       <th>Priority</th>\n";
            echo "       <th><a href=\"javascript:record_sort_by('ttl')\">" . _('TTL') . "</a></th>\n";
            echo "      </tr>\n";
            echo "      </thead>\n";
            echo "      </form>\n";
            echo "      <tbody>\n";
            
            foreach ($result['records'] as $record) {

                echo "      <tr>\n";
                echo "          <td>\n";
                echo "           <a class=\"btn btn-warning btn-sm\" role=\"button\" href=\"edit_record.php?id=" . $record['rid'] . "\"><span class=\"glyphicon glyphicon-edit\" aria-hidden=\"true\" title=\"" . _('Edit record') . " " . $record['name'] . "\" alt=\"[ " . _('Edit record') . " " . $record['name'] . " ]\"></span></a>\n";
                if ($perm_edit != "all" || $perm_edit != "none") {
                    $user_is_zone_owner = do_hook('verify_user_is_owner_zoneid', $record['zid']);
                }
                if ($perm_edit == "all" || ( $perm_edit == "own" && $user_is_zone_owner == "1")) {
                    echo "           <a class=\"btn btn-danger btn-sm\" role=\"button\" href=\"delete_record.php?id=" . $record['rid'] . "\"><span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\" title=\"" . _('Delete record') . " " . $record['name'] . "\" alt=\"[ " . _('Delete record') . " " . $record['name'] . " ]\"></span></a>\n";
                }
                echo "          </td>\n";
                echo "       <td>" . $record['name'] . "</td>\n";
                echo "       <td>" . $record['type'] . "</td>\n";
                echo "       <td>" . $record['content'] . "</td>\n";
                if ($record['type'] == "MX" || $record['type'] == "SRV") {
                    echo "       <td>" . $record['prio'] . "</td>\n";
                } else {
                    echo "       <td>&nbsp;</td>\n";
                }
                echo "       <td>" . $record['ttl'] . "</td>\n";
                echo "      </tr>\n";
            }
            echo "      </tbody>\n";
            echo "     </table>\n";
            echo "     </div>\n";
        }
    } else { // !isset($_POST['query'])
        $wildcards = true;
        $arpa = true;
    }

    // echo "     <h3>" . _('Query') . ":</h3>\n";
    echo "      <form method=\"post\" action=\"" . htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES) . "\">\n";
    echo "      <div class=\"row\">\n";
    echo "      <div class=\"col-lg-6\">\n";
    echo "       <div class=\"input-group\">\n";
    echo "        <input type=\"text\" class=\"form-control\" name=\"query\" value=\"" . $holy_grail . "\">\n";
    echo "         <span class=\"input-group-btn\">\n";
    echo "          <button type=\"submit\" class=\"btn btn-default\">" . _('Search') . "</button>\n";
    echo "         </span>\n";
    echo "       </div>\n";
    echo "       </div>\n";
    echo "       </div>\n";
    echo "       <div class=\"checkbox\">\n";
    echo "        <label>\n";
    echo "         <input type=\"checkbox\" name=\"wildcards\" value=\"true\"" . ($wildcards ? "checked=\"checked\"" : "") . "> " . _('Wildcard') . "\n";
    echo "        </label>\n";
    echo "       </div>\n";
    echo "       <div class=\"checkbox\">\n";
    echo "        <label>\n";
    echo "         <input type=\"checkbox\" name=\"arpa\" value=\"true\"" . ($arpa ? "checked=\"checked\"" : "") . "> " . _('Reverse') . "\n";
    echo "        </label>\n";
    echo "       </div>\n";
    echo "       <p class=\"help-block\">\n";
    echo "       " . _('Enter a hostname or IP address. SQL LIKE syntax supported: an underscore (_) in pattern matches any single character, a percent sign (%) matches any string of zero or more characters.') . "\n";
    echo "       </p>\n";
    echo "      </form>\n";
}

include_once('inc/footer.inc.php');
