<?php

namespace App\Services\FacebookAds;

use Illuminate\Support\Facades\Storage;
use App\Services\FacebookAds\FacebookAds;
use FacebookAds\Object\AdCreative;
use FacebookAds\Object\AdCreativeLinkData;
use FacebookAds\Object\AdCreativeObjectStorySpec;

class FacebookAdsPreview
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
     * Get the ad previews.
     *
     * @param  string $url
     * @param  string $link
     * @param  string $name
     *
     * @return array
     */
    public function getPreview($url, $link, $name)
    {
        $fields = [];
        $params = ['ad_format' => 'DESKTOP_FEED_STANDARD'];
        $creativeId = $this->createAdCreative($url, $link, $name);
        $adCreative = new AdCreative($creativeId);

        $previews = $adCreative->getPreviews($fields, $params)
            ->getResponse()
            ->getContent();

        return $previews;
    }

    /**
     * Creates the ad creative.
     *
     * @param  string $url
     * @param  string $link
     * @param  string $name
     *
     * @return integer
     */
    private function createAdCreative($url, $link, $name)
    {
        $fields = [];
        $params = [
            'title' => "Ad Creative for $name",
            'body' => 'Ad Creative body.',
            'object_story_spec' => $this->createObjectStorySpec($url, $link, $name)
        ];

        $creative = $this->account->createAdCreative($fields, $params);

        return $creative->id;
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
            'message' => "Adopt $name, an adoptable pet."
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
        $imageContents = file_get_contents($url, $name);
        $storedFile = Storage::put("pets/$name.png", $imageContents);

        if (!$storedFile) return "Error on saving file."; //todo - proper handle this error

        return Storage::path("pets/$name.png");
    }
}
