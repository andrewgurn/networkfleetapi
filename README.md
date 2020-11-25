# networkfleetapi
networkfleetapi is a PHP class that connects to Verizon's NetworkFleet API and helps you grab data

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
`

# manual installation
Copy NetworkFleetAPI.php to your project's root and then require it where you need it.  For example, if you copied NetworkFleetAPI to a folder called 'includes' in your webroot:

```
<?php
require __DIR__ .'includes/NetworkFeetAPI.php';
?>

```
