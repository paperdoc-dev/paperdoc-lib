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
        'creator'       => 'Paperdoc',
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

    /*
    |--------------------------------------------------------------------------
    | OCR Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for Optical Character Recognition on scanned documents.
    |
    | - enabled: true = always run OCR, false = never, 'auto' = auto-detect
    | - driver: the OCR engine to use (currently 'tesseract')
    | - min_text_ratio: auto-detection threshold — if the ratio of text elements
    |   to total elements is below this value, the page is considered scanned
    |
    */

    'ocr' => [
        'enabled'        => function_exists('env') ? env('PAPERDOC_OCR_ENABLED', 'auto') : 'auto',
        'driver'         => 'tesseract',
        'language'       => function_exists('env') ? env('PAPERDOC_OCR_LANGUAGE', 'auto') : 'auto',
        'min_text_ratio' => 0.1,

        // Parallel processing: number of concurrent OCR processes
        // 'auto' = number of CPU cores, or set a fixed integer
        'pool_size'       => function_exists('env') ? env('PAPERDOC_OCR_POOL_SIZE', 'auto') : 'auto',
        'process_timeout' => function_exists('env') ? (int) env('PAPERDOC_OCR_TIMEOUT', 60) : 60,

        'tesseract' => [
            'binary'  => function_exists('env') ? env('TESSERACT_PATH', 'tesseract') : 'tesseract',
            'options' => ['--psm 1', '--oem 3'],
        ],

        /*
        |----------------------------------------------------------------------
        | Post-Processing Pipeline
        |----------------------------------------------------------------------
        |
        | Multi-layer pipeline applied after noise filtering, before LLM.
        |
        | Layer 1 (char_substitution): OCR confusion table (0→O, rn→m, |→I…)
        | Layer 2 (spell_correction):  Levenshtein + dictionary
        | Layer 3 (ngram):             Bigram/trigram language model
        | Layer 4 (patterns):          Date, phone, IBAN, amount recognition
        | Layer 5 (structure):         Heading/paragraph/list detection → Markdown
        |
        */

        'post_processing' => [
            'enabled' => true,

            'char_substitution' => true,

            // Layer 2 — requires a dictionary generated via:
            //   php artisan paperdoc:build-dictionary path/to/texts/
            'spell_correction' => [
                'enabled'         => function_exists('env') ? env('PAPERDOC_SPELL_ENABLED', false) : false,
                'dictionary'      => function_exists('env') ? env('PAPERDOC_SPELL_DICTIONARY') : null,
                'max_distance'    => 1,
                'min_word_length' => 5,
                'ignore'          => [],
            ],

            // Layer 3 — requires a model trained via:
            //   php artisan paperdoc:train-ngram path/to/texts/
            'ngram' => [
                'enabled'           => function_exists('env') ? env('PAPERDOC_NGRAM_ENABLED', false) : false,
                'model_path'        => function_exists('env') ? env('PAPERDOC_NGRAM_MODEL') : null,
                'min_score_ratio'   => 5.0,
                'max_edit_distance' => 1,
            ],

            'patterns' => true,

            'structure' => [
                'enabled'            => true,
                'max_heading_length' => 60,
                'emit_markdown'      => true,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | LLM Augmentation Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for LLM-based post-processing of OCR output.
    | Uses NeuronAI to connect to any supported provider.
    |
    | Supported providers: openai, anthropic, gemini, ollama
    |
    */

    'llm' => [
        'enabled'  => function_exists('env') ? env('PAPERDOC_LLM_ENABLED', false) : false,
        'provider' => function_exists('env') ? env('PAPERDOC_LLM_PROVIDER', 'openai') : 'openai',
        'model'    => function_exists('env') ? env('PAPERDOC_LLM_MODEL', 'gpt-4o-mini') : 'gpt-4o-mini',
        'api_key'  => function_exists('env') ? env('PAPERDOC_LLM_API_KEY') : null,
        'base_url' => function_exists('env') ? env('PAPERDOC_LLM_BASE_URL') : null,

        'options' => [
            'temperature' => 0.1,
            'max_tokens'  => 4096,
        ],
    ],

];
