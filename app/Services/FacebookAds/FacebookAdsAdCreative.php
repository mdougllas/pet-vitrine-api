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
        $fields = ['name'];
        $params = [
            'name' => "Ad Creative for $name",
            'body' => "Pet Vitrine generated ad creative for $name",
            'object_story_spec' => $this->createObjectStorySpec($url, $link, $name)
        ];

        $creative = $this->account->createAdCreative($fields, $params);

        return $creative;
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
