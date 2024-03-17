<?php

namespace App\Http\Controllers;

use App\Facades\Utils;
use App\Models\Tenants;
use App\Enums\StatusCode;
use App\Models\Complaints;
use App\Models\Properties;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function onboardTenant(Request $request)
    {
        $requestBody = [
            'tenant_user_id' => 'required',
            'property_id' => 'required',
        ];

        Utils::validate($request->all(), $requestBody);

        $property = Properties::with('slot')->find($request->property_id)->first();
        $property->slot->available_units = $property->slot->available_units - 1;
        $property->slot->save();

        Tenants::create([
            'user_id' => $request->tenant_user_id,
            'has_paid' => true,
            'status' => 'active',
            'property_id' => $request->property_id,
        ]);

        return Utils::setResponse(
            StatusCode::CREATED, 
            null,
            'Onboarding successful, Login with your credentials to continue.'
        );
    }

    public function makeComplaints(Request $request)
    {
        $requestBody = [
            'property_id' => 'required',
            'tenant_id' => 'required',
            'title' => 'required',
            'description' => 'required',
        ];

        Utils::validate($request->all(), $requestBody);

        $complaint = Tenants::find($request->tenant_id)->complaints()->create([
            'title' => $request->title,
            'description' => $request->description,
            'property_id' => $request->property_id,
            'status' => 'pending'
        ]);

        return Utils::setResponse(
            StatusCode::CREATED, 
            $complaint,
            'Complaint created successfully'
        );  
    }

    public function getAllComplaints(Request $request)
    {
        $requestBody = [
            'property_id' => 'required',
        ];

        Utils::validate($request->all(), $requestBody);

        $complaints = Complaints::where('property_id', $request->property_id)->get();

        return Utils::setResponse(
            StatusCode::OK, 
            $complaints,
            'Complaints retrieved successfully'
        );
    }

    public function getComplaint(Request $request)
    {
        $requestBody = [
            'complaint_id' => 'required',
        ];

        Utils::validate($request->all(), $requestBody);

        $complaint = Complaints::find($request->complaint_id);

        return Utils::setResponse(
            StatusCode::OK, 
            $complaint,
            'Complaint retrieved successfully'
        );
    }

    public function updateComplaint(Request $request)
    {
        $requestBody = [
            'complaint_id' => 'required',
            'status' => 'required',
        ];

        Utils::validate($request->all(), $requestBody);

        $complaint = Complaints::find($request->complaint_id);
        $complaint->status = $request->status;
        $complaint->save();

        return Utils::setResponse(
            StatusCode::OK, 
            $complaint,
            'Complaint updated successfully'
        );
    }

    public function deleteComplaint(Request $request)
    {
        $requestBody = [
            'complaint_id' => 'required',
        ];

        Utils::validate($request->all(), $requestBody);

        $complaint = Complaints::find($request->complaint_id);
        if(!$complaint) {
            return Utils::setResponse(
                StatusCode::NOT_FOUND, 
                null,
                'Complaint not found'
            );
        }
        if($complaint->status == 'resolved'){
            return Utils::setResponse(
                StatusCode::BAD_REQUEST, 
                null,
                'You cannot delete a resolved complaint'
            );
        }

        $complaint->delete();

        return Utils::setResponse(
            StatusCode::OK, 
            null,
            'Complaint deleted successfully'
        );
    }

    public function getAllTenantComplaints(Request $request)
    {
        $requestBody = [
            'tenant_id' => 'required',
        ];

        Utils::validate($request->all(), $requestBody);

        $complaints = Tenants::find($request->tenant_id)->complaints;

        return Utils::setResponse(
            StatusCode::OK, 
            $complaints,
            'Complaints retrieved successfully'
        );
    }

    public function makeRentPayment(Request $request)
    {
        $requestBody = [
            'tenant_id' => 'required',
            'property_id' => 'required',
            'amount' => 'required',
        ];

        Utils::validate($request->all(), $requestBody);

        $tenant = Tenants::find($request->tenant_id);
        $tenant->has_paid = true;
        $tenant->save();

        return Utils::setResponse(
            StatusCode::OK, 
            null,
            'Rent payment successful'
        );

    }


}
