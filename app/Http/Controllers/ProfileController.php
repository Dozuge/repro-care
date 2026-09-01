<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Purok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    private const PROFILE_IMAGE_DIRECTORIES = [
        'uploads/profile/',
        'profile/',
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get current authenticated user
     */
    private function getCurrentUser()
    {
        return auth()->user();
    }

    /**
     * Get current user type for view routing
     */
    private function getUserType()
    {
        $user = auth()->user();
        return match($user->role) {
            'midwife' => 'midwife',
            'bhw' => 'bhw',
            'bhw_president' => 'bhw-president',
            'user' => 'user',
            default => null,
        };
    }

    /**
     * Show user's profile
     */
    public function show()
    {
        $user = $this->getCurrentUser();
        if (!$user) return redirect()->route('login');
        
        $userType = $this->getUserType();
        
        // Use role-specific views to keep dashboard visible
        if ($userType === 'midwife') {
            return view('midwife.profile.show', compact('user'));
        } elseif ($userType === 'bhw') {
            return view('bhw.profile.show', compact('user'));
        } elseif ($userType === 'bhw-president') {
            return view('bhw-president.profile.show', compact('user'));
        } else {
            return view('user.profile.show', compact('user'));
        }
    }

    /**
     * Show profile edit form
     */
    public function edit()
    {
        $user = $this->getCurrentUser();
        if (!$user) return redirect()->route('login');

        $userType = $this->getUserType();
        $puroks = Purok::orderBy('name')->get();
        
        // Use role-specific views to keep dashboard visible
        if ($userType === 'midwife') {
            return view('midwife.profile.edit', compact('user', 'puroks'));
        } elseif ($userType === 'bhw') {
            return view('bhw.profile.edit', compact('user', 'puroks'));
        } elseif ($userType === 'bhw-president') {
            return view('bhw-president.profile.edit', compact('user', 'puroks'));
        } else {
            return view('user.profile.edit', compact('user', 'puroks'));
        }
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = $this->getCurrentUser();
        if (!$user) return redirect()->route('login');

        $userType = get_class($user);

        // Determine table for unique email validation
        $emailTable = match($userType) {
            Midwife::class => 'midwives',
            Bhw::class => 'bhws',
            User::class => 'users',
            default => 'users',
        };

        $validationRules = [
            'first_name' => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:2',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:' . $emailTable . ',email,' . $user->id,
            'contact_number' => 'nullable|string|max:20',
            'purok_id' => 'nullable|exists:puroks,id',
            'date_of_birth' => 'nullable|date|before:today',
            'partner_name' => 'nullable|string|max:255',
            'partner_contact' => 'nullable|string|max:20',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120', // Max 5MB
        ];

        $request->validate($validationRules);

        // Manually validate password only if it's being changed
        $passwordValue = $request->input('password');
        if ($passwordValue !== null && $passwordValue !== '' && trim($passwordValue) !== '') {
            if (strlen($passwordValue) < 8) {
                return back()->withErrors(['password' => 'Password must be at least 8 characters.'])->withInput();
            }
            if ($passwordValue !== $request->input('password_confirmation')) {
                return back()->withErrors(['password' => 'Password confirmation does not match.'])->withInput();
            }
        }

        $contactNumber = $request->has('contact_number')
            ? ($request->filled('contact_number') ? $request->contact_number : null)
            : $user->contact_number;

        $dateOfBirth = $request->has('date_of_birth')
            ? ($request->filled('date_of_birth') ? $request->date_of_birth : null)
            : $user->date_of_birth;

        $selectedPurok = $request->filled('purok_id') ? Purok::find($request->purok_id) : null;

        $updateData = [
            'first_name' => $request->first_name,
            'middle_initial' => $request->middle_initial,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'contact_number' => $contactNumber,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'purok_id' => $selectedPurok?->id,
            'barangay' => $selectedPurok?->barangay,
        ];

        if ($request->has('partner_name')) {
            $updateData['partner_name'] = $request->filled('partner_name') ? $request->partner_name : null;
        }

        if ($request->has('partner_contact')) {
            $updateData['partner_contact'] = $request->filled('partner_contact') ? $request->partner_contact : null;
        }

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            $this->deleteProfileImage($user->profile_image);

            // Upload new image with unique filename
            $image = $request->file('profile_image');
            $filename = uniqid() . '_' . time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/profile', $filename, 'public');
            $updateData['profile_image'] = $path;
        }

        // Handle password update if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        // Redirect to previous page or profile show page
        $redirectTo = $request->input('redirect_to', url()->previous());
        
        // If redirect_to is the current edit page, go to profile show instead
        if ($redirectTo === route('profile.edit') || $redirectTo === url()->current()) {
            $redirectTo = route('profile.show');
        }
        
        return redirect($redirectTo)->with('success', 'Profile updated successfully.');
    }

    /**
     * Remove profile image
     */
    public function removeImage()
    {
        $user = $this->getCurrentUser();
        if (!$user) return redirect()->route('login');

        if ($this->deleteProfileImage($user->profile_image)) {
            $user->update(['profile_image' => null]);
        }

        // Redirect to previous page or profile show page
        $redirectTo = request()->input('redirect_to', url()->previous());
        
        // If redirect_to is the current edit page, go to profile show instead
        if ($redirectTo === route('profile.edit') || $redirectTo === url()->current()) {
            $redirectTo = route('profile.show');
        }
        
        return redirect($redirectTo)->with('success', 'Profile image removed successfully.');
    }

    private function deleteProfileImage(?string $filename): bool
    {
        if (!$filename) {
            return false;
        }

        $publicDisk = Storage::disk('public');
        $normalizedFilename = basename($filename);

        if ($publicDisk->exists($filename)) {
            $publicDisk->delete($filename);
            return true;
        }

        foreach (self::PROFILE_IMAGE_DIRECTORIES as $directory) {
            $path = $directory . $normalizedFilename;

            if ($publicDisk->exists($path)) {
                $publicDisk->delete($path);
                return true;
            }
        }

        foreach (self::PROFILE_IMAGE_DIRECTORIES as $directory) {
            $legacyPublicPath = public_path($directory . $normalizedFilename);

            if (file_exists($legacyPublicPath)) {
                @unlink($legacyPublicPath);
                return true;
            }
        }

        return false;
    }

    /**
     * Find user by ID across all tables
     */
    private function findUserById($id)
    {
        $user = User::find($id);
        if ($user) return $user;
        return $user;
    }

    /**
     * Get user profile (for API or other users to view)
     */
    public function viewProfile($id)
    {
        $user = $this->findUserById($id);
        if (!$user) abort(404);
        
        $authUser = $this->getCurrentUser();
        if (!$authUser) return redirect()->route('login');
        
        $userType = $this->getUserType();
        
        // Use role-specific views to keep dashboard visible
        if ($userType === 'midwife') {
            return view('midwife.profile.view', compact('user'));
        } elseif ($userType === 'bhw') {
            return view('bhw.profile.view', compact('user'));
        } elseif ($userType === 'bhw-president') {
            return view('bhw-president.profile.view', compact('user'));
        } else {
            return view('user.profile.view', compact('user'));
        }
    }

    /**
     * Update user's emergency contacts
     */
    public function updateEmergencyContacts(Request $request)
    {
        $user = $this->getCurrentUser();
        if (!$user) return redirect()->route('login');

        $validated = $request->validate([
            // Primary emergency contact validation
            'emergency_name_1' => 'required|string|max:255',
            'emergency_relationship_1' => 'required|string|max:255',
            'emergency_contact_number_1' => 'required|string|max:255',
            'emergency_address_1' => 'nullable|string|max:500',
             
            // Secondary emergency contact validation
            'emergency_name_2' => 'nullable|string|max:255',
            'emergency_relationship_2' => 'nullable|required_with:emergency_name_2|string|max:255',
            'emergency_contact_number_2' => 'nullable|required_with:emergency_name_2|string|max:255',
            'emergency_address_2' => 'nullable|string|max:500',
             
            // Tertiary emergency contact validation
            'emergency_name_3' => 'nullable|string|max:255',
            'emergency_relationship_3' => 'nullable|required_with:emergency_name_3|string|max:255',
            'emergency_contact_number_3' => 'nullable|required_with:emergency_name_3|string|max:255',
            'emergency_address_3' => 'nullable|string|max:500',
        ]);

        // Update/create Primary Emergency Contact
        $user->emergencyContacts()->updateOrCreate(
            ['contact_order' => 1],
            [
                'name' => $validated['emergency_name_1'],
                'relationship' => $validated['emergency_relationship_1'],
                'contact_number' => $validated['emergency_contact_number_1'],
                'address' => $validated['emergency_address_1'] ?? null,
                'contact_order' => 1,
            ]
        );

        // Update/create Secondary Emergency Contact (if provided)
        if (!empty($validated['emergency_name_2'])) {
            $user->emergencyContacts()->updateOrCreate(
                ['contact_order' => 2],
                [
                    'name' => $validated['emergency_name_2'],
                    'relationship' => $validated['emergency_relationship_2'],
                    'contact_number' => $validated['emergency_contact_number_2'],
                    'address' => $validated['emergency_address_2'] ?? null,
                    'contact_order' => 2,
                    'is_primary' => false,
                ]
            );
        } else {
            // Delete secondary contact if it was cleared
            $user->emergencyContacts()->where('contact_order', 2)->delete();
        }

        // Update/create Tertiary Emergency Contact (if provided)
        if (!empty($validated['emergency_name_3'])) {
            $user->emergencyContacts()->updateOrCreate(
                ['contact_order' => 3],
                [
                    'name' => $validated['emergency_name_3'],
                    'relationship' => $validated['emergency_relationship_3'],
                    'contact_number' => $validated['emergency_contact_number_3'],
                    'address' => $validated['emergency_address_3'] ?? null,
                    'contact_order' => 3,
                    'is_primary' => false,
                ]
            );
        } else {
            // Delete tertiary contact if it was cleared
            $user->emergencyContacts()->where('contact_order', 3)->delete();
        }

        return back()->with('success', 'Emergency contacts updated successfully.');
    }
}
