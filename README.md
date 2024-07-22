# PostNL Magento2

This branch of the repository contains the GitHub pages content. When pushing to this branch a [Github Action](actions) will automatically be triggered and this will update the documentation.

The location of the GitHub Pages is as follows:
*PostNL Public*: postnl/magento2 => [https://postnl.github.io/postnl-magento2/)

## Development environment
For local development use the following command (add `-d` to start container in background):
```shell
docker-compose up
```


And goto [http://localhost:4000](http://localhost:4000). Upon changing files the terminal in which you started docker-compose should say it has recompiled after which you can reload your browser. 
