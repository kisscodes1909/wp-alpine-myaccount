#!/usr/bin/env node

const path = require('path');
const esbuild = require('esbuild');

const root = path.resolve(__dirname, '..');
const isProd = process.env.NODE_ENV === 'production';
const isWatch = process.argv.includes('--watch');

const buildTargets = [
  {
    input: 'assets/src/js/alpine/entries/shared-core.js',
    output: 'assets/js/alpine.shared-core.js',
  },
  {
    input: 'assets/src/js/alpine/entries/shared-validation.js',
    output: 'assets/js/alpine.shared-validation.js',
  },
  {
    input: 'assets/src/js/alpine/entries/endpoint-auth.js',
    output: 'assets/js/alpine.auth.js',
  },
  {
    input: 'assets/src/js/alpine/entries/endpoint-orders.js',
    output: 'assets/js/alpine.orders.js',
  },
  {
    input: 'assets/src/js/alpine/entries/endpoint-view-order.js',
    output: 'assets/js/alpine.view-order.js',
  },
  {
    input: 'assets/src/js/alpine/entries/endpoint-payment-methods.js',
    output: 'assets/js/alpine.payment-methods.js',
  },
  {
    input: 'assets/src/js/alpine/entries/endpoint-edit-account.js',
    output: 'assets/js/alpine.edit-account.js',
  },
  {
    input: 'assets/src/js/alpine/entries/endpoint-address.js',
    output: 'assets/js/alpine.address.js',
  },
  {
    input: 'assets/src/js/alpine/init.js',
    output: 'assets/js/alpine.bundle.js',
    globalName: 'AlpineBundle',
  },
];

function getBuildOptions(target) {
  let output = target.output;
  if (isProd) {
    output = output.replace(/\.js$/, '.min.js');
  }
  const options = {
    entryPoints: [path.join(root, target.input)],
    outfile: path.join(root, output),
    bundle: true,
    format: 'iife',
    platform: 'browser',
    sourcemap: isProd ? false : true,
    minify: isProd,
    logLevel: 'silent',
  };

  if (target.globalName) {
    options.globalName = target.globalName;
  }

  return options;
}

async function buildAll() {
  for (const target of buildTargets) {
    const options = getBuildOptions(target);
    await esbuild.build(options);
    const outBasename = path.basename(options.outfile);
    console.log(`[build-js] Wrote assets/js/${outBasename}`);
  }
}

async function watchAll() {
  const contexts = [];

  for (const target of buildTargets) {
    const context = await esbuild.context(getBuildOptions(target));
    await context.watch();
    contexts.push(context);
    console.log(`[build-js] Watching ${target.input} -> ${target.output}`);
  }

  process.on('SIGINT', async () => {
    await Promise.all(contexts.map((context) => context.dispose()));
    process.exit(0);
  });
}

buildAll()
  .then(async () => {
    if (isWatch) {
      await watchAll();
    }
  })
  .catch((error) => {
    console.error('[build-js] Build failed:', error);
    process.exit(1);
  });
