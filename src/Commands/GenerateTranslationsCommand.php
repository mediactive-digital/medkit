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
     * @var string $resourcePath
     */
    protected $resourcePath;

    /**
     * @var string $vendorPath
     */
    protected $vendorPath;

    /**
     * @var \Illuminate\Translation\FileLoader $resourceFileloader
     */
    protected $resourceFileloader;

    /**
     * @var \Illuminate\Translation\FileLoader $vendorFileloader
     */
    protected $vendorFileloader;

    /**
     * @var array $locales
     */
    protected $locales;

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

        $this->resourcePath = resource_path('lang/');
        $this->vendorPath = base_path('vendor/caouecs/laravel-lang/src/');
        $this->resourceFileloader = new FileLoader($this->filesystem, $this->resourcePath);
        $this->vendorFileloader = new FileLoader($this->filesystem, $this->vendorPath);

        $this->locales = config('laravel-gettext.supported-locales');

        $this->referencePath = resource_path('lang/po_laravel/');
        $this->referenceFile = 'po_laravel.php';
        $this->reference = '../resources/lang/po_laravel/' . $this->referenceFile;

        $this->comment = '// Laravel default translations';

        $this->compiler = new PoCompiler;
    }

    /**
     * Execute the console command.
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

        foreach ($this->locales as $locale) {

            $translationsDatas['langs'][$locale] = [
                'translations' => []
            ];

            $path = resource_path('lang/i18n/' . $locale);
            $translationsDatas['langs'][$locale]['file'] = $path . '/LC_MESSAGES/messages.po';

            if (in_array($locale, $createdLocales)) {

                $this->comment('Locale directory created : ' . $path);
                $this->comment('Locale file created : ' . $translationsDatas['langs'][$locale]['file']);
            }
            else {

                $this->comment('Locale directory already exists : ' . $path);
                $this->comment('Locale file already exists : ' . $translationsDatas['langs'][$locale]['file']);
            }

            $fileloader = null;

            if ($this->filesystem->isDirectory($this->resourcePath . $locale)) {

                $translationsPath = $this->resourcePath . $locale;
                $fileloader = $this->resourceFileloader;
            }

            if (!$fileloader && $this->filesystem->isDirectory($this->vendorPath . $locale)) {

                $translationsPath = $this->vendorPath . $locale;
                $fileloader = $this->vendorFileloader;
            }

            if ($fileloader) {

                $this->comment('Laravel translations directory found : ' . $translationsPath);

                $laravelTranslations = $this->filesystem->files($translationsPath);
                $translationsDatas['langs'][$locale]['catalog'] = file_exists($translationsDatas['langs'][$locale]['file']) ? Parser::parseFile($translationsDatas['langs'][$locale]['file']) : null;

                if ($translationsDatas['langs'][$locale]['catalog']) {

                    foreach ($laravelTranslations as $laravelTranslation) {

                        $this->comment('Laravel translations file found : ' . $translationsPath . '/' . $laravelTranslation->getFilename());

                        $group = $laravelTranslation->getBasename('.php');
                        $translations = $fileloader->load($locale, $group);

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

                            foreach ($translations as $key => $translation) {

                                $translations[$group . '.' . $key] = $translation;
                                unset($translations[$key]);
                            }

                            $translationsDatas['langs'][$locale]['translations'] = array_merge($translationsDatas['langs'][$locale]['translations'], Arr::dot($translations));
                            $translationsDatas['keys'] = array_merge($translationsDatas['keys'], array_keys($translationsDatas['langs'][$locale]['translations']));
                        }
                    }
                }
            }
        }

        if ($translationsDatas['keys']) {

            $translationsDatas['keys'] = array_unique($translationsDatas['keys']);
            sort($translationsDatas['keys']);

            foreach ($translationsDatas['langs'] as $lang => $datas) {

                foreach ($translationsDatas['keys'] as $key) {

                    $translation = '';

                    if (isset($datas['translations'][$key]) && is_string($datas['translations'][$key])) {

                        $translation = $datas['translations'][$key];
                    }
                    else if (Str::startsWith($key, 'validation')) {

                        $translation = str_replace('_', ' ', Str::afterLast($key, '.'));
                    }

                    $entry = $datas['catalog']->getEntry($key) ?: new Entry($key, $translation);
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

            foreach ($translationsDatas['keys'] as $index => $key) {

                $translationsDatas['keys'][$index] = FormatHelper::UNESCAPE . '_i(' . FormatHelper::writeValueToPhp($key) . ')';
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
}
