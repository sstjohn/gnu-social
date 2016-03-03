<?php

if (!defined('STATUSNET')) {
    exit(1);
}

class U2FPlugin extends Plugin
{
    const VERSION = GNUSOCIAL_VERSION;
    public $U2FEnabled = false;

    public function initialize()
    {
        require_once(dirname(__FILE__) . "/extlib/U2F.php");
        return true;
    }

    public function cleanup()
    {
        return true;
    }

    public function onCheckSchema()
    {
        $schema = Schema::get();

        $schema->ensureTable('user_u2f_data', User_u2f_data::schemaDef());
        $schema->ensureTable('user_u2f_device', User_u2f_device::schemaDef());

        return true;
    }

    public function onRouterInitialized(URLMapper $m)
    {
        $m->connect('settings/u2f', array('action' => 'u2fsettings'));
        $m->connect('u2f/register/start', array('action' => 'u2f_reg_start'));
        $m->connect('u2f/register/finish', array('action' => 'u2f_reg_finish'));
        $m->connect('u2f/auth/start', array('action' => 'u2f_auth_start'));
        $m->connect('u2f/auth/finish', array('action' => 'u2f_auth_finish'));
        return true;
    }

    public function onEndAccountSettingsNav($action)
    {
        $action_name = $action->trimmed('action');
        $action->menuItem(
            common_local_url('u2fsettings'),
            _m('U2F'),
            _m('U2F configuration page.'),
            $action_name == 'u2fsettings'
        );
        return true;
    }

    public function onEndShowHeadElements($action)
    {
        static $needy = array(
            'U2f_reg_startAction',
            'U2f_auth_startAction',
        );

        if (in_array(get_class($action), $needy)) {
            $action->script($this->path('js/extlib/u2f-api.js'));
        }
   
        return true;
    }

    public function onStartSetUser($user)
    {
        if ($_SESSION['auth-stage2-done']) {
            unset($_SESSION['auth-stage2-done']);
            return true;
        } else if (User_u2f_data::get_device_requirement($user->id)) {
            $_SESSION['auth-stage1-user'] = $user;
            common_redirect(common_local_url("u2f_auth_start"), 303);
            return false;
        }
        return true;
    }

    public function onPluginVersion(array &$versions)
    {
        $versions[] = array('name' => 'U2F',
                            'version' => GNUSOCIAL_VERSION,
                            'author' => 'Saul St John',
                            'homepage' => 'https://git.gnu.io/sstjohn/gnu-social/tree/werk/plugins/U2F',
                            'rawdescription' =>
                          // TRANS: Plugin description.
                            _m('FIDO U2F'));
        return true;
    }
}
