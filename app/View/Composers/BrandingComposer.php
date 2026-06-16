<?php

namespace App\View\Composers;

use App\Services\ComponentService;
use Illuminate\View\View;

class BrandingComposer
{
    private ComponentService $components;

    public function __construct(ComponentService $components)
    {
        $this->components = $components;
    }

    /**
     * Bind branding data into the view.
     */
    public function compose(View $view): void
    {
        $branding = [
            'school_name' => $this->components->getSchoolProfileValue('school_name',config('app.name', 'School Management System')),
            'school_tagline' => $this->components->getSchoolProfileValue('school_tagline', 'Quality Education'),
            'logo' => $this->components->getBrandingValue('logo', asset('global_assets/img/logo_sman_sukamakmur.ico')),
            'favicon' => $this->components->getBrandingValue('favicon', asset('global_assets/img/logo_sman_sukamakmur.ico')),
            'address' => $this->components->getSchoolProfileValue('address',''),
            'phone' => $this->components->getSchoolProfileValue('phone', '0000000000'),
            'email' => $this->components->getSchoolProfileValue('email',''),
        ];

        // Expose under a single namespace to avoid polluting the view scope.
        $view->with('branding', $branding);
    }
}
