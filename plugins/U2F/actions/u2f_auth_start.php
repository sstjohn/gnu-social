<?php

if (!defined('STATUSNET')) {
    exit(1);
}


class U2f_auth_startAction extends FormAction
{
    public function title()
    {
        return _m('TITLE', 'U2F');
    }

    public function getInstructions()
    {
        return _m('U2F authentication');
    }

    public function showContent()
    {
        $form  = new U2f_auth_startForm($this);
        $form->show();
        return;
    }

    public function showPrimaryNav() { }
    public function showNoticeForm() { }
    public function showLocalNav() { }
    public function showAside() { }
}

class U2f_auth_startForm extends Form
{
    public function id()
    {
        return 'u2f_auth_form';
    }

    public function action()
    {
        return common_local_url('u2f_auth_finish');
    }

    public function formClass()
    {
        return 'form';
    }

    public function formData()
    {
        $u2f = new u2flib_server\U2F("https://" . $_SERVER['HTTP_HOST']);
        $script = <<<_END_OF_SCRIPT_

u2f.sign(%s,
    function(deviceResponse) {
      document.getElementById('response-input').value = JSON.stringify(deviceResponse);
      document.getElementById('u2f_auth_form').submit();
    }
);
_END_OF_SCRIPT_;


        $uid = common_current_user()->id; 
        $sign_requests = $u2f->getAuthenticateData(User_u2f_device::get_user_devices($uid));
        $sign_requests_msg = json_encode($sign_requests);
        $_SESSION['u2f-auth-data'] = $sign_requests_msg;

        $this->inlineScript(sprintf(
            $script,
            $sign_requests_msg
        ));

        $this->out->element('p', 'form_guide', 'Activate U2F device to continue...');
        $this->out->hidden('response-input', '');
    }

    public function formActions()
    {
        $this->out->submit('receive-response', "submit", "hidden");
    }
}
