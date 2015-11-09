<?php

namespace machour\yii2\notifications\controllers;

use machour\yii2\notifications\models\Notification;
use machour\yii2\notifications\NotificationsModule;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;

class NotificationsController extends Controller
{
    /**
     * @var integer The current user id
     */
    private $user_id;

    /**
     * @var string The notification class
     */
    private $notificationClass;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->user_id = $this->module->userId;
        $this->notificationClass = $this->module->notificationClass;
        parent::init();
    }

    /**
     * Poll action
     *
     * @return array
     */
    public function actionPoll()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        /** @var Notification $class */
        $class = $this->notificationClass;
        $models = $class::find()->where(['user_id' => $this->user_id])->all();

        $results = [];

        foreach ($models as $model) {
            /** @var Notification $model */
            $results[] = [
                'id' => $model->id,
                'title' => $model->getTitle(),
                'description' => $model->getDescription(),
                'url' => Url::to($model->getRoute()),
                'key' => $model->key,
            ];
        }
        return $results;
    }

    /**
     * Marks a notification as read
     *
     * @param $id
     * @throws HttpException
     */
    public function actionRead($id)
    {
        /** @var Notification $notification */
        $class = $this->notificationClass;
        $notification = $class::findOne($id);
        if (!$notification) {
            throw new HttpException(404, "Unknown notification");
        }

        if ($notification->user_id != $this->user_id) {
            throw new HttpException(500, "Not your notification");
        }

        $notification->seen = 1;
        $notification->save();
    }
}