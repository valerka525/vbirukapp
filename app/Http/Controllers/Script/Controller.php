<?php

namespace App\Http\Controllers\Script;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Support\Facades\File;

class Controller extends BaseController {
    /**
     * Get File
     *
     * @param $file_path
     * @return string|null
     */
    protected function getFile($file_path)
    {
        try {
            $file = File::get($file_path);
        } catch (\Exception $exception) {
            $file = null;
        }

        return $file;
    }

    /**
     * Get content
     *
     * @param string $file
     * @param array $data
     * @return string
     */
    protected function getContent(string $file, array $data)
    {
        if (!empty($file) && !empty($data)) {
            foreach ($data as $key => $value) {
                $file = $this->replaceVariable($key, $value, $file);
            }
        }

        return $file;
    }

    /**
     * Replace variable
     *
     * @param $variable_name
     * @param $value
     * @param $string
     * @return string|string[]
     */
    protected function replaceVariable($variable_name, $value, $string)
    {
        return str_replace("{{ $variable_name }}", $value, $string );
    }
}
