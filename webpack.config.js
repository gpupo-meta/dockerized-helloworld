const path = require('path');

module.exports = {
  context: "/var/www/app",
  entry: {
    'js/helloworld.js': [
        path.resolve(__dirname, 'assets/js', 'helloworld.js')
    ],
    'js/helloworld-ES2015.js': [
        "./assets/js/helloworld-ES2015.js"
    ]
  },
  output: {
      path: "/var/www/app/public/build",
      filename: "[name]",
      publicPath: "/build/",
      pathinfo: false
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: "babel-loader"
        }
      },
      {
          test: /\.scss$/,
          exclude: [/node_modules/],
          use: ['style-loader', 'css-loader', 'sass-loader']
      }
    ]
  }
}
