# PostNL Magento2

This branch of the repository contains the GitHub pages content. When pushing to this branch a [Github Action](actions) will automatically be triggered and this will update the documentation.

Since this repository is located at the TIG organisation and forked by PostNL this must be pulled by PostNL to update the public documentation:

The location of the GitHub Pages is as follows:
- *TIG Private*: tig-nl/tig-extension-tig-postnl-magento2 => [tig-nl.github.io/tig-extension-tig-postnl-magento2](https://tig-nl.github.io/tig-extension-tig-postnl-magento2)
- *TIG Public*: tig-nl/postnl-magento2 => [tig-nl.github.io/postnl-magento2](https://tig-nl.github.io/postnl-magento2)
- *PostNL Public*: postnl/magento2 => [postnl.github.io/magento2](https://postnl.github.io/magento2)

## Development environment
For local development use the following command (add `-d` to start container in background):
```shell
docker-compose up
```


And goto [http://localhost:4000](http://localhost:4000). Upon changing files the terminal in which you started docker-compose should say it has recompiled after which you can reload your browser. 
