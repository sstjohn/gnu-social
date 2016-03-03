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

        $user = common_current_user(); 
        $during_login = false;
        if (empty($user)) {
            $user = $_SESSION['auth-stage1-user'];
            unset($_SESSION['auth-stage1-user']);
            $during_login = true;
        }
        if (empty($user)) {
            throw new Exception('no user context');
        }
        
        $response_msg = $this->arg('response-input');
        $response = json_decode($response_msg);

        $requests_msg = $_SESSION['u2f-auth-data'];
        $requests = json_decode($requests_msg);
        unset($_SESSION['u2f-auth-data']);

        $registrations = User_u2f_device::get_user_devices($user->id);

        try {
            $updated = $u2f->doAuthenticate($requests, $registrations, $response);
            User_u2f_device::update_counter($updated);
            $result = 'success';
            $this->element('p', array('class' => 'result'), 'success!');
            if ($during_login) {
                $_SESSION['auth-stage2-done'] = true;
                common_set_user($user);
                common_redirect(common_get_returnto());
            }
        } catch (Exception $e) {
            $result = 'failed';
        }
        
        $this->element('p', null, sprintf('authentication result: %s', $result));

        return;
    }

    public function handle($args)
    {
        parent::handle($args);

        $this->showPage();
    }
}
