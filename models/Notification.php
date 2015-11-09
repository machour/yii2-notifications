<?php

namespace machour\yii2\notifications\models;

use machour\yii2\notifications\NotificationsModule;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "notification".
 *
 * @property integer $id
 * @property integer $key_id
 * @property string $key
 * @property boolean $seen
 * @property string $created_at
 * @property integer $user_id
 */
abstract class Notification extends ActiveRecord
{

    const TYPE_DEFAULT = 'default';
    const TYPE_ERROR   = 'error';
    const TYPE_WARNING = 'warning';
    const TYPE_SUCCESS = 'success';

    public static $types = [
        self::TYPE_WARNING,
        self::TYPE_DEFAULT,
        self::TYPE_ERROR,
        self::TYPE_SUCCESS,
    ];

    abstract public function getTitle();

    abstract public function getDescription();

    abstract public function getRoute();

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'user_id', 'key', 'created_at'], 'required'],
            [['id', 'key_id', 'created_at'], 'safe'],
            [['key_id', 'user_id'], 'integer'],
        ];
    }

    /**
     * Creates a notification
     *
     * @param string $key
     * @param integer $user_id The user id that will get the notification
     * @param integer $key_id The foreign instance id
     * @param string $type
     * @return bool Returns TRUE on success, FALSE on failure
     * @throws \Exception
     */
    public static function notify($key, $user_id, $key_id = null, $type = self::TYPE_DEFAULT)
    {
        $class = static::class;
        return NotificationsModule::notify(new $class(), $key, $user_id, $key_id, $type);
    }

    /**
     * Creates a warning notification
     *
     * @param string $key
     * @param integer $user_id The user id that will get the notification
     * @param integer $key_id The notification key id
     * @return bool Returns TRUE on success, FALSE on failure
     */
    public static function warning($key, $user_id, $key_id = null)
    {
        return static::notify($key, $user_id, $key_id, self::TYPE_WARNING);
    }


    /**
     * Creates an error notification
     *
     * @param string $key
     * @param integer $user_id The user id that will get the notification
     * @param integer $key_id The notification key id
     * @return bool Returns TRUE on success, FALSE on failure
     */
    public static function error($key, $user_id, $key_id = null)
    {
        return static::notify($key, $user_id, $key_id, self::TYPE_ERROR);
    }


    /**
     * Creates a success notification
     *
     * @param string $key
     * @param integer $user_id The user id that will get the notification
     * @param integer $key_id The notification key id
     * @return bool Returns TRUE on success, FALSE on failure
     */
    public static function success($key, $user_id, $key_id = null)
    {
        return static::notify($key, $user_id, $key_id, self::TYPE_SUCCESS);
    }

}

