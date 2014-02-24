<?php

/**
 * Class ComposerManifestGenerator is responsible for parsing manifest.xml file and allowing to generate from it, valid composer.json file.
 */
class ComposerManifestGenerator {

	/**
	 * @var string
	 */
	private $authorName = null;

	/**
	 * @var ElggPluginManifest
	 */
	private $manifest;

	/**
	 * @var array
	 */
	private $result = array();

	/**
	 * @param $path path to manifest.xml file
	 */
	public function __construct($path, $pluginId = null) {
		if (!file_exists($path)) {
			throw new Exception("File $path does not exists!");
		}
		if (!is_readable($path)) {
			throw new Exception("File $path is not readable!");
		}
		$this->manifest = new ElggPluginManifest($path, $pluginId);
	}

	/**
	 * @param $name
	 * @return $this
	 */
	public function setAuthor($name) {
		$this->authorName = $name;
		return $this;
	}

	/**
	 * @return string
	 */
	private function getLicense() {
		/*
			Apache-2.0
			BSD-2-Clause
			BSD-3-Clause
			BSD-4-Clause
			GPL-2.0
			GPL-2.0+
			GPL-3.0
			GPL-3.0+
			LGPL-2.1
			LGPL-2.1+
			LGPL-3.0
			LGPL-3.0+
			MIT
		 */
		$replacements = array(
			'GNU General Public License version 2' => 'GPL-2.0',
			'MIT (X11)' => 'MIT',
		);
		$license = $this->manifest->getLicense();
		if (isset($replacements[$license])) {
			return $replacements[$license];
		}
		return $license;
	}

	public function convert() {
		$manifest = $this->manifest;

		$this->result = array(
			'name' => $this->authorName . '/' . $manifest->getPluginID(),
			'description' => $manifest->getDescription(),
			'version' => $manifest->getVersion(),
			'type' => 'elgg-plugin',
			'keywords' => array_merge(array('elgg', 'plugin'), (array)$manifest->getCategories()),
			'homepage' => $manifest->getWebsite(),
			'license' => $this->getLicense(),
			'authors' => array(
				array(
					'name' => $manifest->getAuthor(),
					'homepage' => $manifest->getWebsite(),
					'role' => 'Developer',
				),
			),
			'support' => array(
				'issues' => $manifest->getBugTrackerURL(),
				'source' => $manifest->getRepositoryURL(),
			),
			'require' => new stdClass(),//TODO figure out core version requirement handling
		);


//		var_dump($this->manifest->getAuthor());
//		var_dump($this->manifest->getVersion());
//		var_dump($this->manifest->getActivateOnInstall());
//		var_dump($this->manifest->getApiVersion());
//		var_dump($this->manifest->getBlurb());
//		var_dump($this->manifest->getRepositoryURL());
//		var_dump($this->manifest->getBugTrackerURL());
//		var_dump($this->manifest->getDonationsPageURL());
//		var_dump($this->manifest->getCategories());
//		var_dump($this->manifest->getConflicts());
//		var_dump($this->manifest->getCopyright());
//		var_dump($this->manifest->getDescription());
//		var_dump($this->manifest->getLicense());
//		var_dump($this->manifest->getPluginID());
//		var_dump($this->manifest->getName());

		return $this->result;
	}
} 