<?php

namespace MediactiveDigital\MedKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Translation\FileLoader;
use Illuminate\Filesystem\Filesystem;

use Sepia\PoParser\Parser;
use Sepia\PoParser\Catalog\Entry;
use Sepia\PoParser\SourceHandler\FileSystem as SepiaFileSystem;
use Sepia\PoParser\PoCompiler;

use Xinax\LaravelGettext\FileSystem as XinaxFileSystem;
use Xinax\LaravelGettext\Config\ConfigManager;

use MediactiveDigital\MedKit\Helpers\FormatHelper;

use Arr;
use Str;

class GenerateTranslationsCommand extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'medkit:generate-translations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Génère les traductions par défaut de Laravel pour Poedit';

    /**
     * @var \Illuminate\Filesystem\Filesystem $filesystem
     */
    protected $filesystem;

    /**
     * @var array $fileloaders
     */
    protected $fileloaders;

    /**
     * @var array $locales
     */
    protected $locales;

    /**
     * @var string $locale
     */
    protected $locale;

    /**
     * @var string $referencePath
     */
    protected $referencePath;

    /**
     * @var string $referenceFile
     */
    protected $referenceFile;

    /**
     * @var string $reference
     */
    protected $reference;

    /**
     * @var string $comment
     */
    protected $comment;

    /**
     * @var \Sepia\PoParser\PoCompiler $compiler
     */
    protected $compiler;

    /**
     * Create a new console command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $filesystem) {

        parent::__construct();

        $this->filesystem = $filesystem;

        $resourcePath = str_replace('\\', '/', lang_path()) . '/';
        $vendorPath = str_replace('\\', '/', base_path('vendor/laravel-lang/lang/src/'));

        $this->fileloaders = [
            $resourcePath => new FileLoader($this->filesystem, $resourcePath),
            $vendorPath => new FileLoader($this->filesystem, $vendorPath)
        ];

        $this->locales = config('laravel-gettext.supported-locales');
        $this->locale = config('laravel-gettext.locale');

        $this->referencePath = str_replace('\\', '/', lang_path('po_laravel/'));
        $this->referenceFile = 'po_laravel.php';
        $this->reference = '../lang/po_laravel/' . $this->referenceFile;

        $this->comment = '// Laravel default translations';

        $this->compiler = new PoCompiler;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        $this->info('Generating Laravel default translations for Poedit');

        $translationsDatas = [
            'keys' => [],
            'langs' => []
        ];

        $referencePathTrimed = substr($this->referencePath, 0, -1);

        if (!$this->filesystem->isDirectory($this->referencePath)) {

            $this->filesystem->makeDirectory($this->referencePath);

            $this->comment('Lang directory created : ' . $referencePathTrimed);
        }
        else {

            $this->comment('Lang directory already exists : ' . $referencePathTrimed);
        }

        $configManager = ConfigManager::create();
        $filesystem = new XinaxFileSystem($configManager->get(), app_path(), storage_path());
        $localesGenerated = $filesystem->generateLocales();
        $createdLocales = [];

        foreach ($localesGenerated as $localePath) {

            $locale = substr($localePath, -2);
            $createdLocales[] = $locale;
        }

        $path = str_replace('\\', '/', lang_path('i18n/'));

        foreach ($this->locales as $locale) {

            $translationsDatas['langs'][$locale] = [
                'translations' => []
            ];

            $localePath = $path . $locale;
            $translationsDatas['langs'][$locale]['file'] = $localePath . '/LC_MESSAGES/messages.po';
            $translationsDatas['langs'][$locale]['catalog'] = file_exists($translationsDatas['langs'][$locale]['file']) ? Parser::parseFile($translationsDatas['langs'][$locale]['file']) : null;

            if (in_array($locale, $createdLocales)) {

                $this->comment('Locale directory created : ' . $localePath);
                $this->comment('Locale file created : ' . $translationsDatas['langs'][$locale]['file']);
            }
            else {

                $this->comment('Locale directory already exists : ' . $localePath);
                $this->comment('Locale file already exists : ' . $translationsDatas['langs'][$locale]['file']);
            }

            $fileloader = $this->getFileloader($locale);

            if ($fileloader) {

                $this->comment('Laravel translations directory found : ' . $fileloader['path']);

                $laravelTranslations = $this->filesystem->files($fileloader['path']);

                if ($translationsDatas['langs'][$locale]['catalog']) {

                    foreach ($laravelTranslations as $laravelTranslation) {

                        $this->comment('Laravel translations file found : ' . $fileloader['path'] . '/' . $laravelTranslation->getFilename());

                        $group = $laravelTranslation->getBasename('.php');
                        $translations = $fileloader['loader']->load($fileloader['locale'], $group);

                        if ($translations) {

                            if ($group == 'validation') {

                                unset($translations['custom']['attribute-name']);

                                if (!$translations['custom']) {

                                    unset($translations['custom']);
                                }

                                if (!$translations['attributes']) {

                                    unset($translations['attributes']);
                                }
                            }

                            $translations = [
                                $group => $translations
                            ];

                            $dotedTranslations = Arr::dot($translations);
                            $translationsDatas['langs'][$locale]['translations'] = array_merge($translationsDatas['langs'][$locale]['translations'], $dotedTranslations);
                            $translationsDatas['keys'] = array_merge($translationsDatas['keys'], array_keys($dotedTranslations));
                        }
                    }
                }
            }
        }

        if ($translationsDatas['keys']) {

            $translationsDatas['keys'] = array_unique($translationsDatas['keys']);
            sort($translationsDatas['keys']);

            foreach ($translationsDatas['langs'] as $lang => $datas) {

                if ($datas['catalog']) {

                    foreach ($translationsDatas['keys'] as $key) {

                        $translation = '';

                        if (isset($datas['translations'][$key]) && is_string($datas['translations'][$key])) {

                            $translation = $datas['translations'][$key];
                        }
                        else if (Str::startsWith($key, 'validation')) {

                            $translation = str_replace('_', ' ', Str::afterLast($key, '.'));
                        }

                        $entry = $datas['catalog']->getEntry($key);

                        if ($entry && !$entry->getMsgStr()) {

                            $entry->setMsgStr($translation);
                        }

                        $entry = $entry ?: new Entry($key, $translation);
                        $references = $entry->getReference();

                        if (!in_array($this->reference, $references)) {

                            $references[] = $this->reference;
                        }

                        $entry->setReference($references);
                        $datas['catalog']->addEntry($entry);
                    }

                    $fileHandler = new SepiaFileSystem($datas['file']);
                    $fileHandler->save($this->compiler->compile($datas['catalog']));

                    $this->comment('Locale file updated : ' . $datas['file']);
                }
            }

            foreach ($translationsDatas['keys'] as $index => $key) {

                unset($translationsDatas['keys'][$index]);
                Arr::set($translationsDatas['keys'], $key, FormatHelper::UNESCAPE . '_i(' . FormatHelper::writeValueToPhp($key) . ')');
            }

            $fileContents = '<?php' . infy_nl_tab(2, 0) . $this->comment . infy_nl_tab(2, 0) . 'return ' . FormatHelper::writeValueToPhp($translationsDatas['keys']) . ';' . infy_nl_tab(1, 0);
            $filePath = $this->referencePath . $this->referenceFile;
            $exists = $this->filesystem->exists($filePath);
            $this->filesystem->replace($filePath, $fileContents);

            if ($exists) {

                $this->comment('Lang file updated : ' . $filePath);
            }
            else {

                $this->comment('Lang file updated : ' . $filePath);
            }
        }
        else {

            $this->error(' #ERR2 [SKIP] No translations found');
        }
    }

    /**
     * Get file loader for locale.
     *
     * @param string $locale
     *
     * @return array $fileloader
     */
    public function getFileloader(string $locale): array {

        $fileloader = [];

        $locales = [
            $locale
        ];

        if ($locale != $this->locale) {

            $locales[] = $this->locale;
        }

        foreach ($locales as $locale) {

            foreach ($this->fileloaders as $path => $loader) {

                $fullPath = $path . $locale;
                $isDir = $this->filesystem->isDirectory($fullPath);

                if (!$isDir) {

                    $directories = $this->filesystem->directories($path);
                    
                    foreach ($directories as $directory) {

                        $basename = $this->filesystem->basename($directory);

                        if (Str::startsWith($basename, $locale . '-')) {

                            $locale = $basename;
                            $fullPath = $path . $locale;
                            $isDir = true;

                            break;
                        }
                    }
                }

                if ($isDir) {

                    $fileloader = [
                        'locale' => $locale,
                        'path' => $fullPath,
                        'loader' => $loader
                    ];

                    break 2;
                }
            }
        }

        return $fileloader;
    }
}
