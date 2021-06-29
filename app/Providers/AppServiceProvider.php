<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Validator::extend('validip', function($attribute, $value, $parameters, $validator) {
            // http://stackoverflow.com/a/30143143

            if(!filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                return false;
            }

            $networks = array(
                '10.0.0.0'        =>  '255.0.0.0',        //LAN.
                '25.0.0.0'        =>  '25.255.255.255',        //LAN.
                '172.16.0.0'      =>  '255.240.0.0',      //LAN.
                '192.168.0.0'     =>  '255.255.0.0',      //LAN.
                '127.0.0.0'       =>  '255.0.0.0',        //Loopback.
                '169.254.0.0'     =>  '255.255.0.0',      //Link-local.
                '100.64.0.0'      =>  '255.192.0.0',      //Carrier.
                '192.0.2.0'       =>  '255.255.255.0',    //Testing.
                '198.18.0.0'      =>  '255.254.0.0',      //Testing.
                '198.51.100.0'    =>  '255.255.255.0',    //Testing.
                '203.0.113.0'     =>  '255.255.255.0',    //Testing.
                '192.0.0.0'       =>  '255.255.255.0',    //Reserved.
                '224.0.0.0'       =>  '224.0.0.0',        //Reserved.
                '0.0.0.0'         =>  '255.0.0.0');       //Reserved.

            $ip = @inet_pton($value);
            if (strlen($ip) !== 4) { return false; }

            foreach($networks as $network_address => $network_mask) {
                $network_address   = inet_pton($network_address);
                $network_mask      = inet_pton($network_mask);
                assert(strlen($network_address)    === 4);
                assert(strlen($network_mask)       === 4);
                if (($ip & $network_mask) === $network_address)
                    return false;
            }

            return true;
        });

        \Validator::extend('valid_asset_type', function($attribute, $value, $parameters, $validator) {
            if($value == "shirt" || $value == "tshirt" || $value == "pants") {
                return true;
            } else {
                return false;
            }
        });

        \Validator::extend('valid_image', function($attribute, $value, $parameters, $validator) {
            if(exif_imagetype($value) == IMAGETYPE_JPEG || exif_imagetype($value) == IMAGETYPE_PNG) {
                return true;
            } else {
                return false;
            }
        });

        \Validator::extend('valid_email', function($attribute, $value, $parameters, $validator) {
            if(checkDisposableMail($value)) {
                return false;
            } else {
                return true;
            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
