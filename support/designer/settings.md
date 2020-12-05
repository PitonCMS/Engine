# Site and Page Settings

With custom settings, you can create user-managed targeted custom values as data or for logic. _Site_ settings are global and have values available on every page, while _page_ settings are defined for each page that use those settings in the page template.

When you add a new setting to the JSON definition file, that input is made available to the user in Site Settigns or in the Page Content manager for that template. Be sure to commit these files so that you can push to other environments.

If you delete a setting from the definition file, the user will see an orphaned flag when editing the site or page content and can decide to delete the saved value for that setting. Until then the previously saved value remains available in the templates.

## Examples
As a desinger you can utilize settings in many different ways, and not just to print values. Examples include:

* Add a new social media platform link to site settings to print in the footer on all pages
* Add a text area to enter a store address, or create a separate input for each data point in the address.
  * As a site setting this could be used on all pages, or perhaps just in a single template to allow for more targeted styling
* Create a setting with a select list of predefined values to avoid user input errors
* Create a setting as a flag to control page flow
  * For example, create a pair of begin and end effective date inputs, and then use logic in the Twig template to only display content if today is within the effective date range

## Site Settings
Site settings are defined in `structure/definitions/siteSettings.json`, and are updated in the **Settings** manager. Site settings are available on all pages in your template, under the `site.settings.` array and indexed with the `key` you define in the JSON file. You are welcome to delete the default settings that come with PitonCMS as examples.

To edit or add site settings, in the `siteSettings.json` file edit a setting object under the `"settings"` key:

```json
{
 "settings": [
	{
	 "category": "site",
	 "label": "Google Webmaster Verification Link",
	 "key": "googleWebMaster",
	 "value": "",
	 "inputType": "input"
    }
 ]
}
```

## Page and Block Element Settings
Page template settings are defined the custom page template definition file in `structure/templates/pages/*.json`, and are updated in the **Content** page manager for pages using that template. A user can then define different values in different pages using the same template.

Page settings are available on the pages using this template, under the `page.settings.` array and indexed with the `key` you define in the JSON file. You are welcome to delete the default settings that come with PitonCMS as examples.

To edit or add page settings, in the page template JSON file add a setting object under the `"settings"` key:

```json
{
 "blocks": [/* */],
 "settings": [
	{
	 "category": "site",
	 "label": "Google Webmaster Verification Link",
	 "key": "googleWebMaster",
	 "value": "",
	 "inputType": "input"
    }
 ]
}
```
You can also define Block Element settings for target data points per element on a page.

## Setting Definitions
Settings allow for very specific input types. The basic setting object has these properties at a minimum:

* `category` Where the input editor should appear, and which category of setting this is
  * For global settings, options are `site`, `social`, `contact`
  * For page settings you must use `page`
  * The `piton` category is reserved for system settings and should not be used as custom fields
* `label` The display label text for the input
* `key` Unique key you will use to access the variable in your templates. Must only contain a-z, A-Z, 0-9, _ (underscores) and max 60 characters without spaces
* `value` An optional default value. Note, the default value is presented to the user when viewing the setting, but not saved to the database until a user views and then saves. Max 4,000 bytes.
* `inputType` The type of input to present, defaults to `input` if left blank
  * Options are: `input`, `select`, `textarea`, `media`
* `help` Optional help text for input
* `options` If the `inputType` is `select`, then add an array of `name` and `value` options for the select list

### Input Setting
The default, and most used is a basic text input setting.

```json
{
    "category": "site",
    "label": "Google Webmaster Verification Link",
    "key": "googleWebMaster",
    "value": "",
    "inputType": "input"
}
```

### Textarea Setting
Presents a textarea to allow for longer free form content or code (such as tracking code). All values have a max length of 4,000 bytes.

```json
{
    "category": "site",
    "label": "Website Contact Address",
    "key": "contactAddress",
    "value": "",
    "inputType": "textarea"
}
```

### Select Input
Creates a select list of predefined values for the user.

```json
{
    "category": "site",
    "label": "Favorite Color",
    "key": "favColor",
    "value": "blue",
    "inputType": "select",
    "options": [
        {
            "name": "Blue",
            "value": "blue"
        },
        {
            "name": "Green",
            "value": "green"
        },
        {
            "name": "Yellow",
            "value": "yellow"
        }
    ]
}
```

### Media Input
Presents a textarea to allow for embedded media code. With the `media` type you can wrap the value in the appropriate HTML. All values have a max length of 4,000 bytes.

```json
{
    "category": "page",
    "label": "About Video",
    "key": "aboutVideo",
    "value": "",
    "inputType": "media"
}
```