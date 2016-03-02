<?php

if (!defined('STATUSNET')) {
    exit(1);
}


class U2f_auth_finishAction extends Action
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
        $u2f = new u2flib_server\U2F("https://" . $_SERVER['HTTP_HOST']);
        $uid = common_current_user()->id; 

        $response_msg = $this->arg('response-input');
        $response = json_decode($response_msg);

        $requests_msg = $_SESSION['u2f-auth-data'];
        $requests = json_decode($requests_msg);

        $registrations = User_u2f_device::get_user_devices($uid);

        $this->element('p', array('class' => 'result'), 'authentication result: ');
        try {
            $result = $u2f->doAuthenticate($requests, $registrations, $response);
            $this->element('p', array('class' => 'result'), 'success!');
        } catch (Exception $e) {
            $this->element('p', array('class' => 'result'), 'failed!');
        }

        return;
    }

    public function handle($args)
    {
        parent::handle($args);

        $this->showPage();
    }
}
