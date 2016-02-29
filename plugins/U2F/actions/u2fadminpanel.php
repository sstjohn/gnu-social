<?php

if (!defined('STATUSNET')) {
    exit(1);
}

class U2fadminpanelAction extends AdminPanelAction
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
        $form  = new U2fAdminPanelForm($this);
        $form->show();
        return;
    }

    public function saveSettings()
    {
        return;
    }   
}

clas U2fAdminPanelForm extends AdminForm
{
    public function id()
    {
        return 'u2fadminpanel';
    }

    public function formClass()
    {
        return 'form_settings';
    }

    public function action()
    {
        return common_local_url('u2fadminpanel');
    }

    public function formData()
    {
        $this->out->elementStart(
            'fieldset',
            array('id' => 'settings_u2f')
        );
        $this->out->element('legend', null, _m('U2F settings'));
        $this->out->elementStart('ul', 'form_data');

        $this->li();
        $this->out->element('p', 'form_guide', _m('Note: this is just a test.'));
        $this->unli();

        $this->out->elementEnd('ul');
        $this->out->elementEnd('fieldset');
    }

    public function formActions()
    {
        return;
    }
}
