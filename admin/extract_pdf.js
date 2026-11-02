const pdfjsLib = require('pdfjs-dist/build/pdf.js');
const fs = require('fs');

async function extractText(path) {
    const data = new Uint8Array(fs.readFileSync(path));
    const loadingTask = pdfjsLib.getDocument({data});
    const pdfDocument = await loadingTask.promise;
    
    let fullText = '';
    for (let i = 1; i <= Math.min(30, pdfDocument.numPages); i++) {
        const page = await pdfDocument.getPage(i);
        const textContent = await page.getTextContent();
        const pageText = textContent.items.map(item => item.str).join(' ');
        fullText += pageText + '\n\n';
    }
    return fullText;
}

extractText('../oms.pdf').then(text => {
    fs.writeFileSync('extracted_pdf_text.txt', text);
    console.log("Extracted successfully!");
}).catch(console.error);
