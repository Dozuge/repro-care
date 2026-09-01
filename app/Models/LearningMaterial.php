<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;

class LearningMaterial extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'material_type',
        'link_url',
        'image',
        'file',
        'category',
        'video_url',
        'quiz_data',
        'week_number',
    ];

    protected $casts = [
        'quiz_data' => 'array',
    ];

    // Scopes
    public function scopeArticles($query)
    {
        return $query->where('material_type', 'article');
    }

    public function scopeLinks($query)
    {
        return $query->where('material_type', 'link');
    }

    public function scopeFiles($query)
    {
        return $query->where('material_type', 'file');
    }

    public function scopeVideos($query)
    {
        return $query->where('material_type', 'video');
    }

    public function scopeQuizzes($query)
    {
        return $query->where('material_type', 'quiz');
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeWeekGuide($query, ?int $week = null)
    {
        $q = $query->whereNotNull('week_number');
        return $week ? $q->where('week_number', $week) : $q;
    }

    /**
     * Get the YouTube embed URL from a YouTube watch URL
     */
    public function getEmbedUrlAttribute(): ?string
    {
        if (!$this->video_url) return null;
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([\w-]+)/', $this->video_url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }
        return $this->video_url;
    }

    public function getImageUrlAttribute(): ?string
    {
        $imageUrl = $this->resolvePublicAssetUrl($this->image);

        if ($imageUrl) {
            return $imageUrl;
        }

        if ($this->isPreviewableFile()) {
            return $this->resolvePublicAssetUrl($this->file);
        }

        return null;
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->resolvePublicAssetUrl($this->file);
    }

    public function isPreviewableFile(): bool
    {
        if (!$this->file) {
            return false;
        }

        return in_array(strtolower(pathinfo($this->file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
    }

    private function resolvePublicAssetUrl(?string $path): ?string
    {
        $normalizedPath = $this->normalizeStoredPath($path);

        if (!$normalizedPath) {
            return null;
        }

        $publicStoragePath = public_path('storage/' . $normalizedPath);
        if (File::exists($publicStoragePath)) {
            return asset('storage/' . $normalizedPath);
        }

        $storageAppPath = storage_path('app/public/' . $normalizedPath);
        if (File::exists($storageAppPath)) {
            File::ensureDirectoryExists(dirname($publicStoragePath));
            File::copy($storageAppPath, $publicStoragePath);

            return asset('storage/' . $normalizedPath);
        }

        $directPublicPath = public_path($normalizedPath);
        if (File::exists($directPublicPath)) {
            return asset($normalizedPath);
        }

        return asset('storage/' . $normalizedPath);
    }

    private function normalizeStoredPath(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $normalizedPath = ltrim(str_replace('\\', '/', $path), '/');

        foreach (['storage/app/public/', 'public/storage/', 'storage/'] as $prefix) {
            if (str_starts_with($normalizedPath, $prefix)) {
                $normalizedPath = substr($normalizedPath, strlen($prefix));
                break;
            }
        }

        return $normalizedPath;
    }
}
