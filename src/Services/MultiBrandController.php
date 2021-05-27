<?php

namespace BonnierDataLayer\Services;

class MultiBrandController
{
    private $domain = null;

    private $brandCodes = [
        // Illvid
        'illvet.se' => [],
        'illvit.no' => [],
        'tieku.fi' => [],

        // GDS
        'gds.se' => [],
        'gjoerdetselv.com' => [],
        'teeitse.com' => [],

        // Historienet
        'historienet.dk' => [],
        'varldenshistoria.se' => [],
        'historienet.no' => [],
        'historianet.fi' => [],

        // BoBedre
        'bobedre.dk' => [],
        'bobedre.nu' => [],

        // Bolig Magasinet
        'boligmagasinet.dk' => [],

        // Komputer
        'komputer.dk' => [],
        'komputer.no' => [],
        'pctidningen.se' => [],
        'kotimikro.fi' => [],

        // iForm
        'iform.dk' => [],
        'iform.se' => [],
        'iform.nu' => [],
        'kuntoplus.fi' => [],
    ];

    function __construct()
    {
        if (!$this->domain) {
            $tmp_domain = $_SERVER['HTTP_HOST'];
            $domain_explode = explode(':', $tmp_domain);

            if (count($domain_explode) > 1) {
                $tmp_domain = $domain_explode[0];
            }

            $dot_explode = explode('.', $tmp_domain);
            if (count($dot_explode) > 2) {
                $tmp_domain = $dot_explode[count($dot_explode) - 2] . '.' . $dot_explode[count($dot_explode) - 1];
            }

            $this->domain = $tmp_domain;
        }
    }

    /**
     * Site brand code: (3-char) / site-brand-code-error
     * Manual Page Market: (2-char) / page-market-error
     * 
     * Site Type: app
     */
    function getOption()
    {
        if (isset($this->brandCodes[$this->domain])) {
            $output = $this->brandCodes[$this->domain];
        } else {
            $output = [
                'brand' => 'error-missing-brand-code',
                'market' => 'error-missing-market-code'
            ];
        }

        $output['type'] = 'app';

        return $output;
    }
}
