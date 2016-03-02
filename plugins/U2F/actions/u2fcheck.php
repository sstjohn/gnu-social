<?php

if (!defined('STATUSNET')) {
    exit(1);
}


class U2fcheckAction extends FormAction
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
        $form  = new U2fcheckForm($this);
        $form->show();
        return;
    }

    public function showPrimaryNav() { }
    public function showNoticeForm() { }
    public function showLocalNav() { }
    public function showAside() { }
}

class U2fcheckForm extends Form
{
    public function id()
    {
        return 'u2fcheckform';
    }

    public function action()
    {
        return common_local_url('u2fcheckres');
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
      document.getElementById('u2fcheckform').submit();
    }
);
_END_OF_SCRIPT_;


        $uid = common_current_user()->id; 
        $sign_requests = $u2f->getAuthenticateData(User_u2f_device::get_user_devices($uid));
        $sign_requests_msg = json_encode($sign_requests);
        User_u2f_data::set_user_challenge($uid, $sign_requests_msg);

        $this->inlineScript(sprintf(
            $script,
            $sign_requests_msg
        ));

        $this->out->elementStart(
            'fieldset',
            array('id' => 'login_u2f_check')
        );
        $this->out->element('p', 'form_guide', 'Activate U2F device to continue...');
        $this->out->hidden('response-input', '');
        $this->out->elementEnd('fieldset');
    }

    public function formActions()
    {
        $this->out->submit('receive-response', "submit", "hidden");
    }
}
