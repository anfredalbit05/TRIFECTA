<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Activation;
use App\Models\Beneficiary;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */

#[Fillable([
    // Account
    'username',
    'password',

    // Generated Codes
    'uplineCode',
    'referralCode',

    // Personal Information
    'lastname',
    'firstname',
    'middlename',
    'gender',
    'civilstatus',
    'birthdate',
    'contactno',
    'address',
    'latitude',
    'longitude',
    'email',

    // Profile Picture
    'profilepic',

    // Account Settings
    'RoleID',
    'branchCode',
    'status',
])]

#[Hidden([
    'password',
    'two_factor_secret',
    'two_factor_recovery_codes',
    'remember_token'
])]

class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory,
        Notifiable,
        PasskeyAuthenticatable,
        TwoFactorAuthenticatable;


    /*
    |--------------------------------------------------------------------------
    | Primary Key
    |--------------------------------------------------------------------------
    */

    protected $primaryKey = 'UserID';


    /*
    |--------------------------------------------------------------------------
    | Attribute Casts
    |--------------------------------------------------------------------------
    */

        protected function casts(): array
        {
            return [

                // Email verification date
                'email_verified_at' => 'datetime',

                // Convert birthdate from a database string
                // into a Carbon date object
                'birthdate' => 'date',

                // Laravel automatically hashes the password
                'password' => 'hashed',

            ];
        }



    /*
    |--------------------------------------------------------------------------
    | User Initials
    |--------------------------------------------------------------------------
    */

    public function initials(): string
    {
        $name = trim(

            ($this->firstname ?? '') . ' ' .

            ($this->lastname ?? '')

        );


        $initials = Str::initials(
            $name,
            true
        );


        return Str::length($initials) > 1

            ? Str::substr($initials, 0, 1)

                . Str::substr($initials, -1)

            : $initials;
    }



     public function beneficiaries(): HasMany
{
    return $this->hasMany(
        Beneficiary::class,
        'UserID',
        'UserID'
    );
}

public function processedActivations()
{
    return $this->hasMany(
        Activation::class,
        'ProcessedBy',
        'UserID'
    );
}

public function directDownlines(): HasManyThrough
    {
        // Activations where THIS user was the referral owner (UserID)
        // and ProcessedBy is the downline user who activated under them.
        return $this->hasManyThrough(
            User::class,
            Activation::class,
            'UserID',        // Foreign key on activations table (referral/upline)
            'UserID',        // Foreign key on users table (downline)
            'UserID',        // Local key on users table
            'ProcessedBy'    // Local key on activations table (downline user ID)
        );
    }

    /**
     * Get all activations processed under this user's referral code.
     */
    public function referralActivations(): HasMany
    {
        return $this->hasMany(Activation::class, 'UserID', 'UserID');
    }

}
