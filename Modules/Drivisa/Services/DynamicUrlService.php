<?php

namespace Modules\Drivisa\Services;

use GuzzleHttp\Client;

class DynamicUrlService
{
    const BASE_URL = "https://firebasedynamiclinks.googleapis.com/v1/shortLinks?key=AIzaSyB6w2gHLC37VBL0obJpOSmB_vdMhivSRRI";
    const DOMAIN_URL_PREFIX = 'https://link.drivisa.com';
    const ANDROID_PACKAGE_NAME = 'ca.codepaper.drivisa_app_v1';
    const IO_PACKAGE_NAME = 'ca.codepaper.drivisaApp';

    public function dynamicUrl($url)
    {


        try {
            $client = new Client([
                'verify' => false,
            ]);

            $info = [
                'dynamicLinkInfo' => [
                    'domainUriPrefix' => self::DOMAIN_URL_PREFIX,
                    'link' => $url,
                    'androidInfo' => [
                        'androidPackageName' => self::ANDROID_PACKAGE_NAME,
                    ],
                    'iosInfo' => [
                        'iosBundleId' => self::IO_PACKAGE_NAME,
                    ]
                ]
            ];

            $response = $client->post(self::BASE_URL, [
                    'json' => $info
                ]
            );

            $response = json_decode($response->getBody());
            return $response->shortLink;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
