# Contact Forms

With PitonCMS you can easily build contact forms that save visitor messages to the PitonCMS <i class="fas fa-envelope"></i> **Mailbox** to  manage, search, archive, or delete messages. Messages can also be optionally emailed the client administrator.

You can also easily include custom form inputs that allow you to create different types of contact forms including registration forms, order forms, and more.

PitonCMS contact forms also have an automatic spam control honeypot and message length filter.

The Message forms are submitted by XHR to provide a seamless visitor experience. After being submitted the form is then replaced with a custom acknowledgement message set by the user.

## Basic Contact Form
Contact forms come with PitonCMS as a *Contact* Element that the user can easily add to a page Block. However, the form can also be coded directly into a *Page Template* and customized depending on the use.

A basic contact form structure is

```html
{% import "includes/_macros.html" as pitonMacro %}
<form class="contact-form" id="contact-form" method="post" accept-charset="utf-8" data-contact-form="true">
    <input type="hidden" name="{{ site.environment.csrfTokenName }}" value="{{ site.environment.csrfTokenValue }}">
    <input type="hidden" name="context" value="{{ page.title }}">

    <label>Your Name</label>
    <input type="text" name="name" maxlength="100" placeholder="Name" autocomplete="off">

    <label>Your Email<span class="text-danger">*</span></label>
    <input type="email" name="email" maxlength="100" placeholder="Email address" required autocomplete="off">

    <label>Message</label>
    <textarea rows="5" name="message"></textarea>

    <button class="btn" type="submit">Submit</button>

    {{ pitonMacro.contactHoneypot() }}
</form>
```

Key contact form components

| Component | Required | Description |
| --- | --- | --- |
| Twig Macro | Yes | Loads the honeypot Macro |
| `<form/>` | Yes | Include the UTF-8, POST method attributes |
| `data-contact-form="true"` | Yes | Hooks into XHR form submit |
| CSRF Token | Yes | Hidden input is required as a Cross Site Request Forgery control |
| Context |  | This is a hidden input to tag the message source if you have multiple contact forms. Using the *Page Title* is one way to identify the source
| `name` Input |  | Capture name of the submitter |
| `email` Input |  | Capture the email address of the submitter |
| `message` |  | Message body, max 1000 characters |
| `{{ pitonMacro.contactHoneypot() }}` | Yes | Inserts the PitonCMS Message honeypot |

Be sure to include a `button` of `type="submit"` in the `form`. By using a _submit_ button, browsers with HTML5 support will validate and alert the user to any issues.

### Honeypot and Spam Control
To control Message spam PitonCMS contact forms use a honeypot, which is a hidden email input set to a known value. Users will not see the input but bots will typically attempt to complete all form inputs of `type="email"`. If the expected value is altered then PitonCMS will quietly ignore the whole message.

To include the honeypot be sure to import the PitonCMS Macro above the form

```html
{% import "includes/_macros.html" as pitonMacro %}
```

and then print the honeypot macro anywhere inside the form

```html
{{ pitonMacro.contactHoneypot() }}
```

There is also a *Minimum Message Length* Setting to exclude Messages that are shorter than the specified value. This value can be modified in <i class="fas fa-cog"></i> **Settings** menu, **Contact** Settings.

## Custom Input Fields
To extend the contact form with custom field inputs to create ordering or registration forms and more

1. Add any necessary custom inputs to your contact form and give each custom input a unique `name`. The name should only include letters, numbers, dashes, or underscores without spaces
2. Register the custom input in `structure/definitions/contactInputs.json`. You can put all custom inputs for all custom forms in this single array of allowed contact form inputs

For example, to include an Arrival Date and Departure Date on a guest registration form add those inputs to your custom contact form.

```html
<label>Arrival Date</label>
<input type="date" name="arrivalDate">

<label>Departure Date</label>
<input type="date" name="departureDate">
```

In contact JSON Definition file `contactInputs.json` add your custom inputs to to the inputs array `[ ]`.

```json
[
    {
        "name": "Arrival Date",
        "key": "arrivalDate"
    },
    {
        "name": "Departure Date",
        "key": "departureDate"
    }
]
```

**Properties**

| Key | Required | Default | Description |
| --- | --- | --- | --- |
| `name` | Yes | | Description of the field |
| `key` | Yes | | The input `name`. The JSON `key` must match the allowed input `name` |

When the contact form is submitted only custom form inputs with a matching key in the definition file will be saved.
