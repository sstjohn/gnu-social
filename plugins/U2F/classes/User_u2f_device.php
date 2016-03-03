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
                'user_id' => array('type' => 'int', 'not null' => true, 'description' => 'user id'),
                'keyHandle' => array('type' => 'varchar', 'length'=>255, 'not null' => true, 'description' => 'key handle'),
                'publicKey' => array('type' => 'varchar', 'length'=>255, 'not null' => true, 'description' => 'registered device public key'),
                'certificate' => array('type' => 'text', 'not null' => true, 'description' => 'device attestation certificate'),
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
        $udev->user_id = $user_id;
        $udev->keyHandle = $registration_result->keyHandle;
        $udev->publicKey = $registration_result->publicKey;
        $udev->certificate = $registration_result->certificate;
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
        return User_u2f_device::multiGet('user_id', array($user_id))->fetchAll();
    }

    public static function update_counter($updated)
    {
        $udev = User_u2f_device::getKV('device_id', $updated->device_id);
        if (empty($udev)) {
            throw new Exception(sprintf(_m('No device with ID %d found.'), $updated->device_id));
        }

        $orig = clone($udev);
        $udev->counter = $updated->counter;
        $udev->update($orig);
    }       
}
