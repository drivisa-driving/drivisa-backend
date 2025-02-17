<?php

namespace Modules\Setting\Services;

use Illuminate\Support\Facades\Storage;

class ParserService
{
    public static function parseToEnv(array $data)
    {
        if (!count($data)) {
            return;
        }

        $pattern = '/([^\=]*)\=[^\n]*/';

        $envFile = base_path() . '/.env';
        $lines = file($envFile);
        $newLines = [];

        foreach ($lines as $line) {
            preg_match($pattern, $line, $matches);


            if (!count($matches)) {
                $newLines[] = $line;
                continue;
            }

            if (trim($matches[1]) !== "SETTINGS") {
                $newLines[] = $line;
                continue;
            }


            $line = strtoupper(trim($matches[1])) . "=" . json_encode($data) . "\n";
            $newLines[] = $line;
        }

        $newContent = implode('', $newLines);
        Storage::put("./.env", $newContent);
    }
}