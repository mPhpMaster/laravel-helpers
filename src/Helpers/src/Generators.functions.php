<?php
/*
 * Copyright (c) 2020. Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan. 
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna. 
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus. 
 * Vestibulum commodo. Ut rhoncus gravida arcu. 
 */

if ( !function_exists('generator') ) {
    /**
     * @param null $locale
     *
     * @return \Faker\Generator|\Illuminate\Contracts\Foundation\Application|mixed
     */
    function generator($locale = null)
    {
        $generator = app(\Faker\Generator::class);
        if ( !is_null($locale) ) {
            $generator = \Faker\Factory::create(getLocaleByLanguageCode($locale, $locale));
        }

        return $generator;
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
        $code = $code ? strtolower($code) : $code;
        $code = str_contains($code, "_") ? $code : "_{$code}";
        static $localeData = array(
            'aa_DJ', 'aa_ER', 'aa_ET',
            'af_NA', 'af_ZA', 'ak_GH',
            'am_ET', 'ar_AE', 'ar_BH', 'ar_DZ',
            'ar_EG', 'ar_IQ', 'ar_JO', 'ar_KW', 'ar_LB',
            'ar_LY', 'ar_MA', 'ar_OM', 'ar_QA', 'ar_SA',
            'ar_SD', 'ar_SY', 'ar_TN', 'ar_YE',
            'as_IN', 'az_AZ', 'be_BY',
            'bg_BG', 'bn_BD', 'bn_IN',
            'bo_CN', 'bo_IN', 'bs_BA',
            'byn_ER', 'ca_ES',
            'cch_NG', 'cs_CZ',
            'cy_GB', 'da_DK', 'de_AT',
            'de_BE', 'de_CH', 'de_DE', 'de_LI', 'de_LU',
            'dv_MV', 'dz_BT',
            'ee_GH', 'ee_TG', 'el_CY', 'el_GR',
            'en_AS', 'en_AU', 'en_BE', 'en_BW',
            'en_BZ', 'en_CA', 'en_GB', 'en_GU', 'en_HK',
            'en_IE', 'en_IN', 'en_JM', 'en_MH', 'en_MP',
            'en_MT', 'en_NA', 'en_NZ', 'en_PH', 'en_PK',
            'en_SG', 'en_TT', 'en_UM', 'en_US', 'en_VI',
            'en_ZA', 'en_ZW', 'es_AR',
            'es_BO', 'es_CL', 'es_CO', 'es_CR', 'es_DO',
            'es_EC', 'es_ES', 'es_GT', 'es_HN', 'es_MX',
            'es_NI', 'es_PA', 'es_PE', 'es_PR', 'es_PY',
            'es_SV', 'es_US', 'es_UY', 'es_VE',
            'et_EE', 'eu_ES', 'fa_AF',
            'fa_IR', 'fi_FI', 'fil_PH',
            'fo_FO', 'fr_BE', 'fr_CA',
            'fr_CH', 'fr_FR', 'fr_LU', 'fr_MC', 'fr_SN',
            'fur_IT', 'ga_IE',
            'gaa_GH', 'gez_ER', 'gez_ET',
            'gl_ES', 'gsw_CH', 'gu_IN',
            'gv_GB', 'ha_GH', 'ha_NE',
            'ha_NG', 'ha_SD', 'haw_US',
            'he_IL', 'hi_IN', 'hr_HR',
            'hu_HU', 'hy_AM',
            'id_ID', 'ig_NG',
            'ii_CN', 'is_IS',
            'it_CH', 'it_IT',
            'ja_JP', 'ka_GE', 'kaj_NG',
            'kam_KE', 'kcg_NG',
            'kfo_CI', 'kk_KZ', 'kl_GL',
            'km_KH', 'kn_IN',
            'ko_KR', 'kok_IN', 'kpe_GN',
            'kpe_LR', 'ku_IQ', 'ku_IR', 'ku_SY',
            'ku_TR', 'kw_GB', 'ky_KG',
            'ln_CD', 'ln_CG', 'lo_LA',
            'lt_LT', 'lv_LV',
            'mk_MK', 'ml_IN', 'mn_CN',
            'mn_MN', 'mr_IN',
            'ms_BN', 'ms_MY', 'mt_MT',
            'my_MM', 'nb_NO', 'nds_DE',
            'ne_IN', 'ne_NP', 'nl_BE',
            'nl_NL', 'nn_NO',
            'nr_ZA', 'nso_ZA', 'ny_MW',
            'oc_FR', 'om_ET', 'om_KE',
            'or_IN', 'pa_IN', 'pa_PK',
            'pl_PL', 'ps_AF',
            'pt_BR', 'pt_PT', 'ro_MD', 'ro_RO',
            'ru_RU', 'ru_UA', 'rw_RW',
            'sa_IN', 'se_FI', 'se_NO',
            'sh_BA', 'sh_CS', 'sh_YU',
            'si_LK', 'sid_ET', 'sk_SK',
            'sl_SI', 'so_DJ', 'so_ET',
            'so_KE', 'so_SO', 'sq_AL',
            'sr_BA', 'sr_CS', 'sr_ME', 'sr_RS', 'sr_YU',
            'ss_SZ', 'ss_ZA', 'st_LS',
            'st_ZA', 'sv_FI', 'sv_SE',
            'sw_KE', 'sw_TZ', 'syr_SY',
            'ta_IN', 'te_IN', 'tg_TJ',
            'th_TH', 'ti_ER', 'ti_ET',
            'tig_ER', 'tn_ZA',
            'to_TO', 'tr_TR',
            'trv_TW', 'ts_ZA', 'tt_RU',
            'ug_CN', 'uk_UA',
            'ur_IN', 'ur_PK', 'uz_AF', 'uz_UZ',
            've_ZA', 'vi_VN',
            'wal_ET', 'wo_SN', 'xh_ZA',
            'yo_NG', 'zh_CN', 'zh_HK',
            'zh_MO', 'zh_SG', 'zh_TW', 'zu_ZA',
        );

        foreach ($localeData as $locale_name) {
            $locale_name = strtolower($locale_name);
            if ( str_contains($locale_name, $code) ) {
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
        return getLocaleByLanguageCode("{$locale_name}_", $default);
    }
}
