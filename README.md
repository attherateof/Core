# Mage2 Module: MageStack Core
This is a core Magestack module provides reusable logics to other dependent modules.

## Requirements
- Magento 2.4.8
- PHP 8.4
- Opensearch

## Module version
- 1.0.0

## Main Functionalities
- Provides reusable **OpenSearch** data provider to display data in admin grid
- Core configuration path hints

## Installation
1. **Install the module via Composer**:
    To install this module, run the following command in your Magento root directory:
    - ``composer require mage-stack/module-core``
2. **Enable the module:**
    After installation, enable the module by running:
   - ``php bin/magento module:enable MageStack_Core``
3. **Apply database updates:**
    Run the setup upgrade command to apply any database changes:
    - ``php bin/magento setup:upgrade``
4. **Flush the Magento cache:**
    Finally, flush the cache:
   -  ``php bin/magento cache:flush``

## Usage
Once installed and enabled, the functionalities provided by this module are seamlessly available within your Magento 2 application.

This module serves as a foundational component, offering reusable and extendable logic that can be integrated into custom modules. Utilizing these shared services and configurations helps reduce development time, promote consistency, and simplify the implementation of common features across your Magento projects.

## Contributing
If you would like to contribute to this module, feel free to fork the repository and create a pull request. Please make sure to follow the coding standards of Magento 2.

## Reporting Issues
If you encounter any issues or need support, please create an issue on the GitHub Issues page. We will review and address your concerns as soon as possible.

## License
This module is licensed under the MIT License.

## Support
If you find this module useful, consider supporting me By giving this module a star on github