# Custom Settings

In PitonCMS *Settings* are Key : Value pairs that can be used to display small bits of user saved data (like social media links), change a style theme, or even set user saved flags to control application flow.

Settings and the keys are defined as part of the website design in the JSON Definition files and committed to version control, and the values are set by the user in the administration console.

Settings can be defined as
- **Site** Values are available to all Templates on all Pages
- **Page** Values are unique to each saved Page
- **Element** Values are unique to each Element within each Page

When a new Setting is defined in a JSON Definition file, the input is available in the respective editor and the user can save the desired value. If a setting is deleted from the JSON Definition file, the user will see an orphaned flag and can delete the saved value for that setting.

>**Note**: *Contact* and *Social* are also Site Settings available on all Templates, but have separate categories to separate these in the **Settings** menu.

## Defining Settings
Whether a Site, a Page, or an Element Setting, the JSON structure is the same for a custom Setting.

Settings are all defined as a list of setting objects in a `"settings"` array `[]` in the JSON Definiton file.

For example, a *Site* Setting to print a Google Search Console verification code would be (in the `siteSettings.json` file)

```json
{
  "settings": [
    {
      "category": "site",
      "label": "Google SEO Verification Link",
      "key": "googleSeoVerification",
      "inputType": "text",
      "placeholder": "Google Search Console verification"
    }
  ]
}
```

To use this Site Setting in your header use the `key` defined in your Setting

```html
<meta name="google-site-verification" content="{{ site.settings.googleSeoVerification }}">
```

Settings allow for HTML5 input types. The Setting object has these properties

| Key | Required | Default | Description |
| --- | --- | --- |
| `category` | Yes | | The type of setting, one of `site`, `contact`, `social`, `page`, or `element`*
| `label` | Yes | | The label text for the input
| `key` | Yes | | Unique key you will use to access the Setting variable in your templates. Must only contain a-z, A-Z, 0-9, _ (underscore) and max 60 characters without spaces
| `value` | | | A default value. **Note**, the default value is presented to the user when viewing the Setting, but not saved to the database until a user saves the form. Max 4,000 bytes.
| `inputType` | | `text` | The type of HTML5 input to present. Options are `text`, `select`, `textarea`, `color`, `date`, `email`, `number`, `tel`, and `url`.
| `help` | | | Help text for input
| `placeholder` | | | Input placeholder text (if the input supports placeholder)
| `options` | | | If the `inputType` is `select`, then add an array of `name` and `value` options for the select list

## Site Settings
Site settings are defined in `structure/definitions/siteSettings.json`, and are set in the <i class="fas fa-cog"></i> **Settings** menu. Site settings are available to all Page Templates in your website, under the `site.settings` array and indexed with the `key` you defined in the JSON file.

You are welcome to delete the default settings that come with PitonCMS as examples.

To add or edit Site Setting Definitions, in the `siteSettings.json` file add or edit setting objects under the `"settings"` array `[]`.

>**Note**: *Contact* and *Social* are also Site Settings available on all Templates, but have separate categories to separate these in the **Settings** menu.

## Page Settings
Page Settings are defined in the Page Template in `structure/templates/pages/` in the JSON Definition file, and are set in the <i class="fas fa-pencil-alt"></i> **Content** menu Page editor.

Any Page using this Template can set different Setting values unique to that Page URL. The Setting values are available to the Page HTML Template and in any Element HTML Template used by that Page.

To edit Page Settings, in the Page Template JSON Definition file add a setting object under the `"settings"` array.

For Example, to allow the user to easily change the call to action text on a button, create a Page Setting like this

```json
{
  "blocks": [/* */],
  "settings": [
    {
      "category": "page",
      "label": "Hero Button Text",
      "key": "ctaTitle",
      "value": "Learn more!",
      "inputType": "text",
      "help": "Limit 20 characters, keep it brief."
    }
  ]
}
```

And then use it in your button like this

```html
<a href="/signup">{{ element.settings.ctaTitle }}</a>
```

## Element Settings
Element Settings are defined in the Element Template in `structure/templates/elements/` in the Template JSON Definition file, and are set in the <i class="fas fa-pencil-alt"></i> **Content** menu Page in the respective Element editor.

Any Element using this Template can set different values unique to that Block. The Setting values are available to that Element HTML Template.

To edit Element Settings, in the Element Template JSON Definition file add a setting object under the `"settings"` array.

For Example, to allow the user to set effective date range for an Element (such as for a table of commercial rates), create a two Element Settings like this

```json
{
  "settings": [
    {
      "category": "element",
      "label": "Start Date",
      "key": "startDate",
      "inputType": "date",
      "help": "Select the start date for these rates."
    },
    {
      "category": "element",
      "label": "End Date",
      "key": "endDate",
      "inputType": "date",
      "help": "Select the end date for these rates."
    }
  ]
}
```

And then use it in your Element HTML Template like this

```html
{# Create variable and set today's date in ISO8601 format #}
{% set today = 'now'|date('Y-m-d') %}

{# Compare today to effective date range #}
{% if element.settings.startDate <= today and today < element.settings.endDate %}
  <h3>{{ element.title }}</h3>
  <!-- Table of seasonal rates -->
  {{ element.content }}
{% endif %}
```

## Setting Types
PitonCMS supports HTML5 input types to provide some basic client side data validation. You can set `inputType` to `text`, `select`, `textarea`, `color`, `date`, `email`, `number`, `tel`, or `url`.

### Input Setting
The default is a basic `text` input setting.

```json
{
    "category": "site",
    "label": "Google Webmaster Verification Link",
    "key": "googleWebMaster",
    "inputType": "text"
}
```

### Textarea Setting
Presents a textarea to allow for longer free form content or code (such as tracking code). All values have a max length of 4,000 bytes.

```json
{
    "category": "site",
    "label": "Website Contact Address",
    "key": "contactAddress",
    "inputType": "textarea"
}
```

### Select Input
Creates a select list of predefined values for the user to select from.

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
