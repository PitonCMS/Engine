# Page and Element Templates

PitonCMS uses *Templates* to render your custom website design. A Template consists of an *HTML Layout* file and a *JSON Definition* file with exactly matching names (case sensitive) except for the file extenion.

The HTML Layout file defines the visible structure and has Twig variables to be replaced with your saved content, while the JSON Definition file allows you to control features in the HTML Layout as part of your design. Together, Templates provide highly flexible structures that can be resused without writing additional code.

To render Templates, PitonCMS uses [Twig](https://twig.symfony.com/doc/3.x/). Understanding how Twig works as a designer will simplify building custom websites with PitonCMS. You can also read more about the [Twig library in PitonCMS](/admin/support/designer/data).

## Page Structure
Page Templates contain designer defined *Blocks* which represent broad areas of a webpage that hold editable content. Within Blocks are one or more *Elements* which are small reusable chunks of HTML with optional saved custom content. Users cannot modify Blocks on the Page, but they can select and modify content within Elements contained within Blocks.

When a user creates a new Page for the website, they first select a Page Template from a list of options the designer defined as part of the project. Then the user will select one or more pre-defined Elements within defined Blocks, and add custom content.

In the example of the *With Hero* Template that comes with PitonCMS, the Template has *Hero* and *Content* Blocks. The user can then simply add Elements with custom content to these Blocks when editing the Page.

![Page Template Overview"](/admin/img/support/pageBlockElementOverview.png)

When designing a new website, identify how many unique Page Templates are needed. For example, you might need a Home Page Template, a Catalog Page Template, and a general Content Page Template, from which your client can create any number of unique website pages.

Then for each Page Template define an HTML Layout (along with a JSON Definition) and define the Blocks in that Template. Next, define the resulable Elements you will need. Elements can be used across all Page Templates and Blocks, but you can also limit use of an Element to just one Block if needed.

>**Tip**: Put layout structure in Page Blocks, and use the minimum needed HTML in Elements.

## Templates Directory
Your website Template HTML Layout and JSON Definition files are all in the `structure/` project directory at the root of PitonCMS.

```bash
structure
├── definitions
│   ├── contactInputs.json
│   ├── navigation.json
│   └── siteSettings.json
├── sass
|   ├── ...
└── templates
    ├── elements
    │   ├── collection
    │   │   ├── collection.html
    │   │   └── collection.json
    │   ├── contact
    │   │   ├── contact.html
    │   │   └── contact.json
    │   ├── embedded
    │   │   ├── embedded.html
    │   │   └── embedded.json
    │   ├── gallery
    │   │   ├── gallery.html
    │   │   └── gallery.json
    │   ├── hero
    │   │   ├── hero.html
    │   │   └── hero.json
    │   ├── image
    │   │   ├── image.html
    │   │   └── image.json
    │   └── text
    │       ├── text.html
    │       └── text.json
    ├── includes
    │   ├── _footer.html
    │   ├── _macros.html
    │   └── _navbar.html
    ├── pages
    │   ├── _base_layout.html
    │   ├── blog.html
    │   ├── blog.json
    │   ├── collectionDetail.html
    │   ├── collectionDetail.json
    │   ├── contentPage.html
    │   ├── contentPage.json
    │   ├── heroPage.html
    │   └── heroPage.json
    └── system
        └── notFound.html
```

>**Note**: Page and Element Templates (HTML and matching JSON files) can be nested under subdirectories. This is optional.

| Directory | Description |
| --- | --- |
| `structure/definitions/` | Site level JSON Definition files |
| `structure/definitions/contactInputs.json` | Custom input fields for contact message forms |
| `structure/definitions/navigation.json` | Navigation lists |
| `structure/definitions/siteSettings.json` | Custom Site Settings |
| `structure/sass/` | Project Sass files (Optional) |
| `structure/templates/` |  All HTML Layout and JSON Definition files |
| `structure/templates/elements/` | Element HTML and JSON Definitions |
| `structure/templates/includes/` | Optional Twig Include files |
| `structure/templates/pages/` | Page HTML and JSON Definitions |
| `structure/templates/system/` | System HTML files such as Not Found  template |

You are welcome to add, delete, or modify Page and Element Templates (HTML and JSON files). Within `templates/elements/` and `templates/pages/` you may modify the default directory structures (*before* creating saved content) to better organize your custom Page and Element HTML and JSON files.

## HTML Layouts and JSON Definitions
Each Page or Element HTML Layout file requires a matching JSON Definition file, and these two files **must** be in the same sub-directory, and must have the same case sensitive filename (except for the extension `.html` and `.json`).

The HTML file can contain static HTML or can extend an optional layout file, and if you want the user to add dynamic content then you need at least one Block defined.

>**Warning**: Once dynamic content has been saved using a Template do not change the Template name or directory location.

### Page Template Example
The built in *Without Hero* content Page HTML Template `contentPage.html` is very simple. It extends the `_base_layout.html` and includes a single Block named `contentBlock`.

```twig
{% extends 'pages/_base_layout.html' %}

{% block body %}
  {{ getBlockElementsHtml(page.blocks.contentBlock) }}
{% endblock body %}
```

Explanation
- The Twig `extends` statement inherits the surrounding HTML from the `pages/base_layout.html` file
- The `{% block body %}` and `{% endblock body %}` tags wrap the HTML that will replace the matching `body` tags in the base layout file
- There is one Block to hold user saved content named `contentBlock`
- The key `contentBlock` is defined as the Block key in the matching JSON file
- A PitonCMS Twig function `getBlockElementsHtml()` returns all saved Elements in the Page's `contentBlock`
- The Twig print delimiters `{{ }}` print the saved content

In this brief Template we have a custom layout suitable to print general webpage content.

The matching `contentPage.json` JSON Definition file contains information about this HTML Layout, how it is used, defines the custom blocks or settings, and any Element restrictions.

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

### Page Definition Properties

| Key | Type | Required | Default | Description |
| --- | --- | --- | --- | --- |
| `templateName` | String | Yes | | The name of the Template displayed when creating a new Page |
| `templateDescription` | String | | | The description of the Page Template |
| `showFeaturedImage` | Boolean | | `true` | A boolean flag (true or false) on whether to display a primary media selector for these Pages |
| `templateType` | String | Yes | | The type of template, `page` for static content or `collection`  for groups of related content |
| `blocks` | Array | | | An array of objects that define Block areas of the Page layout |
| `showSubTitle` | Boolean | | `true` | Whether the page should have a sub title field

### Block Definition Properties

| Key | Type | Required | Default | Description |
| --- | --- | --- |
| `name` | String | Yes | | The name of the block displayed to the user when editing the page |
| `key` | String | Yes | | A page unique string to identify that block in your Template code. Must not contain any spaces, and only consist of a-z, A-Z, 0-9, underscore ( _ ), with max length 60 characters. Use this key in the Page Template blocks variable `page.blocks.<key>` |
| `description` | String | | | The description of the block displayed to the user when editing the page |
| `elementTypeOptions` | Array | | | An array of *allowed* elements (string, by path with filename without extension) to display to the user. If not provided the user will see all available elements |
| `elementCountLimit` | integer | | | The max number of elements allowed by design. If no value is provided, then the user can add any number of elements. |

Pages and Block Elements can also support custom Settings for small bits of dynamic information.

>**Note**: *Pages* and *Collection Detail Pages* both use Page Templates, but a standard Page is defined in the JSON Definition file as `"templateType": "page"` while a Collection Detail Page is defined as `"templateType": "collection"`.

### Element Template Example
Elements are the smallest unit of reusable HTML on your website. You can create custom Elements (or modify or delete built in PitonCMS Elements) as needed for your website design.

The HTML Layout file for a basic *Text* Element (which has a title and rich textarea) is simple.

```html
  <div class="element">
    <h2 class="element__title">{{ element.title }}</h2>
    {{ element.content }}
  </div>
```

When the Element HTML Layout is loaded, the saved data is available in the `element` array. (You can also use all `site` and `page` data in an Element.)

Element HTML files do not need to contain variables, it may consist of just boilerplate HTML and text.

The matching JSON Definition file contains information about the Element and how it is used. This example includes HTML to start an ordered list.

```json
{
    "elementName": "Text",
    "elementDescription": "Simple text content",
    "contentTextareaDefaultValue": "<ol><li></li></ol>"
}
```

### Element Definition Properties

| Key | Type | Required | Default | Description |
| --- | --- | --- |
| `elementName` | String | Yes | | The name of the element displayed to the user |
| `elementDescription` | String | | | The description of the element displayed to the user |
| `enableInput` | String | | | Display additional built-in input option for the type of element (just one option). Options are `"collection"`, `"embedded"`, `"image"`, and `"gallery"` |
| `showTitle` | Boolean | | `true` | Boolean flag on whether to display the Element Title input.|
| `showContentTextarea` | Boolean | | `true` | Boolean flag on whether to display the Element Content textarea (with or without the rich text editor) |
| `contentTextareaDefaultValue` | String | | | Default text or HTML to display in a new Element |
| `enableEditor` | Boolean | | `true` | Boolean flag on whether to enable the Element content textarea Rich Text Editor |
| `settings` | Array | | | An array of [Custom Settings](/admin/support/designer/settings) objects. |

## Twig Template Features

Consider taking advantage of Twig's powerful templating features.

- [extends](https://twig.symfony.com/doc/3.x/tags/extends.html) syntax allows you to extend a HTML Layout from another base HTML Layout
- [Includes](https://twig.symfony.com/doc/3.x/functions/include.html) supports reusable blocks of frequently used HTML kept in separate files, to help declutter complex templates
- [Macros](https://twig.symfony.com/doc/3.x/tags/macro.html) are HTML functions that can be used in Templates, and are a great way to organize reusable code where the same HTML statement needs to repeat on a page but with different data

>**Note**: You can also have intermediate Template HTML Layouts between the base layout and the Page Template. This may solve some complex layout requirements without repeating code.

Learn more about how PitonCMS uses [Templates](/admin/support/designer/templates).