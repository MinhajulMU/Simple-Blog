<?php

namespace App\Helpers;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Route
{
    /**
     * Load routes from dir path
     *
     * @param string $dirPath
     * @return void
     */
    public static function loadFromDir(string $dirPath): void
    {
        // create iterator instance
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirPath));
        // loop iterator
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                require $file->getPathname();
            }
        }
    }
}
