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
 * Script that handles requests to add new master zones
 *
 * @package     Poweradmin
 * @copyright   2007-2010 Rejo Zenger <rejo@zenger.nl>
 * @copyright   2010-2014 Poweradmin Development Team
 * @license     http://opensource.org/licenses/GPL-3.0 GPL
 */
require_once("inc/toolkit.inc.php");
include_once("inc/header.inc.php");

echo "  <script type=\"text/javascript\" src=\"inc/helper.js\"></script>";

global $pdnssec_use;
global $dns_third_level_check;

$owner = "-1";
if ((isset($_POST['owner'])) && (v_num($_POST['owner']))) {
    $owner = $_POST['owner'];
}

$dom_type = "NATIVE";
if (isset($_POST["dom_type"]) && (in_array($_POST['dom_type'], $server_types))) {
    $dom_type = $_POST["dom_type"];
}

if (isset($_POST['domain'])) {
    $temp = array();
    foreach ($_POST['domain'] as $domain) {
        if ($domain != "") {
            $temp[] = trim($domain);
        }
    }
    $domains = $temp;
} else {
    $domains = array();
}

if (isset($_POST['zone_template'])) {
    $zone_template = $_POST['zone_template'];
} else {
    $zone_template = "none";
}

$enable_dnssec = false;
if (isset($_POST['dnssec']) && $_POST['dnssec'] == '1') {
    $enable_dnssec = true;
}

/*
  Check user permissions
 */
(do_hook('verify_permission' , 'zone_master_add' )) ? $zone_master_add = "1" : $zone_master_add = "0";
(do_hook('verify_permission' , 'user_view_others' )) ? $perm_view_others = "1" : $perm_view_others = "0";

if (isset($_POST['submit']) && $zone_master_add == "1") {
    $error = false;
    foreach ($domains as $domain) {
        if (!is_valid_hostname_fqdn($domain, 0)) {
            error($domain . ' failed - ' . ERR_DNS_HOSTNAME);
        } elseif ($dns_third_level_check && get_domain_level($domain) > 2 && domain_exists(get_second_level_domain($domain))) {
            error($domain . ' failed - ' . ERR_DOMAIN_EXISTS);
            $error = true;
        } elseif (domain_exists($domain) || record_name_exists($domain)) {
            error($domain . ' failed - ' . ERR_DOMAIN_EXISTS);
            // TODO: repopulate domain name(s) to the form if there was an error occured
            $error = true;
        } elseif (add_domain($domain, $owner, $dom_type, '', $zone_template)) {
            $domain_id = get_zone_id_from_name($domain);
            success("<a href=\"edit.php?id=" . $domain_id . "\">" . $domain . " - " . SUC_ZONE_ADD . '</a>');
            log_info(sprintf('client_ip:%s user:%s operation:add_zone zone:%s zone_type:%s zone_template:%s',
                              $_SERVER['REMOTE_ADDR'], $_SESSION["userlogin"],
                              $domain,$dom_type,$zone_template));

            if ($pdnssec_use) {
                if ($enable_dnssec) {
                    dnssec_secure_zone($domain);
                }

                dnssec_rectify_zone($domain_id);
            }
        }
    }

    if (false === $error) {
        unset($domains, $owner, $dom_type, $zone_template);
    }
}

if ($zone_master_add != "1") {
    error(ERR_PERM_ADD_ZONE_MASTER);
} else {
    echo "   <h1 class=\"page-header\">" . _('Add master zone') . "</h1>\n";

    $available_zone_types = array("MASTER", "NATIVE");
    $users = do_hook('show_users');
    $zone_templates = get_list_zone_templ($_SESSION['userid']);
    echo "     <br>\n";
    echo "     <form class=\"form-horizontal\" method=\"post\" action=\"add_zone_master.php\">\n";
    echo "       <div class=\"form-group\">\n";
    echo "        <label for=\"master_ip\" class=\"col-sm-2 control-label\">" . _('Zone name') . "</label>\n";
    echo "        <div class=\"col-sm-5\">\n";
    echo "         <ul class=\"list-unstyled list-spaced\" id=\"domain_names\">\n";
    echo "          <li><input type=\"text\" class=\"form-control\" name=\"domain[]\" value=\"\" id=\"domain_1\"></li>\n";
    echo "         </ul>\n";
    echo "        </div>\n";
    echo "        <div class=\"col-sm-2\">\n";
    echo "         <input class=\"btn btn-default\" type=\"button\" value=\"Add another domain\" onclick=\"addField('domain_names','domain_',0);\" />\n";
    echo "        </div>\n";
    echo "       </div>\n";
    
    echo "       <div class=\"form-group\">\n";
    echo "        <label for=\"master_ip\" class=\"col-sm-2 control-label\">" . _('Owner') . "</label>\n";
    echo "        <div class=\"col-sm-5\">\n";
    echo "         <select class=\"form-control\" name=\"owner\">\n";
    /*
      Display list of users to assign zone to if creating
      user has the proper permission to do so.
     */
    foreach ($users as $user) {
        if ($user['id'] === $_SESSION['userid']) {
            echo "          <option value=\"" . $user['id'] . "\" selected>" . $user['fullname'] . "</option>\n";
        } elseif ($perm_view_others == "1") {
            echo "          <option value=\"" . $user['id'] . "\">" . $user['fullname'] . "</option>\n";
        }
    }
    echo "         </select>\n";
    echo "        </div>\n";
    echo "       </div>\n";
    
    echo "       <div class=\"form-group\">\n";
    echo "        <label for=\"master_ip\" class=\"col-sm-2 control-label\">" . _('Type') . "</label>\n";
    echo "        <div class=\"col-sm-5\">\n";
    echo "         <select class=\"form-control\" name=\"dom_type\">\n";
    foreach ($available_zone_types as $type) {
        echo "          <option value=\"" . $type . "\">" . strtolower($type) . "</option>\n";
    }
    echo "         </select>\n";
    echo "        </div>\n";
    echo "       </div>\n";
    
    echo "       <div class=\"form-group\">\n";
    echo "        <label for=\"master_ip\" class=\"col-sm-2 control-label\">" . _('Template') . "</label>\n";
    echo "        <div class=\"col-sm-5\">\n";
    echo "         <select class=\"form-control\" name=\"zone_template\">\n";
    echo "          <option value=\"none\">none</option>\n";
    foreach ($zone_templates as $zone_template) {
        echo "          <option value=\"" . $zone_template['id'] . "\">" . $zone_template['name'] . "</option>\n";
    }
    echo "         </select>\n";
    echo "        </div>\n";
    echo "       </div>\n";
    
    echo "       <div class=\"form-group\">\n";
    echo "        <label for=\"master_ip\" class=\"col-sm-2 control-label\">" . _('DNSSEC') . "</label>\n";
    echo "        <div class=\"col-sm-5\">\n";
    echo "          <input type=\"checkbox\" class=\"input\" name=\"dnssec\" value=\"1\">\n";
    echo "        </div>\n";
    echo "       </div>\n";
    
    echo "       <div class=\"form-group\">\n";
    echo "        <div class=\"col-sm-offset-2 col-sm-5\">\n";
    echo "         <input type=\"submit\" class=\"btn btn-default\" name=\"submit\" value=\"" . _('Add zone') . "\" onclick=\"checkDomainFilled();return false;\">\n";
    echo "        </div>\n";
    echo "       </div>\n";
    echo "     </form>\n";

}

include_once("inc/footer.inc.php");
