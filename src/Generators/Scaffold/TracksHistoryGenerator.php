<?php

namespace MediactiveDigital\MedKit\Generators\Scaffold;

// use InfyOm\Generator\Generators\Scaffold\MenuGenerator as InfyOmMenuGenerator;

use MediactiveDigital\MedKit\Common\CommandData;
use MediactiveDigital\MedKit\Traits\Reflection;
use MediactiveDigital\MedKit\Helpers\FormatHelper;

use Str;

class TracksHistoryGenerator {

	use Reflection;

	/**  @var CommandData  */
	private $commandData;

	/**  @var string */
	private $providerContents;
	private $providerTemplate;
	private $path;

	public function __construct(CommandData $commandData) {

		$this->commandData = $commandData;

		$this->path	= $this->commandData->config->pathProviders . $this->commandData->config->addOns['tracks_history.provider_file'];
		$this->providerContents	= file_get_contents($this->path);
		$this->providerTemplate	= get_template('scaffold.tracker.provider');
		$this->providerTemplate	= fill_template($this->commandData->dynamicVars, $this->providerTemplate);
	}

	/**
	 *
	 */
	public function generate() {

		$add = false;

		$this->providerContents = preg_replace_callback('/(# TracksHistory)'
			. '([\s\S]*?)'
			. '(# fin TracksHistory)/', function($matches) use (&$add) {

			if (strpos($matches[2], ucfirst($this->commandData->config->mCamel) . '::observe') !== false) {

				$return = $matches[1] . $matches[2] . $matches[3];
			} else {

				$return = $matches[1] . rtrim($matches[2]) . $this->providerTemplate . $matches[3];

				$add = true;
			}

			return $return;
		}, $this->providerContents);

		if ($add) {

			$this->commandData->commandComment("\n" . $this->commandData->config->mCamelPlural . ' tracker history added.');

			file_put_contents($this->path, $this->providerContents);
		} else {

			$this->commandData->commandObj->info('Tracker history ' . $this->commandData->config->mHumanPlural . ' already exists, Skipping Adjustment.');
		}
	}

	public function rollback() {

		$pattern = preg_replace('/\s+/', '\s*', preg_quote($this->providerTemplate, '/'));
        $providerContents = preg_replace('/' . $pattern . '/', FormatHelper::NEW_LINE . FormatHelper::NEW_LINE, $this->providerContents, -1, $count);

        if ($count) {

            file_put_contents($this->path, $providerContents);
            $this->commandData->commandComment('Tracker history deleted');
        }
	}
}
