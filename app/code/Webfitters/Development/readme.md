# Magento 2 Webfitters Development Extension

Extension to aid in development of Magento 2, provides utility commands

## Installation
Clone into [Magento Root]/app/code/Webfitters/Development 
and then run php bin/magento module:enable Webfitters_Development && php bin/magento setup:upgrade 
from the magento root directory.  

## Usage
Once installed, simply run php bin/magento development:reset to have only the Webfitters styles/js regenerated (saves having to run setup:static-content:deploy all the time on windows boxes)