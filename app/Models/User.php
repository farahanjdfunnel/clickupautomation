<?php

namespace App\Models;

use App\Helper\CRM;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    const ROLE_ADMIN = 1;
    const ROLE_LOCATION = 2;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'dob',
        'avatar',
        'location_id',
        'state',
        'city',
        'country',
        'phone'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function crmauth()
    {
        return $this->hasOne(CrmAuths::class, 'user_id');
    }
    public function settings()
    {
        return $this->hasMany(Setting::class);
    }

    public function getSpecificSettings(array $keys)
    {
        return $this->settings()->whereIn('key', $keys)->pluck('value', 'key');
    }
    public static function updateLocationInfo($user)
    {
        $location_id = $user->location_id;
        $fetchLocationsDetail = CRM::crmV2($user->id, 'locations/' . $location_id, 'get');
        if (is_string($fetchLocationsDetail)) {
            $fetchLocationsDetail = json_decode($fetchLocationsDetail, true);
        }

        if ($fetchLocationsDetail && property_exists($fetchLocationsDetail, 'location')) {

            $location = $fetchLocationsDetail->location;
            $user = User::where("location_id", $location_id)->first();
            $user->name = $location->name;
            $user->email = $location->email;
            $user->company_id = $location->companyId;
            $user->phone = $location->phone;
            $user->country = $location->country;
            $user->state = $location->state??'';
            $user->city = $location->city??'';

            $user->save();
            try {
                $url1 = 'https://services.leadconnectorhq.com/contacts';
                $headers = [
                    'Authorization' => "Bearer ".supersetting('private_integration_token'),
                    'Version' => '2021-07-28'
                ];
                $nameParts = explode(' ', trim($user->name), 2);
                $firstName = $nameParts[0] ?? '';
                $lastName = $nameParts[1] ?? null;
                $body = [
                    "firstName" => $firstName,
                    "lastName" => $lastName ?: null, // Don't pass lastName if not available
                    "name" => $user->name,
                    "email" => $user->email,
                    "locationId" => supersetting('location_id'),
                    "tags" => [
                        "app_installed",
                    ],
                ];
                if (is_null($lastName)) {
                    unset($body['lastName']);
                }
                $contactCreated = CRM::makeCall($url1, 'POST', $body, $headers);
                if (is_string($contactCreated)) {
                    $contactCreated = json_decode($contactCreated, true);
                }
                Log::info('Contact Creation Response =>' , $contactCreated);
                Log::info('Header' , $headers);
            } catch (\Throwable $th) {
                Log::info('Contact Creation Failed=>' . $th->getMessage());
            }
        }
        return $user;
    }
}
