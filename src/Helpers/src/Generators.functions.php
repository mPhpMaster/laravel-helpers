<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

if ( !defined('CURRENT_GENERATOR') ) {
    define('CURRENT_GENERATOR', 'CURRENT_GENERATOR');
}

if ( !function_exists('getGenerators') ) {
    /**
     * @param null $key
     * @param null $default
     *
     * @return \Faker\Generator|\Faker\Generator[]|mixed|null
     */
    function getGenerators($key = null, $default = null)
    {
        static $generators = [];

        // get all
        if ( is_null($key) ) {
            return $generators;
        }
        // set (pass generators)
        if ( isClosure($key) ) {
            return call_user_func_array($key, [&$generators]);
        }
        // get by key
        $generator = isset($generators[ $key ]) ? [&$generators[ $key ]] : [$default];
        return head($generator);
    }
}

if ( !function_exists('setGenerators') ) {
    /**
     * @param $locale
     * @param $generator
     *
     * @return \Faker\Generator|\Faker\Generator[]|mixed|null
     */
    function setGenerators($locale, $generator)
    {
        return getGenerators(function (&$generators) use ($locale, &$generator) {
            return $generators[ $locale ] = &$generator;
        });
    }
}

if ( !function_exists('generatorExist') ) {
    /**
     * @param $locale
     *
     * @return bool
     */
    function generatorExist($locale) {
        $generators = filterEach(array_keys(getGenerators()), $locale);

        return !empty($generators);
    }
}

if ( !function_exists('generator') ) {
    /**
     * @param string|array|null $locale
     * @param bool              $force_to_create_new
     *
     * @return \Faker\Generator
     */
    function generator($locale = null, $force_to_create_new = false)
    {
        if(!$force_to_create_new && $locale && is_string($locale) && generatorExist($locale)) {
            return getGenerators($locale);
        }

        /** @var \Faker\Generator[] $generators */
        $generators = getGenerators();

        $defaultCode = getLanguageCodeByLocale(config('app.faker_locale', 'ar_SA'));
        $getLocale = function ($locale = CURRENT_GENERATOR): string {
            return getLocaleByLanguageCode(($locale ?? CURRENT_GENERATOR) === CURRENT_GENERATOR ? currentLocale(true) : $locale);
        };

        if ( func_num_args() === 1 && is_array($locale) ) {
            $_locale = trim(key($locale) ?: $getLocale());
            return setGenerators($_locale, $locale[ $_locale ]);
        }

        $code = $locale ? getLanguageCodeByLocale($locale) : $defaultCode;
        $locale = $getLocale($locale ?? $defaultCode);
        $code = $code ?? getLanguageCodeByLocale($locale);

        if ( toBoolValue($force_to_create_new) === true ) {
            if ( isset($generators[ $locale ]) ) {
                unset($generators[ $locale ]);
            }
        }
        // get existing generator
        else {
            if ( isset($generators[ $_locale = $locale ]) || isset($generators[ $_locale = $getLocale($locale) ]) ) {
                /** @var \Faker\Generator $return */
                $return = getGenerators($_locale);
                return $return;
            }
        }
        $outCode = getLanguageCodeByLocale($code ?: $locale, $getLocale());

        $return = setGenerators($getLocale($locale), \Faker\Factory::create($outCode));
        /** @var \Faker\Generator $return */
        return $return;
    }
}

if ( !function_exists('uniqueGenerator') ) {
    /**
     * @param string|null $locale
     *
     * @param bool        $reset
     * @param int         $maxRetries
     *
     * @return \Faker\Generator|\Illuminate\Contracts\Foundation\Application|mixed
     */
    function uniqueGenerator($locale = null, $reset = true, $maxRetries = 10000)
    {
        $generator = generator($locale);
        return $generator->unique($reset, $maxRetries);
    }
}

if ( !function_exists('generatorUnique') ) {
    /**
     * @param string|null $locale
     *
     * @param bool        $reset
     * @param int         $maxRetries
     *
     * @return \Faker\Generator|\Illuminate\Contracts\Foundation\Application|mixed
     */
    function generatorUnique($locale = null, $reset = true, $maxRetries = 10000)
    {
        return uniqueGenerator($locale, $reset, $maxRetries);
    }
}

