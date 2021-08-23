# Page and Element Templates

PitonCMS relies on *Templates* to render your custom website design. A Template consists of an HTML layout file and a JSON Definition file with matching names, that represent a type of Page or Element layout that can be reused for different content. The JSON Definition file allows you to control features and Page flow as part of the project design.

PitonCMS uses [Twig](https://twig.symfony.com/doc/3.x/) to render templates, and understanding how Twig works as a designer will simplify building custom websites with PitonCMS.

## An Overview
Page Templates have *Blocks* which represent areas of a Page layout that hold editable content. Within Blocks are *Elements* which are small reusable chunks of HTML.

When a user edits a Page they select an Element and add content to predefined Page Blocks.

In this example of the *With Hero* template, the web design has defined the *Hero* and *Content* Blocks. The user can then simply add content *Elements* to these Blocks when editing the Page.

![Page Template Overview"](/admin/img/support/pageBlockElementOverview.png)

When designing a new website, identify how many unique Page layouts are needed. Then for each layout then determine content areas for each Page. These content areas may be considered Blocks you define in the JSON definition file for the Template.

Then define the various types of Elements you need for a custom website. PitonCMS comes with a number of built-in Elements, but feel free to delete or make your own to suit the design.

Consider taking advantage of Twig's powerful templating features.

- Extending Templates from a base layout. Using Twig's [extends](https://twig.symfony.com/doc/3.x/tags/extends.html) syntax, you can define a base file to load shared components and extend Page Templates from the base.
>**Note**: You can also have intermediate layouts between the base layout and the Page Template. This may solve some complex layout requirements without repeating code.
- Twig [Includes](https://twig.symfony.com/doc/3.x/functions/include.html) supports reusable blocks of HTML kept in separate files, to help declutter complex templates.
- Twig [Macros](https://twig.symfony.com/doc/3.x/tags/macro.html). Macros are HTML functions that can be used in templates, and are a great way to organize reusable code where the same HTML statement needs to repeat on a page but with different data.

## Templates Directory Structure
Your website layout files are all in the `structures/` project directory

- `definitions/` Site level definition files
  - `contactInputs.json` Supplemental allowed input fields for contact Message forms
  - `navigation.json` Navigation list definitions
  - siteSettings.json` Custom Site level Settings
- `sass/` Project Sass files
- `templates/` Template HTML and JSON files
  - `elements/` Element HTML and JSON Definitions
  - `includes/` Optional Twig include files
  - `pages/` Page HTML and JSON Definitions
  - `system/` System HTML files such as Not Found template, which can be customized

>**Note**: *Pages* and *Collection Detail Pages* both use Page Templates, but a standard Page is defined in the JSON file as `"templateType": "page"` while a Collection Detail Page is defined as `"templateType": "collection"`.

Within `templates/elements/` and `templates/pages/`  you may modify the file and directory structures (*before* creating saved content). To organize your custom Page and Element HTML and JSON files you may create any level of additional sub-directories.

However, each `page` or `element` layout HTML file has a matching JSON definition file, and these two files **must** be in the same sub-directory, and must have the same filename (except for the extension `.html` and `.json`).

## Page HTML Templates and JSON Definitions
At a minimum a Page Template consists of one HTML file and one JSON file with matching names (except for the extensions) in the same sub-directory.

The HTML file can contain static HTML or can extend an optional layout file, and if you want the user to add dynamic content then you need at least one Block defined.

### Page Template
The built in *Without Hero* content Page HTML Template `contentPage.html` is very simple. It extends the `_base_layout.html` and includes a single Block `contentBlock`.

```html
{% extends 'pages/_base_layout.html' %}

{% block body %}
  {{ getBlockElementsHtml(page.blocks.contentBlock) }}
{% endblock body %}
```

Explanation
- The `extends` statement inherits the surrounding HTML from the `pages/base_layout.html` file
- The `{% block body %}` and `{% endblock body %}` tags wrap the HTML that will replace the matching body tags in the base layout file
- There is one `contentBlock` Block to hold user saved content
  - This key `contentBlock` is defined as the Block key in the matching JSON file
- A PitonCMS function `getBlockElementsHtml()` returns all saved Elements in this Page's `contentBlock`
- The Twig print delimiters `{{ }}` print the loaded content

In this brief Template we have a custom layout suitable to print any generic dynamic content.

The matching `contentPage.json` JSON Definition file contains information about this Template, how it is used, any custom blocks or settings, and Element restrictions.

```json
{
    "templateName": "Without Hero",
    "templateDescription": "Page without hero image and with one content block.",
    "showFeaturedImage": true,
    "templateType": "page",
    "blocks": [
        {
            "name": "Content",
            "key": "contentBlock",
            "description": "Simple text areas"
        }
    ]
}
```

**Page Definition Properties**

| Key | Required | Description |
| --- | --- | --- |
| `templateName` | Yes | The name of the Template displayed when creating a new Page |
| `templateDescription` | | The description of the Page Template |
| `showFeaturedImage` | | A boolean flag (true or false) on whether to display a primary media selector for these Pages |
| `templateType` | Yes | The type of template, `page` for static content or `collection`  for groups of related content |
| `blocks` |  | An array of blocks that define content areas of the Page layout |
| `showSubTitle` |  |  Whether the page should have a sub title field

The `blocks` array `[]` contains objects `{ }` representing how the Block should display and be controlled. For each Block on your page, define a Block in the JSON file.

**Block Definition Properties**

| Key | Required | Description |
| --- | --- | --- |
| `name` | Yes | The name of the block displayed to the user when editing the page |
| `key` | Yes | A page unique string to identify that block in your Template code. Must not contain any spaces, and only consist of a-z, A-Z, 0-9, underscore ( _ ), with max length 60 characters. Use this key in the Page Template blocks variable `page.blocks.<key>` |
| `description` |  | The description of the block displayed to the user when editing the page |
| `elementTypeOptions` |  | An array of *allowed* elements (by path with filename without extension) to display to the user. If not provided the user will see all available elements |
| `elementCountLimit` |  | The max number of elements allowed by design. If no value is provided, then the user can add any number of elements. |

Pages and Block Elements can also support custom Settings for small bits of dynamic information.

## Elements HTML and JSON
At a minimum an Element Template consists of one HTML file and one JSON file with matching names (except for the extensions) in the same sub-directory.

Elements are the smallest unit of reusable HTML on your website. You can create custom Elements (or modify or delete built in PitonCMS Elements) as needed for the site design.

The HTML Template file for a basic *Text* Element (which has a title and rich textarea) can be

```html
  <div class="element">
    <h2 class="element__title">{{ element.title }}</h2>
    {{ element.content }}
  </div>
```

When the Element HTML is loaded, the saved data is available in the `element` array. (You can also use all `site` and `page` data in an Element.)

Element HTML files do not need to contain variables, it may consist of just boilerplate HTML and text.

The matching Text Element JSON Definition file contains information about the Element and how it is used.

```json
{
    "elementName": "Text",
    "elementDescription": "Simple text content"
}
```

**Element Definition Properties**

| Key | Required | Description |
| --- | --- | --- |
| `elementName` | Yes | The name of the element displayed to the user |
| `elementDescription` |  | The description of the element displayed to the user |
| `enableInput` |  | Display additional built-in input option for the type of element (just one option). Options are `"collection"`, `"embedded"`, `"image"`, and `"gallery"` |
| `showContentTextarea` |  | Defaults to true. |
| `enableEditor` |  | Defaults to true. Enables the rich text editor |
| `settings` |  | An array of [Custom Settings](/admin/support/designer/settings). |

