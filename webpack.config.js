const webpack = require("webpack");
const path = require("path");
const dotenv = require("dotenv");
// Init Config Webpack
// Css extraction and minification
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin");

// Clean out build dir in-between builds
const { CleanWebpackPlugin } = require("clean-webpack-plugin");

// Define plugin Path
const destChildTheme = "./";

// Define Work path
const destFileCss = destChildTheme + "/assets/web/sass/app.scss";
const destAdminFileCss = destChildTheme + "/assets/admin/sass/app.scss";
const destAdminFileJs = destChildTheme + "/assets/admin/js/index.js";
const destFileJs = destChildTheme + "/assets/web/js/index.js";
const destOutput = destChildTheme + "/assets/dist";

module.exports = (env, argv) => {
  const mode = argv.mode || "development";
  const envPath = path.resolve(__dirname, `.env.${mode}`);
  const envVars = dotenv.config({ path: envPath }).parsed || {};

  const envKeys = Object.keys(envVars).reduce((acc, key) => {
    acc[`process.env.${key}`] = JSON.stringify(envVars[key]);
    return acc;
  }, {});
  return {
    mode,
    stats: "minimal",
    entry: {
      web: [destFileCss, destFileJs],
      admin: [destAdminFileCss, destAdminFileJs],
    },
    output: {
      filename: destOutput + "/js/[name].min.js",
      path: path.resolve(__dirname),
    },
    module: {
      rules: [
        // js babelization
        {
          test: /\.(js|jsx)$/,
          exclude: /node_modules/,
          loader: "babel-loader",
        },
        // sass compilation
        {
          test: /\.(sass|scss)$/,
          use: [
            MiniCssExtractPlugin.loader,
            {
              loader: "css-loader",
              options: { url: false },
            },
            {
              loader: "sass-loader",
              options: {
                sourceMap: true,
                sassOptions: {
                  outputStyle: "compressed",
                },
              },
            },
          ],
        },
        // Font files
        {
          test: /\.(woff|woff2|ttf|otf)$/,
          loader: "file-loader",
          include: path.resolve(__dirname, "../"),

          options: {
            name: "[hash].[ext]",
            outputPath: "fonts/",
          },
        },
        // loader for images and icons (only required if css references image files)
        {
          test: /\.(png|jpg|gif)$/,
          type: "asset/resource",
          generator: {
            filename: destOutput + "/build/img/[name][ext]",
          },
        },
        //load svg
        {
          test: /\.svg$/,
          use: ["@svgr/webpack"],
          issuer: {
            and: [/\.(ts|tsx|js|jsx|md|mdx)$/],
          },
        },
        //stype loader
        {
          test: /\.css$/i,
          use: ["style-loader", "css-loader"],
        },
        {
          test: /\.yaml$/,
          use: [
            { loader: "json-loader" },
            { loader: "yaml-loader", options: { asJSON: true } },
          ],
        },
      ],
    },
    // externals: {
    //   react: "React",
    // },
    plugins: [
      // Get ENV Variables
      new webpack.DefinePlugin(envKeys),

      // clear out build directories on each build
      new CleanWebpackPlugin({
        cleanOnceBeforeBuildPatterns: [
          destOutput + "/css/*",
          destOutput + "/js/*",
        ],
      }),
      // css extraction into dedicated file
      new MiniCssExtractPlugin({
        filename: destOutput + "/css/[name].min.css",
      }),
      new webpack.ProvidePlugin({
        $: "jquery",
        jQuery: "jquery",
      }),
    ],
    optimization: {
      // minification - only performed when mode = production
      minimizer: [
        // js minification - special syntax enabling webpack 5 default terser-webpack-plugin
        `...`,
        // css minification
        new CssMinimizerPlugin(),
      ],
    },
  };
};
