const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const path = require('path');
const devMode = process.env.NODE_ENV !== 'production'

module.exports = {
  context: "/var/www/app",
  entry: {
    'app': [
        path.resolve(__dirname, 'assets/js', 'app.js')
    ],
    'helloworld': [
        path.resolve(__dirname, 'assets/js', 'helloworld.js')
    ],
    'helloworld-ES2015': [
        path.resolve(__dirname, 'assets/js', 'helloworld-ES2015.js')
    ]
  },
  output: {
      path: path.resolve(__dirname, 'public/build'),
      filename: "[name].min.js",
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
        filename: "[name].min.css"
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
        test: /\.s?[ac]ss$/,
        use: [
            MiniCssExtractPlugin.loader,
            { loader: 'css-loader', options: { url: false, sourceMap: true } },
            { loader: 'sass-loader', options: { sourceMap: true } }
        ]
      }
    ]
  }
}
