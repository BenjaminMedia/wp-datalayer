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

    public function gatherData()
    {
        return [
            'pageId' => $this->pageService->pageId(),
            'userId' => $this->siteService->userID(),
            'pageName' => $this->pageService->pageName(),
            'pageStatus' => $this->pageService->pageStatus(),
            'pageMarket' => $this->siteService->pageMarket(),
            'contentAuthor' => $this->pageService->contentAuthor(),
            'pageCMS' => mb_strtolower($this->siteService->pageCMS()),
            'userLoginStatus' => $this->siteService->userLoginStatus(),
            'siteType' => mb_strtolower($this->siteService->siteType()),
            'pageBrand' => mb_strtolower($this->siteService->brandCode()),
            'contentTextLength' => $this->pageService->contentTextLength(),
            'pagePillar' => mb_strtolower($this->pageService->pagePillar()),
            'contentPublication' => $this->pageService->contentPublication(),
            'contentType' => mb_strtolower($this->pageService->contentType()),
            'contentLastModified' => $this->pageService->contentLastModified(),
            'pageSubPillar' => mb_strtolower($this->pageService->pageSubPillar()),
            'contentCommercialType' => mb_strtolower($this->pageService->contentCommercialType()),
        ];
    }
}