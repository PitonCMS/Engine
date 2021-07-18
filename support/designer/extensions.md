# Extend Piton

You can safely modify the PitonCMS Administration behavior through custom JavaScript extensions. Create custom form validations, disable fields, synch up inputs, modify custom settings etc., to create a client optimized solution.

## JavaScript Extensions
You can create a site-wide JS extension that will run on every page, or create an extension to run on one specific admin page. All extension JS files are in `public/extensions/` and committed with your project.

All custom extension JS files must be named `extension.js`.

### Site Extensions
Site level extensions always run on _every_ PitonCMS administration page. To create a site JS extension add your `extension.js` file to the root of the `public/extensions/` directory.

For example:
```
public/extensions/extension.js
```

### Page Extensions
Page extensions will run only one specific admin page. To create a page JS extension add your `extension.js` file to nested directories matching the administration page URL path to the root of the `public/extensions/` directory.

For examle, if you want to modify the content editor page which has the URL path `/admin/page/edit/`, then create those directories and add your JS file:

```
public/extensions/admin/page/edit/extension.js
```
