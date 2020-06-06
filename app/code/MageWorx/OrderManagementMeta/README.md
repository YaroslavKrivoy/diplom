# MageWorx Order Management Meta Extension for Magento 2

## Upload the extension

### Upload via Composer

**Note**: if you do not have enough expirience with JSON format, follow the "Upload by copying code" section to continue the installation.

1.  Log into Magento server (or switch to) as a user who has permissions to write to the Magento file system.
2.  Create a folder (i.e. path/to/directory/with/zips/) anywhere on your server
    (preferably not in the Magento install dir). When done, upload all extension zip packages in there.
    Read more about [Composer's Artifact repository type](https://getcomposer.org/doc/05-repositories.md#artifact)
3.  To use the folder created above as a packaging repository, run the Composer command:
    ```
    composer config repositories.mageworx artifact path/to/directory/with/zips/
    ```
 
    The command adds these lines into your composer.json file:
    ```
    ,
    "mageworx": {
        "type": "artifact",
        "url": "path/to/directory/with/zips/"
    }
    ```
4.  Install the extension with Composer:
    ```
    composer require mageworx/module-ordereditor
    ```
5.  Update the extension with Composer (if your current extension's version is less then 2.2.2):
    ```
    composer remove mageworx/module-ordermanagementmeta
    composer require mageworx/module-ordereditor
    ``` 


### Upload by copying code

1.  Log into Magento server (or switch to) as a user who has permissions to write to the Magento file system.
2.  Download the "Ready to paste" package from your customer's area, unzip it and upload the 'app' folder 
    to your Magento install dir.

## Enable the extension

1.  Log into Magento server (or switch to) as a user who has permissions to write to the Magento file system.
2.  Go to your Magento install dir:
    ```
    cd path/to/Magento/install/dir/
    ```
3.  And finally, update the database:
    ```
    php bin/magento setup:upgrade
    php bin/magento cache:flush
    php bin/magento setup:static-content:deploy
    ```