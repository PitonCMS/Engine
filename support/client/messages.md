# Messages

PitonCMS has a built-in Mailbox and Messaging system to help you manage communications with your visitors and clients.

If your web design supports it, you may have one or more *Contact Forms* for visitors to submit Messages and requests. All Messages are saved in your website administration console where you can read, archive, or delete them at your convenience. If configured, a copy of the message may also be sent to your personal email account (without sharing with the visitor).

PitonCMS has a strong built in spam message control feature to reduce (but not necessarily eliminate) spam messages.

## How to Configure Message Options
You may configure a few options on how visitor Messages are presented.

1. Go to <i class="fas fa-cog"></i> **Settings** and then **Messages**
2. Modify settings
3. Click **Save** to save your changes, or **Discard** to reset your changes

The Message options are

- **Contact Form Email** (Optional) Where to send a copy of any visitor messages
- **Contact Form Submission Acknowledgement** What to display after a visitor submits a message
- **Minimum Message Length** Set the minimum number of characters in the Message body in order to process the Message (to reduce one word message spam)

>**Note**: All submitted Messages (that pass the Minimum Message Length test and the spam filter) are saved to PitonCMS. Forwarding a copy is optional, and your forwarding address is *not* shown to the public.

## How to Add a Message Contact Form
Contact Forms are configured as part of your web design, and may include additional input fields for custom information.

To display a contact form you need to select a *Contact Element* in your page (if configured as part of your web design).

1. Go to <i class="fas fa-pencil"></i> *Content* and create or edit a Page.
2. Click **Add Element** then select **Contact** (or the custom name in your web design) to the *Block*
3. Optionally add a Title and some text
4. Save and Publish the Page

## How to Manage Messages in PitonCMS
When a visitor submits a Message (and if it passes the spam filter and Minimum Message Length) the Message is saved in PitonCMS. If a forwarding email has been saved, then a copy is also sent to that email address.

The *Mailbox* menu link shows how many unread Messages you have.
<img src="/admin/img/support/mailboxUnreadCount.png" style="width:30%;">

To manage your messages, click on the <i class="fas fa-envelope"></i> **Mailbox** menu link. Here you can mark Messages as read or as unread, archive and and delete messages.

Each Message is displayed in a block, which includes
![Message row](/admin/img/support/messageBlock.png)