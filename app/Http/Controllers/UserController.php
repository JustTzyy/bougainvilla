<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Address;
use App\Models\History;
use App\Models\PermissionRequest;
use Exception;
use Hash;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    use SafeDataAccessTrait;
    use EnhancedLoggingTrait;
    public function admin(Request $request)
    {
        try {
            $query = User::query()->where('roleID', 1);

            $users = $query->paginate(10);
            return view('adminPages.adminrecords', compact('users'));


        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'Failed to load users: ' . $e->getMessage());


        }

    }
    public function frontDesk(Request $request)
    {
        try {
            $query = User::query()->where('roleID', 2);

            $users = $query->paginate(10);
            return view('adminPages.frontdeskrecords', compact('users'));


        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'Failed to load users: ' . $e->getMessage());


        }

    }

    public function cleaner(Request $request)
    {
        try {
            $query = User::query()->where('roleID', 3);

            $users = $query->paginate(10);
            return view('adminPages.cleanerrecords', compact('users'));


        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'Failed to load users: ' . $e->getMessage());


        }

    }
    public function store(UserRequest $request)
    {

        try {


            $name = $request->firstName . ' ' . ($request->middleName ?? '') . ' ' . $request->lastName;

            // Log the user creation operation
            $this->logBusinessOperation('Creating new user', [
                'email' => $request->email,
                'role_id' => $request->roleID
            ]);

            $user = User::create([
                'firstName' => $request->firstName,
                'middleName' => $request->middleName,
                'lastName' => $request->lastName,
                'name' => trim($request->firstName . ' ' . ($request->middleName ?? '') . ' ' . $request->lastName),
                'email' => $request->email,
                'birthday' => $request->birthday,
                'age' => $request->age,
                'sex' => $request->sex,
                'contactNumber' => $request->contactNumber,
                'password' => Hash::make($request->firstName . '123'),
                'password_changed' => false,
                'roleID' => $request->roleID,
                'status' => 'Active',
            ]);

            //Add Adress
            Address::create([
                'userID' => $user->id,
                'street' => $request->street,
                'city' => $request->city,
                'province' => $request->province,
                'zipcode' => $request->zipcode,
            ]);

            // Add History
            History::create([
                'status' => 'Added',
                'userID' => $user->id,

            ]);

            // Redirect based on role
            if ($request->roleID == 1) {
                return redirect()->route('adminPages.adminrecords')->with('success', 'Admin created successfully!');
            } elseif ($request->roleID == 2) {
                return redirect()->route('adminPages.frontdeskrecords')->with('success', 'Front desk user created successfully!');
            } elseif ($request->roleID == 3) {
                return redirect()->route('adminPages.cleanerrecords')->with('success', 'Cleaner created successfully!');
            } else {
                return redirect()->back()->with('success', 'User added successfully!');
            }

        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }

    }

    public function update(UserRequest $request, $id)
    {

        try {


            $user = User::findOrFail($id);


            //Update user
            $user->update([
                'firstName' => $request->firstName,
                'middleName' => $request->middleName,
                'lastName' => $request->lastName,
                'name' => trim($request->firstName . ' ' . ($request->middleName ?? '') . ' ' . $request->lastName),
                'email' => $request->email,
                'birthday' => $request->birthday,
                'age' => $request->age,
                'sex' => $request->sex,
                'contactNumber' => $request->contactNumber,

            ]);


            //Update address
            $address = Address::where('userID', $id)->first();
            if ($address) {
                $address->update([
                    'street' => $request->street,
                    'city' => $request->city,
                    'province' => $request->province,
                    'zipcode' => $request->zipcode,
                ]);
            } else {
                Address::create([
                    'userID' => $user->id,
                    'street' => $request->street,
                    'city' => $request->city,
                    'province' => $request->province,
                    'zipcode' => $request->zipcode,
                ]);
            }

            //Add history
            History::create([
                'status' => 'Updated',
                'userID' => $user->id,

            ]);
            // Redirect based on user role
            if ($user->roleID == 1) {
                return redirect()->route('adminPages.adminrecords')->with('success', 'Admin updated successfully!');
            } elseif ($user->roleID == 2) {
                return redirect()->route('adminPages.frontdeskrecords')->with('success', 'Front desk user updated successfully!');
            } elseif ($user->roleID == 3) {
                return redirect()->route('adminPages.cleanerrecords')->with('success', 'Cleaner updated successfully!');
            } else {
                return redirect()->back()->with('success', 'User and address updated successfully!');
            }


        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }


    }

    // Delete user and address
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            // Soft delete User
            $user->delete();
            
            // Soft delete address if it exists
            if ($user->address) {
                $user->address->delete();
            }

            // Add history
            History::create([
                'status' => 'Deleted',
                'userID' => $user->id,
            ]);

            // Redirect based on user role
            if ($user->roleID == 1) {
                return redirect()->route('adminPages.adminrecords')->with('success', 'Admin archived successfully!');
            } elseif ($user->roleID == 2) {
                return redirect()->route('adminPages.frontdeskrecords')->with('success', 'Front desk user archived successfully!');
            } elseif ($user->roleID == 3) {
                return redirect()->route('adminPages.cleanerrecords')->with('success', 'Cleaner archived successfully!');
            } else {
                return redirect()->back()->with('success', 'User deleted successfully!');
            }
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft-deleted user
     */
    public function restore($id)
    {
        try {
            $user = User::onlyTrashed()->findOrFail($id);
            
            // Find and restore the address first (if it exists)
            $address = \App\Models\Address::onlyTrashed()->where('userID', $user->id)->first();
            if ($address) {
                $address->restore();
            }
            
            // Then restore the user
            $user->restore();

            // Add history
            History::create([
                'status' => 'Restored',
                'userID' => $user->id,
            ]);

            // Redirect based on user role
            if ($user->roleID == 1) {
                return redirect()->route('adminPages.adminrecords')->with('success', 'Admin restored successfully!');
            } elseif ($user->roleID == 2) {
                return redirect()->route('adminPages.frontdeskrecords')->with('success', 'Front desk user restored successfully!');
            } elseif ($user->roleID == 3) {
                return redirect()->route('adminPages.cleanerrecords')->with('success', 'Cleaner restored successfully!');
            } else {
                return redirect()->back()->with('success', 'User restored successfully!');
            }
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Show only soft-deleted admin users
     */
    public function archivedAdmins(Request $request)
    {
        try {
            $users = User::onlyTrashed()
                ->where('roleID', 1)
                ->orderByDesc('deleted_at')
                ->get();

            return view('adminPages.archiveadminrecords', compact('users'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to load archived users: ' . $e->getMessage());
        }
    }

    public function archivedFrontdesk(Request $request)
    {
        try {
            $users = User::onlyTrashed()
                ->where('roleID', 2)
                ->orderByDesc('deleted_at')
                ->get();

            return view('adminPages.archivefrontdeskrecords', compact('users'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to load archived users: ' . $e->getMessage());
        }
    }

    public function archivedCleaners(Request $request)
    {
        try {
            $users = User::onlyTrashed()
                ->where('roleID', 3)
                ->orderByDesc('deleted_at')
                ->get();

            return view('adminPages.archivecleanerrecords', compact('users'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to load archived users: ' . $e->getMessage());
        }
    }

    public function getCleanersList(Request $request)
    {
        try {
            $cleaners = User::where('roleID', 3)
                ->where('status', 'Active')
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'cleaners' => $cleaners
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load cleaners: ' . $e->getMessage()
            ], 500);
        }
    }

    // Settings Methods
    public function updatePersonal(Request $request)
    {
        try {
            // Check if this is an address-only update
            $isAddressOnly = $request->has('street') && $request->has('province') && $request->has('city') && $request->has('zipcode') && 
                           !$request->has('firstName') && !$request->has('lastName') && !$request->has('contactNumber') && !$request->has('birthday') && !$request->has('sex');

            if ($isAddressOnly) {
                // Address-only validation
                $request->validate([
                    'street' => 'required|string|max:255',
                    'province' => 'required|string|max:255',
                    'city' => 'required|string|max:255',
                    'zipcode' => 'required|string|max:10',
                ]);
            } else {
                // Personal info validation
                $request->validate([
                    'firstName' => 'required|string|max:255',
                    'lastName' => 'required|string|max:255',
                    'middleName' => 'nullable|string|max:255',
                    'contactNumber' => 'required|string|max:20',
                    'birthday' => 'required|date',
                    'sex' => 'required|in:Male,Female',
                    // Address fields (optional for personal info update)
                    'street' => 'nullable|string|max:255',
                    'province' => 'nullable|string|max:255',
                    'city' => 'nullable|string|max:255',
                    'zipcode' => 'nullable|string|max:10',
                ]);
            }

            $user = Auth::user();
            
            // Check if user is front desk (roleID = 2) and not address-only update
            if ($user->roleID == 2 && !$isAddressOnly) {
                // Create permission request for front desk users
                $currentData = [
                    'firstName' => $user->firstName,
                    'lastName' => $user->lastName,
                    'middleName' => $user->middleName,
                    'contactNumber' => $user->contactNumber,
                    'birthday' => $user->birthday,
                    'sex' => $user->sex,
                ];
                
                $requestData = [
                    'firstName' => $request->firstName,
                    'lastName' => $request->lastName,
                    'middleName' => $request->middleName,
                    'contactNumber' => $request->contactNumber,
                    'birthday' => $request->birthday,
                    'sex' => $request->sex,
                ];
                
                // Add address data if provided
                if ($request->has('street') && $request->has('province') && $request->has('city') && $request->has('zipcode')) {
                    $address = Address::where('userID', $user->id)->first();
                    $currentData['address'] = $address ? [
                        'street' => $address->street,
                        'city' => $address->city,
                        'province' => $address->province,
                        'zipcode' => $address->zipcode,
                    ] : null;
                    
                    $requestData['address'] = [
                        'street' => $request->street,
                        'city' => $request->city,
                        'province' => $request->province,
                        'zipcode' => $request->zipcode,
                    ];
                }
                
                PermissionRequest::create([
                    'user_id' => $user->id,
                    'request_type' => PermissionRequest::TYPE_PERSONAL_INFO,
                    'request_data' => $requestData,
                    'current_data' => $currentData,
                    'status' => PermissionRequest::STATUS_PENDING,
                ]);
                
                return redirect()->route('frontdesk.settings')->with('success', 'Your request has been submitted for admin approval.');
            }
            
            // For admin users or address-only updates, proceed with direct update
            if (!$isAddressOnly) {
                // Calculate age from birthday
                $birthday = new \DateTime($request->birthday);
                $today = new \DateTime();
                $age = $today->diff($birthday)->y;
                
                $user->update([
                    'firstName' => $request->firstName,
                    'lastName' => $request->lastName,
                    'middleName' => $request->middleName,
                    'contactNumber' => $request->contactNumber,
                    'birthday' => $request->birthday,
                    'age' => $age,
                    'sex' => $request->sex,
                ]);

                // Update the name field as well
                $user->name = $request->firstName . ' ' . $request->lastName;
                $user->save();
            }

            // Update address if address fields are provided
            if ($request->has('street') && $request->has('province') && $request->has('city') && $request->has('zipcode')) {
                $address = Address::where('userID', $user->id)->first();
                if ($address) {
                    $address->update([
                        'street' => $request->street,
                        'city' => $request->city,
                        'province' => $request->province,
                        'zipcode' => $request->zipcode,
                    ]);
                } else {
                    Address::create([
                        'userID' => $user->id,
                        'street' => $request->street,
                        'city' => $request->city,
                        'province' => $request->province,
                        'zipcode' => $request->zipcode,
                    ]);
                }
            }

            // Add history entry
            $historyMessage = $isAddressOnly ? 
                'Updated address information' : 
                'Updated personal information' . ($request->has('street') ? ' and address' : '');
            
            History::create([
                'status' => $historyMessage,
                'userID' => Auth::id(),
            ]);

            $message = $isAddressOnly ? 'Address updated successfully!' : 'Information updated successfully!';
            
            // Redirect based on user role
            if ($user->roleID == 1) {
                return redirect()->route('adminPages.settings')->with('success', $message);
            } else {
                return redirect()->route('frontdesk.settings')->with('success', $message);
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to update information: ' . $e->getMessage())->withInput();
        }
    }

    public function updateEmail(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
                'email_confirmation' => 'required|same:email',
            ]);

            $user = Auth::user();
            
            // Check if user is front desk (roleID = 2)
            if ($user->roleID == 2) {
                // Create permission request for front desk users
                PermissionRequest::create([
                    'user_id' => $user->id,
                    'request_type' => PermissionRequest::TYPE_EMAIL,
                    'request_data' => ['email' => $request->email],
                    'current_data' => ['email' => $user->email],
                    'status' => PermissionRequest::STATUS_PENDING,
                ]);
                
                return redirect()->route('frontdesk.settings')->with('success', 'Your email change request has been submitted for admin approval.');
            }
            
            // For admin users, proceed with direct update
            $user->update([
                'email' => $request->email,
            ]);

            // Add history entry
            History::create([
                'status' => 'Updated email address to: ' . $request->email,
                'userID' => Auth::id(),
            ]);

            // Redirect based on user role
            if ($user->roleID == 1) {
                return redirect()->route('adminPages.settings')->with('success', 'Email updated successfully!');
            } else {
                return redirect()->route('frontdesk.settings')->with('success', 'Email updated successfully!');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to update email: ' . $e->getMessage())->withInput();
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required',
                'password' => 'required|min:8|confirmed',
                'password_confirmation' => 'required',
            ]);

            $user = Auth::user();

            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()->with('error', 'Current password is incorrect.')->withInput();
            }

            // Log the password change operation
            $this->logSecurityEvent('User password changed', [
                'user_id' => $user->id,
                'user_email' => $user->email
            ]);

            $user->update([
                'password' => Hash::make($request->password),
                'password_changed' => true,
            ]);

            // Add history entry
            History::create([
                'status' => 'Updated password',
                'userID' => Auth::id(),
            ]);

            // Redirect based on user role
            $user = Auth::user();
            if ($user->roleID == 1) {
                return redirect()->route('adminPages.settings')->with('success', 'Password updated successfully!');
            } else {
                return redirect()->route('frontdesk.settings')->with('success', 'Password updated successfully!');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to update password: ' . $e->getMessage())->withInput();
        }
    }

    public function deactivateAccount(Request $request)
    {
        try {
            $request->validate([
                'password' => 'required',
            ]);

            $user = Auth::user();

            // Verify current password
            if (!Hash::check($request->password, $user->password)) {
                return redirect()->back()->with('error', 'Incorrect password. Please try again.')->withInput();
            }

            // Add history entry before deactivating
            History::create([
                'status' => 'Deactivated account',
                'userID' => Auth::id(),
            ]);

            // Soft delete the user account
            $user->delete();

            // Logout the user
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('success', 'Your account has been deactivated successfully. Contact an administrator to reactivate your account.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to deactivate account: ' . $e->getMessage())->withInput();
        }
    }

   
}
