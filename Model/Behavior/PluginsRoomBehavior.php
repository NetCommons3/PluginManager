<?php
/**
 * Plugin Behavior
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('ModelBehavior', 'Model');

/**
 * Plugin Behavior
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\PluginManager\Model\Behavior
 */
class PluginsRoomBehavior extends ModelBehavior {

/**
 * plugin_keyを基準にPluginsRoomの登録
 *
 * @param Model $model ビヘイビア呼び出し元モデル
 * @param array $roomIds ルームIDリスト
 * @param string $pluginKey プラグインKey
 * @return bool True on success
 * @throws InternalErrorException
 */
	public function savePluginsRoomsByPluginKey(Model $model, $roomIds, $pluginKey) {
		$model->loadModels([
			'PluginsRoom' => 'PluginManager.PluginsRoom',
		]);

		//トランザクションBegin
		$model->begin();

		try {
			$conditions = array(
				$model->alias . '.room_id NOT' => $roomIds,
				$model->alias . '.plugin_key' => $pluginKey,
			);
			if (! $model->deleteAll($conditions, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			//PluginsRoomテーブルの登録
			foreach ($roomIds as $roomId) {
				$this->__savePluginRoom($model, array(
					'room_id' => $roomId, 'plugin_key' => $pluginKey
				));
			}

			//トランザクションCommit
			$model->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$model->rollback($ex);
		}

		return true;
	}

/**
 * plugin_keyを基準にPluginsRoomの登録
 *
 * @param Model $model ビヘイビア呼び出し元モデル
 * @param int $roomId ルームID
 * @param array $pluginKeys プラグインKeyリスト
 * @return bool True on success
 * @throws InternalErrorException
 */
	public function savePluginsRoomsByRoomId(Model $model, $roomId, $pluginKeys) {
		$model->loadModels([
			'PluginsRoom' => 'PluginManager.PluginsRoom',
		]);

		//トランザクションBegin
		$model->begin();

		try {
			$this->__savePluginsRoomsByRoomId($model, $roomId, $pluginKeys);

			//トランザクションCommit
			$model->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$model->rollback($ex);
		}

		return true;
	}

/**
 * plugin_keyを基準にPluginsRoomの登録
 *
 * @param Model $model ビヘイビア呼び出し元モデル
 * @param int $roomId ルームID
 * @param array $pluginKeys プラグインKeyリスト
 * @return bool True on success
 * @throws InternalErrorException
 */
	public function savePluginsRoomsByPrivateRoomId(Model $model, $roomId, $pluginKeys) {
		$model->loadModels([
			'PluginsRoom' => 'PluginManager.PluginsRoom',
			'Room' => 'Rooms.Room',
		]);

		//トランザクションBegin
		$model->begin();

		$db = $model->getDataSource();

		try {
			$this->__savePluginsRoomsByRoomId($model, $roomId, $pluginKeys);

			//プライベートのIDを取得
			$rooms = $model->Room->children($roomId, false, 'Room.id', 'Room.rght');
			$roomIds = Hash::extract($rooms, '{n}.Room.id');

			//※処理をDELETE + INSERTにしているが、必要なところだけINSERTにした方が良い。
			//　ただ大量データを考慮すると、DELETE + INSERTの方が早い。
			$conditions = array(
				$model->alias . '.room_id' => $roomIds,
			);
			if (! $model->deleteAll($conditions, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			$pluginRoomTable = $model->PluginsRoom->tablePrefix . $model->PluginsRoom->table;
			$pluginRoomTableAs = $pluginRoomTable . ' AS ' . $model->PluginsRoom->alias;
			$roomTable = $model->Room->tablePrefix . $model->Room->table . ' AS ' . $model->Room->alias;

			//リクエストデータを使うため、escapeする
			$dbRoomId = $db->value($roomId, 'string');

			$values = array(
				'room_id' => $model->Room->escapeField('id'),
				'plugin_key' => $model->PluginsRoom->escapeField('plugin_key'),
				'created' => $model->PluginsRoom->escapeField('created'),
				'created_user' => $model->PluginsRoom->escapeField('created_user'),
				'modified' => $model->PluginsRoom->escapeField('modified'),
				'modified_user' => $model->PluginsRoom->escapeField('modified_user'),
			);

			$fields = implode(', ', array_keys($values));

			$sql = 'INSERT INTO ' . $pluginRoomTable . '(' . $fields . ') ' .
					'SELECT ' . implode(', ', $values) . ' ' .
					'FROM ' . $pluginRoomTableAs . ', ' . $roomTable . ' ' .
					'WHERE ' . $model->PluginsRoom->escapeField('room_id') . ' = ' . $dbRoomId . ' ' .
					'AND ' . $model->Room->escapeField('id') . ' IN ' . '(' . implode(',', $roomIds) . ')';

			//PluginsRoomテーブルの登録
			$model->PluginsRoom->query($sql);
			$result = $model->PluginsRoom->getAffectedRows() > 0;
			if (! $result) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			//トランザクションCommit
			$model->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$model->rollback($ex);
		}

		return true;
	}

/**
 * plugin_keyを基準にPluginsRoomの登録
 *
 * @param Model $model ビヘイビア呼び出し元モデル
 * @param int $roomId ルームID
 * @param array $pluginKeys プラグインKeyリスト
 * @return bool True on success
 * @throws InternalErrorException
 */
	private function __savePluginsRoomsByRoomId(Model $model, $roomId, $pluginKeys) {
		$conditions = array(
			$model->alias . '.room_id' => $roomId,
			$model->alias . '.plugin_key NOT' => $pluginKeys,
		);
		if (! $model->deleteAll($conditions, false)) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}

		//PluginsRoomテーブルの登録
		foreach ($pluginKeys as $pluginKey) {
			$this->__savePluginRoom($model, array(
				'room_id' => $roomId, 'plugin_key' => $pluginKey
			));
		}

		return true;
	}

/**
 * plugin_keyを基準にPluginsRoomの登録
 *
 * @param Model $model ビヘイビア呼び出し元モデル
 * @param array $data 登録データ
 * @return bool True on success
 * @throws InternalErrorException
 */
	private function __savePluginRoom(Model $model, $data) {
		$model->loadModels([
			'PluginsRoom' => 'PluginManager.PluginsRoom',
		]);

		$count = $model->find('count', array(
			'recursive' => -1, 'conditions' => $data,
		));
		if ($count > 0) {
			return true;
		}

		$model->create();
		if (! $model->save($data, false)) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}

		return true;
	}

}
