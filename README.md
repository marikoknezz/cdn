# CDN
It is tested with VSHosting CDN. Cache lifetime control is not set in this bundle, VSH CDN can override it.

It is recommended set `Autoconversion to WebP` to `ON` in VSH CDN settings. 

## Installation

**I. Copy lines below to your `composer.json` to be able to download this package**
```diff
 ...
+ "repositories": [
+     {
+         "type": "vcs",
+         "url": "https://gitlab+deploy-token-23:r3JFL4v-MPmhxEmzu3ZL@gitlab.shopsys.cz/ss6-projects/cdn.git"
+     }
+ ],
 "require": {
 ...
```

**II. Install package `composer require shopsys/cdn "^1.0.0"`**

**III. Bundle registration**
```patch
--- a/config/bundles.php
+++ b/config/bundles.php
@@ -41,4 +41,5 @@ return [
+    Shopsys\Cdn\CdnBundle::class => ['all' => true],
``` 

**IV. Allow CDN for assets in Symfony**
```patch
--- a/config/packages/framework.yaml
+++ b/config/packages/framework.yaml
@@ -19,5 +19,8 @@ framework:
-    assets: ~
+    assets:
+        base_urls:
+            # When you do not want to use CDN, it is used value '//' as workaround by https://github.com/symfony/symfony/issues/28391
+            - '%env(CDN_DOMAIN)%'
``` 

**V. Add parameter with CDN domain**
```patch
--- a/config/parameters.yaml.dist
+++ b/config/parameters.yaml.dist
@@ -95,3 +95,6 @@ parameters:
+
+    # When you do not want to use CDN, it is used value '//' as workaround by https://github.com/symfony/symfony/issues/28391
+    env(CDN_DOMAIN): '//'
``` 

**VI. Add environment variable to project deployment**
```patch
--- a/deploy/deploy-project.sh
+++ b/deploy/deploy-project.sh
@@ -33,6 +33,7 @@ function deploy() {
+        ["CDN_DOMAIN"]=${CDN_DOMAIN}
``` 

**VII. Add patching script for kubernetes deployment**
```patch
--- a/deploy/deploy-project.sh
+++ b/deploy/deploy-project.sh
@@ -52,6 +53,7 @@ function deploy() {
 function merge() {
     source "${BASE_PATH}/vendor/devops/kubernetes-deployment/deploy/functions.sh"
     merge_configuration
+    source "${BASE_PATH}/vendor/shopsys/cdn/deploymentPatch/cdnPatch.sh"
 }
``` 

**VIII. Update local nginx configuration to testing on localhost**
```patch
--- a/docker/nginx/nginx.conf
+++ b/docker/nginx/nginx.conf
@@ -17,6 +17,8 @@ server {
         try_files @app @app;
     }
     location / {
+        add_header "Access-Control-Allow-Origin" "*";
+
         # try to serve existing files directly, fallback to @app
         try_files $uri @app;
 
@@ -49,6 +51,8 @@ server {
     }
 
     location @app {
+        add_header "Access-Control-Allow-Origin" "";
+
         fastcgi_pass php-upstream;
         include fastcgi_params;
         # use $realpath_root instead of $document_root
``` 

**IX. Add header for on-fly generated images (It is mandatory for using via CDN)**
```patch
--- a/src/Controller/Front/ImageController.php
+++ b/src/Controller/Front/ImageController.php
@@ -101,6 +101,7 @@ class ImageController extends FrontBaseController
             $headers = [
                 'content-type' => $this->filesystem->getMimetype($imageFilepath),
                 'content-size' => $this->filesystem->getSize($imageFilepath),
+                'Access-Control-Allow-Origin' => '*',
             ];
``` 

**X. Add CDN domain for assets in webpack**
```patch
--- a/webpack.config.js
+++ b/webpack.config.js
@@ -16,7 +16,7 @@ if (!Encore.isRuntimeEnvironmentConfigured()) {
-    .setPublicPath('/build')
+    .setPublicPath((process.env.CDN_DOMAIN ? process.env.CDN_DOMAIN : '') + '/build')
``` 

**XI. Create variable `CDN_DOMAIN` in GitlabCI with your CDN domain**

**XII. Whitelist IP addresses listed below**
> Follow tutorial for whitelisting IP addresses here: https://gitlab.shopsys.cz/devops/kubernetes-deployment/-/tree/master#whitelist-ip-addresses
```text
allow 93.185.110.99/32;
allow 93.185.110.100/32;
allow 93.185.110.101/32;
allow 185.198.191.147/32;
allow 204.145.66.226/32;
allow 77.81.119.26/32;
allow 86.105.155.150/32;
allow 185.115.0.0/24;
allow 77.247.124.1/32;
```

More information can be found here: https://support.vshosting.cz/content/31/33/cs/cdn-%C5%99esen%C3%AD.html

## Troubleshooting
When your project overrides some classes like this bundle, there is conflict and project extended classes have to extend bundle classes instead of framework classes.
This applies to the following classes:
* `Shopsys\FrameworkBundle\Component\Collector\ShopsysFrameworkDataCollector`
* `Shopsys\FrameworkBundle\Component\Domain\Domain`
* `Shopsys\FrameworkBundle\Component\Image\ImageFacade`
* `Shopsys\FrameworkBundle\Model\Localization\LocalizationListener`
* `Shopsys\FrameworkBundle\Twig\LocalizationExtension`
