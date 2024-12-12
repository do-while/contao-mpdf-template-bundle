# contao-mpdf-template-bundle

**With the contao-mpdf-template-bundle extension, you can design the PDF output in the article (syndication) with a PDF template file. The mPDF library is used in this extension**


With the contao-mpdf-template-bundle extension, a saved PDF file can be used as a layout template for the PDF output. The template must be in the format **PDF Specification 1.4 (Acrobat 5)**. The template can contain one or more pages, which are included in the output in the order in which they appear. If more pages are output than are available in the template, the last page is repeated until the end of the output.

The **file name of the download** can be specified as the GET parameter `&t=foo` in the URL. For example, the template `mod_article.html5` can be adapted for this purpose.


### Installation
Simply install the extension with the **Contao Manager** or on the command line with the **Composer**:

`composer require do-while/contao-mpdf-template-bundle`



### Contao Demo
In the Contao demo, the syndication icons are replaced by CSS, but no thought has been given to a PDF button. As a result, the PDF icon is no longer visible.
As a **workaround** please enter an additional style in your CSS:
```
.syndication .pdf:before {
  background: url('assets/contao/images/iconPDF.svg') no-repeat;
  background-size: contain;
}
```


### Settings
The settings are made in the ‘Website root’ and apply to all pages below this starting point. This makes it possible to use different PDF layouts depending on the language or domain. Depending on the layout, the margins of the text output can be set so that the header or footer area of the template is not overwritten by the article content.

Using the template `mpdf_default` or your own templates beginning with `mpdf_...`, further commands or settings, such as header/footer, etc. can be transferred to the mpdf module. The template is processed twice, once when the PDF is initialised and once when the content is output. Specific adjustments can be made here, the template is provided with comments so that you can quickly find the right position for your customisation.

Data such as the template file used, the margins and the template used can be **overwritten** directly in the article, which is activated for PDF output by ticking the checkbox.



### HTML/CSS support
The HTML and CSS support of mPDF is limited in some respects. More detailed information can be found in the [mPDF documentation](https://mpdf.github.io/css-stylesheets/introduction.html)


For the output, **Custom CSS** files can be specified for formatting the content. Other CSS files from the website are not processed.

You may have to try out the CSS instructions, as not all nuances are supported by mPDF.



___
Softleister - 2024-12-12

The extension is based on the mPDF module, see (https://github.com/mpdf/mpdf)
