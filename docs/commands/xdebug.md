## Xdebug

This Command enables, disables or returns the xdebug status from the `*__shop` container. 
To change the container suffix use the `config:update` command. 

``` note::
    The shell command is tuned for Dockware - https://dockware.io/ - images.
    But as long as the containers name is `*__shop` it should work anyways. (It is configure able)
```

* Returns status of xdebug


        diw xdebug


* Disable xdebug


         diw xdebug -d
         or
         diw xdebug --disable
         
* Enable xdebug


         diw xdebug -e
         or
         diw xdebug --enable
