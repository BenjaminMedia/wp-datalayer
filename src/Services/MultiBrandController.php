<?php

namespace BonnierDataLayer\Services;

class MultiBrandController
{
    private $domain = null;

    private $brandCodes = [
        // Illvid
        'illvet.se' => [
            'market' => 'SE',
            'brand' => 'ILL'
        ],
        'illvit.no' => [
            'market' => 'NO',
            'brand' => 'ILL'
        ],
        'tieku.fi' => [
            'market' => 'FI',
            'brand' => 'ILL'
        ],

        // GDS
        'goerdetselv.dk' => [
            'market' => 'DK',
            'brand' => 'GDS'
        ],
        'gds.se' => [
            'market' => 'SE',
            'brand' => 'GDS'
        ],
        'gjoerdetselv.com' => [
            'market' => 'NO',
            'brand' => 'GDS'
        ],
        'teeitse.com' => [
            'market' => 'FI',
            'brand' => 'GDS'
        ],

        // Historienet
        'historienet.dk' => [
            'market' => 'DK',
            'brand' => 'HIS'
        ],
        'varldenshistoria.se' => [
            'market' => 'SE',
            'brand' => 'HIS'
        ],
        'historienet.no' => [
            'market' => 'NO',
            'brand' => 'HIS'
        ],
        'historianet.fi' => [
            'market' => 'FI',
            'brand' => 'HIS'
        ],

        // BoBedre
        'bobedre.dk' => [
            'market' => 'DK',
            'brand' => 'BOB'
        ],
        'bobedre.nu' => [
            'market' => 'NO',
            'brand' => 'BOB'
        ],

        // Bolig Magasinet
        'boligmagasinet.dk' => [
            'market' => 'DK',
            'brand' => 'BOM'
        ],

        // Komputer
        'komputer.dk' => [
            'market' => 'DK',
            'brand' => 'KOM'
        ],
        'komputer.no' => [
            'market' => 'NO',
            'brand' => 'KOM'
        ],
        'pctidningen.se' => [
            'market' => 'SE',
            'brand' => 'KOM'
        ],
        'kotimikro.fi' => [
            'market' => 'FI',
            'brand' => 'KOM'
        ],

        // iForm
        'iform.dk' => [
            'market' => 'DK',
            'brand' => 'IFO'
        ],
        'iform.se' => [
            'market' => 'SE',
            'brand' => 'IFO'
        ],
        'iform.nu' => [
            'market' => 'NO',
            'brand' => 'IFO'
        ],
        'kuntoplus.fi' => [
            'market' => 'FI',
            'brand' => 'IFO'
        ],
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
