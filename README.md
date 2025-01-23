# Universe Management Module for Magento 2

This Magento 2 module allows you to manage different "universes" on your website. Universes are determined through 
configuration, allowing administrators to choose a code, homepage, category root ID, or product attribute value. Based 
on the selected universe, the module dynamically applies specific layouts and adds a body class to the page, depending 
on the universe and page type (homepage, category, or product).

## Features

1. **Universe Configuration**  
   Choose a universe code, homepage, category root ID, or product attribute value to define a universe. Go to 
   Stores > Configuration > Blackbird extensions > Universes.

2. **Dynamic Layouts**  
   Allows to add a layout prefixed by the selected universe code for different page types.  
   Example : `{universe_code}_catalog_category_view.xml`

3. **Body Class Injection**  
   Add a custom body class dynamically, reflecting the selected universe.  
   Example : `{universe_code}-universe`

4. **Context resolving**  
   With these service and view model, you can check a universe's context from anywhere :  
   `Blackbird\Universes\ViewModel\Universes`  
   `Blackbird\Universes\Api\UniverseResolverInterface`

5. **Flexible Configuration**  
   Admins can easily configure multiple universes, depending on the needs.

## Installation

**Composer Package:**

```
composer require blackbird/module-universes
```

### Install the module

Go to your Magento root, then run the following Magento command:

```
php bin/magento setup:upgrade
```

**If you are in production mode, do not forget to recompile and redeploy the static resources, or to use the `--keep-generated` option.**

## Contact

For further information, contact us:

- by email: hello@bird.eu
- or by form: [https://black.bird.eu/en/contacts/](https://black.bird.eu/contacts/)

## Authors

- **Lucas Ulmer** - *Maintainer* [It's me!](https://github.com/Lucas-Blackbird)
- **Emilie Wittmann** - *Contributor* - [It's me!](https://github.com/emilie-blackbird)
- **Blackbird Team** - *Contributor* - [They're awesome!](https://github.com/blackbird-agency)

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

***That's all folks!***