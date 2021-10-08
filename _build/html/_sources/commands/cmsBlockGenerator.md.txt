## CMS Block & Element Generator

This will generate a new cms block and element for your shopware 6 project with ease.
Just answer the upcoming (3) questions.
[Shopware 6 documentation](https://docs.shopware.com/en/shopware-platform-dev-en/how-to/custom-cms-block)

``` note::
    This command is made for Shopware 6 - https://github.com/shopware/platform.
    And requires two plugins (<ProjectName>Core, <ProjectName>Theme)
```

1. Select your CMS block choice
Use the number or start typing. Your input will get auto completed.

2. Enter a feature name
It should be lowercase (if not it will get transformed to lower)
Use `-` if the feature consists of several words. 

3. Define root project
The root project path is where a `project.json`, `.env` and the `vendor` folder is located.
If you just hit enter the current directory is used as the root project folder.

``` note::
    Make sure you have uploaded the new created block & Element (Changes made in both *Core & *Theme plugins)
    and rebuild storefront & administration
```

``` note::
    *Core and *Theme suffix can be changed via the config:update command
```
