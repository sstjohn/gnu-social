<?php

if (!defined('STATUSNET')) {
    exit(1);
}


class U2fregresponseAction extends SettingsAction
{
    public function title()
    {
        return _m('TITLE', 'U2F');
    }

    public function getInstructions()
    {
        return _m('U2F registration response');
    }

    public function showForm()
    {
        $form  = new U2fregresponseForm($this);
        $form->show();
        return;
    }

}

class U2fregresponseForm extends Form
{
    public function id()
    {
        return 'u2fregresponse';
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
            array('id' => 'settings_u2f_regresponse')
        );

        $uid = common_current_user()->id;
        $challenge_msg = User_u2f_data::get_user_challenge($uid);

        $this->out->element('p', 'form_guide', $challenge_msg);
        $this->out->element('p', 'form_guide', $this->arg('response-input'));
        
        $this->out->elementEnd('fieldset');
    }

    public function formActions()
    {
        $this->out->submit('done', _m('BUTTON', 'OK'));
    }
}
