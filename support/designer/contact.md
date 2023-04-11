# Contact Forms

With PitonCMS you can easily build contact forms that save visitor messages to the PitonCMS *Mailbox* to manage, search, archive, or delete messages. Messages can also be emailed the client administrator if an email is set in Settings > Messages > Contact Form Email.

You can also easily include custom form inputs that allow you to create different types of contact forms including registration forms, order forms, and more on the same website.

PitonCMS contact forms also have an automatic spam control honeypot and message length filter.

The Message forms are submitted by XHR to provide a seamless visitor experience. After being submitted the form is then replaced with a custom acknowledgement message set by the client.

## Basic Contact Form
An example *Contact* form comes with PitonCMS as an Element that the user can easily add to a page Block with custom text.

A basic contact form HTML Layout structure with documentation:

```twig
{# Import the PitonCMS Twig Marco file to enable the Honeypot #}
{% import "includes/_macros.html" as pitonMacro %}

<div class="element">

  {# The client can add a title and content to the Element #}
  <h2 class="element__title">{{ element.title }}</h2>
  {{ element.content }}

  {# Note: the form element must include the data attribute `data-contact-form="true"` for the XHR request to work #}
  <form class="contact-form" id="contact-form" method="post" accept-charset="utf-8" data-contact-form="true">

    {# Be sure to include the CSRF Token hidden input, otherwise PitonCMS will reject the XHR request #}
    <input type="hidden" name="{{ site.environment.csrfTokenName }}" value="{{ site.environment.csrfTokenValue }}">

    {#
        The `context` input value is optional, can be set to any text string as way to organize and identify message types or sources in the Mailbox.

        By default the `context` is set to the Page Title, but this input could be a select list, a radio, or a checkbox, as long as a `context=value` is sent
    #}
    <input type="hidden" name="context" value="{{ page.title }}">

    {# Standard inputs #}
    <div class="input-group">
      <label>Your Name</label>
      <input type="text" class="contact-form__form-control" name="name" maxlength="100" placeholder="Name"
        autocomplete="off">
    </div>

    <div class="input-group">
      <label>Your Email<span class="text-danger"> *</span></label>
      <input type="email" class="contact-form__form-control" name="email" maxlength="100" placeholder="Email address"
        required autocomplete="off">
    </div>

    <div class="input-group span">
      <label>Message<span class="text-danger"> *</span></label>
      <textarea class="contact-form__form-control" rows="5" name="message" required></textarea>
    </div>

    <div class="input-group span">
      <button class="btn btn-submit" type="submit">Submit</button>
    </div>

    <div class="input-group span">
      <p><span class="text-danger">* </span>Required</p>
    </div>

    {# The Honeypot Macro, helps dismiss bot spam message submissions #}
    {{ pitonMacro.contactHoneypot() }}
  </form>

  {# Preload the client response message text, but keep it hidden. PitonCMS will display the message after form submission #}
  <div class="contact-form__response" data-contact-response="true" hidden>
    <p>{{ site.settings.contactFormAcknowledgement }}</p>
  </div>

</div>
```

Key contact form components

| Component | Required | Description |
| --- | --- | --- |
| Twig Macro | Yes | Loads the honeypot Macro |
| `<form/>` | Yes | Include the UTF-8, POST method attributes |
| `data-contact-form="true"` | Yes | Hooks into XHR form submit |
| CSRF Token | Yes | Hidden input is required as a Cross Site Request Forgery control |
| Context |  | This is a hidden input to tag the message source if you have multiple contact forms. Using the *Page Title* is one way to identify the source
| `name` Input | | Capture name of the submitter |
| `email` Input | Yes | Capture the email address of the submitter |
| `message` | Yes | Message body, max 1000 characters |
| `{{ pitonMacro.contactHoneypot() }}` | Yes | Inserts the PitonCMS Message honeypot |
| `data-contact-response="true" hidden` | | When the form is successfully submitted, this element will be displayed |

Be sure to include a `button` of `type="submit"` in the `form`. By using a _submit_ button, browsers with HTML5 support will validate and alert the user to any issues.

## Honeypot and Spam Control
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
