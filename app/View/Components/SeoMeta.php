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

    public function render(): View|Closure|string
    {
        return view('components.seo-meta');
    }
}
