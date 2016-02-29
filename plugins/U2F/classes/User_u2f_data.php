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
            ),
            'primary key' => array('user_id'),
            'foreign keys' => array(
                'user_u2f_data_user_id_fkey' => array('user', array('user_id' => 'id'))
            ),
        );
    }
}
