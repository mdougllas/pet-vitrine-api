<?php

namespace App\Services\FacebookAds;

use App\Helpers\HandleHttpException;
use App\Services\FacebookAds\FacebookAds;
use FacebookAds\Object\AdCreativeLinkData;
use FacebookAds\Object\AdCreativeObjectStorySpec;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FacebookAdsAdCreative
{
    private $account;
    private string $pageId;
    private AdcreativeObjectStorySpec $objectStory;
    private AdCreativeLinkData $linkData;

    /**
     * Instantiates / set up the class.
     *
     * @param  object App\FacebookAdsAccount
     * @param  object FacebookAds\Object\AdCreativeObjectStorySpec
     * @param  object FacebookAds\Object\AdCreativeLinkData
     *
     * @return void
     */
    public function __construct(FacebookAdsAccount $account, AdCreativeObjectStorySpec $objectStory, AdCreativeLinkData $linkData)
    {
        $this->account = $account::adAccountInstance();
        $this->pageId = $account->getPageId();
        $this->objectStory = $objectStory;
        $this->linkData = $linkData;
    }

    /**
     * Lists all Ad Creatives.
     *
     * @return Illuminate\Database\Eloquent\Collection;
     */
    public function listAdCreatives()
    {
        $ads = [];
        $fields = ['name'];
        $cursor = $this->account->getAdCreatives($fields);
        $cursor->setUseImplicitFetch(true);

        foreach ($cursor as $ad) {
            $ads[] = [
                'id' => $ad->id,
                'name' => $ad->name
            ];
        }

        return collect($ads)->all();
    }

    /**
     * Creates the ad creative.
     *
     * @param  string $url
     * @param  string $link
     * @param  string $name
     *
     * @return FacebookAds\Object\AdCreative;
     */
    public function createAdCreative($url, $link, $name)
    {
        // $fields = ['name'];
        // $params = [
        //     'name' => "Ad Creative for $name",
        //     'body' => "Pet Vitrine generated ad creative for $name",
        //     'object_story_spec' => $this->createObjectStorySpec($url, $link, $name),
        //     'degrees_of_freedom_spec' => [
        //         'creative_features_spec' => [
        //             'standard_enhancements' => [
        //                 'enroll_status' => 'OPT_IN'
        //             ]
        //         ]
        //     ]
        // ];

        // $creative = $this->account->createAdCreative($fields, $params);

        $body = [
            "name" => "Testing Creating Creative",
            "object_story_spec" => [
                "link_data" => [
                    "picture" => $url,
                    "link" => $link,
                    "message" => "Hey there! Care for a pet? $name is near you. Adopt a pet instead of buying and experience true animal love."
                ],
                "page_id" => config('services.facebook.page_id')
            ]
        ];

        $endpoint = 'https://graph.facebook.com/v23.0/act_1793082717452005/adcreatives?access_token=EAAC73fSImUcBPHy2kwYaOfrRYZBy11F92QjoZAAL1lJzPecZCbmWvQqxXieZA6jENRosHsIEJElMIxbF8R2rKH1TBMRqfTAVDWywPEoGQHGTQORhQNGRBdc3GH9WkhHgZCUuRviEJSLiPBQc8Dopu27kHD7H07wQeKaUdovhnlBHxyLttBSFBOlJCHT7LwyOv5AZDZD';

        $creative = Http::post($endpoint, $body);

        return (int) $creative->body();
    }

    /**
     * Creates the object story specifications.
     *
     * @param  string $url
     * @param  string $link
     * @param  string $name
     *
     * @return FacebookAds\Object\AdCreativeObjectStorySpec
     */
    private function createObjectStorySpec($url, $link, $name)
    {
        return $this->objectStory->setData([
            'page_id' => $this->pageId,
            'link_data' => $this->createAdLinkData($url, $link, $name)
        ]);
    $body = [
        "name" => "Testing Creating Creative",
        "object_story_spec" => [
            "link_data" => [
                "picture" => "https://dbw3zep4prcju.cloudfront.net/animal/74905f69-9221-4e94-941f-6595c2241cb6/image/1afef9a1-8f3d-496a-8f0b-ed684ef31403.jpg?versionId=xM7I9HMUbBwQ3b6rhfbj0S1FFEfe.IIt&bust=1711898277&width=400",
                "link" => "https://www.petfinder.com/dog/shinzo-58910024/ut/salt-lake-city/community-animal-welfare-society-caws-ut71/",
                "message" => "testing"
            ],
            "page_id" => $pageId
        ]
    ];

    }

    /**
     * Creates the ad link data.
     *
     * @param  string $url
     * @param  string $link
     * @param  string $name
     *
     * @return FacebookAds\Object\AdCreativeLinkData
     */
    private function createAdLinkData($url, $link, $name)
    {
        $imageFile = $this->storeImage($url, $name);
        $image = $this->createAdImage($imageFile);
        $imageProperties = collect($image->images)->first();
        $imageHash = $imageProperties['hash'];

        $data = [
            'image_hash' => $imageHash,
            'link' => $link,
            'call_to_action' => [
                'type' => 'LEARN_MORE'
            ],
            'message' => "Hey there! Care for a pet? $name is near you. Adopt a pet instead of buying and experience true animal love."
        ];

        return $this->linkData->setData($data);
    }

    /**
     * Creates the ad image.
     *
     * @param  string $url
     *
     * @return FacebookAds\Object\AdImage
     */
    private function createAdImage($url)
    {
        $fields = [];
        $params = ['filename' => $url];

        return $this->account->createAdImage($fields, $params);
    }

    /**
     * Stores the ad image on local drive and returns the path.
     *
     * @param  string $url
     * @param  string $name
     *
     * @return string
     */
    private function storeImage($url, $name)
    {
        $image = Http::get($url);
        $image->onError(fn ($err) => HandleHttpException::throw($err));

        Storage::put("pets/$name.png", $image);

        return Storage::path("pets/$name.png");
    }
}
