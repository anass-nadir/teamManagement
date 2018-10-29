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
    }
  }
