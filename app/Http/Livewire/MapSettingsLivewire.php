<?php

namespace App\Http\Livewire;

use App\Models\Order;
use App\Models\SmsGateway;
use Exception;
use LVR\Colour\Hex;

class MapSettingsLivewire extends BaseLivewireComponent
{

    // App settings
    public $useGoogleOnApp = true;
    public $googleMapKey;
    public $what3wordsApiKey;
    public $opencageApiKey;
    public $radarApiKey;
    public $locationiqApiKey;
    public $placeFilterCountryCodes;
    public $geocoders = ["Google", "Opencage", "Radar", "Locationiq"];
    public $geocoderType;


    public function mount()
    {

        //
        if (!\App::environment('production')) {
            $this->googleMapKey = "XXXXXXXXXXXX";
            $this->what3wordsApiKey = "XXXXXXXXXXXX";
            $this->opencageApiKey = "XXXXXXXXXXXX";
            $this->radarApiKey = "XXXXXXXXXXXX";
            $this->locationiqApiKey = "XXXXXXXXXXXX";
        } else {
            $this->googleMapKey = setting('googleMapKey', "");
            $this->what3wordsApiKey = setting('what3wordsApiKey');
            $this->opencageApiKey = setting('map.opencageApiKey');
            $this->radarApiKey = setting('map.radarApiKey');
            $this->locationiqApiKey = setting('map.locationiqApiKey');
        }
        
        //
        $this->geocoderType = setting('map.geocoderType');
        $this->useGoogleOnApp = (bool) setting('map.useGoogleOnApp', 1);
        $this->placeFilterCountryCodes = setting('map.placeFilterCountryCodes');
    }

    public function render()
    {

        $this->mount();
        return view('livewire.settings.map-settings');
    }


    public function saveAppSettings()
    {

        $this->validate([
            "googleMapKey" => "sometimes|nullable|string",
            "what3wordsApiKey" => "sometimes|nullable|string",
            "opencageApiKey" => "sometimes|nullable|string",
            "radarApiKey" => "sometimes|nullable|string",
            "locationiqApiKey" => "sometimes|nullable|string",
            "geocoderType" => "required",
        ]);

        try {

            $this->isDemo();
            $appSettings = [
                'googleMapKey' => ($this->googleMapKey == "XXXXXXXXXXXX") ? setting('googleMapKey', 'XXXXXXXXXXXX') : $this->googleMapKey,
                'what3wordsApiKey' =>  $this->what3wordsApiKey,
                "map" => [
                    'opencageApiKey' =>  $this->opencageApiKey,
                    'radarApiKey' =>  $this->radarApiKey,
                    'locationiqApiKey' =>  $this->locationiqApiKey,
                    'geocoderType' =>  $this->geocoderType,
                    'useGoogleOnApp' =>  $this->useGoogleOnApp,
                    'placeFilterCountryCodes' =>  $this->placeFilterCountryCodes,
                ],
            ];

            // update the site name
            setting($appSettings)->save();



            $this->showSuccessAlert(__("Map Settings saved successfully!"));
            $this->reset();
        } catch (Exception $error) {
            $this->showErrorAlert($error->getMessage() ?? __("Map Settings save failed!"));
        }
    }
}
