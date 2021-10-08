## Shell

This Command automatically connects via SSH to a running `*__shop` container 
To change the container suffix use the `config:update` command. 

``` note::
    The shell command is tuned for Dockware - https://dockware.io/ - images.
    But as long as the containers name is `*__shop` it should work anyways. (It is configure able)
```

Drop into a shell no matter which bash directory you are currently on

       diw shell

``` note::
    If your container has a explicit user that should be used. Set it via `config:update` command.
```
