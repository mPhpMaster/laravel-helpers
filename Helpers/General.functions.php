<?php
/**
 * Copyright © 2023 mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

if( !function_exists('columnLocalize') ) {
    /**
     * Localize column name.
     *
     * @param string      $columnName Column name
     * @param string|null $locale     Locale name, Null = current locale name
     *
     * @return string
     */
    function columnLocalize(string $columnName = 'name', ?string $locale = null)
    {
        return ltrim($columnName, '_') . '_' . ($locale ?: currentLocale());
    }
}
