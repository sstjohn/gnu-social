<?php

if (!defined('STATUSNET')) {
    exit(1);
}

class User_u2f_device extends Managed_DataObject
{
    public $__table = 'user_u2f_device';
    public $device_id;
    public $device_data;
    public $user_id;
    public $counter;

    public static function schemaDef()
    {
        return array(
            'fields' => array(
                'device_id' => array('type' => 'serial', 'not null' => true, 'description' => 'primary key'),
                'key_handle' => array('type' => 'varchar', 'length'=>128, 'not null' => true, 'description' => 'key handle'),
                'user_id' => array('type' => 'int', 'not null' => true, 'description' => 'user id'),
                'counter' => array('type' => 'int', 'not null' => true, 'description' => 'device usage counter'),
            ),
            'primary key' => array('device_id'),
            'foreign keys' => array(
                'user_u2f_device_user_id_fkey' => array('user', array('user_id' => 'id'))
            ),
        );
    }

    public static function add_user_device($user_id, $registration_result)
    {
        $udev = new User_u2f_device();
        $udev->key_handle = $registration_result->keyHandle;
        $udev->user_id = $user_id;
        $udev->counter = $registration_result->counter;
        $result = $udev->insert();
        if (!$result) {
            throw new Exception(sprintf(_m('Unable to save device for user %d.'), $user_id));
        }
    }

    public static function del_user_device($user_id, $device_id)
    {
        $udev = User_u2f_device::getKV('device_id', $device_id);
        if (empty($udev) || $udev->user_id != $user_id) {
            throw new Exception(sprintf(_m('Specified device registered to user %d not found.'), $user_id));
        }
        $result = $udev->delete();
        if (!$result) {
            throw new Exception(sprintf(_m('Unable to delete device registered to user %d.'), $user_id));
        }
    }

    public static function get_user_devices($user_id)
    {
        $udevs = User_u2f_device::multiGet('user_id', array($user_id));
        if (empty($udevs)) {
            return null;
        }

        $device_datas = array();
        foreach ($udevs as $udev) {
            $device_datas[$udev->device_id] = $udev->device_data;
        }

        return $device_datas;
    }
}
