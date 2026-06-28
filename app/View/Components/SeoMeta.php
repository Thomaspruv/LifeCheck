<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SeoMeta extends Component
{
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $image = null,
        public string $type = 'website',
        public ?string $canonical = null,
        public bool $noindex = false,
    ) {
        $this->title ??= config('app.name');
        $this->description ??= 'LifeCheck — Suivez votre bien-être quotidien : humeur, émotions, sommeil, énergie. Streaks, badges et tendances personnalisées.';
        $this->image ??= asset('images/og-default.png');
        $this->canonical ??= url()->current();
    }

    public function jsonLd(): string
    {
        return json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'WebApplication',
            'name' => config('app.name'),
            'url' => $this->canonical,
            'description' => $this->description,
            'applicationCategory' => 'LifestyleApplication',
            'operatingSystem' => 'Web',
            'browserRequirements' => 'Requires JavaScript',
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }

    public function render(): View|Closure|string
    {
        return view('components.seo-meta');
    }
}