if ( !function_exists('generateMobileNumber') ) {
    /**
     * @param string $format
     * @param null   $locale
     *
     * @return string
     */
    function generateMobileNumber($format = "05########", $locale = null)
    {
        return generator($locale)->numerify($format);
    }
}

if ( !function_exists('getLocaleByLanguageCode') ) {
    /**
     * @param      $code
     * @param null $default
     *
     * @return mixed|string|null
     */
    function getLocaleByLanguageCode($code, $default = null)
    {
        $localeData = getLanguageCodes();
        $code = str_replace('-', '_', strtolower($code));
        $code = data_get(getLocaleInfo($code), 'code', $code);
        $codes = stringContains($code, "_") ? $code : ["_{$code}", "{$code}_"];

        if ( count($locale_name = filterEach($localeData, $codes)) ) {
            $locale_name = head(explode('_', head($locale_name)));
            return $locale_name;
        }

        foreach ($localeData as $locale_name) {
            $locale_name = strtolower($locale_name);

            if ( stringContains($locale_name, $codes) ) {
                $locale_name = head(explode('_', $locale_name));
                return $locale_name;
            }
        }

        return $default;
    }
}

if ( !function_exists('getLanguageCodeByLocale') ) {
    /**
     * @param      $locale_name
     * @param null $default
     *
     * @return array|mixed|null[]|string|null
     */
    function getLanguageCodeByLocale($locale_name, $default = null)
    {
        $localeData = getLanguageCodes();
        $locale = str_replace('-', '_', strtolower($locale_name));
        ['locale' => $locale, 'country' => $country, 'code' => $code] = getLocaleInfo($locale);
        $locale_name = $country ? $code : "{$locale}_";

        if ( count($locale_name = filterEach($localeData, $locale_name)) ) {
            $locale_name = head($locale_name);
            return data_get(getLocaleInfo($locale_name), 'code', $locale_name);
        }

        return $locale . "_" . strtoupper($locale);
    }
}

