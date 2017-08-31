<?php

namespace machour\yii2\notifications\commands;

use yii\console\Controller;


class NotificationsController extends Controller
{

	/**
	 * Clean obsolete notifications
	 */
	public function actionClean()
	{
		$class = $this->module->notificationClass;

		// Delete all notifications seen or flashed
		$criteria = ['or', ['seen=1'], ['flashed=1']];

		// Delete old notification according to expiration time setting
		if ( $this->module->expirationTime > 0 ) {
			$criteria[] = ['<', 'created_at', time()-$this->module->expirationTime ];
		}

		$records_deleted = $class::deleteAll($criteria);

		echo "$records_deleted obsolete notifications removed\n";
	}
}
