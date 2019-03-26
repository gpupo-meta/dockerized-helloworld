const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const path = require('path');
const devMode = process.env.NODE_ENV !== 'production'

module.exports = {
  context: "/var/www/app",
  entry: {
    'js/app.js': [
        path.resolve(__dirname, 'assets/js', 'app.js')
    ],
    'js/helloworld.js': [
        path.resolve(__dirname, 'assets/js', 'helloworld.js')
    ],
    'js/helloworld-ES2015.js': [
        path.resolve(__dirname, 'assets/js', 'helloworld-ES2015.js')
    ]
    ,
    'css/app.css': [
        path.resolve(__dirname, 'assets/scss', 'app.scss')
    ]
  },
  output: {
      path: "/var/www/app/public/build",
      filename: "[name]",
      publicPath: "/build/",
      pathinfo: false
  },
  resolve: {
       extensions: [
         ".js",
         ".jsx",
         ".vue",
         ".ts",
         ".tsx"
       ]
  },

  plugins: [
    new MiniCssExtractPlugin({
      // Options similar to the same options in webpackOptions.output
      // both options are optional
      filename: "[name].css",
      chunkFilename: "[id].css"
    })
  ],

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
        test: /\.(sc|c)ss$/,
        use: [
          MiniCssExtractPlugin.loader,
          'css-loader',
          {
            "loader": "postcss-loader",
            "options": {
              "sourceMap": false,
              "config": {
                "path": "config/postcss.config.js"
              }
            }
          },
          'sass-loader'
        ],
      }
    ]
  }
}
