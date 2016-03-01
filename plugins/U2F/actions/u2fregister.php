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
        return 'u2fregistrationform';
    }

    public function formClass()
    {
        return 'form_settings';
    }

    public function action()
    {
        return common_local_url('u2fregresponse');
    }

    public function formData()
    {
        $u2f = new u2flib_server\U2F("https://" . $_SERVER['HTTP_HOST']);
        $script = <<<_END_OF_SCRIPT_

var challenge = %s;
u2f.register([challenge], [],
    function(deviceResponse) {
      document.getElementById('response-input').value = JSON.stringify(deviceResponse);
      document.getElementById('u2fregistrationform').submit();
    }
);
_END_OF_SCRIPT_;


        $challenge = $u2f->getRegisterData();
        $challenge_msg = json_encode($challenge[0]) . "\n";
        $uid = common_current_user()->id; 
        User_u2f_data::set_user_challenge($uid, $challenge_msg);

        $this->inlineScript(sprintf(
            $script,
            $challenge_msg
        ));

        $this->out->elementStart(
            'fieldset',
            array('id' => 'settings_u2f_register')
        );
        $this->out->element('p', 'form_guide', $challenge_msg);
        $this->out->hidden('response-input', '');
        $this->out->elementEnd('fieldset');
    }

    public function formActions()
    {
        $this->out->submit('receive-response', _m('BUTTON', 'blah', 'hidden'));
    }
}
