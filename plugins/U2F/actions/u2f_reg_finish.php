<?php

if (!defined('STATUSNET')) {
    exit(1);
}


class U2f_reg_finishAction extends SettingsAction
{
    public function title()
    {
        return _m('TITLE', 'U2F');
    }

    public function getInstructions()
    {
        return _m('U2F registration response');
    }
}

class U2f_reg_finishForm extends Form
{
    public function id()
    {
        return 'u2f_reg_result';
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
        $u2f = new u2flib_server\U2F("https://" . $_SERVER['HTTP_HOST']);

        $uid = common_current_user()->id;
        $challenge_msg = $_SESSION['u2f-reg-data'];
        $challenge = json_decode($challenge_msg);

        $response_msg = $this->arg("response-input");
        $response = json_decode($response_msg);

        try {
            $result = $u2f->doRegister($challenge, $response);
            User_u2f_device::add_user_device($uid, $result);
            $msg = 'success';
        }
        catch (Exception $e) {
            $msg = 'failure';
        }

        $this->out->element('p', 'form_guide', sprintf('registration result: %s', $msg));
    }

    public function formActions()
    {
        $this->out->submit('done', _m('BUTTON', 'OK'));
    }
}
