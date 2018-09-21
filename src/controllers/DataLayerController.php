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
        $this->data['pageId'] = $this->pageService->pageId();
        $this->data['pageName'] = $this->pageService->pageName();
        $this->data['pageStatus'] = $this->pageService->pageStatus();
        $this->data['pageMarket'] = $this->siteService->pageMarket();
        $this->data['contentAuthor'] = $this->pageService->contentAuthor();
        $this->data['pageCMS'] = mb_strtolower($this->siteService->pageCMS());
        $this->data['siteType'] = mb_strtolower($this->siteService->siteType());
        $this->data['pageBrand'] = mb_strtolower($this->siteService->brandCode());
        $this->data['contentTextLength'] = $this->pageService->contentTextLength();
        $this->data['pagePillar'] = mb_strtolower($this->pageService->pagePillar());
        $this->data['contentPublication'] = $this->pageService->contentPublication();
        $this->data['contentType'] = mb_strtolower($this->pageService->contentType());
        $this->data['contentLastModified'] = $this->pageService->contentLastModified();
        $this->data['pageSubPillar'] = mb_strtolower($this->pageService->pageSubPillar());
        $this->data['contentCommercialType'] = mb_strtolower($this->pageService->contentCommercialType());

        return $this->data;
    }
}