const FTPDeploy=require("ftp-deploy");
const path=require("path");
const cnf=require("./deploy.config.js");

var ftpDeploy=new FTPDeploy;

var config={
    user: cnf.user,
    password: cnf.password,
    host: cnf.host,
    port: cnf.port,
    localRoot: path.resolve(__dirname, "dist"),
    remoteRoot: cnf.remote,
    include: ["**/*", "**/.*"],
    forcePasv: true
}

ftpDeploy.on("uploading", (data) => {
    console.log("Uploaded: "+data.transferredFileCount+"/"+data.totalFilesCount+" Current: "+data.filename);
});

ftpDeploy.deploy(config).then((res) => console.log("Finished")).catch((e) => console.log("Error: ", e));