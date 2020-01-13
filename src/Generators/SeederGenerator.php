<?php

namespace MediactiveDigital\MedKit\Generators;

use InfyOm\Generator\Generators\SeederGenerator as InfyOmSeederGenerator;
use InfyOm\Generator\Utils\FileUtil;

use MediactiveDigital\MedKit\Common\CommandData;
use MediactiveDigital\MedKit\Traits\Reflection;
use MediactiveDigital\MedKit\Helpers\FormatHelper;

use DB;

class SeederGenerator extends InfyOmSeederGenerator {

    use Reflection;

    /** 
     * @var CommandData 
     */
    private $commandData;

    /** 
     * @var string 
     */
    private $path;

    /** 
     * @var string 
     */
    private $fileName;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $chunk;

    public function __construct(CommandData $commandData) {

        parent::__construct($commandData);

        $this->commandData = $commandData;
        $this->path = $this->getReflectionProperty('path');
        $this->fileName = $this->getReflectionProperty('fileName');
        $this->limit = config('infyom.laravel_generator.seeds.limit', -1);
        $this->chunk = config('infyom.laravel_generator.seeds.chunk', 1000);
    }

    public function generate() {

        $templateData = get_template('seeds.model_seeder');
        $templateData = fill_template($this->commandData->dynamicVars, $templateData);
        $templateData = str_replace('$SEEDS$', $this->generateSeeds(), $templateData);

        FileUtil::createFile($this->path, $this->fileName, $templateData);

        $this->commandData->commandComment("\nSeeder created: ");
        $this->commandData->commandInfo($this->fileName);
    }

    /**
     * Generate seeds.
     *
     * @return string
     */
    public function generateSeeds() {

    	$seeds = [];
    	$records = DB::table($this->commandData->dynamicVars['$TABLE_NAME$'])->limit($this->limit)->get();

        foreach ($records as $key => $record) {

            $records[$key] = (array)$record;
        }

        $chunks = $this->chunk >= 0 ? $records->chunk($this->chunk)->toArray() : [$records->toArray()];

        foreach ($chunks as $chunk) {

        	$chunk = array_values($chunk);
        	$seeds[] = 'DB::table(\'' . $this->commandData->dynamicVars['$TABLE_NAME$'] . '\')->insert(' . FormatHelper::writeValueToPhp($chunk, 2) . ');';
        }

        return implode(infy_nl_tab(2, 2), $seeds);
    }

    public function updateMainSeeder() {

        $mainSeederContent = file_get_contents($this->commandData->config->pathDatabaseSeeder);
        $className = $this->commandData->config->mPlural . 'TableSeeder';
        $classConstant = $className . '::class';

        preg_match_all('/(\$this->call[\s]*?\()([\s\S]*?)(\)[\s]*?;)/', $mainSeederContent, $matches, PREG_OFFSET_CAPTURE);

        if ($matches[0]) {
            
            $values = [];
                
            foreach ($matches[2] as $seedClasses) {
                
                $seedClasses = preg_replace('/\s+/', '', $seedClasses[0]);
                $seedClasses = trim($seedClasses, '[]),');
                $arrayPrefix = 'array(';
                
                if (substr($seedClasses, 0, strlen($arrayPrefix)) == $arrayPrefix) {
                    
                    $seedClasses = substr($seedClasses, strlen($arrayPrefix));
                }
                
                $values[] = explode(',', $seedClasses);
            }

            if (!in_array($classConstant, array_merge(...$values))) {

                $values = array_unique(end($values));
                $values[] = $classConstant;
                $values = preg_filter('/^/', FormatHelper::UNESCAPE, $values);
                $lastMatch = end($matches[2]);

                $mainSeederContent = substr_replace($mainSeederContent, FormatHelper::writeValueToPhp($values, 2), $lastMatch[1], strlen($lastMatch[0]));
            }
            else {

                $this->commandData->commandObj->info($className . ' entry found in DatabaseSeeder. Skipping Adjustment.');

                return;
            }
        }
        else {

            $newSeederStatement = infy_nl_tab(2, 2) . '$this->call(' . FormatHelper::writeValueToPhp([FormatHelper::UNESCAPE . $classConstant], 2) . ');' . infy_nl_tab(1, 1);

            $mainSeederContent = preg_replace_callback('/(public[\s]*?function[\s]*?run[\s]*?\([\s\S]*?\)[\s]*?{)([\s\S]*?)(})/', function($matches) use ($newSeederStatement) {

                $return = $matches[1] . rtrim($matches[2]) . $newSeederStatement . $matches[3];
        
                return $return;

            }, $mainSeederContent);
        }

        file_put_contents($this->commandData->config->pathDatabaseSeeder, $mainSeederContent);
        $this->commandData->commandComment('Main Seeder file updated.');
    }
}
