<?php declare(strict_types=1);


namespace DIW\Components\CmsBlockElement;


use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class HydrationHandler
{
    /** @var string */
    private $originDir;

    /** @var string */
    private $targetDir;

    /** @var array */
    private $replaceMap;

    /** @var Filesystem */
    private $fileSystem;

    /**
     * Class ComposerHydrationHandler Constructor.
     *
     * @param string $originDir
     * @param string $targetDir
     * @param array $replaceMap
     */
    public function __construct(string $originDir, string $targetDir, array $replaceMap)
    {
        $this->originDir = $originDir;
        $this->targetDir = $targetDir;
        $this->replaceMap = $replaceMap;
        $this->fileSystem = new Filesystem();
    }

    public function hydrateFileContents(): void
    {
        $finder = new Finder();
        $finder->in($this->originDir);
        $finder->ignoreDotFiles(false);
        $finder->notPath('vendor');
        $finder->notName('composer.json');

        # Find files.
        foreach ($this->replaceMap as $search => $replace) {
            # Restrict files by search.
            $finder->contains($search);
        }

        $count = \iterator_count($finder);
        if (!$count) {
            return;
        }

        foreach ($finder as $file) {
            $filePath = $file->getPathname();
            $filePath = \str_replace($this->originDir, $this->targetDir, $filePath);

            # Replace values.
            $fileContent = \str_replace(\array_keys($this->replaceMap), \array_values($this->replaceMap), $file->getContents());
            $filePath = \str_replace(\array_keys($this->replaceMap), \array_values($this->replaceMap), $filePath);

            # Save file with new replaced content.
            if ($file->isFile()) {
                $this->fileSystem->dumpFile($filePath, $fileContent);
            }
        }
    }

    public function hydrateFolders(): void
    {
        $finder = new Finder();
        $finder->in($this->originDir);
        $finder->ignoreDotFiles(false);
        $finder->exclude('vendor');
        $finder->directories();

        foreach ($this->replaceMap as $search => $replace) {
            # Restrict files by search.
            $finder->name(".*$search*");
            $finder->name("*$search*");
        }

        $count = \iterator_count($finder);
        if (!$count) {
            return;
        }

        /** @var \SplFileInfo $finderItem */
        foreach (\iterator_to_array($finder, true) as $finderItem) {
            $currentName = $finderItem->getPathname();
            $currentName = \str_replace($this->originDir, $this->targetDir, $currentName);
            $newName = \str_replace(\array_keys($this->replaceMap), \array_values($this->replaceMap), $currentName, $count);
            # create dir
            $this->fileSystem->mkdir($newName);
        }
    }

    public function postHydrate(string $corePluginPath, string $blockType, string $featureName): void
    {
        $filename = \sprintf('%ssrc/Resources/app/administration/src/main.js', $corePluginPath);
        $content = \sprintf("\nimport './module/sw-cms/blocks/%s/%s';\nimport './module/sw-cms/elements/%s';\n", $blockType, $featureName, $featureName);

        $this->fileSystem->appendToFile($filename, $content);
    }
}
