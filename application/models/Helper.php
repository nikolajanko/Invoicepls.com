<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Helper extends CI_Model {

    public function json( $type, $message, $data = null)
    {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => $type,
            'message' => $message,
            'data' => $data
        ]);
        die();
    }
}
