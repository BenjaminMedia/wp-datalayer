<?php

namespace BonnierDataLayer\Controllers;

use BonnierDataLayer\Services\PageService;
use BonnierDataLayer\Services\SiteService;
use BonnierDataLayer\Services\MultiBrandController;

class DataLayerController
{
    protected $data = [];
    protected $pageService;
    protected $siteService;
    protected $multiBrand;

    public function __construct()
    {
        $this->pageService = new PageService();
        $this->siteService = new SiteService();
        $this->multiBrand = new MultiBrandController();
    }

    public function getData()
    {
        return $this->gatherData();
    }

    private function gatherData()
    {
        $multiBrandOptions = $this->multiBrand->getOption();

        if ($this->siteService->multiBrand()) {
            $brandCode = $multiBrandOptions['brand'];
            $pageMarket = $multiBrandOptions['market'];
            $siteType = $multiBrandOptions['type'];
        } else {
            $brandCode = $this->siteService->brandCode();
            $pageMarket = $this->siteService->pageMarket();
            $siteType = mb_strtolower($this->siteService->siteType());
        }

        $data = [
            'pageId' => $this->pageService->pageId(),
            'pageCMS' => $this->siteService->pageCMS(),
            'pageName' => $this->pageService->pageName(),
            'pageBrand' => $brandCode,
            'pageStatus' => $this->pageService->pageStatus(),
            'pageMarket' => $pageMarket,
            'pagePillar' => $this->pageService->pagePillar(),
            'contentType' => $this->pageService->contentType(),
            'contentAuthor' => $this->pageService->contentAuthor(),
            'pageSubPillar' => $this->pageService->pageSubPillar(),
            'userLoginStatus' => $this->siteService->userLoginStatus(),
            'siteType' => $siteType,
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
