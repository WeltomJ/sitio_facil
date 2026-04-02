/**
 * copy-assets.js
 * Copia libs do node_modules para public/vendor.
 * Execute: npm run setup   (ou npm run build)
 */

const fs   = require('fs');
const path = require('path');

const root   = path.resolve(__dirname, '..');
const nm     = path.join(root, 'node_modules');
const vendor = path.join(root, 'public', 'vendor');

const assets = [
    // Bootstrap
    { src: 'bootstrap/dist/css/bootstrap.min.css',           dest: 'bootstrap/css/bootstrap.min.css' },
    { src: 'bootstrap/dist/js/bootstrap.bundle.min.js',      dest: 'bootstrap/js/bootstrap.bundle.min.js' },

    // Popper.js (dependência do Bootstrap)
    { src: '@popperjs/core/dist/umd/popper.min.js',          dest: 'popperjs/popper.min.js' },

    // jQuery
    { src: 'jquery/dist/jquery.min.js',                      dest: 'jquery/jquery.min.js' },

    // jQuery Validation
    { src: 'jquery-validation/dist/jquery.validate.min.js',  dest: 'jquery-validate/jquery.validate.min.js' },
    { src: 'jquery-validation/dist/localization/messages_pt_BR.min.js', dest: 'jquery-validate/messages_pt_BR.min.js' },

    // Font Awesome
    { src: '@fortawesome/fontawesome-free/css/all.min.css',  dest: 'fontawesome/css/all.min.css' },

    //Moment.js
    { src: 'moment/min/moment.min.js',                      dest: 'moment/moment.min.js' },
];

// Webfonts do Font Awesome (diretório inteiro)
const faWebfonts = path.join(nm, '@fortawesome', 'fontawesome-free', 'webfonts');
const faWebfontsDest = path.join(vendor, 'fontawesome', 'webfonts');

let ok = 0, fail = 0;

for (const { src, dest } of assets) {
    const from = path.join(nm, src);
    const to   = path.join(vendor, dest);

    fs.mkdirSync(path.dirname(to), { recursive: true });

    try {
        fs.copyFileSync(from, to);
        console.log(`✔  ${dest}`);
        ok++;
    } catch (e) {
        console.error(`✘  ${dest}  →  ${e.message}`);
        fail++;
    }
}

// Copia webfonts
if (fs.existsSync(faWebfonts)) {
    fs.mkdirSync(faWebfontsDest, { recursive: true });
    for (const file of fs.readdirSync(faWebfonts)) {
        try {
            fs.copyFileSync(path.join(faWebfonts, file), path.join(faWebfontsDest, file));
            console.log(`✔  fontawesome/webfonts/${file}`);
            ok++;
        } catch (e) {
            console.error(`✘  fontawesome/webfonts/${file}  →  ${e.message}`);
            fail++;
        }
    }
}

console.log(`\n${ok} arquivo(s) copiado(s), ${fail} erro(s).`);
if (fail > 0) process.exit(1);
