<?php

namespace App\Http\Controllers;

use App\Jobs\ClientWelcomeEmail;
use App\Models\RetailerType;
use App\Models\Shop;
use App\Models\User;
use Error;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PartnerRegisterController extends Controller
{
    public function registerForm()
    {
        return view('auth.partner.register');
    }

    public function registerStore(Request $request)
    {

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'type' => ['required'],
            'referral' => ['nullable'],
            'type' => ['required', 'in:2,5,7,9'],
            "city" => "required|string",
            "country" => "required|string",
            "state" => "required|string",
            "address" => "required|string",
            "post_code" => "required|string",
        ];

        if ($request->type != 2) {
            $rules['username'] = ['required', 'string', 'max:255', 'alpha_num', 'unique:shops,user_name'];
        }

        $parent = null;
        $data = [
            'name' => $request->name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' =>  Hash::make($request->password),
            'role_id' => $request->type,

        ];
        $parent = new User();
        if ($request->filled('referral') && User::where('id', $request->referral)->first()) {
            $parent = User::where('id', $request->referral)->first();
        }

        $data['partner_id'] = $parent->id;
        if (session('user_id')) {
            $data['partner_id'] = session('user_id');
        }
        $request->validate($rules);


        try {
            DB::beginTransaction();
            User::unsetEventDispatcher();
            $user = User::create($data);
            $user->createMetas([
                'country' => $request->country,
                'city' => $request->city,
                'address' => $request->address,
                'post_code' => $request->post_code,
                'state' => $request->state,
            ]);



            if ($request->type != 2) {

                $type =  RetailerType::where('rank', 1)->first()->id;
                if ($user->role_id == 5) {
                    $type =  RetailerType::where('rank', 0)->first()->id;
                }
                if ($user->role_id == 9) {
                    $type =  RetailerType::where('rank', -1)->first()->id;
                }
                $user->retailer()->create([
                    'type' => $type,
                    'parent_id' => $parent ? $parent->id : null
                ]);


                $shop = Shop::create([
                    'user_id' => $user->id,
                    'user_name' => $request->username,
                    'retailer_id' => $user->id,
                    'terms' => setting('terms.no'),
                    'contract_signed' => true,
                    'establishment' => true,
                    'can_provide_service' => true
                ]);
            }
            DB::commit();
            if ($user->role_id == 7) {
                $user->createMetas(['questions' => $request->question]);
            }

            ClientWelcomeEmail::dispatch($user);

            Auth::login($user);
            return redirect($user->dashboardUrl());
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->getMessage());
        } catch (Error $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
}
