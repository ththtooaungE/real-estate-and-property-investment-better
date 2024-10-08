<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class FileService
{
    private $disk = 'public';

    // public function storePhoto($data, $location): string
    // {
    //     $path = "$location/" . uniqid(now()->format('Y_m_d') . '_') . '.png';
    //     Storage::disk($this->disk)->put($path, base64_decode($data));

    //     return $path;
    // }

    public function storePhoto($data, $location): string
    {
        // Ensure the directory exists
        if (!file_exists(public_path($location))) {
            mkdir(public_path($location), 0755, true);
        }

        $filename = uniqid(now()->format('Y_m_d') . '_') . '.png';
        $path = "$location/" . $filename;

        file_put_contents(public_path($path), base64_decode($data));

        return $path;
    }


    // public function deletePhoto($location)
    // {
    //     Storage::disk($this->disk)->delete($location);
    // }

    public function deletePhoto($location)
    {
        $path = public_path($location);
        
        if (file_exists($path)) {
            unlink($path); // Delete the file
        }
    }

}
