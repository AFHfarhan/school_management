<?php

namespace Tests\Feature;

use App\Models\Component;
use App\Services\ComponentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComponentServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_can_be_resolved_from_container(): void
    {
        $service = $this->app->make(ComponentService::class);

        $this->assertInstanceOf(ComponentService::class, $service);
    }

    public function test_set_creates_component_and_get_returns_it(): void
    {
        $service = $this->app->make(ComponentService::class);

        $component = $service->set(Component::CODE_SCHOOL_PROFILE, [
            'name' => 'School Profile',
            'category' => 'default',
            'structure' => 'school',
            'description' => 'Core school settings',
            'data' => ['school_name' => 'Example School'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->assertInstanceOf(Component::class, $component);
        $this->assertTrue($service->exists(Component::CODE_SCHOOL_PROFILE));
        $this->assertSame('Example School', $service->getValue(Component::CODE_SCHOOL_PROFILE, 'school_name'));
        $this->assertSame('School Profile', $service->get(Component::CODE_SCHOOL_PROFILE)?->name);
    }
}
