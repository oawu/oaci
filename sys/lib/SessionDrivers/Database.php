<?php defined ('BASEPATH') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2017, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class SessionDatabaseDriver extends SessionDriver implements SessionHandlerInterface {
	private $model;
	private $rowExists = false;

	public function __construct ($cookie) {
		parent::__construct ($cookie);

		if (!use_model ())
			Exceptions::showError ('[Session] SessionDatabaseDriver 錯誤，無法連線資料庫。');

		if (!($this->config['model'] && class_exists ($this->config['model'])))
			Exceptions::showError ('[Session] SessionDatabaseDriver 錯誤，找不到指定的 Model。Model：' . $this->config['model']);

		$this->model = $this->config['model'];
		ini_set ('session.save_path', $this->model);
	}

	public function open ($model, $name) {
		if (!($model && class_exists ($model)))
			Exceptions::showError ('[Session] SessionDatabaseDriver 錯誤，找不到指定的 Model。Model：' . $model);

		$this->model = $this->config['model'] = $model;
		return $this->succ ();
	}

	public function read ($session_id) {
		$model = $this->model;

		if ($this->getLock ($session_id) !== false) {
			$this->sessionId = $session_id;

			if (!$obj = $model::find ('first', array ('select' => 'data', 'conditions' => $this->config['match_ip'] ? array ('session_id = ? AND ip = ?', $session_id, $_SERVER['REMOTE_ADDR']) : array ('session_id = ?', $session_id)))) {
				$this->rowExists = false;
				$this->fingerprint = md5 ('');
				return '';
			}

			$result = $obj->data;
			$this->fingerprint = md5 ($result);
			$this->rowExists = true;
			return $result;
		}

		$this->fingerprint = md5 ('');
		return '';
	}

	public function write ($session_id, $session_data) {
		$model = $this->model;

		if ($session_id !== $this->sessionId) {
			if (!$this->releaseLock () || !$this->getLock ($session_id))
				return $this->fail();

			$this->rowExists = false;
			$this->sessionId = $session_id;
		} else if ($this->lock === false) {
			return $this->fail ();
		}

		if ($this->rowExists === false) {
			if (create_model ($model, array ('session_id' => $session_id, 'ip_address' => $_SERVER['REMOTE_ADDR'], 'timestamp' => time (), 'data' => $session_data))) {
				$this->fingerprint = md5 ($session_data);
				$this->rowExists = true;
				return $this->succ ();
			}

			return $this->fail ();
		}

		if (!$obj = $model::find ('first', array ('select' => 'id, data, timestamp', 'conditions' => $this->config['match_ip'] ? array ('session_id = ? AND ip = ?', $session_id, $_SERVER['REMOTE_ADDR']) : array ('session_id = ?', $session_id))))
			return $this->fail ();

		$obj->timestamp = time ();

		if ($this->fingerprint !== md5 ($session_data))
			$obj->data = $session_data;

		if ($obj->save ()) {
			$this->fingerprint = md5 ($session_data);
			return $this->succ ();
		}

		return $this->fail ();
	}

	public function close () {
		return ($this->lock && !$this->releaseLock ()) ? $this->fail() : $this->succ ();
	}

	public function destroy ($session_id) {
		$model = $this->model;

		if ($this->lock)
			if (!(($obj = $model::find ('first', array ('select' => 'id, data, timestamp', 'conditions' => $this->config['match_ip'] ? array ('session_id = ? AND ip = ?', $session_id, $_SERVER['REMOTE_ADDR']) : array ('session_id = ?', $session_id)))) && $obj->destroy ()))
				return $this->fail ();

		if ($this->close () === $this->succ ()) {
			$this->cookieDestroy ();
			return $this->succ ();
		}

		return $this->fail ();
	}

	public function gc ($maxlifetime) {
		$model = $this->model;
		return $model::delete_all (array('conditions' => array('timestamp < ?', time () - $maxlifetime))) ? $this->succ () : $this->fail ();
	}

	protected function getLock ($session_id) {
		$model = $this->model;
		$arg = md5 ($session_id . ($this->config['match_ip'] ? '_' . $_SERVER['REMOTE_ADDR'] : ''));

		if (($obj = $model::query ('SELECT GET_LOCK("' . $arg . '", 300) AS session_lock')->fetch ()) && $obj['session_lock']) {
			$this->lock = $arg;
			return true;
		}
		return false;
	}

	protected function releaseLock () {
		if (!$this->lock)
			return true;

		$model = $this->model;
		if (($obj = $model::query ('SELECT RELEASE_LOCK("' . $this->lock . '") AS session_lock')->fetch ()) && $obj['session_lock']) {
			$this->lock = false;
			return true;
		}

		return false;
	}
}
