<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AppRelease extends Model
{
    use HasFactory;

    protected $fillable = [
        'version',
        'build_number',
        'description',
        'file_path',
        'file_name',
        'file_size',
        'file_hash',
        'platform',
        'status',
        'is_latest',
        'is_force_update',
        'release_date',
        'changelog',
        'download_count',
    ];

    protected $casts = [
        'release_date' => 'datetime',
        'changelog' => 'array',
        'is_latest' => 'boolean',
        'is_force_update' => 'boolean',
        'file_size' => 'integer',
        'download_count' => 'integer',
    ];

    /**
     * Get the formatted file size
     */
    public function getFormattedFileSizeAttribute()
    {
        $bytes = $this->file_size;
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
     * Get the download URL
     */
    public function getDownloadUrlAttribute()
    {
        return Storage::url($this->file_path);
    }

    /**
     * Get the platform icon
     */
    public function getPlatformIconAttribute()
    {
        return $this->platform === 'android' ? 'fa-android' : 'fa-apple';
    }

    /**
     * Get the platform color
     */
    public function getPlatformColorAttribute()
    {
        return $this->platform === 'android' ? 'success' : 'primary';
    }

    /**
     * Get the status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'draft' => 'warning',
            'published' => 'success',
            'archived' => 'secondary',
            default => 'secondary'
        };
    }

    /**
     * Get the status label
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'draft' => 'Draft',
            'published' => 'Published',
            'archived' => 'Archived',
            default => 'Unknown'
        };
    }

    /**
     * Scope to get only published releases
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope to get only latest releases
     */
    public function scopeLatest($query)
    {
        return $query->where('is_latest', true);
    }

    /**
     * Scope to get only Android releases
     */
    public function scopeAndroid($query)
    {
        return $query->where('platform', 'android');
    }

    /**
     * Scope to get only iOS releases
     */
    public function scopeIos($query)
    {
        return $query->where('platform', 'ios');
    }

    /**
     * Increment download count
     */
    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }

    /**
     * Store uploaded file and calculate hash
     */
    public static function storeFile($file, $platform = 'android')
    {
        $fileName = $file->getClientOriginalName();
        $fileHash = hash_file('sha256', $file->getPathname());
        $fileSize = $file->getSize();
        
        $storagePath = $file->store('releases/' . $platform, 'public');
        
        return [
            'file_name' => $fileName,
            'file_path' => $storagePath,
            'file_hash' => $fileHash,
            'file_size' => $fileSize,
        ];
    }

    /**
     * Check if file exists in storage
     */
    public function fileExists()
    {
        return Storage::disk('public')->exists($this->file_path);
    }

    /**
     * Delete file from storage
     */
    public function deleteFile()
    {
        if ($this->fileExists()) {
            return Storage::disk('public')->delete($this->file_path);
        }
        return false;
    }

    /**
     * Set this release as latest and unset others
     */
    public function setAsLatest()
    {
        // Unset other latest releases for this platform
        static::where('platform', $this->platform)
            ->where('is_latest', true)
            ->update(['is_latest' => false]);
        
        // Set this as latest
        $this->update(['is_latest' => true]);
    }
}
