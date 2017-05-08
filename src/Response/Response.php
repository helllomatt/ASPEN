<?php

namespace ASPEN;

class Response {
    private $data = [];
    private $ignoreCount = false;
    private static $count = 0;

    public function __construct() {
        $this->data['status'] = 'fail';
        $this->data['data'] = [];
        return $this;
    }

    public function ignoreCount() {
        $this->ignoreCount = true;
        return $this;
    }

    public function getRaw() {
        return $this->data;
    }

    public function status($is) {
        if (!in_array($is, ['success', 'fail', 'error'])) $is = 'error';
        $this->data['status'] = $is;
        return $this;
    }

    public function getStatus() {
        return $this->data['status'];
    }

    public function add($key, $value) {
        $this->data['data'][$key] = $value;
        return $this;
    }

    public function getData() {
        return $this->data['data'];
    }

    public function fail() {
        if ($this->hasResponded()) return;
        $this->status('fail');
        $this->respond();
    }

    public function success() {
        if ($this->hasResponded()) return;
        $this->status('success');
        $this->respond();
    }

    public function error($message = '', $code = 0, $includeData = false) {
        if ($this->hasResponded()) return;
        $this->status('error');
        if ($code != 0) $this->data['code'] = $code;
        if (!$includeData) unset($this->data['data']);
        $this->data['message'] = $message;
        $this->respond();
    }

    public function respond() {
        if (!headers_sent()) header('Content-Type: application/json');
        echo json_encode($this->data);
        self::$count++;
    }

    public function hasResponded() {
        if ($this->ignoreCount) return false;
        return self::$count > 0;
    }
}
