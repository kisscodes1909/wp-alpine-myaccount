#!/usr/bin/env node

const fs = require('fs');
const path = require('path');
const postcss = require('postcss');
const atImport = require('postcss-import');
const nestedAncestors = require('postcss-nested-ancestors');
const nested = require('postcss-nested');
const autoprefixer = require('autoprefixer');
const cssnano = require('cssnano');

const root = path.resolve(__dirname, '..');
const isProd = process.env.NODE_ENV === 'production';
const isWatch = process.argv.includes('--watch');
const useLivereload = process.argv.includes('--livereload');

const buildTargets = [
  {
    input: 'assets/src/css/myaccount-global.css',
    output: 'assets/css/ma-global.css',
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
    input: 'assets/src/css/myaccount-module-returns.css',
    output: 'assets/css/ma-module-returns.css',
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
    input: 'assets/src/css/myaccount-endpoint-wishlist.css',
    output: 'assets/css/ma-wishlist.css',
  },
  {
    input: 'assets/src/css/myaccount-endpoint-auth.css',
    output: 'assets/css/ma-auth.css',
  },
  {
    input: 'assets/src/css/myaccount-navigation-vertical-entry.css',
    output: 'assets/css/ma-navigation-vertical.css',
  },
  {
    input: 'assets/src/css/myaccount-navigation-stacked-entry.css',
    output: 'assets/css/ma-navigation-stacked.css',
  },
  {
    input: 'assets/src/css/structure-file.css',
    output: 'assets/css/myaccount.css',
  },
];

async function buildOne(target) {
  const inputPath = path.join(root, target.input);
  let outputPath = path.join(root, target.output);
  if (isProd) {
    outputPath = outputPath.replace(/\.css$/, '.min.css');
  }
  const mapPath = `${outputPath}.map`;
  const css = fs.readFileSync(inputPath, 'utf8');

  const plugins = [
    atImport(),
    nestedAncestors(),
    nested(),
    autoprefixer(),
  ];
  if (isProd) {
    plugins.push(cssnano());
  }

  const result = await postcss(plugins).process(css, {
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
  /** LiveReload server only — không watch assets/css (tránh reload khi mới ghi file đầu, build chưa xong). */
  let lrServer = null;
  if (useLivereload) {
    try {
      const livereload = require('livereload');
      /* createServer() đã listen port 35729 (trừ khi noListen). */
      lrServer = livereload.createServer({ port: 35729 });
      console.log(
        '[build-css] LiveReload ws://localhost:35729 — bật extension trên tab My Account, sau đó sửa CSS.'
      );
    } catch (e) {
      console.warn(
        '[build-css] LiveReload không chạy (npm install livereload?). Watch vẫn hoạt động.',
        e.message
      );
    }
  }

  const debouncedBuild = () => {
    if (timeoutId) {
      clearTimeout(timeoutId);
    }

    timeoutId = setTimeout(async () => {
      try {
        await buildAll();
        if (lrServer) {
          lrServer.refresh('/');
        }
      } catch (error) {
        console.error('[build-css] Build failed:', error.message);
      }
    }, 150);
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
