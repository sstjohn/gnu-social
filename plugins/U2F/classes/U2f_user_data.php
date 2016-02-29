<?php

if (!defined('STATUSNET')) {
    exit(1);
}

class U2f_user_data extends Managed_DataObject
{
    public $__table = 'u2f_user_data';
    public $user_id;

    public static function schemaDef()
    {
        return array(
            'fields' => array(
                'user_id' => array('type' => 'int', 'not null' => true),
            ),
            'primary key' => array('user_id'),
        );
    }
}
