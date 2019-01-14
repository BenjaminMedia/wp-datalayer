<?php

namespace BonnierDataLayer\Controllers;

use BonnierDataLayer\Services\PageService;
use BonnierDataLayer\Services\SiteService;

class DataLayerController
{
    protected $data = [];
    protected $pageService;
    protected $siteService;

    public function __construct()
    {
        $this->pageService = new PageService();
        $this->siteService = new SiteService();
    }

    public function getData()
    {
        return $this->gatherData();
    }

    private function gatherData()
    {
        $data = [
            'pageId' => $this->pageService->pageId(),
            'pageCMS' => $this->siteService->pageCMS(),
            'pageName' => $this->pageService->pageName(),
            'pageBrand' => $this->siteService->brandCode(),
            'pageStatus' => $this->pageService->pageStatus(),
            'pageMarket' => $this->siteService->pageMarket(),
            'pagePillar' => $this->pageService->pagePillar(),
            'contentType' => $this->pageService->contentType(),
            'contentAuthor' => $this->pageService->contentAuthor(),
            'pageSubPillar' => $this->pageService->pageSubPillar(),
            'userLoginStatus' => $this->siteService->userLoginStatus(),
            'siteType' => mb_strtolower($this->siteService->siteType()),
            'contentTextLength' => $this->pageService->contentTextLength(),
            'contentPublication' => $this->pageService->contentPublication(),
            'contentLastModified' => $this->pageService->contentLastModified(),
            'contentCommercialType' => $this->pageService->contentCommercialType(),
        ];

        if (has_filter('bp_data_modification')) {
            $data = apply_filters('bp_data_modification', $data);
        }

        // Remove null values
        return array_filter($data, 'strlen');
    }
}