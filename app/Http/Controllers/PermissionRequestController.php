<?php

namespace App\Http\Controllers;

use App\Models\PermissionRequest;
use App\Models\Address;
use App\Models\History;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PermissionRequestController extends Controller
{
    public function index()
    {
        try {
            $pendingRequests = PermissionRequest::with(['user', 'approvedBy'])
                ->where('status', PermissionRequest::STATUS_PENDING)
                ->orderBy('created_at', 'desc')
                ->get();
                
            return view('adminPages.permission-requests', compact('pendingRequests'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to load permission requests: ' . $e->getMessage());
        }
    }

    public function approve(Request $request, $id)
    {
        try {
            $permissionRequest = PermissionRequest::findOrFail($id);
            
            if ($permissionRequest->status !== PermissionRequest::STATUS_PENDING) {
                return redirect()->back()->with('error', 'This request has already been processed.');
            }

            DB::beginTransaction();

            $user = $permissionRequest->user;
            
            if ($permissionRequest->request_type === PermissionRequest::TYPE_PERSONAL_INFO) {
                $requestData = $permissionRequest->request_data;
                
                // Calculate age from birthday
                $birthday = new \DateTime($requestData['birthday']);
                $today = new \DateTime();
                $age = $today->diff($birthday)->y;
                
                // Update user information
                $user->update([
                    'firstName' => $requestData['firstName'],
                    'lastName' => $requestData['lastName'],
                    'middleName' => $requestData['middleName'],
                    'contactNumber' => $requestData['contactNumber'],
                    'birthday' => $requestData['birthday'],
                    'age' => $age,
                    'sex' => $requestData['sex'],
                ]);

                // Update the name field
                $user->name = $requestData['firstName'] . ' ' . $requestData['lastName'];
                $user->save();
                
                // Update address if provided
                if (isset($requestData['address'])) {
                    $address = Address::where('userID', $user->id)->first();
                    if ($address) {
                        $address->update([
                            'street' => $requestData['address']['street'],
                            'city' => $requestData['address']['city'],
                            'province' => $requestData['address']['province'],
                            'zipcode' => $requestData['address']['zipcode'],
                        ]);
                    } else {
                        Address::create([
                            'userID' => $user->id,
                            'street' => $requestData['address']['street'],
                            'city' => $requestData['address']['city'],
                            'province' => $requestData['address']['province'],
                            'zipcode' => $requestData['address']['zipcode'],
                        ]);
                    }
                }
                
                $historyMessage = 'Approved personal information update request for ' . $user->name;
                
            } elseif ($permissionRequest->request_type === PermissionRequest::TYPE_EMAIL) {
                $requestData = $permissionRequest->request_data;
                
                // Update email
                $user->update([
                    'email' => $requestData['email'],
                ]);
                
                $historyMessage = 'Approved email change request for ' . $user->name . ' to: ' . $requestData['email'];
            }

            // Update permission request
            $permissionRequest->update([
                'status' => PermissionRequest::STATUS_APPROVED,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'admin_notes' => $request->admin_notes ?? null,
            ]);

            // Add history entry
            History::create([
                'status' => $historyMessage,
                'userID' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Permission request approved successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to approve request: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        try {
            $permissionRequest = PermissionRequest::findOrFail($id);
            
            if ($permissionRequest->status !== PermissionRequest::STATUS_PENDING) {
                return redirect()->back()->with('error', 'This request has already been processed.');
            }

            $permissionRequest->update([
                'status' => PermissionRequest::STATUS_REJECTED,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'admin_notes' => $request->admin_notes ?? 'Request rejected by admin.',
            ]);

            // Add history entry
            History::create([
                'status' => 'Rejected ' . $permissionRequest->request_type . ' request for ' . $permissionRequest->user->name,
                'userID' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'Permission request rejected successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to reject request: ' . $e->getMessage());
        }
    }
}