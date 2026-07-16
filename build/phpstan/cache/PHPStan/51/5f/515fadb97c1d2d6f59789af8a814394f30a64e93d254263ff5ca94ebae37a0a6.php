<?php declare(strict_types = 1);

// odsl-/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Ocr
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1-enums',
   'data' => 
  array (
    '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Ocr/OcrManager.php' => 
    array (
      0 => 'ce1d79620de27d56bf31dbd35414dfb9846a7971fed42163d81fb4fc70546402',
      1 => 
      array (
        0 => 'paperdoc\\ocr\\ocrmanager',
      ),
      2 => 
      array (
        0 => 'paperdoc\\ocr\\__construct',
        1 => 'paperdoc\\ocr\\getprocessor',
        2 => 'paperdoc\\ocr\\getdetectedlanguage',
        3 => 'paperdoc\\ocr\\setpipeline',
        4 => 'paperdoc\\ocr\\getpipeline',
        5 => 'paperdoc\\ocr\\needsocr',
        6 => 'paperdoc\\ocr\\resolvelanguage',
        7 => 'paperdoc\\ocr\\detectfromimage',
        8 => 'paperdoc\\ocr\\detectlanguagefromtext',
        9 => 'paperdoc\\ocr\\processsection',
        10 => 'paperdoc\\ocr\\processsections',
        11 => 'paperdoc\\ocr\\postprocess',
        12 => 'paperdoc\\ocr\\processimagesparallel',
        13 => 'paperdoc\\ocr\\resolvelanguagefromsections',
        14 => 'paperdoc\\ocr\\processimage',
        15 => 'paperdoc\\ocr\\saveimagetotemp',
        16 => 'paperdoc\\ocr\\cleanocrnoise',
        17 => 'paperdoc\\ocr\\isnoiseline',
        18 => 'paperdoc\\ocr\\isnaturalword',
        19 => 'paperdoc\\ocr\\hasnaturalwords',
        20 => 'paperdoc\\ocr\\cleanlineedges',
        21 => 'paperdoc\\ocr\\stripgarbledprefix',
        22 => 'paperdoc\\ocr\\findfirstimage',
      ),
      3 => 
      array (
      ),
    ),
    '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Ocr/PostProcessing/NgramScorer.php' => 
    array (
      0 => '366d14414f45223504e71d1a302aea34165402d9bb7fb5a71c7bc2130a3e6736',
      1 => 
      array (
        0 => 'paperdoc\\ocr\\postprocessing\\ngramscorer',
      ),
      2 => 
      array (
        0 => 'paperdoc\\ocr\\postprocessing\\__construct',
        1 => 'paperdoc\\ocr\\postprocessing\\setminscoreratio',
        2 => 'paperdoc\\ocr\\postprocessing\\setmaxeditdistance',
        3 => 'paperdoc\\ocr\\postprocessing\\setprotectedwords',
        4 => 'paperdoc\\ocr\\postprocessing\\getname',
        5 => 'paperdoc\\ocr\\postprocessing\\getstats',
        6 => 'paperdoc\\ocr\\postprocessing\\train',
        7 => 'paperdoc\\ocr\\postprocessing\\savemodel',
        8 => 'paperdoc\\ocr\\postprocessing\\loadmodel',
        9 => 'paperdoc\\ocr\\postprocessing\\scorebigram',
        10 => 'paperdoc\\ocr\\postprocessing\\scoreword',
        11 => 'paperdoc\\ocr\\postprocessing\\isknownword',
        12 => 'paperdoc\\ocr\\postprocessing\\process',
        13 => 'paperdoc\\ocr\\postprocessing\\processline',
        14 => 'paperdoc\\ocr\\postprocessing\\findbestcandidate',
        15 => 'paperdoc\\ocr\\postprocessing\\contextscore',
        16 => 'paperdoc\\ocr\\postprocessing\\lookslikepropername',
        17 => 'paperdoc\\ocr\\postprocessing\\tokenize',
        18 => 'paperdoc\\ocr\\postprocessing\\matchcase',
        19 => 'paperdoc\\ocr\\postprocessing\\intmap',
      ),
      3 => 
      array (
      ),
    ),
    '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Ocr/PostProcessing/OcrConfusionCorrector.php' => 
    array (
      0 => 'f8506d01685ba7587eb536335072b34f9fe6f722ea250086a8114c32144f0cf0',
      1 => 
      array (
        0 => 'paperdoc\\ocr\\postprocessing\\ocrconfusioncorrector',
      ),
      2 => 
      array (
        0 => 'paperdoc\\ocr\\postprocessing\\__construct',
        1 => 'paperdoc\\ocr\\postprocessing\\getname',
        2 => 'paperdoc\\ocr\\postprocessing\\process',
        3 => 'paperdoc\\ocr\\postprocessing\\correcttoken',
        4 => 'paperdoc\\ocr\\postprocessing\\defaultwordsubstitutions',
        5 => 'paperdoc\\ocr\\postprocessing\\defaultdigitsubstitutions',
        6 => 'paperdoc\\ocr\\postprocessing\\defaultglobalpatterns',
      ),
      3 => 
      array (
      ),
    ),
    '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Ocr/PostProcessing/PatternValidator.php' => 
    array (
      0 => 'd906616b09e0b574daffafbfccf7e7f9bf7b84966fbdf0230f3e3bcecfdb6d5e',
      1 => 
      array (
        0 => 'paperdoc\\ocr\\postprocessing\\patternvalidator',
      ),
      2 => 
      array (
        0 => 'paperdoc\\ocr\\postprocessing\\__construct',
        1 => 'paperdoc\\ocr\\postprocessing\\getname',
        2 => 'paperdoc\\ocr\\postprocessing\\process',
        3 => 'paperdoc\\ocr\\postprocessing\\builtinrules',
      ),
      3 => 
      array (
      ),
    ),
    '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Ocr/PostProcessing/PipelineFactory.php' => 
    array (
      0 => 'ed3a0f50e344ffdad314007e0b82d98cb46c771eebe1c8e40d9e50e8034bdd97',
      1 => 
      array (
        0 => 'paperdoc\\ocr\\postprocessing\\pipelinefactory',
      ),
      2 => 
      array (
        0 => 'paperdoc\\ocr\\postprocessing\\fromconfig',
        1 => 'paperdoc\\ocr\\postprocessing\\isdisabled',
        2 => 'paperdoc\\ocr\\postprocessing\\stringmap',
        3 => 'paperdoc\\ocr\\postprocessing\\stringkeyedmap',
        4 => 'paperdoc\\ocr\\postprocessing\\stringlist',
        5 => 'paperdoc\\ocr\\postprocessing\\patternrules',
      ),
      3 => 
      array (
      ),
    ),
    '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Ocr/PostProcessing/PostProcessingPipeline.php' => 
    array (
      0 => '88702fc0a1cd95eba5261fc1717d0680b1a6d93bc485768a1239864503873c2b',
      1 => 
      array (
        0 => 'paperdoc\\ocr\\postprocessing\\postprocessingpipeline',
      ),
      2 => 
      array (
        0 => 'paperdoc\\ocr\\postprocessing\\addlayer',
        1 => 'paperdoc\\ocr\\postprocessing\\process',
        2 => 'paperdoc\\ocr\\postprocessing\\getlayers',
        3 => 'paperdoc\\ocr\\postprocessing\\getcontext',
      ),
      3 => 
      array (
      ),
    ),
    '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Ocr/PostProcessing/PostProcessorInterface.php' => 
    array (
      0 => '5f73f2f39fe97547e2905a749df56ee2a94ec7e681a07bcbe2eae2b916249443',
      1 => 
      array (
        0 => 'paperdoc\\ocr\\postprocessing\\postprocessorinterface',
      ),
      2 => 
      array (
        0 => 'paperdoc\\ocr\\postprocessing\\process',
        1 => 'paperdoc\\ocr\\postprocessing\\getname',
      ),
      3 => 
      array (
      ),
    ),
    '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Ocr/PostProcessing/SpellCorrector.php' => 
    array (
      0 => '2f22fe7dd64eb70f8b3f9fd148bb228e80e75e5f9ff41b597b0b8433666778c4',
      1 => 
      array (
        0 => 'paperdoc\\ocr\\postprocessing\\spellcorrector',
      ),
      2 => 
      array (
        0 => 'paperdoc\\ocr\\postprocessing\\__construct',
        1 => 'paperdoc\\ocr\\postprocessing\\getname',
        2 => 'paperdoc\\ocr\\postprocessing\\loadfile',
        3 => 'paperdoc\\ocr\\postprocessing\\trainfromtext',
        4 => 'paperdoc\\ocr\\postprocessing\\savedictionary',
        5 => 'paperdoc\\ocr\\postprocessing\\addword',
        6 => 'paperdoc\\ocr\\postprocessing\\addignorelist',
        7 => 'paperdoc\\ocr\\postprocessing\\getdictionarysize',
        8 => 'paperdoc\\ocr\\postprocessing\\getdictionary',
        9 => 'paperdoc\\ocr\\postprocessing\\filterbyfrequency',
        10 => 'paperdoc\\ocr\\postprocessing\\process',
        11 => 'paperdoc\\ocr\\postprocessing\\processline',
        12 => 'paperdoc\\ocr\\postprocessing\\lookslikepropername',
        13 => 'paperdoc\\ocr\\postprocessing\\findclosest',
        14 => 'paperdoc\\ocr\\postprocessing\\matchcase',
      ),
      3 => 
      array (
      ),
    ),
    '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Ocr/PostProcessing/StructureDetector.php' => 
    array (
      0 => 'fa0ee5f3b3e6876426ce2d021e15dcb4895c433b47dc404b309a381e7551eb73',
      1 => 
      array (
        0 => 'paperdoc\\ocr\\postprocessing\\structuredetector',
      ),
      2 => 
      array (
        0 => 'paperdoc\\ocr\\postprocessing\\__construct',
        1 => 'paperdoc\\ocr\\postprocessing\\getname',
        2 => 'paperdoc\\ocr\\postprocessing\\process',
        3 => 'paperdoc\\ocr\\postprocessing\\detectblocks',
        4 => 'paperdoc\\ocr\\postprocessing\\detectheading',
        5 => 'paperdoc\\ocr\\postprocessing\\detectlistitem',
        6 => 'paperdoc\\ocr\\postprocessing\\isseparator',
        7 => 'paperdoc\\ocr\\postprocessing\\rendermarkdown',
        8 => 'paperdoc\\ocr\\postprocessing\\totitlecase',
      ),
      3 => 
      array (
      ),
    ),
    '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Ocr/ProcessPool.php' => 
    array (
      0 => '62522dcf31f7e8e1bbc1fedff3e2ddd4f965f95cf7ce6dd9f6e3959ee7e1f57b',
      1 => 
      array (
        0 => 'paperdoc\\ocr\\processpool',
      ),
      2 => 
      array (
        0 => 'paperdoc\\ocr\\__construct',
        1 => 'paperdoc\\ocr\\submit',
        2 => 'paperdoc\\ocr\\run',
        3 => 'paperdoc\\ocr\\getqueuesize',
        4 => 'paperdoc\\ocr\\startprocess',
        5 => 'paperdoc\\ocr\\filteroutput',
        6 => 'paperdoc\\ocr\\detectcpucores',
      ),
      3 => 
      array (
      ),
    ),
    '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Ocr/TesseractOcrProcessor.php' => 
    array (
      0 => '9664a74aa920f8ab575d42c246226999324db1e9ebb6f77b02d989799d3a7415',
      1 => 
      array (
        0 => 'paperdoc\\ocr\\tesseractocrprocessor',
      ),
      2 => 
      array (
        0 => 'paperdoc\\ocr\\__construct',
        1 => 'paperdoc\\ocr\\buildcommand',
        2 => 'paperdoc\\ocr\\recognize',
        3 => 'paperdoc\\ocr\\isavailable',
        4 => 'paperdoc\\ocr\\detectscript',
      ),
      3 => 
      array (
      ),
    ),
  ),
));