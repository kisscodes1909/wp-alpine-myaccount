(async () => {
    const esbuild = require("esbuild");
    const browserSync = require('browser-sync').create();

    // esbuild options
    const buildType = process.argv.slice(2)[0];
    const options = {
        entryPoints: ["./assets/js/yup.js"],
        outdir: "./assets/build/js",
        bundle: true,
        metafile: true,
        sourcemap: buildType === 'production',
        minify: buildType === 'production',
    };

    if (buildType === 'watch') {
        options.watch = {
            onRebuild(error, result) {
                if (error) console.error('Rebuild failed:', error);
                else {
                    console.log('Rebuild succeeded');
                    browserSync.reload(); // Trigger BrowserSync reload
                }
            },
        };

        // Initialize BrowserSync
        browserSync.init({
            proxy: "https://wptailwindcss.test", // Replace with your local dev URL
            files: ["./assets/**/*.css", "./assets/**/*.js", "./**/*.php"], // Watch CSS and PHP files for changes
        });
    }

    // Execute build with esbuild
    try {
        const result = await esbuild.build(options);
        const text = await esbuild.analyzeMetafile(result.metafile);
        console.log(text);
    } catch (error) {
        console.error('Build failed:', error);
        process.exit(1);
    }
})();
