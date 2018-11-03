const path=require("path");
const CopyWebpackPlugin=require("copy-webpack-plugin");

module.exports={
    mode: process.env.NODE_ENV || "development",
    entry: "./src/script/main.js",
    output: {
        path: path.resolve(__dirname, "dist"),
        filename: "script/bundle.js",
        libraryTarget: "var",
        library: "ui"
    },
    module: {
        rules: [
            {
                test: /\.scss$/,
                use: [
                    "style-loader",
                    "css-loader",
                    "sass-loader"
                ]
            },
            {
                test: /\.html$/,
                use: [
                    "raw-loader"
                ]
            }
        ]
    },
    plugins: [
        new CopyWebpackPlugin([
            {
                from: "src",
                to: ".",
                ignore: [
                    "script/*",
                    "style/*"
                ]
            }
        ])
    ]
};