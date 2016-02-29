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
        return true;
    }

    public function onRouterInitialized(URLMapper $m)
    {
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
