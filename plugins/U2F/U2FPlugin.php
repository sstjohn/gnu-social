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
        return true;
    }

    public function cleanup()
    {
        return true;
    }

    public function onAutoload($cls)
    {
        $dir = dirname(__FILE__);

        switch ($cls)
        {
        case 'U2F':
            include_once $dir . '/extlib/U2F.php';
            return false;
        }

        return parent::onAutoload($cls);
    }

    public function onCheckSchema()
    {
        $schema = Schema::get();

        $schema->ensureTable('user_u2f_data', User_u2f_data::schemaDef());

        return true;
    }

    public function onEndShowScripts($action)
    {
        static $needy = array(
            'U2fregisterAction',
        );

        if (in_array(get_class($action), $needy)) {
            $action->script($this->path('js/extlib/u2f-api.js'));
        }

        return true;
    }

    public function onRouterInitialized(URLMapper $m)
    {
        $m->connect('settings/u2f', array('action' => 'u2fsettings'));
        $m->connect('settings/u2f_register', array('action' => 'u2fregister'));

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
