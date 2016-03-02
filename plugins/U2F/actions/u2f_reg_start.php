<?php

if (!defined('STATUSNET')) {
    exit(1);
}


class U2f_reg_startAction extends SettingsAction
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
        $form  = new U2f_reg_startForm($this);
        $form->show();
        return;
    }

    public function saveSettings()
    {
    }   
}

class U2f_reg_startForm extends Form
{
    public function id()
    {
        return 'u2f_reg_form';
    }

    public function formClass()
    {
        return 'form_settings';
    }

    public function action()
    {
        return common_local_url('u2f_reg_finish');
    }

    public function formData()
    {
        $u2f = new u2flib_server\U2F("https://" . $_SERVER['HTTP_HOST']);
        $script = <<<_END_OF_SCRIPT_

var challenge = %s;
var devices = %s;
u2f.register([challenge], devices,
    function(deviceResponse) {
      document.getElementById('response-input').value = JSON.stringify(deviceResponse);
      document.getElementById('u2f_reg_form').submit();
    }
);
_END_OF_SCRIPT_;


        $uid = common_current_user()->id; 
        list($challenge, $sigs) = $u2f->getRegisterData(User_u2f_device::get_user_devices($uid));
        $challenge_msg = json_encode($challenge);
        $_SESSION['u2f-reg-data'] = $challenge_msg;
        $devices_msg = json_encode($sigs);

        $this->inlineScript(sprintf(
            $script,
            $challenge_msg,
            $devices_msg
        ));

        $this->out->element('p', 'form_guide', 'Activate U2F device to continue...');
        $this->out->hidden('response-input', '');
    }

    public function formActions()
    {
        $this->out->submit('receive-response', "submit", "hidden");
    }
}
