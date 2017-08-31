<?php

namespace machour\yii2\notifications\controllers;

use machour\yii2\notifications\models\Notification;
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
        Yii::$app->response->format = Response::FORMAT_JSON;

        $this->user_id = $this->module->userId;
        $this->notificationClass = $this->module->notificationClass;
        parent::init();
    }

    /**
     * Poll action
     *
     * @param int $seen Whether to show already seen notifications
     * @return array
     */
    public function actionPoll($seen = 0)
    {
        $seen = $seen ? 'true' : 'false';

        /** @var Notification $class */
        $class = $this->notificationClass;
        $models = $class::find()
            ->where(['user_id' => $this->user_id])
            ->andWhere(['or', "seen=$seen", 'flashed=false'])
            ->orderBy('created_at DESC')
            ->all();

        $results = [];

        foreach ($models as $model) {

            // give user a chance to parse the date as needed
            $date = \DateTime::createFromFormat($this->module->dbDateFormat, $model->created_at)
                ->format('Y-m-d H:i:s');

            /** @var Notification $model */
            $results[] = [
                'id' => $model->id,
                'type' => $model->type,
                'title' => $model->getTitle(),
                'description' => $model->getDescription(),
                'url' => Url::to(['notifications/rnr', 'id' => $model->id]),
                'key' => $model->key,
                'flashed' => $model->flashed,
                'date' => $date,
            ];
        }
        return $results;
    }

    /**
     * Marks a notification as read and redirects the user to the final route
     *
     * @param int $id The notification id
     * @return Response
     * @throws HttpException Throws an exception if the notification is not
     *         found, or if it don't belongs to the logged in user
     */
    public function actionRnr($id)
    {
        $notification = $this->actionRead($id);
        return $this->redirect(Url::to($notification->getRoute()));
    }

    /**
     * Marks a notification as read
     *
     * @param int $id The notification id
     * @return Notification The updated notification record
     * @throws HttpException Throws an exception if the notification is not
     *         found, or if it don't belongs to the logged in user
     */
    public function actionRead($id)
    {
        $notification = $this->getNotification($id);

        $notification->seen = 1;
        $notification->save();

        return $notification;
    }

    /**
     * Marks all notification as read
     *
     * @throws HttpException Throws an exception if the notification is not
     *         found, or if it don't belongs to the logged in user
     */
    public function actionReadAll()
    {
        $notificationsIds = Yii::$app->request->post('ids', []);

        foreach ($notificationsIds as $id) {
            $notification = $this->getNotification($id);

            $notification->seen = 1;
            $notification->save();
        }

        return true;
    }

    /**
     * Delete all notifications
     *
     * @throws HttpException Throws an exception if the notification is not
     *         found, or if it don't belongs to the logged in user
     */
    public function actionDeleteAll()
    {
        $notificationsIds = Yii::$app->request->post('ids', []);

        foreach ($notificationsIds as $id) {
            $notification = $this->getNotification($id);

            $notification->delete();
        }

        return true;
    }

    /**
     * Deletes a notification
     *
     * @param int $id The notification id
     * @return int|false Returns 1 if the notification was deleted, FALSE otherwise
     * @throws HttpException Throws an exception if the notification is not
     *         found, or if it don't belongs to the logged in user
     */
    public function actionDelete($id)
    {
        $notification = $this->getNotification($id);
        return $notification->delete();
    }

    public function actionFlash($id)
    {
        $notification = $this->getNotification($id);

        $notification->flashed = 1;
        $notification->save();

        return $notification;
    }


    /**
     * Gets a notification by id
     *
     * @param int $id The notification id
     * @return Notification
     * @throws HttpException Throws an exception if the notification is not
     *         found, or if it don't belongs to the logged in user
     */
    private function getNotification($id)
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

        return $notification;
    }
}
