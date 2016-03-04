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
            common_redirect(common_local_url('u2f_reg_start'), 307);
        } else if ($this->arg('deldev')) {
            User_u2f_device::del_user_device(common_current_user()->id, $this->arg('keyselection'));
        } else if ($this->arg('submit')) {
            User_u2f_data::set_device_requirement(common_current_user()->id, $this->boolean('u2f_required'));
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
        $uid = common_current_user()->id;
        $this->out->elementStart(
            'fieldset',
            array('id' => 'settings_u2f')
        );
        $this->out->elementStart('ul', 'form_data');

        $this->li();
        $this->out->checkbox(
            'u2f_required', 
            _m('Require U2F authentication'),
            User_u2f_data::get_device_requirement($uid) ,
            _m("Require two-factor authentication with U2F upon login.")
        );
        $this->unli();

        $this->out->elementEnd('ul');
        $this->out->element('p', '', 'Registered Devices');
        $this->out->elementStart('ul');
        $devices = User_u2f_device::get_user_devices($uid);
        foreach ($devices as $d) {
            $this->li();
            $this->out->elementStart('div');
            $this->out->element('input', array('type' => 'radio', 'name' => 'keyselection', 'value' => $d->device_id));
            $this->out->element('span', '', Certificate::parse_certificate($d->certificate)['subject']['CN']);
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
