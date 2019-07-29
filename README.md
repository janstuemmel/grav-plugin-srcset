[![Build Status](https://travis-ci.org/janstuemmel/grav-plugin-srcset.svg?branch=master)](https://travis-ci.org/janstuemmel/grav-plugin-srcset)

# Srcset Plugin

The **Srcset** Plugin provides support for the srcset attribute in rendered markdown content. Instead of manipulating the rendered content, it extends the markdown parser. It wont't work with editors that are not using the Grav Parsedown Parser. So it will not work with tinymce editor plugin.  

## Manual Installation

To install the plugin manually, download the zip-version of this repository and unzip it under `/your/site/grav/user/plugins`. Then rename the folder to `srcset`. You can find these files on [GitHub](https://github.com//grav-plugin-srcset) or via [GetGrav.org](http://getgrav.org/downloads/plugins#extras).

You should now have all the plugin files under

    /your/site/grav/user/plugins/srcset

## Configuration

Before configuring this plugin, you should copy the `user/plugins/srcset/srcset.yaml` to `user/config/plugins/srcset.yaml` and only edit that copy.

Here is the default configuration and an explanation of available options:

```yaml
enabled: true
```

Note that if you use the Admin Plugin, a file with your configuration named srcset.yaml will be saved in the `user/config/plugins/`-folder once the configuration is saved in the Admin.
