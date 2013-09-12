<?php
/*
 * @name make.php
 * @class MakeSite
 * @description ganerate a static site
 * @author 2dkun
 */

require_once 'lib/Markdown.php';
require_once 'lib/Spyc.php';
require_once 'lib/Tools.php';

class MakeSite {
	protected $config;
	protected $wwwPath;
	protected $filePath;
	protected $pages;	// all pages data
	protected $tmplData;	// used for tmpl

	public function __construct() {
		$this->config = spyc_load_file('config.yml');
		$this->wwwPath = $this->config['path'];
		$this->filePath = $this->wwwPath; // ?
		$this->initPage();
		$this->initTmplData();

        $this->createPages();
		//$this->createArchives();
	}

	// init pages data
	protected function initPage() { // TODO also as arg and GET
		$this->pages = array();
		$index = array();
     
        $sourcedirrecursive = new RecursiveDirectoryIterator( $this->config['sourcedir'] );
        foreach (new RecursiveIteratorIterator($sourcedirrecursive) as $filenamepath => $file) { // ? diffrenc $file  vs $filenamepath

            $directoriesName = explode('/', $filenamepath ) ;
            $filename = array_pop($directoriesName) ;               // e.g. my.page.md
            $directoriesName = implode('/', $directoriesName) ;     // e.g ../_source/mysubdir/

            $filenameExtension = explode('.', $filename ) ;
            array_pop( $filenameExtension ) ;                       // remove fileextension
            $lemma = implode('.', $filenameExtension ) ;            // e.g. my.page



            $page = array();
            $page['url'] = $this->config['baseurl'] . $lemma ;
            $page['filePath'] = $this->config['htmldir'] . $lemma ; // TODO fill inn $directoriesName

            $tmpInfo = getYamlObj( $filenamepath );
            $page['layout'] = $this->filePath['layout'] . $tmpInfo['layout'] . '.php';
            $page['name'] = $tmpInfo['name'];
            $page['index'] = $tmpInfo['index'];
            $page['comment'] = $tmpInfo['comment'];
            
            $page['content'] = getMdHtml( $filenamepath );

            // <!-- more --> cutter //~ TODO move to outermarkdown
            //~ $more = explode('<!--more-->', $item['content']);
            //~ if (count($more) >= 2) {
                //~ $item['more'] = $more[0];
            //~ } else {
                //~ $item['more'] = false;
            //~ }

            $this->pages[] = $page;
            $index[] = $page['index'];


		}
		// sort pages by index
		//print_r($index);
		array_multisort($index, SORT_ASC, $this->pages);
	}

	// init data for tmpl
	protected function initTmplData() {
		$this->tmplData = array(
			'config' => $this->config,
			'pages' => $this->pages,
			'page' => null
		);
		$this->tmplData['config']['siteTitle'] = $this->config['siteName'];
	}

	// create somepage/index.html
	protected function createPages() {
		foreach ($this->pages as $item) {
			$filePath = $item['filePath'];
			if (!is_dir($filePath)) {
				mkdir($filePath);
			}
			$target = $filePath . '/index.html'; // TODO dir lorem/index.html or lorem.html or lorem
			$layout = $item['layout'];
			$this->tmplData['page'] = $item;
			$this->tmplData['config']['siteTitle'] = $item['name'] . ' | ' .$this->tmplData['config']['siteName'];
			makeHtmlFile($target, $layout, $this->tmplData);
			$this->initTmplData();
			suc('page: ' . $item['name']);
		}
	}
}

new MakeSite();

?>
