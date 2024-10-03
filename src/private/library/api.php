<?php

namespace AS\infra;

include_once('page.php');

abstract class api extends page {
    protected $response = [];

    public function run() {
        if (!$this->is_auth()) {
            echo json_encode(['error' => 'You are not authorized to access this page.']);
        }
        $this->request();
        echo json_encode($this->response);
    }

    protected function validate_request() {
        return true;
    }
}