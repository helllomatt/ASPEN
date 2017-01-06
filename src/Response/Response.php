<?php

namespace ASPEN;

class Response {
    private $data = [];

    public function __construct() {
        $this->data['status'] = 'fail';
        $this->data['data'] = [];
    }

    public function status($is) {
        if (!in_array($is, ['success', 'fail', 'error'])) $is = 'error';
        $this->data['status'] = $is;
    }

    public function add($key, $value) {
        $this->data['data'][$key] = $value;
    }

    public function fail() {
        $this->status('fail');
        $this->respond();
    }

    public function success() {
        $this->status('success');
        $this->respond();
    }

    public function error($message = '', $code = 0, $includeData = false) {
        $this->status('error');
        if ($code != 0) $this->data['code'] = $code;
        if (!$includeData) unset($this->data['data']);
        $this->data['message'] = $message;
        $this->respond();
    }

    public function respond() {
        header('Content-Type: application/json');
        echo json_encode($this->data);
    }
}
