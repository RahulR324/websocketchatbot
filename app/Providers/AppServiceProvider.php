<?php

namespace App\Providers;

use App\Services\FallbackAiService;
use App\Services\GeminiService;
use App\Services\GroqService;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Laravel\Reverb\Application;
use Laravel\Reverb\Contracts\ApplicationProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(FallbackAiService::class, function ($app) {
            return new FallbackAiService(
                $app->make(GroqService::class),
                $app->make(GeminiService::class),
            );
        });

        // Ensure Reverb receives int/array types (env values can arrive as strings).
        $this->app->extend(ApplicationProvider::class, function ($provider, $app) {
            return new class($provider) implements ApplicationProvider
            {
                public function __construct(private ApplicationProvider $inner) {}

                public function all(): Collection
                {
                    return $this->inner->all()->map(fn ($a) => $this->normalize($a));
                }

                public function findById(string $id): Application
                {
                    return $this->normalize($this->inner->findById($id));
                }

                public function findByKey(string $key): Application
                {
                    return $this->normalize($this->inner->findByKey($key));
                }

                public function find(string $key, mixed $value): Application
                {
                    return $this->normalize($this->inner->find($key, $value));
                }

                private function normalize(Application $app): Application
                {
                    $d = $app->toArray();
                    $rl = $app->rateLimiting();
                    if (is_array($rl)) {
                        $rl['enabled'] = filter_var($rl['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
                        $rl['max_attempts'] = (int) ($rl['max_attempts'] ?? 60);
                        $rl['decay_seconds'] = (int) ($rl['decay_seconds'] ?? 60);
                        $rl['terminate_on_limit'] = filter_var($rl['terminate_on_limit'] ?? false, FILTER_VALIDATE_BOOLEAN);
                    }
                    $max = $app->maxConnections();

                    return new Application(
                        (string) $app->id(),
                        (string) $app->key(),
                        (string) $app->secret(),
                        (int) $app->pingInterval(),
                        (int) $app->activityTimeout(),
                        is_array($app->allowedOrigins()) ? $app->allowedOrigins() : ['*'],
                        (int) $app->maxMessageSize(),
                        $max !== null && $max !== '' ? (int) $max : null,
                        $d['accept_client_events_from'] ?? 'members',
                        $rl,
                        is_array($d['options'] ?? null) ? $d['options'] : [],
                    );
                }
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
