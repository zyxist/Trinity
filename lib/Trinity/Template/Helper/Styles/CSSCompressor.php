<?php
namespace Trinity\Template\Helper\Styles;
/**
 * Kompresja kodu CSS
 * Łączy wiele plików styli, robi czystki i kompresję kodu
 * 
 * @authors		Kamil Brenk <kontakt@kamilbrenk.pl>
 * @license		GPL
 * @link		http://blog.kamilbrenk.pl/css-minify/
 * @version		1.0
**/
class CSSCompressor
{
	private
		/**
		 * Default configuration.
		**/
		$_config = array(
			'charset' => 'utf-8',
			'clean_code' => true,
			'compress_code' => true,
			'import_mode' => true,
			'gzip_contents' => false,
			'gzip_level' => 6,
			'cache_enabled' => true,
			'cache_directory' => 'tmp/',
			'use_cache_browser' => true,
			'time_cache_browser' => 3600
		),
		$_cssCode,
		$_filesRequire = array(),
		$_filesLoaded = array(),
		$_filesImport = array(),
		$_filename = null,
		$_sizes = array(
			array(' 0px', ' 0em', ' 0%', ' 0ex', ' 0cm', ' 0mm', ' 0in', ' 0pt', ' 0pc'),
			array(':0px', ':0em', ':0%', ':0ex', ':0cm', ':0mm', ':0in', ':0pt', ':0pc')
		),
		$_shortcuts = array(
			', '=> ',', ' , ' => ',', ';}'=> '}', '; }' => '}', ' ; }'=> '}',
			' :'=> ':', ': '=> ':', ' {'=> '{', '; '=> ';',
			':black'=> ':#000', ':darkgrey' => ':#666', ':fuchsia'=> ':#F0F',
			':lightgrey'=> ':#CCC', ':orange' => ':#F60', ':white'=> ':#FFF',
			':yellow' => ':#FF0', ':silver' => ':#C0C0C0', ':gray' => ':#808080',
			':maroon' => ':#800000', ':red'=> ':#FF0000', ':purple' => ':#800080',
			':green'=> ':#008000', ':lime' => ':#00FF00', ':olive'=> ':#808000',
			':navy' => ':#000080', ':blue' => ':#0000FF', ':teal' => ':#008080',
			':aqua' => ':#00FFFF'
		),
		$_fontWeightToNum = array(
			'lighter' => 100, 'normal' => 400, 'bold' => 700, 'bolder' => 900
		);
	
	/**
	 * Constructs and initializes with provided array.
	 *
	 * @param array $config	optional Options.
	**/
	public function __construct($config=array())
	{
		foreach($config as $name => $type)
		{
			if(in_array($name, $this->_config))
			{
				$this->_config[$name] = $config[$name];
			}
		}
	} // end __construct();

	/**
	 * Add files.
	 *
	 * @param array $files Files array.
	**/
	public function addFiles($files)
	{
		foreach($files as $id => $file)
		{
			$this->addFile(trim($file));
		}
	} // end addFiles();
	
	/**
	 * Add CSS file.
	 *
	 * @param string $file CSS file address.
	**/
	public function addFile($file)
	{
		array_push($this->_filesRequire, $file);
	} // end addFile();
	
	/**
	 * Compress code, remove unneeded whitespaces, change color names into HEX.
	 *
	 * @param string $code CSS code to compress.
	 * @return Compressed code.
	**/
	public function compressCode($code)
	{
		$code = str_replace($this->_sizes[0], ' 0', $code);
		$code = str_replace($this->_sizes[1], ':0', $code);
		$code = str_ireplace(
			array_keys($this->_shortcuts),
			array_values($this->_shortcuts),
			$code
		);
		$search = array(
			1 => '/([^=])#([a-f\\d])\\2([a-f\\d])\\3([a-f\\d])\\4([\\s;\\}])/i',
			2 => '/url\([\'"](.*?)[\'"]\)/s',
			3 => '/(font-weight|font):([a-z- ]*)(normal|bolder|bold|lighter)/ie'
		);
		$replace = array(
			1 => '$1#$2$3$4$5',
			2 => 'url($1)',
			3 => '"$1:$2" . $this->_fontWeightToNum["$3"]'
		);
		$code = preg_replace($search, $replace, $code);
		return $code;
	} // end compressCode();
	
	/**
	 * Cleans CSS code, reduces unneeded whitespaces, removes comments and charset declarations.
	 *
	 * @param string $code CSS code to clean.
	 * @return Cleaned code.
	**/
	public function cleanCode($code)
	{
		$code = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', null, $code);	
		$code = str_replace (array("\r\n", "\r", "\n", "\t", '  ', '    '), null, $code);
		return $code;
	} // end cleanCode();

