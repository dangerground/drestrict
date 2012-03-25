<?php
/**
 * DokuWiki Plugin drestrict (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Danny Götte <danny.goette@fem.tu-ilmenau.de>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

if (!defined('DOKU_LF')) define('DOKU_LF', "\n");
if (!defined('DOKU_TAB')) define('DOKU_TAB', "\t");
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');

require_once DOKU_PLUGIN.'action.php';

class action_plugin_drestrict extends DokuWiki_Action_Plugin {

    // stop if domains do not match, thanks to dokuwiki the eMail has the magic number 3, which may change in future versions
	const EMAIL_INDEX = 3;

    public function register(Doku_Event_Handler &$controller) {

       $controller->register_hook('AUTH_USER_CHANGE', 'BEFORE', $this, 'handle_auth_user_change');
   
    }

    public function handle_auth_user_change(Doku_Event &$event, $param) {

        // check for domain setting        
        $confDomain = $this->getConf('domain');
        if (trim($confDomain) == false) {
            return true;
        }

        // skip delete action
        if ($event->data['type'] === 'delete') {
            return true;
        }

        if (!isset($event->data['params'][self::EMAIL_INDEX])) {
        	return true;
        }

        $email = explode('@', $event->data['params'][self::EMAIL_INDEX]);
        $domain = $email[1];
        if (strtolower($domain) != strtolower($confDomain)) {
            msg(sprintf($this->getLang('wrong_domain'), $confDomain), -1);
//            $event->stopPropagation();
            $event->preventDefault();
            $event->data['params'] = array();
        }
        return false;
    }

}

// vim:ts=4:sw=4:et:
