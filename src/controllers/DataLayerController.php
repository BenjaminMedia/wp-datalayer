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
        $this->data['pageBrand'] = $this->siteService->brandCode();
        $this->data['contentType'] = $this->pageService->contentType();
        $this->data['siteType'] = $this->siteService->siteType();
        $this->data['contentAuthor'] = $this->pageService->contentAuthor();
        $this->data['pageId'] = $this->pageService->pageId();
        $this->data['pageName'] = $this->pageService->pageName();
        $this->data['contentPublication'] = $this->pageService->contentPublication();
        $this->data['contentLastModified'] = $this->pageService->contentLastModified();

        return $this->data;
    }
}