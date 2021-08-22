# Printing Website Data

All PitonCMS website data is saved to a MySQL database, and when a page is rendered the relevant data is made available to the Page Template as Twig variables.

## The Basics
When you include Twig variables in your custom Templates, the variable and surrounding delimiters are replaced with the replacement text. For example, if you want to print the *Page Title* in your Template, include

```html
<h1>{{ page.title }}</h1>
```

and when Twig renders the Page you will see

```html
<h1>My Life on the Water</h1>
```

When a Page is loaded the data is injected into the Page Template as an `array`. To access a specific key in the array use dot notation. For example, the `title` key inside the `page` array is accessed as `page.title`.

If you want to print all elements in the array (such as in a list) use the Twig [for loop](https://twig.symfony.com/doc/3.x/tags/for.html) syntax

```html
<ul>
    {% for user in users %}
        <li>{{ user.username }}</li>
    {% endfor %}
</ul>
```

If you are unsure what variables are available to you in a Page, use the Twig `dump()` debugger statement to print all available variables.

```html
{{ dump() }}
```
To get more details on a sub-array, specify the key to print

```html
{{ dump(page.blocks) }}
```

To learn more about Twig syntax, see the [Twig](https://twig.symfony.com/) support documents.

## The Page Data Array
When a Page is requested the standard data array injected into each Page Template containing these keys

- `site` Website data available to all Pages
  - `settings` Built-in and custom *Site Settings* from your JSON definition file
  - `environment` Application configuration values
- `page` Saved content for this Page
- `alert` System messages

To access any one of these sub-arrays, use the dot separator between keys, as in `site.settings.twitterLink`.

The `page` array contains all user saved content for this specific Page. The primary keys in the `page` array include

- `blocks` A sub-array of the Page *Blocks* defined in your Template, keyed using the Block `key` from your JSON definition file
- `settings` A sub-array of custom Page *Settings*, if included in your Template
- `media` The *Page Image* Media object, if included in your Template

Other useful Page properties can be inspected with the `{{ dump(page) }}` command.

### Using Page Blocks
Within the `blocks` array you will see sub-arrays for each Block you defined in your JSON definition file for this Template. Each Block requires a *unique* Block key. If you name a sidebar Block in a Page Template as `"key": "sidebar"`, then you would access Elements in this Block as `page.blocks.sidebar.<elementIndex>`.

Each Block may then have one or more user created Elements. To access Element content use the PitoncMS template function `getBlockElementsHtml()` to print all Elements using the correct Element Template in the correct display order.

```html
{{ getBlockElementsHtml(page.blocks.sidebar) }}
```

### Using Page Elements
When `getBlockElementsHtml()` loops through the saved Elements it will load and print the Element HTML Template file making variable replacements. Within the Element Template, you can reference

- `site` Any Site Setting
- `page` Any Page data
- `element` The key for the current Element variables sub-array

Within the `element` array, the primary Element fields are

- `settings` Custom *Element Settings*
- `media` Media properties if the Element enabled a media selector
- `title` The Element Title field
- `content` The rich text content

Furthermore, Element Templates can have some additional fields enabled by the web design. To see other Element properties use the `{{ dump(element) }}` statement in your Element Template.

### Using Page Settings
Custom *Page Settings* behave like the Site Settings, but are defined in the Page Template and the saved values are unique to each Page. This is useful to define small user editable small bits of data that do not require a content editor, or to add flags to conditionally display content.

You define Page Settings in the Page Template JSON file. See the [Page Templates](/admin/support/designer/templates) support document.

### Other Page Data Properties
In addition, each `page` array contains these keys to use in your templates

- `id` Page ID
- `collection_slug` Optional, collection URL segment
- `collection_title` Optional, collection title
- `collection_id` Optional, collection ID
- `page_slug` Page URL segment
- `template` Page template file to load
- `title` Page title
- `sub_title` Page subtitle (if enabled)
- `meta_description` Page meta description
- `published_date` Page published date
- `media_id` Featured media ID (if enabled)
- `created_by` User ID who created the page
- `created_date` Created date (which can be different from the published date)
- `updated_by` User ID who last updated the page
- `updated_date` Last updated date and time

## Using Media in Pages
If enabled in the Page Definition JSON file, a user can select a Page Image, either at the Page level or in an Element. The Image is accessed directly in the `page.media` array, while an image defined in an Element is accessed as the `element.media` array in the Element.

The `media` array includes
* `aspectRatio` Calculated image aspect ratio
* `orientation` Either `landscape` or `portrait`
* `id` Media ID
* `filename` Media filename
* `width` Original image width
* `height` Original image height
* `caption` Media caption

The `filename` property contains the PitonCMS generated filename when the media file was uploaded. To easily print the relative path as an `img` source use the PitonCMS function `getMediaPath()` or to generate a source set use `getMediaSrcSet()`.

### getMediaPath()
The `getMediaPath()` Template function returns the relative path to your media file and has the signature

```php
    /**
     * Get Media Path
     *
     * @param  string $filename Media file name to parse
     * @param  string $size     Media size: original|xlarge|large|small|thumb
     * @return string
     */
    public function getMediaPath(?string $filename, string $size = 'original'): ?string
```

Pass in the `filename` and optionally a string representing the requested size (one of `'original'`, `'xlarge'`, `'large'`, `'small'`, or `'thumb'`). For example, to request the 'large' Page Image media file in your Template

```html
<img src="{{ getMediaPath(page.media.filename, 'large') }}">
```

And to access the `media` array in an Element reference the `element` key instead of the `page` key

```html
<img src="{{ getMediaPath(element.media.filename, 'large') }}">
```

### getMediaSrcSet()
The `getMediaSrcSet()` Template function returns a fully formed HTML string to display the requested media file, and has the signature

```php
    /**
     * Get Media Source Set
     *
     * Creates list of available image files in source set format
     * @param string $filename Media filename
     * @param string $altText  Media caption to use as alt text
     * @param array  $options   Options array, includes "sizes", "style"
     * @return string
     */
    public function getMediaSrcSet(string $filename = null, string $altText = null, array $options = null): ?string
```

Pass in the `filename`, the alternate text (or `null`), and an optional array of `options` for `sizes` or `style`

```html
{{ getMediaSrcSet(page.media.filename, page.media.caption, {'sizes': '(max-width: 767px) 100vw, (max-width: 899px) 50vw, 33vw'}) }}
```

Which prints

```html
<img srcset="/media/af/afe54811a462/afe54811a462-thumb.jpg 350w,
    /media/af/afe54811a462/afe54811a462-small.jpg 1024w,
    /media/af/afe54811a462/afe54811a462-large.jpg 2000w,
    /media/af/afe54811a462/afe54811a462-xlarge.jpg 3264w"
    sizes="(max-width: 767px) 100vw, (max-width: 899px) 50vw, 33vw"
    src="/media/af/afe54811a462/afe54811a462-xlarge.jpg" alt="Sunset over Terrace Gardens." >
```

>**Note**: Alternate media sizes are only created if a TinyJPG key was registered in PitonCMS and if the **Optimize** option was enabled when the media file was uploaded.


## Using Site Data
The *Site* array contains an array of variables under the `site` key, most of which are user managed in the **Settings** menu. All site variables are available on each page.

The `site` array contains
- `settings` Configuration and runtime values
- `environment` Properties related to the current environment

### Site Settings
The `site.settings` array properties are available to print in all Templates, and are defined in the project `siteSettings.json` Definition file with user saved values.

Site Settings are useful to print small bits of data such as a link to a Twitter account, but can also be used as a flag (Y or N, or a date range for example) to control Page flow.

To print a Site Setting in a Template, specify the Setting key

```html
<a href="{{ site.settings.twitterLink }}">Find us on Twitter</a>
```

### Site Environment
The `site.environment` array contains system and environment information such as the logged in user, the PitonCMS version, and production flag. These values cannot be modified but can be used in the Template layout.

There are many environment properties, but primary ones to consider are
- `production` Production flag (boolean, true or false) from the config file
- `cspNonce` The nonce to print with some elements (See [Security](/admin/support/designer/security) support document.)
- `csrfTokenName` and `csrfTokenValue` CSRF Token required in forms

The `production` variable is a boolean flag (true or false) that relies on the `$config['environment']['production']` setting in `config.local.php` that is unique to each environment. You can use this flag in your code to conditionally print content depending on whether the code is running on a development server or a production server.

For example, to only run tracking analytics code in your Template when in production, wrap the analytics code in a Twig `if` statement referencing the `production` flag

```html
{% if site.environment.production %}
    <!-- My Tracking Analytics Code -->
{% endif %}
```

When building contact *Message* forms, be sure to include the [CSRF Token](https://owasp.org/www-community/attacks/csrf) in the form as a hidden input otherwise the POST request will be rejected by PitonCMS for security reasons.

```html
<input type="hidden" name="{{ site.environment.csrfTokenName }}" value="{{ site.environment.csrfTokenValue }}">
```

## Printing Dates
Dates are stored in PitonCMS in the ISO 8601 format, YYYY-MM-DD. If you print a date variable you will get exactly that, E.g. "2020-10-05".

To print a date or datetime in a more friendly way that is localised to the user, you can provide the date format mask in the Twig `date()` filter using standard [PHP date formats](https://www.php.net/manual/en/datetime.format.php)

```html
<div class="published-date">Published on {{ page.published_date|date('F jS, Y') }}</div>
```
Would then print
```html
<div class="published-date">Published on October 5th, 2020</div>
```
