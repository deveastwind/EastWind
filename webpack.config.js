const path = require('path');
const webpack = require('webpack');

module.exports = {
  entry: {
    'admin': [
      path.resolve(__dirname, 'src/js/admin.js')
    ],
    'client': [
      path.resolve(__dirname, 'src/js/client.js')
    ],    
  },
  devServer:{
    contentBase: './dist'
  },
  output: {
    filename: '[name].js',
    path: path.resolve(__dirname, 'dist'),
    publicPath: '/',
  },
  module: {
    rules: [{
           test: /\.css$/, // To load the css in react
           loaders: ['style-loader', 'css-loader'],
           include: /node_modules/
        }, {
           test: /\.jsx?$/, // To load the js and jsx files
           loader: 'babel-loader',
           exclude: /node_modules/,
           query: {
              presets: ['es2015', 'react', 'stage-2']
           }
        }]  
  },
  resolve: {
    extensions: ['.js', '.jsx'],
  },
  plugins: [
    new webpack.DefinePlugin({
        'process.env': {
            NODE_ENV: JSON.stringify('development')
        }
    })
  ]
};