if ( !function_exists('getLanguageCodes') ) {
    /**
     * @return string[]
     */
    function getLanguageCodes()
    {
        return [
            'aa_DJ',
            'aa_ER',
            'aa_ET',
            'af_NA',
            'af_ZA',
            'ak_GH',
            'am_ET',
            'ar_AE',
            'ar_BH',
            'ar_DZ',
            'ar_EG',
            'ar_IQ',
            'ar_JO',
            'ar_KW',
            'ar_LB',
            'ar_LY',
            'ar_MA',
            'ar_OM',
            'ar_QA',
            'ar_SA',
            'ar_SD',
            'ar_SY',
            'ar_TN',
            'ar_YE',
            'as_IN',
            'az_AZ',
            'be_BY',
            'bg_BG',
            'bn_BD',
            'bn_IN',
            'bo_CN',
            'bo_IN',
            'bs_BA',
            'byn_ER',
            'ca_ES',
            'cch_NG',
            'cs_CZ',
            'cy_GB',
            'da_DK',
            'de_AT',
            'de_BE',
            'de_CH',
            'de_DE',
            'de_LI',
            'de_LU',
            'dv_MV',
            'dz_BT',
            'ee_GH',
            'ee_TG',
            'el_CY',
            'el_GR',
            'en_AS',
            'en_AU',
            'en_BE',
            'en_BW',
            'en_BZ',
            'en_CA',
            'en_GB',
            'en_GU',
            'en_HK',
            'en_IE',
            'en_IN',
            'en_JM',
            'en_MH',
            'en_MP',
            'en_MT',
            'en_NA',
            'en_NZ',
            'en_PH',
            'en_PK',
            'en_SG',
            'en_TT',
            'en_UM',
            'en_US',
            'en_VI',
            'en_ZA',
            'en_ZW',
            'es_AR',
            'es_BO',
            'es_CL',
            'es_CO',
            'es_CR',
            'es_DO',
            'es_EC',
            'es_ES',
            'es_GT',
            'es_HN',
            'es_MX',
            'es_NI',
            'es_PA',
            'es_PE',
            'es_PR',
            'es_PY',
            'es_SV',
            'es_US',
            'es_UY',
            'es_VE',
            'et_EE',
            'eu_ES',
            'fa_AF',
            'fa_IR',
            'fi_FI',
            'fil_PH',
            'fo_FO',
            'fr_BE',
            'fr_CA',
            'fr_CH',
            'fr_FR',
            'fr_LU',
            'fr_MC',
            'fr_SN',
            'fur_IT',
            'ga_IE',
            'gaa_GH',
            'gez_ER',
            'gez_ET',
            'gl_ES',
            'gsw_CH',
            'gu_IN',
            'gv_GB',
            'ha_GH',
            'ha_NE',
            'ha_NG',
            'ha_SD',
            'haw_US',
            'he_IL',
            'hi_IN',
            'hr_HR',
            'hu_HU',
            'hy_AM',
            'id_ID',
            'ig_NG',
            'ii_CN',
            'is_IS',
            'it_CH',
            'it_IT',
            'ja_JP',
            'ka_GE',
            'kaj_NG',
            'kam_KE',
            'kcg_NG',
            'kfo_CI',
            'kk_KZ',
            'kl_GL',
            'km_KH',
            'kn_IN',
            'ko_KR',
            'kok_IN',
            'kpe_GN',
            'kpe_LR',
            'ku_IQ',
            'ku_IR',
            'ku_SY',
            'ku_TR',
            'kw_GB',
            'ky_KG',
            'ln_CD',
            'ln_CG',
            'lo_LA',
            'lt_LT',
            'lv_LV',
            'mk_MK',
            'ml_IN',
            'mn_CN',
            'mn_MN',
            'mr_IN',
            'ms_BN',
            'ms_MY',
            'mt_MT',
            'my_MM',
            'nb_NO',
            'nds_DE',
            'ne_IN',
            'ne_NP',
            'nl_BE',
            'nl_NL',
            'nn_NO',
            'nr_ZA',
            'nso_ZA',
            'ny_MW',
            'oc_FR',
            'om_ET',
            'om_KE',
            'or_IN',
            'pa_IN',
            'pa_PK',
            'pl_PL',
            'ps_AF',
            'pt_BR',
            'pt_PT',
            'ro_MD',
            'ro_RO',
            'ru_RU',
            'ru_UA',
            'rw_RW',
            'sa_IN',
            'se_FI',
            'se_NO',
            'sh_BA',
            'sh_CS',
            'sh_YU',
            'si_LK',
            'sid_ET',
            'sk_SK',
            'sl_SI',
            'so_DJ',
            'so_ET',
            'so_KE',
            'so_SO',
            'sq_AL',
            'sr_BA',
            'sr_CS',
            'sr_ME',
            'sr_RS',
            'sr_YU',
            'ss_SZ',
            'ss_ZA',
            'st_LS',
            'st_ZA',
            'sv_FI',
            'sv_SE',
            'sw_KE',
            'sw_TZ',
            'syr_SY',
            'ta_IN',
            'te_IN',
            'tg_TJ',
            'th_TH',
            'ti_ER',
            'ti_ET',
            'tig_ER',
            'tn_ZA',
            'to_TO',
            'tr_TR',
            'trv_TW',
            'ts_ZA',
            'tt_RU',
            'ug_CN',
            'uk_UA',
            'ur_IN',
            'ur_PK',
            'uz_AF',
            'uz_UZ',
            've_ZA',
            'vi_VN',
            'wal_ET',
            'wo_SN',
            'xh_ZA',
            'yo_NG',
            'zh_CN',
            'zh_HK',
            'zh_MO',
            'zh_SG',
            'zh_TW',
            'zu_ZA',
        ];
    }
}

if ( !function_exists('getLocaleInfo') ) {
    /**
     * @param $locale
     *
     * @return array
     */
    function getLocaleInfo($locale)
    {
        $locale = strtolower(str_ireplace(['-', '_', ' '], '_', $locale));
        $localing = explode('_', $locale . (stringContains($locale, '_') ? "" : "_"));
        return [
            'locale' => $locale = head($localing),
            'country' => $country = last($localing),
            'code' => $locale . ($country ? "_" . strtoupper($country) : ""),
        ];
    }
}
