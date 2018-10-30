var path = require('path')

function resolve(dir) {
    return path.join(__dirname, './', dir)
}
module.exports = {
    outputDir: '../public',
    indexPath: process.env.NODE_ENV === 'production'
      ? '../resources/views/index.blade.php'
      : 'index.html',
      css: {
        sourceMap: true,
        loaderOptions: {
          sass: {
            data: `@import "@/styles/index.scss";`
          }
        }
    },
    chainWebpack: config => {
        config.module
            .rule('svg')
            .exclude.add(resolve('src/icons'))
            .end()

        config.module
            .rule('icons')
            .test(/\.svg$/)
            .include.add(resolve('src/icons'))
            .end()
            .use('svg-sprite-loader')
            .loader('svg-sprite-loader')
            .options({
                symbolId: 'icon-[name]'
            })
    }
}
