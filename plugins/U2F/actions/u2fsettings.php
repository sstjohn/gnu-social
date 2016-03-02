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

    protected function doPost()
    {
        if ($this->arg('newdev')) {
            common_redirect(common_local_url('u2fregister'), 307);
        } else if ($this->arg('deldev')) {
            User_u2f_device::del_user_device(common_current_user()->id, $this->arg('keyselection'));
        } else if ($this->arg('submit')) {
            return _('Settings saved.');
        }
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
            false ,
            _m("Enable two-factor authentication with U2F.")
        );
        $this->unli();

        $this->out->elementEnd('ul');
        $this->out->element('p', '', 'Registered Devices');
        $this->out->elementStart('ul');
        $devices = User_u2f_device::get_user_devices(common_current_user()->id);
        foreach ($devices as $d) {
            $this->li();
            $this->out->elementStart('div');
            $this->out->element('input', array('type' => 'radio', 'name' => 'keyselection', 'value' => $d->device_id));
            $this->out->element('span', '', $d->keyHandle);
            $this->out->elementEnd('div');
            $this->unli();
        }
        $this->out->elementEnd('ul');
        $this->out->elementEnd('fieldset');
    }

    public function formActions()
    {
        $this->out->submit('submit', _m('BUTTON', 'Save'));
        $this->out->submit('newdev', _m('BUTTON', 'Add U2F device'));
        $this->out->submit('deldev', _m('BUTTON', 'Remove U2F device'));
    }
}
