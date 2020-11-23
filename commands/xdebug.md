## Xdebug

This Command enables, disables or returns the xdebug status from the `*__shop` container 

``` note::
    The shell command is tuned for Dockware - https://dockware.io/ - images.
    But as long as the containers name is `*__shop` it should work anyways.
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
