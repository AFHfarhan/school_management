<?php

namespace App\Services;

use App\Models\Component;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

class ComponentService
{
    private const COMPONENT_CACHE_PREFIX = 'component:';
    private const ACTIVE_COMPONENTS_CACHE_KEY = 'components:active';

    /**
     * Retrieve a component by its unique code.
     *
     * @param string $code
     * @return Component|null
     */
    public function get(string $code): ?Component
    {
        $this->assertCode($code);

        return Cache::remember($this->cacheKey($code), now()->addMinutes(15), function () use ($code): ?Component {
            return Component::query()
                ->byCode($code)
                ->first();
        });
    }

    /**
     * Retrieve a specific configuration value from a component.
     *
     * @param string $code
     * @param string $key
     * @param mixed $default
     * @return mixed
     *
     * @throws InvalidArgumentException When the component does not exist.
     */
    public function getValue(string $code, string $key, mixed $default = null): mixed
    {
        $component = $this->get($code);

        if ($component === null) {
            throw new InvalidArgumentException("Component configuration '{$code}' was not found.");
        }

        return $component->getConfig($key, $default);
    }

    /**
     * Retrieve a school profile setting with a safe fallback.
     */
    public function getSchoolProfileValue(string $key, mixed $default = null): mixed
    {
        return $this->resolveValue(Component::CODE_SCHOOL_PROFILE, $key, $default);
    }

    /**
     * Retrieve a branding setting with a safe fallback.
     */
    public function getBrandingValue(string $key, mixed $default = null): mixed
    {
        return $this->resolveValue(Component::CODE_SCHOOL_BRANDING, $key, $default);
    }

    /**
     * Create or update a component configuration entry.
     *
     * @param string $code
     * @param array<string, mixed> $data
     * @return Component
     */
    public function set(string $code, array $data): Component
    {
        $this->assertCode($code);

        $configData = $data['data'] ?? $data;

        $component = Component::query()->updateOrCreate(
            ['code' => $code],
            [
                'name' => (string) ($data['name'] ?? ucfirst(str_replace('_', ' ', $code))),
                'structure' => $data['structure'] ?? null,
                'description' => $data['description'] ?? null,
                'category' => $data['category'] ?? 'default',
                'data' => is_array($configData) ? $configData : ['value' => $configData],
                'is_active' => (bool) ($data['is_active'] ?? true),
                'sort_order' => (int) ($data['sort_order'] ?? 0),
                'created_by' => $data['created_by'] ?? null,
                'updated_by' => $data['updated_by'] ?? null,
            ]
        );

        $this->clearComponentCache($code);

        return $component;
    }

    /**
     * Check whether a component exists for the given code.
     *
     * @param string $code
     * @return bool
     */
    public function exists(string $code): bool
    {
        return $this->get($code) !== null;
    }

    /**
     * Return all active components, ordered for UI and runtime consumption.
     *
     * @return Collection<int, Component>
     */
    public function allActive(): Collection
    {
        return Cache::remember(self::ACTIVE_COMPONENTS_CACHE_KEY, now()->addMinutes(15), function (): Collection {
            return Component::query()
                ->active()
                ->ordered()
                ->get();
        });
    }

    /**
     * Build the cache key for a component code.
     *
     * @param string $code
     * @return string
     */
    private function cacheKey(string $code): string
    {
        return self::COMPONENT_CACHE_PREFIX . $code;
    }

    private function resolveValue(string $code, string $key, mixed $default = null): mixed
    {
        try {
            return $this->getValue($code, $key, $default);
        } catch (InvalidArgumentException) {
            return $default;
        }
    }

    /**
     * Clear the cache entries for a component and the active list.
     *
     * @param string $code
     * @return void
     */
    private function clearComponentCache(string $code): void
    {
        Cache::forget($this->cacheKey($code));
        Cache::forget(self::ACTIVE_COMPONENTS_CACHE_KEY);
    }

    /**
     * Validate the component code.
     *
     * @param string $code
     * @return void
     *
     * @throws InvalidArgumentException When the code is empty.
     */
    private function assertCode(string $code): void
    {
        if (trim($code) === '') {
            throw new InvalidArgumentException('Component code cannot be empty.');
        }
    }

    //for clear cache that related to component, can be used in controller or command after update component data
    public function flush(string $code): void
    {
        $this->clearComponentCache($code);
    }
}
