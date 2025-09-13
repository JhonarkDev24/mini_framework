<?php

namespace App\Controller;

class TestController {
    public function index () {
        // return jsonResponse(['Title' => 'My Little Framework']);
        return view('views.Welcome');
    }
}