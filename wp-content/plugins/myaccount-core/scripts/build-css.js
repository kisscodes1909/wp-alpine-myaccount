#!/usr/bin/env node

const fs = require('fs');
const path = require('path');
const postcss = require('postcss');
const atImport = require('postcss-import');
const nestedAncestors = require('postcss-nested-ancestors');
const nested = require('postcss-nested');
const autoprefixer = require('autoprefixer');

const root = path.resolve(__dirname, '..');
const isProd = process.env.NODE_ENV === 'production';
const isWatch = process.argv.includes('--watch');

const buildTargets = [
  {
    input: 'assets/src/css/myaccount-shared.css',
    output: 'assets/css/ma-shared.css',
  },
  {
    input: 'assets/src/css/myaccount-endpoint-orders.css',
    output: 'assets/css/ma-orders.css',
  },
  {
    input: 'assets/src/css/myaccount-endpoint-view-order.css',
    output: 'assets/css/ma-view-order.css',
  },
  {
    input: 'assets/src/css/myaccount-endpoint-payment-methods.css',
    output: 'assets/css/ma-payment-methods.css',
  },
  {
    input: 'assets/src/css/myaccount-endpoint-edit-account.css',
    output: 'assets/css/ma-edit-account.css',
  },
  {
    input: 'assets/src/css/myaccount-endpoint-address.css',
    output: 'assets/css/ma-address.css',
  },
  {
    input: 'assets/src/css/myaccount-endpoint-auth.css',
    output: 'assets/css/ma-auth.css',
  },
  {
    input: 'assets/src/css/structure-file.css',
    output: 'assets/css/myaccount.css',
  },
];

async function buildOne(target) {
  const inputPath = path.join(root, target.input);
  const outputPath = path.join(root, target.output);
  const mapPath = `${outputPath}.map`;
  const css = fs.readFileSync(inputPath, 'utf8');

  const result = await postcss([
    atImport(),
    nestedAncestors(),
    nested(),
    autoprefixer(),
  ]).process(css, {
    from: inputPath,
    to: outputPath,
    map: isProd ? false : { inline: false },
  });

  fs.mkdirSync(path.dirname(outputPath), { recursive: true });
  fs.writeFileSync(outputPath, result.css, 'utf8');

  if (result.map) {
    fs.writeFileSync(mapPath, result.map.toString(), 'utf8');
  } else if (fs.existsSync(mapPath)) {
    fs.rmSync(mapPath);
  }

  console.log(`[build-css] Wrote ${path.relative(root, outputPath)}`);
}

async function buildAll() {
  for (const target of buildTargets) {
    await buildOne(target);
  }
}

function watch() {
  let timeoutId = null;

  const debouncedBuild = () => {
    if (timeoutId) {
      clearTimeout(timeoutId);
    }

    timeoutId = setTimeout(async () => {
      try {
        await buildAll();
      } catch (error) {
        console.error('[build-css] Build failed:', error.message);
      }
    }, 100);
  };

  fs.watch(path.join(root, 'assets/src/css'), { recursive: true }, debouncedBuild);
  console.log('[build-css] Watching assets/src/css ...');
}

buildAll()
  .then(() => {
    if (isWatch) {
      watch();
    }
  })
  .catch((error) => {
    console.error('[build-css] Build failed:', error);
    process.exit(1);
  });
