<?php

namespace ASPEN;

class Response {
    private $data = [];
    private $ignoreCount = false;
    private static $count = 0;
    private $rusage;

    /**
     * Defines the default response information
     *
     * @return ASPEN\Response
     */
    public function __construct() {
        $this->data['status'] = 'fail';
        $this->data['data'] = [];
        return $this;
    }

    /**
     * Calculates the exeuction information on this call
     *
     * @return array
     */
    public function getExecutionInfo() {
        $u = Config::get("usage");
        $n = microtime(true);

        $mbytes = memory_get_usage() - $u['memory'];
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($mbytes, 0);
        $pow = floor(($mbytes ? log($mbytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        $memory = round($mbytes, $precision).' '.$units[$pow];

        return [
            "time" => ($n - $u['time'])." seconds",
            "memory" => $memory
        ];
    }

    /**
     * Allows the response to show up even if any have shown already.
     *
     * @return ASPEN\Response
     */
    public function ignoreCount() {
        $this->ignoreCount = true;
        return $this;
    }

    /**
     * Returns the raw response data (unformatted as a response);
     *
     * @return array
     */
    public function getRaw() {
        return $this->data;
    }

    /**
     * Defines the status of the response
     *
     * @param  string $is
     * @return ASPEN\Response
     */
    public function status($is) {
        if (!in_array($is, ['success', 'fail', 'error'])) $is = 'error';
        $this->data['status'] = $is;
        return $this;
    }

    /**
     * Returns the status of the response
     *
     * @return string
     */
    public function getStatus() {
        return $this->data['status'];
    }

    /**
     * Adds data to the response
     *
     * @param string  $key
     * @param any  $value
     */
    public function add($key, $value) {
        $this->data['data'][$key] = $value;
        return $this;
    }

    /**
     * Returns the data of the response
     *
     * @return array
     */
    public function getData() {
        return $this->data['data'];
    }

    /**
     * Sets up the response information to fail, then responds as a failure
     *
     * @return void
     */
    public function fail() {
        if ($this->hasResponded()) return;
        $this->status('fail');
        $this->respond();
    }

    /**
     * Sets up the response information to succeed, the responds as success
     *
     * @return void
     */
    public function success() {
        if ($this->hasResponded()) return;
        $this->status('success');
        $this->respond();
    }

    /**
     * Sets up the resposne to error out, then errors out
     *
     * @param  string  $message
     * @param  integer $code
     * @param  boolean $includeData
     * @return void
     */
    public function error($message = '', $code = 0, $includeData = false) {
        if ($this->hasResponded()) return;
        $this->status('error');
        if ($code != 0) $this->data['code'] = $code;
        if (!$includeData) unset($this->data['data']);
        $this->data['message'] = $message;
        $this->respond();
    }

    /**
     * Responds by setting the header, outputting the data and counting up
     * to avoid multiple responses
     *
     * @return void
     */
    public function respond() {
        if (!headers_sent()) header('Content-Type: application/json');
        if (Config::get("respond-with-statistics", true)) {
            $this->data['execution-stats'] = $this->getExecutionInfo();
        }
        echo json_encode($this->data);
        self::$count++;
    }

    /**
     * Checks to see if the dev has responded already to avoid multiple
     * responses
     *
     * @return boolean
     */
    public function hasResponded() {
        if ($this->ignoreCount) return false;
        return self::$count > 0;
    }
}
