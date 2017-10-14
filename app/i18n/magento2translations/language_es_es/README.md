# European Spanish (español de España) Magento2 Language Pack (es_ES)
This is a Language Pack generated from the [official Magento2 translations project](https://crowdin.com/project/magento-2) at [Crowdin](https://crowdin.com).
The European Spanish (español de España) translations used can be found [here](https://crowdin.com/project/magento-2/es-ES).
This translation is usefull for people living in the Spain (España).

For our other language packs look at the [Magento2Translations](http://magento2translations.github.io/) page.

# Version & progress
This translation is generated from the branch [Head](https://crowdin.com/project/magento-2/es-ES#/Head) at Crowdin and based on the Magento 2.1.1 sourcefiles.
There have been  7835 strings translated of the 8412 strings in the Magento source.

Translation progress:![Progress](http://progressed.io/bar/93)

# Instalation
## Via composer
To install this translation package with composer you need access to the command line of your server and you need to have [Composer](https://getcomposer.org).
```
cd <your magento path>
composer require magento2translations/language_es_es:dev-master
php bin/magento cache:clean
```
## Manually
To install this language package manually you need access to your server file system.
* Download the zip file [here](https://github.com/Magento2Translations/language_es_es/archive/master.zip).
* Upload the contents to `<your magento path>/app/i18n/magento2translations/language_es_es`.
* The composer files should then be located like this `<your magento path>/app/i18n/magento2translations/es_ES/es_ES.csv`.
* Go to your Magento admin panel and clear the caches.

#Usage
To use this language pack login to your admin panel and goto `Stores -> Configuration -> General > General -> Locale options` and set the '*locale*' option as '*European Spanish (Spain)*'

# Contribute
To help push the '*European Spanish (español de España) Magento2 Language Pack (es_ES)*' forward please goto [this](https://crowdin.com/project/magento-2/es-ES) crowdin page and translate the lines.

# Authors
The translations are done by the [official Magento2 translations project](https://crowdin.com/project/magento-2).

Code generation is sponsored by [Wijzijn.Guru](http://www.wijzijn.guru/).