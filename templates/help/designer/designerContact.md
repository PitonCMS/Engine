# Custom Contact Forms

Let visitors contact your client! With PitonCMS contact forms, you can easily build contact forms that save the message to the PitonCMS message inbox and optionally email the site administrator.

You can also include custom fields that allow you to create variations on contact forms, including registration forms, order forms and more.

All contact messages are saved to the PitonCMS **Mailbox** where the client can manage and archive

## Basic Contact Form
Custom contact forms are best created as a Page Element that the client can add to a page. However, the form can also be coded directly into the Page Template if desired.

When a visitor submits the form, the form submitted by Ajax to provide a seamless user experience. If successful, the form is then replaced with an acknowledgement message set in **Settings > Contact > Contact Form Submission Acknowledgement**. If an email address was provided **Settings > Contact > Contact Form Email** then the administrator is emailed a copy of the contact message.

A basic contact form structure.
```html
{% import "includes/_macros.html" as pitonMacro %}
<form class="contact-form" id="contact-form" method="post" accept-charset="utf-8" data-contact-form="true">
    <input type="hidden" name="context" value="{{ page.title }}">

    <label>Your Name</label>
    <input type="text" class="contact-form__form-control" name="name" maxlength="100" placeholder="Name" autocomplete="off">

    <label>Your Email<span class="text-danger">*</span></label>
    <input type="email" class="contact-form__form-control" name="email" maxlength="100" placeholder="Email address" required autocomplete="off">

    <label>Message</label>
    <textarea class="contact-form__form-control" rows="5" name="message"></textarea>

    <button class="btn" type="submit">Submit</button>

    {{ pitonMacro.contactHoneypot() }}
</form>
```

### Form Structure
The data- attribute `data-contact-form="true"` in the `form` element is used by PitonCMS for the Ajax form submission.

The value of the hidden input `name="context"` can be set to any desired value to provide information to the site administrator on _which_ contract form was used to submit the message. This is useful if you have multiple types of submit forms. The value can be static text, or in this example dynamically sets the Page Title.

The inputs `name="name"` and `name="email"` are limited to 100 characters and should be included in all forms. The `textarea` `name="message"` captures a free text area, and is optional.

Be sure to include a `button` of `type="submit"` in the `form`. By using a *submit* button, browsers with HTML5 support will validate and alert the user to any validation issues.

### Honeypot
To manage comment spam, PitonCMS contact forms can use a honeypot, which is a hidden email input set with a known value. Bots will typically attempt to complete all form inputs of `type="email"`. If the known value is overrode then PitonCMS will quietly ignore the whole message.

To include the honeypot be sure to import the Piton Twig Macros somewhere at the top of the page above the form.
```html
{% import "includes/_macros.html" as pitonMacro %}
```

And then print the honeypot macro anywhere inside the form.
```html
{{ pitonMacro.contactHoneypot() }}
```

## Custom Input Fields
To extend the contact form with addition custom inputs to create order or registration forms,

1. Add any custom inputs to your form and give each custom input a unique `name`. The name should only include letters, numbers, dashes, or underscores.
2. Register the custom input in `structure/definitions/contactInputs.json`

For example, to include an Arrival and Departure Date on a guest contact form, add these inputs to your form.

```html
<label>Arrival Date</label>
<input type="date" class="contact-form__form-control" name="arrivalDate">

<label>Departure Date</label>
<input type="date" class="contact-form__form-control" name="departureDate">
```

Note, the `type` can be either simple **text** or **date** types.

Then in `contactInputs.json` add to the array `[ ]` two custom input objects.

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

Where the **name** is a user friendly descriptive label to use in the email and message inbox, and **key** matches the HTML form input `name` attribute.

When the form is submitted, only custom form inputs with a matching key in the definition file will be save.

