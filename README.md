# Grav Problems Plugin

`Problems` is a [Grav](http://github.com/getgrav/grav) Plugin and allows to detect issues.

This plugin is required and you will find it in any package distributed that contains Grav. If you decide to clone Grav from GitHub, you will most likely want to install this.


# Installation

To install this plugin, just download the zip version of this repository and unzip it under `/your/site/grav/user/plugins`. Then, rename the folder to `problems`.

You should now have all the plugin files under

	/your/site/grav/user/plugins/problems

>> NOTE: This plugin is a modular component for Grav which requires [Grav](http://github.com/getgrav/grav), the [Error](https://github.com/getgrav/grav-plugin-error) and [Problems](https://github.com/getgrav/grav-plugin-problems) plugins, and a theme to be installed in order to operate.

# Usage

`Problems` runs in the background and most of the time you will not know it is there. Although as soon as an issue is caught, the plugin will let you know.

Problems uses the cache as refresh indicator. That means that if nothing has changed anywhere, the plugin will just skip its validation tests altogether. 

If a change is caught and the cache is refreshed, the plugin will loop through its validation tests and making sure nothing is out of place.

`Problems` gets also triggered if any fatal exception is caught.
