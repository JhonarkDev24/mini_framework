<?php

function jsonResponse(array $response, int $code = 200) {
    header('Content-Type: application/json');
    http_response_code($code);
    echo json_encode($response);
}