	/**
	 * Import file from @import
	 *
	 * @param string $result CSS file address.
	 * @return Retreived and minified CSS code.
	**/
	private function _import($result)
	{
		$filename = !empty($result[1]) ? $result[1] : $result[2];	
		if($this->_config['import_mode'])
		{
			return $this->_getCSSCode(array($filename));
		}
		$this->_filesImport += array($filename => $result[3]);
	} // end _import();
	
	/**
	 * Creates @import code.
	 *
	 * @return string @import code.
	**/
	private function getImportRules()
	{
		$code = null;
		foreach($this->_filesImport as $filename => $type)
		{
			$code .= '@import url("'.$filename.'") '.$type.';';
		}
		return $code;
	} // end getImportRules();
	
	/**
	 * Gets and minifies CSS code.
	 *
	 * @param array $files Files do minify.
	 * @return string Minified code.
	**/
	private function _getCSSCode($files)
	{
		$fullCode = null;
		foreach($files as $id => $file)
		{
			if(!in_array($file, $this->_filesLoaded) && ($code = @file_get_contents($file)))
			{
				array_push($this->_filesLoaded, $file);
				$code = preg_replace('/@charset\s["\']([0-9A-Za-z-]+)["\'];?/', null, $code);
				$code = preg_replace_callback(
					'/@import\s(?:url\([\'"]?([^\'"]+)[\'"]?\)|["\'](.+)[\'"])\s?([ A-Za-z0-9,]*)?;/',
					array($this, '_import'),
					$code
				);
				if($this->_config['clean_code'])
				{
					$code = $this->cleanCode($code);
				}
				if($this->_config['compress_code'])
				{
					$code = $this->compressCode($code);
				}
				$fullCode .= $code;
				unset($code);
			}
		}
		return $fullCode;
	} // end _getCSSCode();
	
	/**
	 * Check cache modify time.
	**/
	private function checkModifiedFiles()
	{
		$cacheLife = @filemtime($this->_config['cache_directory'].$this->_getCacheName());
		foreach($this->_filesRequire as $id => $file)
		{
			if(file_exists($file))
			{
				$fileLife = @filemtime($file);
				if($fileLife > $cacheLife)
				{
					@unlink($this->_config['cache_directory'].$this->_getCacheName());
				}
			}
		}
	} // end checkModifiedFiles();
	
	/**
	 * Creates cache file name.
	 *
	 * reutrn string Cache file name.
	**/
	private function _getCacheName()
	{
		if($this->_filename == null)
		{
			if($this->_config['gzip_contents'])
			{
				$this->_filename = md5(serialize($this->_config).implode('', $this->_filesRequire)).'.php';
			}
			else
			{
				$this->_filename = md5(serialize($this->_config).implode('', $this->_filesRequire)).'.css';
			}
		}
		return $this->_filename;
	} // end _getCacheName();

	/**
	 * Return saved code file address.
	 *
	 * @return string Minified CSS file address.
	**/
	public function minifyCode()
	{
		$this->checkModifiedFiles();
		if(!($this->_cssCode = @file_get_contents($this->_config['cache_directory'].$this->_getCacheName())))
		{
			$cssCode = $this->_getCSSCode($this->_filesRequire);
			//$this->_cssCode .= '@charset "'.$this->_config['charset'].'";';
			$this->_cssCode .= $this->getImportRules();
			$this->_cssCode .= $cssCode;
			$this->_cssCode .= "\n// generated by CSS Minify (http://blog.kamilbrenk.pl/css-minify/)";
		//echo $this->_getCacheName(); exit;
			file_put_contents($this->_config['cache_directory'].$this->_getCacheName(), ($this->_config['gzip_contents']?$this->gzip():$this->_cssCode));
			unset($cssCode);
		}
		return $this->_config['cache_directory'].$this->_getCacheName();
	} // end showCode();

	protected function gzip()
	{
		$return = '<?php header(\'Content-Type: text/css; charset='.$this->_config['charset'].'\');';
		if($this->_config['use_cache_browser'])
		{
			$return .= 'header(\'Last-Modified: '.gmdate('D, d M Y H:i:s', @filemtime($this->_config['cache_directory'].$this->_getCacheName())).' GMT\');
			header(\'Cache-Control: public, must-revalidate, max-age='.$this->_config['time_cache_browser'].'\');
			header(\'Expires: '.gmdate('D, d M Y H:i:s', time() + $this->_config['time_cache_browser']).' GMT\');';
		}
		else
		{
			$return .= 'header(\'Cache-Control: no-cache, must-revalidate\');
			header(\'Expires: Sat, 26 Jul 1997 05:00:00 GMT\');';
		}
		if(extension_loaded("zlib") && !ini_get('zlib.output_compression'))
		{
			$return .= 'header(\'Content-Encoding: gzip\');';
		}
		$return .= 'header(\'Content-Length: '.strlen($this->_cssCode).'\');';
		$return .= 'echo gzencode(\''.$this->_cssCode.'\', '.$this->_config['gzip_level'].');';
		return $return;
	} // end gzip();
} // end CSSCompressor;