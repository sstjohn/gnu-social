<?php

if (!defined('STATUSNET')) {
    exit(1);
}

class U2fregisterAction extends SettingsAction
{
    public function title()
    {
        return _m('TITLE', 'U2F');
    }

    public function getInstructions()
    {
        return _m('U2F registration');
    }

    public function showForm()
    {
        $form  = new U2fRegisterForm($this);
        $form->show();
        return;
    }

    public function saveSettings()
    {
    }   
}

class U2fRegisterForm extends Form
{
    public function id()
    {
        return 'u2fregister';
    }

    public function formClass()
    {
        return 'form_settings';
    }

    public function action()
    {
        return common_local_url('u2fregister');
    }

    public function formData()
    {
        $this->out->elementStart(
            'fieldset',
            array('id' => 'settings_u2f_register')
        );
        $this->out->elementStart('ul', 'form_data');

        $this->li();
        $this->out->element('p', 'form_guide', 'test test test');
        $this->unli();

        $this->out->elementEnd('ul');
        $this->out->elementEnd('fieldset');
    }

    public function formActions()
    {
    }
}
