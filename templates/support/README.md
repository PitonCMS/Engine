# PitonCMS Support Files Structure

Support files consist of one markdown file for each topic, divided between subjects (client and designer).

The filename is passed as a parameter to the `/support/{subject}/{file}` route without the `.md` extension.

The markdown file is rendered to HTML at runtime.

PitonCMS more or less uses the Microsoft Technical style guide for formatting and marking up support content.
https://docs.microsoft.com/en-us/style-guide/procedures-instructions/formatting-text-in-instructions

For the support article outline, PitonCMS uses the Digital Ocean conceptual template.
https://www.digitalocean.com/community/tutorials/digitalocean-s-technical-writing-guidelines

## Outline
Support articles should have a title, possibly an introduction, and may have steps or sections before a conclusion (optional).

- Article Title (Level 1 heading, in Title Case)
- Introduction (Level 3 heading), optional
- Doing the First Thing (Level 2 heading)
- Doing the Next Thing (Level 2 heading)
- …
- Doing the Last Thing (Level 2 heading)
- Conclusion (Level 2 heading), optional

Try to avoid using Level 4 headings, and use Level 3 sparingly.

## Formatting


**Piton Terminology**
Italicize the first mention of a Piton term within an article and include a definition, and capitalize the remainder.
>*Pages* can either be standalone or part of a collection of related Pages (called a *Collection*).

**UI Elements and Steps**
Bold button, checkbox, select lists, or link names, and other UI interactions to perform a task.
>To save your Page, click on the **Save** button.

