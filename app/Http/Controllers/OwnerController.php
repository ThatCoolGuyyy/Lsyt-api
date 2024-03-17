<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Facades\Utils;
use App\Enums\StatusCode;
use App\Models\Properties;
use Illuminate\Http\Request;
use App\Mail\TenantOnboardingEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class OwnerController extends Controller
{
    public function createProperty(Request $request)
    {
        $requestBody = [
            'owner_id' => 'required',
            'slots' => 'required',
            'title' => 'required', 
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'price' => 'required',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];

        $image_url =  Storage::put('images', $request->file('image'), 'public');

        Utils::validate($request->all(), $requestBody);

        $property = Properties::create([
            'owner_id' => $request->owner_id,
            'title' => $request->title,
            'description' => $request->description,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'price' => $request->price,
            'image_url' => $image_url,
        ]);

        $slots = $property->slot()->create([
            'available_units' => $request->slots,
            'total_units' => $request->slots,
        ]);
        
        $property->slots = $slots;

        return Utils::setResponse(
            StatusCode::CREATED, 
            $property,
            'Property created successfully'
        );
    }

    public function getProperties(Request $request)
    {
        $properties = Properties::where('owner_id', $request->owner_id)->get();

        return Utils::setResponse(
            StatusCode::OK, 
            $properties,
            'Properties retrieved successfully'
        );
    }

    public function getProperty(Request $request)
    {
        $requestBody = [
            'property_id' => 'required|numeric',
        ];

        Utils::validate($request->all(), $requestBody);

        $property = Properties::with('slot')
                    ->where('id', $request->property_id)
                    ->first();

        return Utils::setResponse(
            StatusCode::OK, 
            $property,
            'Property retrieved successfully'
        );
    }

    public function deleteProperty(Request $request)
    {
        $requestBody = [
            'property_id' => 'required|numeric',
            'owner_id' => 'required|numeric'
        ];

        Utils::validate($request->all(), $requestBody);

        $property = Properties::with('slot')
                    ->find($request->property_id)
                    ->first();

        if (!$property) {
            return Utils::setResponse(
                StatusCode::NOT_FOUND,
                null,
                'Property not found'
            );
        }

        if($property->owner_id !== $request->owner_id){
            return Utils::setResponse(
                StatusCode::FORBIDDEN, 
                null,
                'You are not authorized to delete this property'
            );
        }

        if($property->slot->available_units !== $property->slot->total_units){
            return Utils::setResponse(
                StatusCode::FORBIDDEN, 
                null,
                'You cannot delete a property with occupied units'
            );
        }

        return Utils::setResponse(
            StatusCode::OK, 
            null,
            'Property deleted successfully'
        );
    }

    public function sendOnboardingLinkToTenant(Request $request)
    {
        $requestBody = [
            'email' => 'required|email',
            'property_id' => 'required',
            'tenant_user_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ];
        
        Utils::validate($request->all(), $requestBody);

        $property = Properties::with('slot')->find($request->property_id)->first();

        if($property->slot->available_units === 0){
            return Utils::setResponse(
                StatusCode::BAD_REQUEST, 
                null,
                'No available units in this property'
            );
        }
        
        $tenant = User::find($request->tenant_user_id);

        $onboardingLink = env('APP_URL') . '/api/v1/tenant/onboard?property_id=' . $request->property_id . '&tenant_user_id=' . $request->tenant_user_id;

        Mail::send('emails.tenant-onboarding-email', [
            'tenant' => $tenant, 
            'onboardingLink' => $onboardingLink, 
            'property'  => $property
       ], function($message) use ($request, $tenant) {
                   $message->to($request->email, $tenant->first_name)
                           ->subject('Onboarding Email');
               });

        return Utils::setResponse(
            StatusCode::CREATED, 
            null,
            'Onboarding link sent successfully'
        );
    }

    public function viewTenantComplaints(Request $request)
    {
        $request->validate([
            'tenants_id' => 'required'
        ]);

        $complaints = Complaint::where('tenant_id', auth()->user()->id)->get();

        return Utils::setResponse(
            StatusCode::OK, 
            $complaints,
            'Complaints retrieved successfully'
        );
    }

    public function viewTenantPayments(Request $request)
    {
        $request->validate([
            'tenants_id' => 'required'
        ]);

        $payments = Payment::where('tenant_id', auth()->user()->id)->get();

        return Utils::setResponse(
            StatusCode::OK, 
            $payments,
            'Payments retrieved successfully'
        );
    }

    public function viewUserProperties(Request $request)
    {
        $request->validate([
            'owner_id' => 'required'
        ]);

        $properties = PropePropertiesrty::where('owner_id', auth()->user()->id)->get();

        return Utils::setResponse(
            StatusCode::OK, 
            $properties,
            'Properties retrieved successfully'
        );
    }

    public function viewPropertyTenants(Request $request)
    {
        $request->validate([
            'property_id' => 'required'
        ]);

        $tenants = Tenant::where('property_id', $request->property_id)->get();

        return Utils::setResponse(
            StatusCode::OK, 
            $tenants,
            'Tenants retrieved successfully'
        );
    }

    public function viewPropertyComplaints(Request $request)
    {
        $request->validate([
            'property_id' => 'required'
        ]);

        $complaints = Complaint::where('property_id', $request->property_id)->get();

        return Utils::setResponse(
            StatusCode::OK, 
            $complaints,
            'Complaints retrieved successfully'
        );
    }

    public function viewPropertyPayments(Request $request)
    {
        $request->validate([
            'property_id' => 'required'
        ]);

        $payments = Payment::where('property_id', $request->property_id)->get();

        return Utils::setResponse(
            StatusCode::OK, 
            $payments,
            'Payments retrieved successfully'
        );
    }

    public function viewAllTenantsComplaints(Request $request)
    {
        $request->validate([
            'owner_id' => 'required'
        ]);
        $complaints = Complaint::where('owner_id', $request->owner_id)->get();

        return Utils::setResponse(
            StatusCode::OK, 
            $complaints,
            'Complaints retrieved successfully'
        );
    }
}