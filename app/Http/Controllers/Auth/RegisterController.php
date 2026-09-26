<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'surname' => ['required', 'string', 'max:100'],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],

            'gender' => [
                'required',
                Rule::in(['Male', 'Female', 'Other']),
            ],

            'civil_status' => [
                'required',
                Rule::in([
                    'Single',
                    'Married',
                    'Widowed',
                    'Separated',
                ]),
            ],

            'birthdate' => ['required', 'date', 'before_or_equal:today'],
            'occupation' => ['nullable', 'string', 'max:150'],

            'address' => ['required', 'string', 'max:500'],

            'mobile' => [
                'required',
                'string',
                'max:30',
                'unique:users,mobile',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'username' => [
                'required',
                'string',
                'max:100',
                'unique:users,username',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'agree_to_terms' => ['required', 'accepted'],

            'beneficiaries' => ['nullable', 'array', 'max:3'],

            'beneficiaries.*.name' => [
                'nullable',
                'string',
                'max:150',
            ],

            'beneficiaries.*.birthdate' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],

            'beneficiaries.*.relationship' => [
                'nullable',
                'string',
                'max:100',
            ],
        ]);

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $this->buildFullName($validated),

                'surname' => $validated['surname'],
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,

                'gender' => $validated['gender'],
                'civil_status' => $validated['civil_status'],
                'birthdate' => $validated['birthdate'],
                'occupation' => $validated['occupation'] ?? null,

                'address' => $validated['address'],

                'mobile' => $validated['mobile'],
                'email' => $validated['email'] ?? null,
                'username' => $validated['username'],

                'password' => Hash::make($validated['password']),
            ]);

            foreach ($validated['beneficiaries'] ?? [] as $beneficiary) {
                if (
                    empty($beneficiary['name']) &&
                    empty($beneficiary['birthdate']) &&
                    empty($beneficiary['relationship'])
                ) {
                    continue;
                }

                Beneficiary::create([
                    'user_id' => $user->id,
                    'name' => $beneficiary['name'] ?? null,
                    'birthdate' => $beneficiary['birthdate'] ?? null,
                    'relationship' => $beneficiary['relationship'] ?? null,
                ]);
            }

            return $user;
        });

        return response()->json([
            'message' => 'Registration successful.',
            'user' => $user,
        ], 201);
    }

    private function buildFullName(array $data): string
    {
        return collect([
            $data['first_name'],
            $data['middle_name'] ?? null,
            $data['surname'],
        ])
            ->filter()
            ->implode(' ');
    }
}

