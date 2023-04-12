# PitonCMS Support Files Structure

Support files consist of one markdown file for each topic, divided between client and designer subjects.

The filename is passed as a parameter to the `/support/{subject}/{file}` route without the `.md` extension.

The markdown file is rendered to HTML at runtime.

PitonCMS is in the process of converting to the DigitalOcean Technical Writing Guideline.
https://www.digitalocean.com/community/tutorials/digitalocean-s-technical-writing-guidelines

## Outline
Support articles should have a title, possibly a brief introduction, and may have steps or sections before a conclusion (optional).

- Article Title (Level 1 heading, in Title Case)
- Introduction (Level 3 heading), optional
- Prerequisites (Level 2 heading), optional
- Doing the First Thing (Level 2 heading)
- Doing the Next Thing (Level 2 heading)
- …
- Doing the Last Thing (Level 2 heading)
- Conclusion (Level 2 heading), optional

Try to avoid using Level 4 headings, and use Level 3 sparingly.

## Formatting

### Piton Terminology
Italicize the first mention of a Piton term within an article and include a definition, and capitalize the first letter of other references.
>*Pages* can either be standalone or part of a collection of related Pages (called a *Collection*).

### UI Elements and Steps
Use **bold** button, checkbox, select lists, or link names, and other UI interactions to perform a task.
>To save your Page, click on the **Save** button.

## Syntax Hightling
PitonCMS support pages relies on Highlight JS for syntax highlighting. https://highlightjs.org/download/ The PitonCMS highlight build includes all default languages plus Twig. The syntax style sheet is called Agate. To update the highlighter, download a recent build of highlight.js with Twig and any additional languages, and then copy the `agate.min.css`, the `highlight.min.js`, and `LICENSE` files to `engine/assets/highlight/`.

## Support Assets
Custom graphics for PitonCMS support files are managed in Google Slides https://docs.google.com/presentation/d/1xgpDeCaA10AuDVTfgQS8qvaHjCHjMqD8AACz1evKS68/edit#slide=id.g4aebb7f868_0_0