const { execSync, spawn } = require('child_process');
const fs = require('fs');
const os = require('os');
const path = require('path');

const outputPath = process.argv[2];
const topM = process.argv[3] || '0';
const bottomM = process.argv[4] || '0';
const leftM = process.argv[5] || '0';
const rightM = process.argv[6] || '0';

if (!outputPath) {
    console.error(JSON.stringify({ error: 'Output yoli berilmadi' }));
    process.exit(1);
}

let fullHtml = '';
process.stdin.setEncoding('utf8');
process.stdin.on('data', (chunk) => { fullHtml += chunk; });
process.stdin.on('end', () => {
    try {
        const tmpHtml = '/var/www/artiqle_new/public/temp_' + Date.now() + '.html';
        fs.writeFileSync(tmpHtml, fullHtml);
        
        const args = [
	'--page-size', 'A4',
    '--margin-top', topM || '0',
    '--margin-bottom', bottomM || '0',
    '--margin-left', leftM || '0',
    '--margin-right', rightM || '0',
    '--encoding', 'UTF-8',
    '--enable-local-file-access',
    '--images',
    '--no-stop-slow-scripts',
    '--javascript-delay', '3000',
    '--zoom', '1',
    '--dpi', '96',
    'file://' + tmpHtml,
    outputPath           

        ];
        
        execSync('wkhtmltopdf ' + args.map(a => '"' + a + '"').join(' '), { timeout: 60000 });
        fs.unlinkSync(tmpHtml);
        console.log(JSON.stringify({ success: true, path: outputPath }));
    } catch (e) {
        fs.appendFileSync('/var/www/artiqle_new/puppeteer_error.log', e.message + "\n");
        console.error(JSON.stringify({ error: e.message }));
        process.exit(1);
    }
});
