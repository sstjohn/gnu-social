<?php

if (!defined('STATUSNET')) {
    exit(1);
}

class User_u2f_data extends Managed_DataObject
{
    public $__table = 'user_u2f_data';
    public $user_id;
    public $required;

    public static function schemaDef()
    {
        return array(
            'fields' => array(
                'user_id' => array('type' => 'int', 'not null' => true, 'description' => 'user id'),
                'required' => array('type' => 'boolean', 'not null' => true, 'description' => 'u2f token required for login'),
                'outstanding_challenge' => array('type' => 'varchar(256)', 'description' => 'pending device challenge'),
            ),
            'primary key' => array('user_id'),
            'foreign keys' => array(
                'user_u2f_data_user_id_fkey' => array('user', array('user_id' => 'id'))
            ),
        );
    }

    public static function set_user_challenge($user_id, $challenge)
    {
        $udata = User_u2f_data::getKV('user_id', $user_id);
        if (empty($udata)) {
            $udata = new User_u2f_data();
            $udata->user_id = $user_id;
            $udata->required = false;
            $udata->outstanding_challenge = $challenge;
            $result = $udata->insert();
        } else {
            $orig = clone($udata);
            $udata->outstanding_challenge = $challenge;
            $result = $udata->update($orig);
        }
        if (!$result) {
            throw new Exception(sprintf(_m('Could not save device challenge data for user %d.'), $user_id));
        }
    }

    public static function get_user_challenge($user_id)
    {
        $udata = User_u2f_data::getKV('user_id', $user_id);
        if (!empty($udata) && !empty($udata->outstanding_challenge)) {
            return $udata->outstanding_challenge;
        }
        return '';
    }
}
