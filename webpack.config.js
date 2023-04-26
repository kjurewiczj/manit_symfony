const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    .enableSassLoader()
    .addEntry('app', './assets/app.js')
    .addEntry('bootstrap-bundle-js', './node_modules/bootstrap/dist/js/bootstrap.js')
    .addEntry('soft-design-system-js', './node_modules/soft-ui-design-system/assets/js/soft-design-system.js')
    .addEntry('delete-action', './assets/defaults/delete_action.js')
    .addStyleEntry('soft-design-system-fonts', './node_modules/soft-ui-design-system/assets/css/nucleo-icons.css')
    .addStyleEntry('soft-design-system', './node_modules/soft-ui-design-system/assets/css/soft-design-system.css')

    .enableStimulusBridge('./assets/controllers.json')
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.23';
    })
;
module.exports = Encore.getWebpackConfig();
