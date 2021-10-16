<?php declare(strict_types=1);

namespace DIW\Commands;


use DIW\Components\CmsBlockElement\HydrationHandler;
use Silly\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;

define('BLOCK_TYPE_TEXT', 'text');
define('BLOCK_TYPE_TEXT_IMAGE', 'text-image');
define('BLOCK_TYPE_IMAGE', 'image');
define('BLOCK_TYPE_VIDEO', 'video');
define('BLOCK_TYPE_COMMERCE', 'commerce');
define('BLOCK_TYPE_SIDEBAR', 'sidebar');
define('BLOCK_TYPE_FORM', 'form');
define('BLOCK_TYPE_SPECIAL', 'special');

define('BLOCK_TYPES', [
    BLOCK_TYPE_TEXT,
    BLOCK_TYPE_TEXT_IMAGE,
    BLOCK_TYPE_IMAGE,
    BLOCK_TYPE_VIDEO,
    BLOCK_TYPE_COMMERCE,
    BLOCK_TYPE_SIDEBAR,
    BLOCK_TYPE_FORM,
    BLOCK_TYPE_SPECIAL,
]);


class CmsBlockElementGenerator implements CommandInterface
{
    public static function command(Application $app): void
    {
        $app->command('generate:cms-block', function (InputInterface $input, OutputInterface $output) {
            $io = new SymfonyStyle($input, $output);
            $helper = $this->getHelperSet()->get('question'); // @phpstan-ignore-line

            $blockTypeQuestion = new ChoiceQuestion('Select cms block-type (defaults to text)', BLOCK_TYPES, 0);
            $blockTypeQuestion->setErrorMessage('BlockType %s is invalid.');
            $blockTypeQuestion->setAutocompleterValues(BLOCK_TYPES);
            $blockType = $helper->ask($input, $output, $blockTypeQuestion);

            $featureQuestion = new Question('Enter a feature name (defaults to feature-name)' . PHP_EOL, 'feature-name');
            $featureName = \strtolower($helper->ask($input, $output, $featureQuestion));
            $featureBlockName = \str_replace('-', '_', $featureName);

            $projectRootPathQuestion = new Question('Define root project path (defaults to current)' . PHP_EOL, getcwd());
            $projectRootPath = $helper->ask($input, $output, $projectRootPathQuestion);
            # add an slash to the end
            $projectRootPath = \rtrim($projectRootPath, '/') . '/';
            $composerJsonPath = $projectRootPath . 'composer.json';
            if (\file_exists($composerJsonPath) === false) {
                $io->error('Invalid shopware 6 root folder: ' . $composerJsonPath);

                return 1;
            }

            $corePluginSuffix = $_ENV['cms_block']['suffix_plugin_name']['core'];
            $themePluginSuffix = $_ENV['cms_block']['suffix_plugin_name']['theme'];
            $pluginsFolder = $projectRootPath . 'custom/plugins/';
            $finder = new Finder();
            $finder->in($pluginsFolder);
            $finder->name(\sprintf('*%s', $_ENV['cms_block']['suffix_plugin_name']['core']));
            $finder->name(\sprintf('*%s', $_ENV['cms_block']['suffix_plugin_name']['theme']));
            $finder->depth(0);

            $count = \iterator_count($finder);

            if ($count < 2) {
                $io->error(\sprintf('No *%s and/or *%s plugins found in: %s', $pluginsFolder, $corePluginSuffix, $themePluginSuffix));

                return 1;
            }

            if ($count > 2) {
                $projectNameQuestion = new Question(\sprintf('Sorry we found multi *%s and *%s Plugins' . PHP_EOL, $corePluginSuffix, $themePluginSuffix));
                $projectName = \ucfirst(\strtolower($helper->ask($input, $output, $projectNameQuestion)));
            } else {
                $pluginsArray = \iterator_to_array($finder, true);
                /** @var \SplFileInfo $plugin */
                $plugin = array_shift($pluginsArray);
                $pluginName = $plugin->getBasename();
                if (\strpos($pluginName, $corePluginSuffix)) {
                    $projectName = \str_replace($corePluginSuffix, '', $pluginName);
                } elseif (\strpos($pluginName, $themePluginSuffix)) {
                    $projectName = \str_replace($themePluginSuffix, '', $pluginName);
                } else {
                    $projectNameQuestion = new Question(\sprintf('Sorry we found multi *%s and *%s Plugins' . PHP_EOL, $corePluginSuffix, $themePluginSuffix));
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
            $hydrationHandler->postHydrate(\sprintf('%s%s%s/', $pluginsFolder, $projectName, $corePluginSuffix), $blockType, $featureName);

            $io->success('Successfully created a cms block & element with the feature name: ' . $featureName);

            $io->note('Please upload the source code to your container and run a build/compile command to see the results.');

            return 0;
        })->descriptions('Creates a shopware 6 CMS block & element');
    }
}
