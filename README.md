# networkfleetapi
networkfleetapi is a PHP class that connects to Verizon's NetworkFleet API and helps you grab data

This is a work in progess.  As of now it'll get you connected and you can pass in queries you contruct yourself.  I am working on the functions that do the queries for you as I get the itme to doo so.

# composer installation
Add this repo to your composer.json and require it. For example:

    {
        "name": "myNamespace/myProject",
        "repositories": [
            {
                "type": "vcs",
                "url": "https://github.com/andrewgurn/networkfleetapi"
            }
        ],
        "require": {
            "andrewgurn/networkfleetapi": "dev-main"
        }
    }

# manual installation
Copy NetworkFleetAPI.php to your project's root and then require it where you need it.  For example, if you copied NetworkFleetAPI to a folder called 'includes' in your webroot:

```
<?php
require __DIR__ .'includes/NetworkFeetAPI.php';
?>

```
