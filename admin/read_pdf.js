const fs = require('fs');
const pdf = require('pdf-parse');

let dataBuffer = fs.readFileSync('../oms.pdf');

pdf(dataBuffer).then(function(data) {
    // number of pages
    console.log("Pages:", data.numpages);
    // info
    console.log("Info:", data.info);
    // PDF text
    console.log("Text:", data.text.substring(0, 5000)); // just first 5000 chars for a start
}).catch(function(error) {
    console.error("Error parsing PDF:", error);
});
