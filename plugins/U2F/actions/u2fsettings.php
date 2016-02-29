<?php

if (!defined('STATUSNET')) {
    exit(1);
}

class U2fsettingsAction extends SettingsAction
{
    public function title()
    {
        return _m('TITLE', 'U2F');
    }

    public function getInstructions()
    {
        return _m('U2F settings');
    }

    public function showForm()
    {
        $form  = new U2fSettingsForm($this);
        $form->show();
        return;
    }

    public function saveSettings()
    {
    }   
}

class U2fSettingsForm extends Form
{
    public function id()
    {
        return 'u2fsettings';
    }

    public function formClass()
    {
        return 'form_settings';
    }

    public function action()
    {
        return common_local_url('u2fsettings');
    }

    public function formData()
    {
        $this->out->elementStart(
            'fieldset',
            array('id' => 'settings_u2f')
        );
        $this->out->elementStart('ul', 'form_data');

        $this->li();
        $this->out->checkbox(
            'enabled', 
            _m('Enable U2F authentication'),
            (bool) $this->value('u2f', 'enabled'),
            _m("Enable two-factor authentication with U2F.")
        );
        $this->unli();

        $this->out->elementEnd('ul');
        $this->out->elementEnd('fieldset');
    }

    public function formActions()
    {
        $this->out->submit('submit', _m('BUTTON', 'Save'), 'submit', null,
            _m('Save the U2F settings.'));
    }
}
