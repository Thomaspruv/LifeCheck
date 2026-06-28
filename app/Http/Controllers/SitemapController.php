<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

class SitemapController extends Controller
{
    public function sitemap()
    {
        $routes = $this->getPublicRoutes();

        return response()
            ->view('sitemap', ['routes' => $routes])
            ->header('Content-Type', 'application/xml');
    }

    public function robots()
    {
        $appUrl = config('app.url');

        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "\n";
        $content .= "Sitemap: {$appUrl}/sitemap.xml\n";

        return response($content)
            ->header('Content-Type', 'text/plain');
    }

    private function getPublicRoutes(): array
    {
        $routes = [];
        $appUrl = config('app.url');

        // Définir manuellement les routes publiques à indexer
        $publicRoutes = [
            ['uri' => '/', 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['uri' => '/login', 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['uri' => '/register', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['uri' => '/forgot-password', 'priority' => '0.3', 'changefreq' => 'monthly'],
            ['uri' => '/dashboard', 'priority' => '0.8', 'changefreq' => 'daily'],
            ['uri' => '/insights', 'priority' => '0.7', 'changefreq' => 'weekly'],
            ['uri' => '/history', 'priority' => '0.6', 'changefreq' => 'weekly'],
            ['uri' => '/streaks', 'priority' => '0.5', 'changefreq' => 'weekly'],
            ['uri' => '/progression', 'priority' => '0.6', 'changefreq' => 'weekly'],
            ['uri' => '/challenges', 'priority' => '0.7', 'changefreq' => 'weekly'],
            ['uri' => '/goals', 'priority' => '0.7', 'changefreq' => 'weekly'],
            ['uri' => '/journal', 'priority' => '0.6', 'changefreq' => 'weekly'],
            ['uri' => '/timeline', 'priority' => '0.5', 'changefreq' => 'weekly'],
            ['uri' => '/breathing', 'priority' => '0.5', 'changefreq' => 'weekly'],
            ['uri' => '/settings', 'priority' => '0.4', 'changefreq' => 'monthly'],
            ['uri' => '/profile', 'priority' => '0.4', 'changefreq' => 'monthly'],
            ['uri' => '/export', 'priority' => '0.3', 'changefreq' => 'monthly'],
            ['uri' => '/tags', 'priority' => '0.4', 'changefreq' => 'weekly'],
            ['uri' => '/templates', 'priority' => '0.4', 'changefreq' => 'weekly'],
        ];

        foreach ($publicRoutes as $route) {
            $routes[] = [
                'loc' => $appUrl . $route['uri'],
                'priority' => $route['priority'],
                'changefreq' => $route['changefreq'],
            ];
        }

        return $routes;
    }
}
