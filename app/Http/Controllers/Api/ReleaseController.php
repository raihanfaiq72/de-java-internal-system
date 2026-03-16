<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ReleaseController extends Controller
{
    /**
     * Get all available app releases
     */
    public function index()
    {
        $releases = [];
        $basePath = public_path('app-release');
        
        // Check if directory exists
        if (!File::exists($basePath)) {
            return response()->json($releases);
        }

        // Get all directories in app-release folder
        $directories = File::directories($basePath);
        
        foreach ($directories as $directory) {
            $folderName = basename($directory);
            
            // Parse folder name (format: YYYYMMDD)
            if (preg_match('/^(\d{4})(\d{2})(\d{2})$/', $folderName, $matches)) {
                $year = $matches[1];
                $month = $matches[2];
                $day = $matches[3];
                
                // Create date object
                $date = \DateTime::createFromFormat('Y-m-d', "{$year}-{$month}-{$day}");
                
                if ($date) {
                    // Get APK files in this directory
                    $apkFiles = File::glob($directory . '/*.apk');
                    
                    foreach ($apkFiles as $apkFile) {
                        $fileName = basename($apkFile);
                        $filePath = 'app-release/' . $folderName . '/' . $fileName;
                        
                        // Get file size
                        $fileSizeBytes = File::size($apkFile);
                        $fileSize = $this->formatFileSize($fileSizeBytes);
                        
                        // Extract version from filename or use date
                        $version = $this->extractVersionFromFileName($fileName, $date);
                        
                        $releases[] = [
                            'version' => $version,
                            'date' => $date->format('Y-m-d'),
                            'fileName' => $fileName,
                            'filePath' => $filePath,
                            'downloadUrl' => url($filePath),
                            'fileSize' => $fileSize,
                            'fileSizeBytes' => $fileSizeBytes,
                            'folderName' => $folderName,
                            'releaseDate' => $date->format('d F Y'),
                            'timestamp' => $date->getTimestamp()
                        ];
                    }
                }
            }
        }
        
        // Sort by timestamp (newest first)
        usort($releases, function($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });
        
        return response()->json($releases);
    }
    
    /**
     * Get specific release details
     */
    public function show($folderName)
    {
        $basePath = public_path('app-release') . '/' . $folderName;
        
        if (!File::exists($basePath)) {
            return response()->json(['error' => 'Release not found'], 404);
        }
        
        // Parse folder name
        if (!preg_match('/^(\d{4})(\d{2})(\d{2})$/', $folderName, $matches)) {
            return response()->json(['error' => 'Invalid folder format'], 400);
        }
        
        $year = $matches[1];
        $month = $matches[2];
        $day = $matches[3];
        
        $date = \DateTime::createFromFormat('Y-m-d', "{$year}-{$month}-{$day}");
        
        if (!$date) {
            return response()->json(['error' => 'Invalid date'], 400);
        }
        
        // Get APK files
        $apkFiles = File::glob($basePath . '/*.apk');
        $releases = [];
        
        foreach ($apkFiles as $apkFile) {
            $fileName = basename($apkFile);
            $filePath = 'app-release/' . $folderName . '/' . $fileName;
            
            $fileSizeBytes = File::size($apkFile);
            $fileSize = $this->formatFileSize($fileSizeBytes);
            $version = $this->extractVersionFromFileName($fileName, $date);
            
            $releases[] = [
                'version' => $version,
                'date' => $date->format('Y-m-d'),
                'fileName' => $fileName,
                'filePath' => $filePath,
                'downloadUrl' => url($filePath),
                'fileSize' => $fileSize,
                'fileSizeBytes' => $fileSizeBytes,
                'folderName' => $folderName,
                'releaseDate' => $date->format('d F Y'),
                'timestamp' => $date->getTimestamp()
            ];
        }
        
        return response()->json([
            'folderName' => $folderName,
            'releaseDate' => $date->format('d F Y'),
            'releases' => $releases
        ]);
    }
    
    /**
     * Format file size to human readable format
     */
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
    
    /**
     * Extract version from filename or generate from date
     */
    private function extractVersionFromFileName($fileName, $date)
    {
        // Try to extract version from filename
        if (preg_match('/(\d+\.\d+\.\d+)/', $fileName, $matches)) {
            return $matches[1];
        }
        
        // Try to extract version from other patterns
        if (preg_match('/v?(\d+(?:\.\d+)*)/i', $fileName, $matches)) {
            return $matches[1];
        }
        
        // Generate version from date (format: v.YY.MM.DD)
        return 'v' . $date->format('y.m.d');
    }
}
