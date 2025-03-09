<?php

namespace Dragonite\Common\Helpers\Concerns;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait Reader
{
    /**
     * @return \list<list<(string | null)>>
     */
    public function readCSV($filename, $delimiter = ',', $enclosure = '"', $skipHeader = false): ?array
    {
        $file = fopen($filename, 'r');
        $data = [];

        $i = 0;
        while (($row = fgetcsv($file, 0, $delimiter, $enclosure)) !== false) {
            if ($skipHeader && $i == 0) {
                continue;
            }

            $data[] = $row;
            $i++;
        }

        fclose($file);

        return $data;
    }

    public function extractSource($path): ?string
    {
        $extractedPath = storage_path('app/tmp');
        $file = collect(explode('/', $path))->last();
        // Create the temporary storage directory if it doesn't exist
        if (! Storage::exists('tmp')) {
            Storage::makeDirectory('tmp');
        }

        // Build the command to extract the file
        $command = "tar -xvf {$path} -C {$extractedPath}";
        Process::run($command);

        return $extractedPath.'/'.Str::of($file)->replace('.tar.xz', '.csv');
    }
}
