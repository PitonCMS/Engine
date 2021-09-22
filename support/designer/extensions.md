# Extending Piton

You can safely modify PitonCMS Administration console through *Extensions*. You can create custom Javascript or PHP extensions to modify client and server side code.

## JavaScript Extensions
You can create a site-wide JS extension that will run on every page, or create an extension to run on one specific admin page.

All administration custom extension JS files are in `public/extensions/` and committed with your project.

All custom extension JS files must be named `extension.js`, but the path to the file will vary depending on whether you are creating a Site or a Page extension.

### Site Extensions
Site level extensions always run on _every_ PitonCMS administration page. To create a site JS extension add your `extension.js` file to the root of the `public/extensions/` directory.

For example

```sh
public/extensions/extension.js
```

At runtime your site extension will be loaded last, allowing you to overwrite native JS.

### Page Extensions
Page extensions will run only one specific admin page. To create a page JS extension add your `extension.js` file to nested directories matching the administration page URL path.

For examle, if you want to modify the Page content editor which has the URL path `/admin/page/edit/` (without any trailing record ID segment), then create those directories under `public/extensions/` and add your JS file

```sh
public/extensions/admin/page/edit/extension.js
```

### Selecting Custom Setting Inputs
A common use for Javascript extensions is to add input validations to custom settings, or trigger an update to other inputs.

To help in selecting Setting inputs, a `data-` attribute has been added to these Setting value inputs.

This `data-` attribute has the structure `data-setting-<category>="<key>"` where the category is either `site`, `page`, `element`, `social`, `contact`, and the `key` is the key defined in your Settings JSON Definition file.

For example, if you want the Site Setting "Site Name" (key: `siteName`) to prepopulate a footer textarea Site Setting (key: `footerAbout`), you would add an extension file `public/extensions/admin/settings/site/edit/extension.js` containing

```js
const siteName = document.querySelector(`[data-setting-site="siteName"]`);
const footerAbout = document.querySelector(`[data-setting-site="footerAbout"]`);

siteName.addEventListener("input", () => {
    footerAbout.value = "About " + siteName.value;
}, false);
```

## PHP Extensions
At runtime PitonCMS loads a series of bootstrap configuration files. This process includes loading any custom project extensions that modify core PitonCMS behavior.

Extension Hooks

| Hook | Description |
| --- | --- |
| `config/dependencies.php` | Override Dependency Injection Container (DIC) |
| `config/routes.php` | Override front end or backend routes |

The easiest way to extend PitonCMS is to use the Dependency Injection Container to load custom closures or override default closures.