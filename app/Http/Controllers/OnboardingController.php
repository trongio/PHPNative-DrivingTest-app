<?php

namespace App\Http\Controllers;

use App\Models\LicenseType;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OnboardingController extends Controller
{
    /**
     * Display license selection screen for onboarding.
     */
    public function licenseSelection(Request $request): Response
    {
        $licenseTypes = LicenseType::parents()->with('children')->get();

        return Inertia::render('onboarding/license-selection', [
            'licenseTypes' => $licenseTypes,
        ]);
    }

    /**
     * Save the selected default license type.
     */
    public function saveLicense(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'license_type_id' => 'required|exists:license_types,id',
        ]);

        $request->user()->update([
            'default_license_type_id' => $request->license_type_id,
        ]);

        return redirect()->intended(route('dashboard'));
    }
}
