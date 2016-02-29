<?php

if (!defined('STATUSNET')) {
    exit(1);
}

class U2FPlugin extends Plugin
{
    function initialize()
    {
        return true;
    }

    function cleanup()
    {
        return true;
    }

    function onCheckSchema()
    {
        $schema = Schema::get();

        $schema->ensureTable('u2f_user_data', U2f_user_data::schemaDef());

        return true;
    }

    public function onRouterInitialized(URLMapper $m)
    {
	$m->connect('panel/u2f', array('action' => 'u2fadminpanel'));

        return true;
    }

    public function onAdminPanelCheck($name, &$isOK)
    {
        if ($name == 'u2f') {
            $isOK = true;
            return false;
        }
        return true;
    }

    public function onEndAdminPanelNav($nav)
    {
        if (AdminPanelAction::canAdmin('u2f')) {
            $action_name = $nav->action->trimmed('action');
            $nav->out->menuItem(
                common_local_url('u2fadminpanel'),
                _m('U2F'),
                _m('U2F configuration page.'),
                $action_name == 'u2fadminpanel',
                'nav_u2f_admin_panel'
            );
        }
        return true;
    }

    function onPluginVersion(array &$versions)
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
