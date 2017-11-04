<?php

namespace App;

/**
 * A simple class to load stub files and replace variables.
 *
 * @category Core
 * @package  SecretSanta
 * @author   Jordan Kniest <contact@jkniest.de>
 * @license  MIT <opensource.org/licenses/MIT>
 * @link     https://jkniest.de
 */
class Stub
{
    /**
     * Load a given stub file and replace some variables in it. The file path is relative to the
     * 'app/Stubs' directory.
     *
     * @param string $file         The file path, relative to 'app/Stubs'
     * @param array  $replacements The replacements
     *
     * @return string
     */
    public static function load(string $file, array $replacements = [])
    {
        $content = file_get_contents(
            __DIR__ . "/Stubs/{$file}"
        );

        foreach ($replacements as $key => $value) {
            $content = str_replace("{{{$key}}}", $value, $content);
        }

        return $content;
    }
}
