<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'conference_id',
        'country_article_number',
        'author_id',
        'author_name',
        'author_affiliation',
        'co_authors',
        'title',
        'abstract',
        'keywords',
        'references',
        'content',
        'pdf_path',
        'content_path',
        'formatted_pdf_path',
        'page_count',
        'page_range',
        'order_number',
        'status',
        'article_link',
        'published_at',
    ];

    /**
     * Muallif ismini olish - qo'lda kiritilgan yoki user nomidan
     */
    public function getAuthorDisplayNameAttribute(): string
    {
        if ($this->author_name) {
            return $this->author_name;
        }
        return $this->author?->name ?? 'Noma\'lum muallif';
    }

    /**
     * Muallif tashkilotini olish
     */
    public function getAuthorAffiliationDisplayAttribute(): string
    {
        return $this->author_affiliation ?? '';
    }

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Maqola tegishli bo'lgan konferensiya
     */
    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class);
    }

    /**
     * Maqola muallifi
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Maqolaga tegishli sertifikat
     */
    public function certificate(): HasOne
    {
        return $this->hasOne(Certificate::class);
    }

    /**
     * Maqola havolasini generatsiya qilish
     */
    public function generateLink(): string
    {
        $countryName = strtolower(str_replace(' ', '-', $this->conference->country->name_en ?? $this->conference->country->name ?? 'international'));
        $articleNum = $this->country_article_number ?? $this->id;
        $link = url("/article/{$countryName}/{$articleNum}");
        $this->update(['article_link' => $link]);
        return $link;
    }

    /**
     * Get article standard URL
     */
    public function getUrlAttribute(): string
    {
        if ($this->article_link) {
            return $this->article_link;
        }
        $countryName = strtolower(str_replace(' ', '-', $this->conference->country->name_en ?? $this->conference->country->name ?? 'international'));
        $articleNum = $this->country_article_number ?? $this->id;
        return route('article.show', ['countrySlug' => $countryName, 'articleNumber' => $articleNum]);
    }

    /**
     * Sahifalar diapazoni haqida ma'lumot
     */
    public function getPageRangeInfoAttribute(): string
    {
        return "{$this->page_range} ({$this->page_count} bet)";
    }

    /**
     * Nashr etilgan maqolalar
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Kutilayotgan maqolalar
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
