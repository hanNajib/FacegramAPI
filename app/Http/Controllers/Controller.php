<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public static function message($message, $status = 200) {
        return response()->json([
            "message" => $message
        ], $status);
    }

    public static function json($json = [], $status = 200) {
        return response()->json($json, $status);
    }
}
