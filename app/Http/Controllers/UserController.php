<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\CodeToRegister;
use App\Models\JobCategory;
use App\Mail\CodeToRegisterMail;

class UserController extends Controller
{

    public function register(Request $request){
        try {
            // Validate request data
            $validatedData = $request->validate([
                'user_name' => 'required|string|max:255',
                'email' => 'required|email|max:100|unique:users,email|unique:admins,email',
                'phone' => [
                    'required',
                    'numeric',
                    'digits:10',
                    'unique:users,phone',
                    'unique:admins,phone',
                    'regex:/^(072|078|073|079)\d{7}$/',
                ],
            ]);

            // Create seeker_code logic...
            $lastUserCode = DB::table('users')->max('user_code');
            if ($lastUserCode) {
                preg_match('/\d+$/', $lastUserCode, $matches);
                $sequenceNumber = isset($matches[0]) ? (int)$matches[0] + 1 : 1;
            } else {
                $sequenceNumber = 1;
            }

            $prefix = date('y');
            $middle = 'JSR';
            $formattedNumber = str_pad($sequenceNumber, 5, '0', STR_PAD_LEFT);
            $seeker_code = $prefix . $middle . $formattedNumber;

            // Create the user
            $user = User::create([
                'user_code' => $seeker_code,
                'provider_name' => 'Usual_reg',
                'user_name' => $request->user_name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            // Log the  user creation for debugging
            \Log::info('User created: ' . $user->id);

            // Log the successful token creation
            $token = Auth::guard('user')->login($user);
            \Log::info('Auth token generated: ' . $token);

            // Delete old codes for this email
            CodeToRegister::where('email', $user->email)->delete();

            // Generate a new verification code
            $data = [
                'email' => $user->email,
                'code' => mt_rand(100000, 999999),
            ];

            // Store the new code in the database
            $got_data = CodeToRegister::create($data);

            // Send the email with the verification code
            Mail::to($user->email)->send(new CodeToRegisterMail($got_data->email,$got_data->code));

            // Return response
            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully',
                'user' => $user,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors as JSON response
            return response()->json([
                'status' => 'error',
                'message' => 'Validation errors occurred.',
                'errors' => $e->errors() // This will return the validation error details
            ], 422);
        } catch (\Exception $e) {
            // Log the error to Laravel logs
            \Log::error('Registration failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Registration failed. ' . $e->getMessage()
            ], 500);
        }
    }

    // Start of fill missed info
    public function fill_missed_info(Request $request, $email){

        try {

            $validated = $request->validate([
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'gender' => 'required|string',
                'birthdate' => 'required|date',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = User::where('email', $email)->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found',
                ], 404);
            }

            $user->firstname = $validated['firstname'];
            $user->lastname = $validated['lastname'];
            $user->gender = $validated['gender'];
            $user->birthdate = $validated['birthdate'];
            $user->image = 'user.png';
            $user->password = bcrypt($validated['password']);
            $user->save();

            $token = Auth::guard('user')->login($user);

            \Log::info('User updated: ', ['user' => $user]);

            return response()->json([
                'status' => 'success',
                'message' => 'User updated successfully',
                'user' => $user,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ],
                // 'redirect_url' => route('seeker.dashboard') // Add the redirection URL here
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation errors occurred.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Update failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Update failed. ' . $e->getMessage()
            ], 500);
        }

    }
    // End of fill-missed-info


	public function View_information(){
        $user = Auth::guard('user')->user();

        if ($user) {

            return response()->json([
                'status' => 'Auth user info',
                'user_info' => $user
            ], 200);

        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function edit_info(Request $request,$id){

        if ($id) {

            $user = User::find($id);

            if ($user) {

                $validatedData = $request->validate([
                    'user_name' => 'required|string|max:255',
                    'firstname' => 'required|string|max:255',
                    'lastname' => 'required|string|max:255',
                    'gender' => 'required|string|max:255',
                    'phone' => 'required|string|max:255|unique:users,phone,' . $id.'|unique:admins,phone,' . $id,
                    'birthdate' => 'required|string|max:255',
                ]);

                $user->update($validatedData);

                return response()->json([
                    'message' => 'Data updated successfully!',
                ], 200);
            }

            return response()->json(['error' => 'User not found'], 404);
        
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    
    }


    public function profile_picture(){
        return 'profile public';
    }

    public function verify_code_to_register(Request $request, $email){
        // Validate the code input
        $request->validate([
            'code' => 'required|numeric|digits:6' // Ensure a 6-digit code is required
        ]);

        // Check if the code is in an array and implode if so
        if (is_array($request->code)) {
            $code = implode('', $request->code);
        } else {
            $code = $request->code;
        }

        // Check if the code exists in the database for the given email
        $register_Code = CodeToRegister::where('email', $email)->where('code', $code)->first();

        if ($register_Code) {
            // Check if the code is older than one hour
            if ($register_Code->created_at->diffInMinutes(now()) > 60) {
                // Code expired
                $register_Code->delete();
                return response()->json(['error' => 'Your code is expired!'], 400);
            } else {
                // Code is valid, delete the code after use
                $register_Code->delete();
                return response()->json(['info' => 'Now fill missed info!'], 200);
            }
        } else {
            // Code does not match or has expired
            return response()->json(['error' => 'The code is not valid. Please try again.'], 400);
        }
    }


    public function submitCategories(Request $request){

        // Validate that 'selectedItems' is provided and is an array
        $validated = $request->validate([
            'selectedItems' => 'required|array',
        ]);

        // Retrieve the selected categories from the request
        $selectedCategories = $request->input('selectedItems');
        
        // Set the user ID (hardcoded for now)
        $userId = Auth::guard('user')->user()->id;

        // Check if user is authenticated (this part is optional if user ID is set)
        if (!$userId) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        // Array of categories that are already stored
        $storedCategories = [];

        // Check if selectedCategories is an array before using foreach
        if (is_array($selectedCategories)) {
            foreach ($selectedCategories as $category) {
                $existingCategory = JobCategory::where('category_name', $category)
                                                ->where('user_fk_id', $userId)
                                                ->first();

                if (!$existingCategory) {
                    JobCategory::create([
                        'category_name' => $category,
                        'user_fk_id' => $userId
                    ]);

                    // Add the new category to the storedCategories array
                    $storedCategories[] = $category;
                }
            }

            return response()->json([
                'message' => 'Job categories submitted successfully',
                'storedCategories' => $storedCategories
            ]);
        } else {
            return response()->json(['message' => 'Selected categories must be an array'], 400);
        }
    }

    public function fetch_user_job_Categories()
    {
        // $userId = Auth::guard('user')->user()->id;

        // $jobCategories = JobCategory::where('user_fk_id', $userId)
        //                              ->pluck('category_name');

        // return response()->json([
        //     'categ_id' => $jobCategories->id,
        //     'skills' => $jobCategories
        // ]);
        $userId = Auth::guard('user')->user()->id;

        $jobCategories = JobCategory::where('user_fk_id', $userId)
                                     ->select('id', 'category_name as skills')
                                     ->get();

        return response()->json([
            'category_names' => $jobCategories
        ]);

        
    }

    public function count_job_category(){
        $userId = Auth::guard('user')->user()->id;

        $jobCategories = JobCategory::where('user_fk_id', $userId)
                                     ->pluck('category_name');

        $count_jobCategories = collect($jobCategories)->count();

        return response()->json([
            'count_categories' => $count_jobCategories
        ]);
        
    }

    public function removeJobCategory($categoryId){

        $userId = Auth::guard('user')->user()->id;
        
        $category = JobCategory::where('id', $categoryId)
                               ->where('user_fk_id', $userId)
                               ->first();

        if ($category) {
            $category->delete();
            return response()->json(['message' => 'Category removed successfully'], 200);
        } else {
            return response()->json(['error' => 'Category not found or unauthorized'], 404);
        }
    }


    public function remove_job_category(Request $request,$id){
        $userId = Auth::guard('user')->user()->id;

        $RemovejobCategories = JobCategory::where('user_fk_id', $userId)
                                ->where('id', $id)
                                ->delete();

        if($RemovejobCategories){
            return response()->json([
                'success' => "category removed !"
            ]);
        }else{
            return response()->json([
                'error' => "Error to remove category ,try again !"
            ]);
        }
        
    }


}
