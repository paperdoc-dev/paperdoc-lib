<?php declare(strict_types = 1);

// odsl-/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Parsers
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1-enums',
   'data' => 
  array (
    '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Parsers/AbstractParser.php' => 
    array (
      0 => '0fa5f8e7116a70051e5a04659c73441072d516411b7cab7e84a4d443dc35244f',
      1 => 
      array (
        0 => 'paperdoc\\parsers\\abstractparser',
      ),
      2 => 
      array (
        0 => 'paperdoc\\parsers\\assertfilereadable',
      ),
      3 => 
      array (
      ),
    ),
    '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Parsers/CsvParser.php' => 
    array (
      0 => '6cbf6f01f904f2cf519c0fa5024d70dd10b58c42a1d72fd60d1606b238f2488f',
      1 => 
      array (
        0 => 'paperdoc\\parsers\\csvparser',
      ),
      2 => 
      array (
        0 => 'paperdoc\\parsers\\setdelimiter',
        1 => 'paperdoc\\parsers\\setenclosure',
        2 => 'paperdoc\\parsers\\setfirstrowisheader',
        3 => 'paperdoc\\parsers\\supports',
        4 => 'paperdoc\\parsers\\parse',
      ),
      3 => 
      array (
      ),
    ),
    '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Parsers/DocParser.php' => 
    array (
      0 => '0560c055d0ca6f12db431b0bc391209cdc55ddac7ec029def481212850dc285e',
      1 => 
      array (
        0 => 'paperdoc\\parsers\\docparser',
      ),
      2 => 
      array (
        0 => 'paperdoc\\parsers\\supports',
        1 => 'paperdoc\\parsers\\parse',
        2 => 'paperdoc\\parsers\\extracttext',
        3 => 'paperdoc\\parsers\\extractinlinetext',
        4 => 'paperdoc\\parsers\\findutf16letextstart',
        5 => 'paperdoc\\parsers\\lookslikeutf16le',
        6 => 'paperdoc\\parsers\\findcp1252textstart',
        7 => 'paperdoc\\parsers\\parsefib',
        8 => 'paperdoc\\parsers\\gettablestream',
        9 => 'paperdoc\\parsers\\extracttextfrompiecetable',
        10 => 'paperdoc\\parsers\\parseclx',
        11 => 'paperdoc\\parsers\\convertcompressedtext',
        12 => 'paperdoc\\parsers\\fallbacktextextraction',
        13 => 'paperdoc\\parsers\\extractsummaryinfo',
        14 => 'paperdoc\\parsers\\parsesummaryinfo',
        15 => 'paperdoc\\parsers\\buildelements',
        16 => 'paperdoc\\parsers\\readuint16',
        17 => 'paperdoc\\parsers\\readuint32',
        18 => 'paperdoc\\parsers\\readint32',
      ),
      3 => 
      array (
      ),
    ),
    '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Parsers/DocxParser.php' => 
    array (
      0 => 'c17e188e8a97c1899a750e29c93184ec5007ff01355a1308604e96e12a5fe0a0',
      1 => 
      array (
        0 => 'paperdoc\\parsers\\docxparser',
      ),
      2 => 
      array (
        0 => 'paperdoc\\parsers\\supports',
        1 => 'paperdoc\\parsers\\parse',
        2 => 'paperdoc\\parsers\\extractmetadata',
        3 => 'paperdoc\\parsers\\loadrelationships',
        4 => 'paperdoc\\parsers\\loadstyles',
        5 => 'paperdoc\\parsers\\loadnumbering',
        6 => 'paperdoc\\parsers\\parsebody',
        7 => 'paperdoc\\parsers\\parseparagraph',
        8 => 'paperdoc\\parsers\\extractnumberinginfo',
        9 => 'paperdoc\\parsers\\handlelistparagraph',
        10 => 'paperdoc\\parsers\\ensurelistfornumbering',
        11 => 'paperdoc\\parsers\\pruneliststack',
        12 => 'paperdoc\\parsers\\resetliststate',
        13 => 'paperdoc\\parsers\\detectheadinglevel',
        14 => 'paperdoc\\parsers\\extractparagraphstyle',
        15 => 'paperdoc\\parsers\\parseruns',
        16 => 'paperdoc\\parsers\\parserun',
        17 => 'paperdoc\\parsers\\parserunhyperlink',
        18 => 'paperdoc\\parsers\\extractrunstyle',
        19 => 'paperdoc\\parsers\\parsetable',
        20 => 'paperdoc\\parsers\\extracttablestyle',
        21 => 'paperdoc\\parsers\\parsedrawing',
        22 => 'paperdoc\\parsers\\guessmimetype',
        23 => 'paperdoc\\parsers\\numfmttostyle',
        24 => 'paperdoc\\parsers\\getattributevalue',
        25 => 'paperdoc\\parsers\\extractplaintext',
        26 => 'paperdoc\\parsers\\twipstopt',
        27 => 'paperdoc\\parsers\\queryelements',
        28 => 'paperdoc\\parsers\\queryelement',
      ),
      3 => 
      array (
      ),
    ),
    '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Parsers/HtmlParser.php' => 
    array (
      0 => '4c905e4c2081b51e2f2a0ffaf2d933aebeb5417b9e173bcaec3aeab27d02fb33',
      1 => 
      array (
        0 => 'paperdoc\\parsers\\htmlparser',
      ),
      2 => 
      array (
        0 => 'paperdoc\\parsers\\supports',
        1 => 'paperdoc\\parsers\\parse',
        2 => 'paperdoc\\parsers\\parsechildnodes',
        3 => 'paperdoc\\parsers\\parseparagraph',
        4 => 'paperdoc\\parsers\\parseinlinecontent',
        5 => 'paperdoc\\parsers\\addstyledrun',
        6 => 'paperdoc\\parsers\\parsetable',
        7 => 'paperdoc\\parsers\\parseimage',
        8 => 'paperdoc\\parsers\\parsefigure',
        9 => 'paperdoc\\parsers\\parsefallbackelement',
        10 => 'paperdoc\\parsers\\extracttablestyle',
        11 => 'paperdoc\\parsers\\extractparagraphstyle',
        12 => 'paperdoc\\parsers\\extracttextstyle',
        13 => 'paperdoc\\parsers\\parsecssproperties',
        14 => 'paperdoc\\parsers\\parseptvalue',
      ),
      3 => 
      array (
      ),
    ),
    '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Parsers/MarkdownParser.php' => 
    array (
      0 => '9709327e35db553d2eb534a6df76a25fb842a5f4028596e8ef14a3e209d878f6',
      1 => 
      array (
        0 => 'paperdoc\\parsers\\markdownparser',
      ),
      2 => 
      array (
        0 => 'paperdoc\\parsers\\supports',
        1 => 'paperdoc\\parsers\\parse',
        2 => 'paperdoc\\parsers\\extractfrontmatter',
        3 => 'paperdoc\\parsers\\parselines',
        4 => 'paperdoc\\parsers\\parseparagraph',
        5 => 'paperdoc\\parsers\\parsetable',
        6 => 'paperdoc\\parsers\\parsetablerow',
        7 => 'paperdoc\\parsers\\parsetablealignment',
        8 => 'paperdoc\\parsers\\parselist',
        9 => 'paperdoc\\parsers\\parseblockquote',
        10 => 'paperdoc\\parsers\\parsecodeblock',
        11 => 'paperdoc\\parsers\\parseinlinecontent',
        12 => 'paperdoc\\parsers\\offsetcapture',
        13 => 'paperdoc\\parsers\\stripinlineformatting',
      ),
      3 => 
      array (
      ),
    ),
    '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Parsers/PdfParser.php' => 
    array (
      0 => '66ce26302606c599f08007aa5fa5fee2c6309e194ff4ce9292865806a5aa5b95',
      1 => 
      array (
        0 => 'paperdoc\\parsers\\pdfparser',
      ),
      2 => 
      array (
        0 => 'paperdoc\\parsers\\supports',
        1 => 'paperdoc\\parsers\\parse',
        2 => 'paperdoc\\parsers\\extractmetadata',
        3 => 'paperdoc\\parsers\\parseobjects',
        4 => 'paperdoc\\parsers\\unpackobjectstreams',
        5 => 'paperdoc\\parsers\\getrawobject',
        6 => 'paperdoc\\parsers\\buildfontcmaps',
        7 => 'paperdoc\\parsers\\parsecmapstream',
        8 => 'paperdoc\\parsers\\hextoutf8',
        9 => 'paperdoc\\parsers\\resolvepagefontmap',
        10 => 'paperdoc\\parsers\\decodehexviacmap',
        11 => 'paperdoc\\parsers\\findpages',
        12 => 'paperdoc\\parsers\\getallpagestreams',
        13 => 'paperdoc\\parsers\\findxobjectrefs',
        14 => 'paperdoc\\parsers\\extractstreamfromobjnum',
        15 => 'paperdoc\\parsers\\extractstreamfromobject',
        16 => 'paperdoc\\parsers\\looksliketextstream',
        17 => 'paperdoc\\parsers\\extracttextlines',
        18 => 'paperdoc\\parsers\\processgraphicsops',
        19 => 'paperdoc\\parsers\\multiplyctm',
        20 => 'paperdoc\\parsers\\transformpoint',
        21 => 'paperdoc\\parsers\\parsetextblockwithctm',
        22 => 'paperdoc\\parsers\\sortandbuildelements',
        23 => 'paperdoc\\parsers\\clustersegmentsbyx',
        24 => 'paperdoc\\parsers\\looksliketableclusters',
        25 => 'paperdoc\\parsers\\collecttablefromgroups',
        26 => 'paperdoc\\parsers\\countcolumnmatches',
        27 => 'paperdoc\\parsers\\assignsegmentstocolumns',
        28 => 'paperdoc\\parsers\\grouplinesbyy',
        29 => 'paperdoc\\parsers\\extractpageimages',
        30 => 'paperdoc\\parsers\\findimagexobjectrefs',
        31 => 'paperdoc\\parsers\\extractimagefromobject',
        32 => 'paperdoc\\parsers\\rawtopng',
        33 => 'paperdoc\\parsers\\isgarbagetext',
        34 => 'paperdoc\\parsers\\decodepdfstring',
        35 => 'paperdoc\\parsers\\lookslikeutf16be',
        36 => 'paperdoc\\parsers\\extracttjtext',
        37 => 'paperdoc\\parsers\\collapsecidspacing',
      ),
      3 => 
      array (
      ),
    ),
    '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Parsers/PptParser.php' => 
    array (
      0 => '1051ac3a1d00d5f6f0d0ceb99488bee85035e7733719a4e3a7486fa6b16e1cd0',
      1 => 
      array (
        0 => 'paperdoc\\parsers\\pptparser',
      ),
      2 => 
      array (
        0 => 'paperdoc\\parsers\\supports',
        1 => 'paperdoc\\parsers\\parse',
        2 => 'paperdoc\\parsers\\extractslidetexts',
        3 => 'paperdoc\\parsers\\grouptextsintoslides',
        4 => 'paperdoc\\parsers\\cleantext',
        5 => 'paperdoc\\parsers\\extractsummaryinfo',
        6 => 'paperdoc\\parsers\\readuint16',
        7 => 'paperdoc\\parsers\\readuint32',
      ),
      3 => 
      array (
      ),
    ),
    '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Parsers/PptxParser.php' => 
    array (
      0 => '679f88f36202112f7a0c076eddedbac0d7afc5ef431879bd551e978d46c3b126',
      1 => 
      array (
        0 => 'paperdoc\\parsers\\pptxparser',
      ),
      2 => 
      array (
        0 => 'paperdoc\\parsers\\supports',
        1 => 'paperdoc\\parsers\\parse',
        2 => 'paperdoc\\parsers\\getslideorder',
        3 => 'paperdoc\\parsers\\discoverslides',
        4 => 'paperdoc\\parsers\\parseslide',
        5 => 'paperdoc\\parsers\\extractshapetext',
        6 => 'paperdoc\\parsers\\parseparagraph',
        7 => 'paperdoc\\parsers\\detectheadinglevel',
        8 => 'paperdoc\\parsers\\extractrunstyle',
        9 => 'paperdoc\\parsers\\extractslidetable',
        10 => 'paperdoc\\parsers\\extracttextfrombody',
        11 => 'paperdoc\\parsers\\extractslideimages',
        12 => 'paperdoc\\parsers\\extractmetadata',
        13 => 'paperdoc\\parsers\\loadrelationships',
        14 => 'paperdoc\\parsers\\resolverelpath',
        15 => 'paperdoc\\parsers\\guessmimetype',
        16 => 'paperdoc\\parsers\\queryelements',
        17 => 'paperdoc\\parsers\\queryelement',
      ),
      3 => 
      array (
      ),
    ),
    '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Parsers/XlsParser.php' => 
    array (
      0 => '476b1a51347ef339b7532c6f7f7dc3093c3024a9fa1ddbe52b77ff043f211001',
      1 => 
      array (
        0 => 'paperdoc\\parsers\\xlsparser',
      ),
      2 => 
      array (
        0 => 'paperdoc\\parsers\\supports',
        1 => 'paperdoc\\parsers\\parse',
        2 => 'paperdoc\\parsers\\parserecords',
        3 => 'paperdoc\\parsers\\parsesst',
        4 => 'paperdoc\\parsers\\readcontinue',
        5 => 'paperdoc\\parsers\\buildtable',
        6 => 'paperdoc\\parsers\\extractsummaryinfo',
        7 => 'paperdoc\\parsers\\parsesummaryinfo',
        8 => 'paperdoc\\parsers\\readbiffstring',
        9 => 'paperdoc\\parsers\\decoderk',
        10 => 'paperdoc\\parsers\\formatnumber',
        11 => 'paperdoc\\parsers\\unpackfloat',
        12 => 'paperdoc\\parsers\\readuint16',
        13 => 'paperdoc\\parsers\\readuint32',
      ),
      3 => 
      array (
      ),
    ),
    '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Parsers/XlsxParser.php' => 
    array (
      0 => '45b7a4ef4fe5aea09d8a26691ae82cd9720c4ea30451406e7eff6859f70e8c0c',
      1 => 
      array (
        0 => 'paperdoc\\parsers\\xlsxparser',
      ),
      2 => 
      array (
        0 => 'paperdoc\\parsers\\supports',
        1 => 'paperdoc\\parsers\\parse',
        2 => 'paperdoc\\parsers\\loadsharedstrings',
        3 => 'paperdoc\\parsers\\loadworkbook',
        4 => 'paperdoc\\parsers\\parsesheet',
        5 => 'paperdoc\\parsers\\getcellvalue',
        6 => 'paperdoc\\parsers\\colreftoindex',
        7 => 'paperdoc\\parsers\\extractimages',
        8 => 'paperdoc\\parsers\\extractmetadata',
        9 => 'paperdoc\\parsers\\loadrelationships',
        10 => 'paperdoc\\parsers\\guessmimetype',
        11 => 'paperdoc\\parsers\\queryelements',
        12 => 'paperdoc\\parsers\\queryelement',
      ),
      3 => 
      array (
      ),
    ),
  ),
));