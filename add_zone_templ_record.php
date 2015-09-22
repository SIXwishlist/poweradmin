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
 * Script that handles requests to add new records to zone templates
 *
 * @package     Poweradmin
 * @copyright   2007-2010 Rejo Zenger <rejo@zenger.nl>
 * @copyright   2010-2014 Poweradmin Development Team
 * @license     http://opensource.org/licenses/GPL-3.0 GPL
 */
require_once("inc/toolkit.inc.php");
include_once("inc/header.inc.php");

/*
  Check and make sure all post values have made it through
  if not set them.
 */
$zone_templ_id = "-1";
if ((isset($_GET['id'])) && (v_num($_GET['id']))) {
    $zone_templ_id = $_GET['id'];
}

$ttl = $dns_ttl;
if ((isset($_POST['ttl'])) && (v_num($_POST['ttl']))) {
    $ttl = $_POST['ttl'];
}

$prio = "";
if ((isset($_POST['prio'])) && (v_num($_POST['prio']))) {
    $prio = $_POST['prio'];
}

if (isset($_POST['name'])) {
    $name = $_POST['name'];
} else {
    $name = "";
}

if (isset($_POST['type'])) {
    $type = $_POST['type'];
} else {
    $type = "";
}

if (isset($_POST['content'])) {
    $content = $_POST['content'];
} else {
    $content = "";
}

if ($zone_templ_id == "-1") {
    error(ERR_INV_INPUT);
    include_once("inc/footer.inc.php");
    exit;
}

$templ_details = get_zone_templ_details($zone_templ_id);
$owner = get_zone_templ_is_owner($zone_templ_id, $_SESSION['userid']);

/*
  If the form as been submitted
  process it!
 */
if (isset($_POST["commit"])) {
    if (!(do_hook('verify_permission' , 'zone_master_add' )) || !$owner) {
        error(ERR_PERM_ADD_RECORD);
    } else {
        if (add_zone_templ_record($zone_templ_id, $name, $type, $content, $ttl, $prio)) {
            success(_('The record was successfully added.'));
            $name = $type = $content = $ttl = $prio = "";
        }
    }
}

/*
  Display form to add a record
 */
echo "    <h1 class=\"page-header\">" . _('Add record to zone template') . " \"" . $templ_details['name'] . "\"</h1>\n";

if (!(do_hook('verify_permission' , 'zone_master_add' )) || !$owner) {
    error(ERR_PERM_ADD_RECORD);
} else {
    echo "     <form class=\"form-inline\" method=\"post\">\n";
    echo "      <input type=\"hidden\" name=\"domain\" value=\"" . $zone_templ_id . "\">\n";
    echo "     <div class=\"table-responsive\">\n";
    echo "      <table class=\"table\">\n";
    echo "      <thead>\n";
    echo "       <tr>\n";
    echo "        <th>" . _('Name') . "</th>\n";
    echo "        <th>" . _('Type') . "</th>\n";
    echo "        <th>" . _('Content') . "</th>\n";
    echo "        <th>" . _('Priority') . "</th>\n";
    echo "        <th>" . _('TTL') . "</th>\n";
    echo "       </tr>\n";
    echo "      </thead>\n";
    echo "      <tbody>\n";
    echo "       <tr>\n";
    echo "        <td><input type=\"text\" class=\"form-control\" name=\"name\" value=\"" . $name . "\"></td>\n";
    echo "        <td>\n";
    echo "        <label>IN</label>\n";
    echo "         <select class=\"form-control\" name=\"type\">\n";
    $found_selected_type = !(isset($type) && $type);
    foreach (get_record_types() as $record_type) {
        if (isset($type) && $type) {
            if ($type == $record_type) {
                $add = " SELECTED";
                $found_selected_type = true;
            } else {
                $add = "";
            }
        } else {
            // TODO: from where comes $zone_name value and why this check exists here?
            if (isset($zone_name) && preg_match('/i(p6|n-addr).arpa/i', $zone_name) && strtoupper($record_type) == 'PTR') {
                $add = " SELECTED";
            } elseif (strtoupper($record_type) == 'A') {
                $add = " SELECTED";
            } else {
                $add = "";
            }
        }
        echo "          <option" . $add . " value=\"" . $record_type . "\">" . $record_type . "</option>\n";
    }
    if (!$found_selected_type)
        echo "          <option SELECTED value=\"" . htmlspecialchars($type) . "\"><i>" . htmlspecialchars($type) . "</i></option>\n";
    echo "         </select>\n";
    echo "        </td>\n";
    echo "        <td><input class=\"form-control\" type=\"text\" name=\"content\" value=\"" . $content . "\"></td>\n";
    echo "        <td><input class=\"form-control\" type=\"text\" name=\"prio\" value=\"" . $prio . "\"></td>\n";
    echo "        <td><input class=\"form-control\" type=\"text\" name=\"ttl\" value=\"" . $ttl . "\"</td>\n";
    echo "       </tr>\n";
    echo "     <tr>\n";
    echo "      <td colspan=\"5\"><br><b>Hint:</b></td>\n";
    echo "     </tr>\n";
    echo "     <tr>\n";
    echo "      <td colspan=\"5\">" . _('The following placeholders can be used in template records') . "</td>\n";
    echo "     </tr>\n";
    echo "     <tr>\n";
    echo "      <td colspan=\"5\"><br>&nbsp;&nbsp;&nbsp;&nbsp; * [ZONE] - " . _('substituted with current zone name') . "<br>";
    echo "&nbsp;&nbsp;&nbsp;&nbsp; * [SERIAL] - " . _('substituted with current date and 2 numbers') . " (YYYYMMDD + 00)</td>\n";
    echo "     </tr>\n";
    echo "      </tbody>\n";
    echo "      </table>\n";
    echo "     </div>\n";
    echo "      <button type=\"submit\" name=\"commit\" class=\"btn btn-default\">" . _('Add record') . "</button>\n";
    echo "     </form>\n";
}

include_once("inc/footer.inc.php");
