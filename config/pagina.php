<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Format
    |--------------------------------------------------------------------------
    |
    | The default output format when none is specified.
    |
    */

    'default_format' => 'pdf',

    /*
    |--------------------------------------------------------------------------
    | Default Text Style
    |--------------------------------------------------------------------------
    |
    | Default typography settings applied to all documents.
    |
    */

    'default_style' => [
        'font_family' => 'Helvetica',
        'font_size'   => 12.0,
        'color'       => '#000000',
    ],

    /*
    |--------------------------------------------------------------------------
    | PDF Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for the native PDF writer.
    |
    */

    'pdf' => [
        'page_width'    => 595.28,  // A4 in points
        'page_height'   => 841.89,
        'margin_top'    => 40,
        'margin_bottom' => 40,
        'margin_left'   => 40,
        'margin_right'  => 40,
        'creator'       => 'Pagina',
    ],

    /*
    |--------------------------------------------------------------------------
    | HTML Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for the HTML writer.
    |
    */

    'html' => [
        'charset' => 'UTF-8',
        'lang'    => 'fr',
    ],

    /*
    |--------------------------------------------------------------------------
    | CSV Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for the CSV writer/parser.
    |
    */

    'csv' => [
        'delimiter' => ',',
        'enclosure' => '"',
        'escape'    => '\\',
        'bom'       => true,
    ],

];
