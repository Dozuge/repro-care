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
        return $query->where('material_type', 'video')
            ->orWhereNotNull('video_url');
    }

    public function scopeQuizzes($query)
    {
        return $query->where('material_type', 'quiz');
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeHcwTraining($query)
    {
        return $query->where('category', 'hcw-training');
    }

    public function scopeWeekGuide($query, ?int $week = null)
    {
        $q = $query->whereNotNull('week_number');
        return $week ? $q->where('week_number', $week) : $q;
    }

    /**
     * Check if material has a playable video (MP4 file or streaming link).
     */
    public function isPlayableVideo(): bool
    {
        if ($this->material_type === 'video' || !empty($this->video_url)) {
            return true;
        }

        return $this->isDirectVideoFile();
    }

    /**
     * Check if the uploaded file is a direct video file (MP4, WEBM, MOV, AVI).
     */
    public function isDirectVideoFile(): bool
    {
        if (!$this->file) {
            return false;
        }

        $ext = strtolower(pathinfo($this->file, PATHINFO_EXTENSION));
        return in_array($ext, ['mp4', 'webm', 'mov', 'avi', 'm4v'], true);
    }

    /**
     * Get the YouTube / Vimeo embed URL from watch / share URLs.
     */
    public function getEmbedUrlAttribute(): ?string
    {
        if (!$this->video_url) {
            return null;
        }

        $url = trim($this->video_url);

        // YouTube watch URL or shorts or youtu.be
        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([\w-]+)/', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1] . '?rel=0&modestbranding=1&enablejsapi=1';
        }

        // Vimeo URL
        if (preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|)(\d+)/', $url, $m)) {
            return 'https://player.vimeo.com/video/' . $m[1];
        }

        return $url;
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
