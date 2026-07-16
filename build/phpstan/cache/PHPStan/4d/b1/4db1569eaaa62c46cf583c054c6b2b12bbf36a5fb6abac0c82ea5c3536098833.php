<?php declare(strict_types = 1);

// odsl-/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Renderers
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1-enums',
   'data' => 
  array (
    '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Renderers/AbstractRenderer.php' => 
    array (
      0 => '1567b833d8ca932d31983aff22423c43956a11e14032bdc0263e0e495c77df44',
      1 => 
      array (
        0 => 'paperdoc\\renderers\\abstractrenderer',
      ),
      2 => 
      array (
        0 => 'paperdoc\\renderers\\save',
        1 => 'paperdoc\\renderers\\ensuredirectorywritable',
      ),
      3 => 
      array (
      ),
    ),
    '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Renderers/CsvRenderer.php' => 
    array (
      0 => 'fd3bad9385264eeb450e4568bf6ed6ce95bad3aa6a93b92f28bfe57a0ed2eb68',
      1 => 
      array (
        0 => 'paperdoc\\renderers\\csvrenderer',
      ),
      2 => 
      array (
        0 => 'paperdoc\\renderers\\getformat',
        1 => 'paperdoc\\renderers\\setdelimiter',
        2 => 'paperdoc\\renderers\\setenclosure',
        3 => 'paperdoc\\renderers\\setbom',
        4 => 'paperdoc\\renderers\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Renderers/DocRenderer.php' => 
    array (
      0 => '0e652162ad60304c3367ee7443d130f7178026a5cfdb462f26c83f6318bed292',
      1 => 
      array (
        0 => 'paperdoc\\renderers\\docrenderer',
      ),
      2 => 
      array (
        0 => 'paperdoc\\renderers\\getformat',
        1 => 'paperdoc\\renderers\\render',
        2 => 'paperdoc\\renderers\\save',
        3 => 'paperdoc\\renderers\\builddoc',
        4 => 'paperdoc\\renderers\\collecttext',
        5 => 'paperdoc\\renderers\\calculatefibsize',
        6 => 'paperdoc\\renderers\\buildfib',
        7 => 'paperdoc\\renderers\\buildclx',
      ),
      3 => 
      array (
      ),
    ),
    '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Renderers/DocxRenderer.php' => 
    array (
      0 => '28ef9e79c0df787a27b1dd40c8b50f0545319edb01312857812127851f081ab7',
      1 => 
      array (
        0 => 'paperdoc\\renderers\\docxrenderer',
      ),
      2 => 
      array (
        0 => 'paperdoc\\renderers\\getformat',
        1 => 'paperdoc\\renderers\\render',
        2 => 'paperdoc\\renderers\\save',
        3 => 'paperdoc\\renderers\\builddocx',
        4 => 'paperdoc\\renderers\\wrapdocument',
        5 => 'paperdoc\\renderers\\builddocumentbody',
        6 => 'paperdoc\\renderers\\rendersection',
        7 => 'paperdoc\\renderers\\renderblock',
        8 => 'paperdoc\\renderers\\rendertableofcontents',
        9 => 'paperdoc\\renderers\\renderhorizontalrule',
        10 => 'paperdoc\\renderers\\renderparagraph',
        11 => 'paperdoc\\renderers\\renderheading',
        12 => 'paperdoc\\renderers\\renderparagraphproperties',
        13 => 'paperdoc\\renderers\\renderlist',
        14 => 'paperdoc\\renderers\\renderlistitem',
        15 => 'paperdoc\\renderers\\registerlist',
        16 => 'paperdoc\\renderers\\renderblockquote',
        17 => 'paperdoc\\renderers\\renderquotedparagraph',
        18 => 'paperdoc\\renderers\\rendercodeblock',
        19 => 'paperdoc\\renderers\\renderbookmark',
        20 => 'paperdoc\\renderers\\wrapbookmark',
        21 => 'paperdoc\\renderers\\sanitisebookmarkname',
        22 => 'paperdoc\\renderers\\renderimageblock',
        23 => 'paperdoc\\renderers\\resolveimagedimensions',
        24 => 'paperdoc\\renderers\\renderrun',
        25 => 'paperdoc\\renderers\\renderplainrun',
        26 => 'paperdoc\\renderers\\renderhyperlinkrun',
        27 => 'paperdoc\\renderers\\hyperlinkstyle',
        28 => 'paperdoc\\renderers\\renderrunproperties',
        29 => 'paperdoc\\renderers\\rendertable',
        30 => 'paperdoc\\renderers\\computetablecolumntwips',
        31 => 'paperdoc\\renderers\\rendertablerow',
        32 => 'paperdoc\\renderers\\rendertablecell',
        33 => 'paperdoc\\renderers\\buildsectpr',
        34 => 'paperdoc\\renderers\\preparerunningelements',
        35 => 'paperdoc\\renderers\\buildrunningpartxml',
        36 => 'paperdoc\\renderers\\buildstyles',
        37 => 'paperdoc\\renderers\\buildnumbering',
        38 => 'paperdoc\\renderers\\buildabstractnum',
        39 => 'paperdoc\\renderers\\registerrelationship',
        40 => 'paperdoc\\renderers\\builddocumentrels',
        41 => 'paperdoc\\renderers\\buildcontenttypes',
        42 => 'paperdoc\\renderers\\buildrootrels',
        43 => 'paperdoc\\renderers\\buildcoreprops',
        44 => 'paperdoc\\renderers\\buildappprops',
        45 => 'paperdoc\\renderers\\extensionformime',
        46 => 'paperdoc\\renderers\\detectmime',
        47 => 'paperdoc\\renderers\\escapexml',
      ),
      3 => 
      array (
      ),
    ),
    '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Renderers/HtmlRenderer.php' => 
    array (
      0 => '6dd9632b4b0eb7dd20fc10b20419da815b633da20fa3d0dad071a32e30115deb',
      1 => 
      array (
        0 => 'paperdoc\\renderers\\htmlrenderer',
      ),
      2 => 
      array (
        0 => 'paperdoc\\renderers\\getformat',
        1 => 'paperdoc\\renderers\\buildheadmetatags',
        2 => 'paperdoc\\renderers\\render',
        3 => 'paperdoc\\renderers\\rendersection',
        4 => 'paperdoc\\renderers\\renderrunningelement',
        5 => 'paperdoc\\renderers\\buildsectionstyle',
        6 => 'paperdoc\\renderers\\cssbackgroundsize',
        7 => 'paperdoc\\renderers\\resolveimageurl',
        8 => 'paperdoc\\renderers\\renderblock',
        9 => 'paperdoc\\renderers\\rendertableofcontents',
        10 => 'paperdoc\\renderers\\renderhorizontalrule',
        11 => 'paperdoc\\renderers\\rendertextzone',
        12 => 'paperdoc\\renderers\\rendertextzoneblocks',
        13 => 'paperdoc\\renderers\\rendertextzoneinline',
        14 => 'paperdoc\\renderers\\firstparagraphstyle',
        15 => 'paperdoc\\renderers\\firstrunstyle',
        16 => 'paperdoc\\renderers\\estimatelineclamp',
        17 => 'paperdoc\\renderers\\renderheading',
        18 => 'paperdoc\\renderers\\renderlist',
        19 => 'paperdoc\\renderers\\renderlistitem',
        20 => 'paperdoc\\renderers\\renderblockquote',
        21 => 'paperdoc\\renderers\\rendercodeblock',
        22 => 'paperdoc\\renderers\\renderbookmark',
        23 => 'paperdoc\\renderers\\renderparagraph',
        24 => 'paperdoc\\renderers\\rendertextrun',
        25 => 'paperdoc\\renderers\\registerfootnotemarker',
        26 => 'paperdoc\\renderers\\renderfootnotesblock',
        27 => 'paperdoc\\renderers\\rendertable',
        28 => 'paperdoc\\renderers\\renderparagraphinline',
        29 => 'paperdoc\\renderers\\renderimage',
      ),
      3 => 
      array (
      ),
    ),
    '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Renderers/MarkdownRenderer.php' => 
    array (
      0 => '8d881e2f4f2db0b401a156d9e7f2a0c89db9b32054237cf76b92d04a69dce947',
      1 => 
      array (
        0 => 'paperdoc\\renderers\\markdownrenderer',
      ),
      2 => 
      array (
        0 => 'paperdoc\\renderers\\getformat',
        1 => 'paperdoc\\renderers\\render',
        2 => 'paperdoc\\renderers\\buildfrontmatterdata',
        3 => 'paperdoc\\renderers\\renderfrontmatter',
        4 => 'paperdoc\\renderers\\yamlscalar',
        5 => 'paperdoc\\renderers\\rendersection',
        6 => 'paperdoc\\renderers\\renderblock',
        7 => 'paperdoc\\renderers\\renderheading',
        8 => 'paperdoc\\renderers\\renderlist',
        9 => 'paperdoc\\renderers\\renderblockquote',
        10 => 'paperdoc\\renderers\\rendercodeblock',
        11 => 'paperdoc\\renderers\\renderbookmark',
        12 => 'paperdoc\\renderers\\renderparagraph',
        13 => 'paperdoc\\renderers\\renderruns',
        14 => 'paperdoc\\renderers\\registerfootnotemarker',
        15 => 'paperdoc\\renderers\\renderfootnotesblock',
        16 => 'paperdoc\\renderers\\rendertableofcontents',
        17 => 'paperdoc\\renderers\\githubslug',
        18 => 'paperdoc\\renderers\\formatmarkdownlink',
        19 => 'paperdoc\\renderers\\rendertable',
        20 => 'paperdoc\\renderers\\celltotext',
        21 => 'paperdoc\\renderers\\cellelementtoinline',
        22 => 'paperdoc\\renderers\\renderimage',
      ),
      3 => 
      array (
      ),
    ),
    '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Renderers/PdfRenderer.php' => 
    array (
      0 => '18c29d126da1362c0061c7b5cb68b488365f3b5a2fa084aa479870c40003ac46',
      1 => 
      array (
        0 => 'paperdoc\\renderers\\pdfrenderer',
      ),
      2 => 
      array (
        0 => 'paperdoc\\renderers\\getformat',
        1 => 'paperdoc\\renderers\\render',
        2 => 'paperdoc\\renderers\\save',
        3 => 'paperdoc\\renderers\\buildpdf',
        4 => 'paperdoc\\renderers\\countdeclaredpages',
        5 => 'paperdoc\\renderers\\paintpagechrome',
        6 => 'paperdoc\\renderers\\applypagesetup',
        7 => 'paperdoc\\renderers\\writesection',
        8 => 'paperdoc\\renderers\\drawheaderfooter',
        9 => 'paperdoc\\renderers\\drawrunningelement',
        10 => 'paperdoc\\renderers\\writeblock',
        11 => 'paperdoc\\renderers\\handlepagebreak',
        12 => 'paperdoc\\renderers\\reserveascentfor',
        13 => 'paperdoc\\renderers\\writeheading',
        14 => 'paperdoc\\renderers\\writeparagraph',
        15 => 'paperdoc\\renderers\\writetextrun',
        16 => 'paperdoc\\renderers\\writefootnotemarker',
        17 => 'paperdoc\\renderers\\writesectionfootnotes',
        18 => 'paperdoc\\renderers\\writelist',
        19 => 'paperdoc\\renderers\\writelistitemline',
        20 => 'paperdoc\\renderers\\writeblockquote',
        21 => 'paperdoc\\renderers\\writequotedparagraph',
        22 => 'paperdoc\\renderers\\writecodeblock',
        23 => 'paperdoc\\renderers\\writetable',
        24 => 'paperdoc\\renderers\\cellimagesforpdf',
        25 => 'paperdoc\\renderers\\celltextforpdf',
        26 => 'paperdoc\\renderers\\cellstyleforpdf',
        27 => 'paperdoc\\renderers\\stylesequivalent',
        28 => 'paperdoc\\renderers\\writetextzone',
        29 => 'paperdoc\\renderers\\writetableofcontents',
        30 => 'paperdoc\\renderers\\writehorizontalrule',
        31 => 'paperdoc\\renderers\\hextorgb',
        32 => 'paperdoc\\renderers\\writeimage',
        33 => 'paperdoc\\renderers\\resolveimagepath',
        34 => 'paperdoc\\renderers\\istempfile',
      ),
      3 => 
      array (
      ),
    ),
    '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Renderers/PptRenderer.php' => 
    array (
      0 => 'b550c60fa3cee6f9718e76a29338d99bd3282c3988f509b536027ff1edc251ce',
      1 => 
      array (
        0 => 'paperdoc\\renderers\\pptrenderer',
      ),
      2 => 
      array (
        0 => 'paperdoc\\renderers\\getformat',
        1 => 'paperdoc\\renderers\\render',
        2 => 'paperdoc\\renderers\\save',
        3 => 'paperdoc\\renderers\\buildppt',
        4 => 'paperdoc\\renderers\\buildpowerpointdocument',
        5 => 'paperdoc\\renderers\\buildcurrentuserstream',
        6 => 'paperdoc\\renderers\\collectslidetexts',
        7 => 'paperdoc\\renderers\\atom',
        8 => 'paperdoc\\renderers\\container',
      ),
      3 => 
      array (
      ),
    ),
    '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Renderers/PptxRenderer.php' => 
    array (
      0 => '3783c9222d6da02841e421fffbc8f31f5d525f63d7df4e8dbb7c15215cd2a028',
      1 => 
      array (
        0 => 'paperdoc\\renderers\\pptxrenderer',
      ),
      2 => 
      array (
        0 => 'paperdoc\\renderers\\getformat',
        1 => 'paperdoc\\renderers\\render',
        2 => 'paperdoc\\renderers\\save',
        3 => 'paperdoc\\renderers\\buildpptx',
        4 => 'paperdoc\\renderers\\buildslide',
        5 => 'paperdoc\\renderers\\renderparagraphxml',
        6 => 'paperdoc\\renderers\\renderrunxml',
        7 => 'paperdoc\\renderers\\rendertablexml',
        8 => 'paperdoc\\renderers\\wrapintextbox',
        9 => 'paperdoc\\renderers\\wrapingraphicframe',
        10 => 'paperdoc\\renderers\\buildcontenttypes',
        11 => 'paperdoc\\renderers\\buildrootrels',
        12 => 'paperdoc\\renderers\\buildpresentation',
        13 => 'paperdoc\\renderers\\buildpresentationrels',
        14 => 'paperdoc\\renderers\\buildsliderels',
        15 => 'paperdoc\\renderers\\buildslidemaster',
        16 => 'paperdoc\\renderers\\buildslidemasterrels',
        17 => 'paperdoc\\renderers\\buildslidelayout',
        18 => 'paperdoc\\renderers\\buildslidelayoutrels',
        19 => 'paperdoc\\renderers\\buildtheme',
        20 => 'paperdoc\\renderers\\buildcoremeta',
        21 => 'paperdoc\\renderers\\escapexml',
      ),
      3 => 
      array (
      ),
    ),
    '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Renderers/XlsRenderer.php' => 
    array (
      0 => 'ea41a04506b256bb6cce84443097b29eae75e3e2494398200af98e725ae2388d',
      1 => 
      array (
        0 => 'paperdoc\\renderers\\xlsrenderer',
      ),
      2 => 
      array (
        0 => 'paperdoc\\renderers\\getformat',
        1 => 'paperdoc\\renderers\\render',
        2 => 'paperdoc\\renderers\\save',
        3 => 'paperdoc\\renderers\\buildxls',
        4 => 'paperdoc\\renderers\\collectsheets',
        5 => 'paperdoc\\renderers\\buildsst',
        6 => 'paperdoc\\renderers\\buildworkbookglobals',
        7 => 'paperdoc\\renderers\\buildsheet',
        8 => 'paperdoc\\renderers\\biffrecord',
        9 => 'paperdoc\\renderers\\biffstring',
        10 => 'paperdoc\\renderers\\biffunicodestring',
      ),
      3 => 
      array (
      ),
    ),
    '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Renderers/XlsxRenderer.php' => 
    array (
      0 => '2e45ad3f09b1e97c19329e36e20b2ffa0f0a8affd35382ac7491091a8596a74b',
      1 => 
      array (
        0 => 'paperdoc\\renderers\\xlsxrenderer',
      ),
      2 => 
      array (
        0 => 'paperdoc\\renderers\\getformat',
        1 => 'paperdoc\\renderers\\render',
        2 => 'paperdoc\\renderers\\save',
        3 => 'paperdoc\\renderers\\buildxlsx',
        4 => 'paperdoc\\renderers\\collectsheets',
        5 => 'paperdoc\\renderers\\buildsheet',
        6 => 'paperdoc\\renderers\\colindextoletter',
        7 => 'paperdoc\\renderers\\buildsharedstrings',
        8 => 'paperdoc\\renderers\\buildcontenttypes',
        9 => 'paperdoc\\renderers\\buildrootrels',
        10 => 'paperdoc\\renderers\\buildworkbook',
        11 => 'paperdoc\\renderers\\buildworkbookrels',
        12 => 'paperdoc\\renderers\\buildstyles',
        13 => 'paperdoc\\renderers\\buildcoremeta',
        14 => 'paperdoc\\renderers\\escapexml',
      ),
      3 => 
      array (
      ),
    ),
  ),
));