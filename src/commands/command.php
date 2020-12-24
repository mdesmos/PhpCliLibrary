<?php


namespace PhpCliLibrary\Commands;

class Command {

    public static function commandMap() {
        $file = file_get_contents( __DIR__ . '/commands.json' );
        return json_decode($file);
    }
}


