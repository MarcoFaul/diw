<?php declare(strict_types=1);

namespace DIW\Commands;


use DIW\Components\CmsBlockElement\HydrationHandler;
use Silly\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

define('BLOCK_TYPE_TEXT', 'text');
define('BLOCK_TYPE_TEXT_IMAGE', 'text-image');
define('BLOCK_TYPE_IMAGE', 'image');
define('BLOCK_TYPE_VIDEO', 'video');
define('BLOCK_TYPE_COMMERCE', 'commerce');
define('BLOCK_TYPE_SIDEBAR', 'sidebar');
define('BLOCK_TYPE_FORM', 'form');
define('BLOCK_TYPE_SPECIAL', 'special');

define('BLOCK_TYPES', [BLOCK_TYPE_TEXT, BLOCK_TYPE_TEXT_IMAGE, BLOCK_TYPE_IMAGE, BLOCK_TYPE_VIDEO, BLOCK_TYPE_COMMERCE, BLOCK_TYPE_SIDEBAR, BLOCK_TYPE_FORM, BLOCK_TYPE_SPECIAL]);

/**
 * @copyright 2020 dasistweb GmbH (https://www.dasistweb.de)
 */
class CmsBlockElementGenerator implements CommandInterface
{
    public static function command(Application $app): void
    {
        $app->command('generate', function (InputInterface $input, OutputInterface $output) {
            $io = new SymfonyStyle($input, $output);
            $helper = $this->getHelperSet()->get('question');
            $blockTypeQuestion = new ChoiceQuestion('Select cms block-type (defaults to text)', BLOCK_TYPES, 0);
            $blockTypeQuestion->setErrorMessage('BlockType %s is invalid.');
            $blockTypeQuestion->setAutocompleterValues(BLOCK_TYPES);
            $blockType = $helper->ask($input, $output, $blockTypeQuestion);

            $featureQuestion = new Question('Enter a feature name (defaults to feature-name)', 'feature-name');
            $featureName = \strtolower($helper->ask($input, $output, $featureQuestion));
            $featureBlockName = \str_replace('-', '_', $featureName);


            #@TODO: enable this again
//            $projectRootPathQuestion = new Question('Define root project path (defaults to current)', getcwd());
            $projectRootPathQuestion = new Question('Define root project path (defaults to current)', '/Users/marcofaul/Desktop/dinzler-shop');
            $projectRootPath = $helper->ask($input, $output, $projectRootPathQuestion);
            # add an slash to the end
            $projectRootPath = \rtrim($projectRootPath, '/') . '/';
            $composerJsonPath = $projectRootPath . 'composer.json';
            if (\file_exists($composerJsonPath) === false) {
                $io->error('Invalid shopware 6 root folder: ' . $composerJsonPath);

                return 1;
            }

            $pluginsFolder = $projectRootPath . 'custom/plugins/';
            $finder = new Finder();
            $finder->in($pluginsFolder);
            $finder->name('*Core');
            $finder->name('*Theme');
            $finder->depth(0);

            $count = \iterator_count($finder);

            if ($count < 2) {
                $io->error('No *Core and/or *Theme plugins found in : ' . $pluginsFolder);

                return 1;
            }

            if ($count > 2) {
                $projectNameQuestion = new Question('Sorry we found multi *Core and *Theme Plugins');
                $projectName = \ucfirst(\strtolower($helper->ask($input, $output, $projectNameQuestion)));
            } else {
                $pluginsArray = \iterator_to_array($finder, true);
                /** @var \SplFileInfo $plugin */
                $plugin = array_shift($pluginsArray);
                $pluginName = $plugin->getBasename();
                if (\strpos($pluginName, 'Core')) {
                    $projectName = \str_replace('Core', '', $pluginName);
                } elseif (\strpos($pluginName, 'Theme')) {
                    $projectName = \str_replace('Theme', '', $pluginName);
                } else {
                    $projectNameQuestion = new Question('Sorry we found multi *Core and *Theme Plugins');
                    $projectName = \ucfirst(\strtolower($helper->ask($input, $output, $projectNameQuestion)));
                }
            }

            $replaceMap = [
                '{BLOCK_TYPE}' => $blockType,
                '{FEATURE_NAME}' => $featureName,
                '{FEATURE_BLOCK_NAME}' => $featureBlockName,
                '{PROJECT_NAME}' => $projectName
            ];

            $hydrationHandler = new HydrationHandler(__DIR__ . '/../stubs/CmsBlockElement/', $projectRootPath, $replaceMap);
            $hydrationHandler->hydrateFolders();
            $hydrationHandler->hydrateFileContents();
            $hydrationHandler->postHydrate(\sprintf('%s%sCore/', $pluginsFolder, $projectName), $blockType, $featureName);

            $io->success('Successfully created cms block & elment feature: '. $featureName);
            $io->note('Please run a build/compile command to see the result.');
            return 0;
        })->descriptions('Installs all dependencies');
    }
}
