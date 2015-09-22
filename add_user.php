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
 * Script that handles requests to add new users
 *
 * @package     Poweradmin
 * @copyright   2007-2010 Rejo Zenger <rejo@zenger.nl>
 * @copyright   2010-2014 Poweradmin Development Team
 * @license     http://opensource.org/licenses/GPL-3.0 GPL
 */
require_once("inc/toolkit.inc.php");
include_once("inc/header.inc.php");

if (!do_hook('verify_permission' , 'user_add_new' )) {
    error(ERR_PERM_ADD_USER);
} else {
    if (isset($_POST["commit"])) {
        if (do_hook('add_new_user' , $_POST )) {
            success(SUC_USER_ADD);
        }
    }

    echo "     <h1 class=\"page-header\">" . _('Add user') . "</h1>\n";
    echo "     <form class=\"form-horizontal\" method=\"post\" action=\"add_user.php\">\n";
    echo "       <div class=\"form-group\">\n";
    echo "        <label for=\"username\" class=\"col-sm-2 control-label\">" . _('Username') . "</label>\n";
    echo "         <div class=\"col-sm-10\">\n";
    echo "         <input type=\"text\" class=\"form-control\" name=\"username\" value=\"\">\n";
    echo "         </div>\n";
    echo "        </div>\n";
    echo "       <div class=\"form-group\">\n";
    echo "        <label for=\"fullname\" class=\"col-sm-2 control-label\">" . _('Fullname') . "</label>\n";
    echo "         <div class=\"col-sm-10\">\n";
    echo "         <input type=\"text\" class=\"form-control\" name=\"fullname\" value=\"\">\n";
    echo "         </div>\n";
    echo "        </div>\n";
    echo "       <div class=\"form-group\">\n";
    echo "        <label for=\"password\" class=\"col-sm-2 control-label\">" . _('Password') . "</label>\n";
    echo "         <div class=\"col-sm-10\">\n";
    echo "         <input type=\"password\" class=\"form-control\" name=\"password\" value=\"\">\n";
    echo "         </div>\n";
    echo "        </div>\n";
    echo "       <div class=\"form-group\">\n";
    echo "        <label for=\"email\" class=\"col-sm-2 control-label\">" . _('Email address') . "</label>\n";
    echo "         <div class=\"col-sm-10\">\n";
    echo "         <input type=\"text\" class=\"form-control\" name=\"email\" value=\"\">\n";
    echo "         </div>\n";
    echo "        </div>\n";
    if (do_hook('verify_permission' , 'user_edit_templ_perm' )) {
    echo "       <div class=\"form-group\">\n";
    echo "        <label for=\"perm_templ\" class=\"col-sm-2 control-label\">" . _('Permission template') . "</label>\n";
    echo "         <div class=\"col-sm-10\">\n";
    echo "         <select class=\"form-control\" name=\"perm_templ\">\n";
        foreach (do_hook('list_permission_templates' ) as $template) {
    echo "          <option value=\"" . $template['id'] . "\">" . $template['name'] . "</option>\n";
        }
    echo "         </select>\n";
    echo "         </div>\n";
    echo "        </div>\n";
    }
    echo "       <div class=\"form-group\">\n";
    echo "        <label for=\"descr\" class=\"col-sm-2 control-label\">" . _('Description') . "</label>\n";
    echo "         <div class=\"col-sm-10\">\n";
    echo "         <textarea class=\"form-control\" rows=\"3\" name=\"descr\"></textarea>\n";
    echo "         </div>\n";
    echo "        </div>\n";
    echo "       <div class=\"form-group\">\n";
    echo "       <div class=\"col-sm-offset-2 col-sm-10\">\n";
    echo "         <div class=\"checkbox\">\n";
    echo "         <label><input type=\"checkbox\" name=\"active\" value=\"1\" CHECKED>" . _('Enabled') . "</label>\n";
    echo "         </div>\n";
    echo "        </div>\n";
    echo "        </div>\n";
    echo "       <div class=\"form-group\">\n";
    echo "       <div class=\"col-sm-offset-2 col-sm-10\">\n";
    echo "        <button type=\"submit\" name=\"commit\" class=\"btn btn-default\">" . _('Commit changes') . "</button>\n";
    echo "        </div>\n";
    echo "        </div>\n";
    echo "     </form>\n";
}

include_once("inc/footer.inc.php");
