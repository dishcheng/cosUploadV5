<?php











namespace Composer;

use Composer\Autoload\ClassLoader;
use Composer\Semver\VersionParser;






class InstalledVersions
{
private static $installed = array (
  'root' => 
  array (
    'pretty_version' => 'dev-master',
    'version' => 'dev-master',
    'aliases' => 
    array (
    ),
    'reference' => '59111c7bc68488f32f40a672bb9b39db441c9feb',
    'name' => 'dishcheng/cos-upload-v5',
  ),
  'versions' => 
  array (
    'dishcheng/cos-upload-v5' => 
    array (
      'pretty_version' => 'dev-master',
      'version' => 'dev-master',
      'aliases' => 
      array (
      ),
      'reference' => '59111c7bc68488f32f40a672bb9b39db441c9feb',
    ),
    'guzzlehttp/command' => 
    array (
      'pretty_version' => '1.2.2',
      'version' => '1.2.2.0',
      'aliases' => 
      array (
      ),
      'reference' => '7883359e0ecab8a8f7c43aad2fc36360a35d21e8',
    ),
    'guzzlehttp/guzzle' => 
    array (
      'pretty_version' => '7.4.5',
      'version' => '7.4.5.0',
      'aliases' => 
      array (
      ),
      'reference' => '1dd98b0564cb3f6bd16ce683cb755f94c10fbd82',
    ),
    'guzzlehttp/guzzle-services' => 
    array (
      'pretty_version' => '1.3.2',
      'version' => '1.3.2.0',
      'aliases' => 
      array (
      ),
      'reference' => '4989d902dd4e0411b320e851c46f3c94d652d891',
    ),
    'guzzlehttp/promises' => 
    array (
      'pretty_version' => '1.5.1',
      'version' => '1.5.1.0',
      'aliases' => 
      array (
      ),
      'reference' => 'fe752aedc9fd8fcca3fe7ad05d419d32998a06da',
    ),
    'guzzlehttp/psr7' => 
    array (
      'pretty_version' => '2.4.0',
      'version' => '2.4.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '13388f00956b1503577598873fffb5ae994b5737',
    ),
    'guzzlehttp/uri-template' => 
    array (
      'pretty_version' => 'v1.0.1',
      'version' => '1.0.1.0',
      'aliases' => 
      array (
      ),
      'reference' => 'b945d74a55a25a949158444f09ec0d3c120d69e2',
    ),
    'psr/http-client' => 
    array (
      'pretty_version' => '1.0.1',
      'version' => '1.0.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '2dfb5f6c5eff0e91e20e913f8c5452ed95b86621',
    ),
    'psr/http-client-implementation' => 
    array (
      'provided' => 
      array (
        0 => '1.0',
      ),
    ),
    'psr/http-factory' => 
    array (
      'pretty_version' => '1.0.1',
      'version' => '1.0.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '12ac7fcd07e5b077433f5f2bee95b3a771bf61be',
    ),
    'psr/http-factory-implementation' => 
    array (
      'provided' => 
      array (
        0 => '1.0',
      ),
    ),
    'psr/http-message' => 
    array (
      'pretty_version' => '1.0.1',
      'version' => '1.0.1.0',
      'aliases' => 
      array (
      ),
      'reference' => 'f6561bf28d520154e4b0ec72be95418abe6d9363',
    ),
    'psr/http-message-implementation' => 
    array (
      'provided' => 
      array (
        0 => '1.0',
      ),
    ),
    'qcloud/cos-sdk-v5' => 
    array (
      'pretty_version' => 'v2.5.6',
      'version' => '2.5.6.0',
      'aliases' => 
      array (
      ),
      'reference' => '607ee49d372a799964206b6ae0a9eb2816201c42',
    ),
    'ralouphie/getallheaders' => 
    array (
      'pretty_version' => '3.0.3',
      'version' => '3.0.3.0',
      'aliases' => 
      array (
      ),
      'reference' => '120b605dfeb996808c31b6477290a714d356e822',
    ),
    'symfony/deprecation-contracts' => 
    array (
      'pretty_version' => 'v2.5.2',
      'version' => '2.5.2.0',
      'aliases' => 
      array (
      ),
      'reference' => 'e8b495ea28c1d97b5e0c121748d6f9b53d075c66',
    ),
    'symfony/polyfill-php80' => 
    array (
      'pretty_version' => 'v1.26.0',
      'version' => '1.26.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'cfa0ae98841b9e461207c13ab093d76b0fa7bace',
    ),
  ),
);
private static $canGetVendors;
private static $installedByVendor = array();







public static function getInstalledPackages()
{
$packages = array();
foreach (self::getInstalled() as $installed) {
$packages[] = array_keys($installed['versions']);
}


if (1 === \count($packages)) {
return $packages[0];
}

return array_keys(array_flip(\call_user_func_array('array_merge', $packages)));
}









public static function isInstalled($packageName)
{
foreach (self::getInstalled() as $installed) {
if (isset($installed['versions'][$packageName])) {
return true;
}
}

return false;
}














public static function satisfies(VersionParser $parser, $packageName, $constraint)
{
$constraint = $parser->parseConstraints($constraint);
$provided = $parser->parseConstraints(self::getVersionRanges($packageName));

return $provided->matches($constraint);
}










public static function getVersionRanges($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

$ranges = array();
if (isset($installed['versions'][$packageName]['pretty_version'])) {
$ranges[] = $installed['versions'][$packageName]['pretty_version'];
}
if (array_key_exists('aliases', $installed['versions'][$packageName])) {
$ranges = array_merge($ranges, $installed['versions'][$packageName]['aliases']);
}
if (array_key_exists('replaced', $installed['versions'][$packageName])) {
$ranges = array_merge($ranges, $installed['versions'][$packageName]['replaced']);
}
if (array_key_exists('provided', $installed['versions'][$packageName])) {
$ranges = array_merge($ranges, $installed['versions'][$packageName]['provided']);
}

return implode(' || ', $ranges);
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getVersion($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

if (!isset($installed['versions'][$packageName]['version'])) {
return null;
}

return $installed['versions'][$packageName]['version'];
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getPrettyVersion($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

if (!isset($installed['versions'][$packageName]['pretty_version'])) {
return null;
}

return $installed['versions'][$packageName]['pretty_version'];
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getReference($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

if (!isset($installed['versions'][$packageName]['reference'])) {
return null;
}

return $installed['versions'][$packageName]['reference'];
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getRootPackage()
{
$installed = self::getInstalled();

return $installed[0]['root'];
}







public static function getRawData()
{
return self::$installed;
}



















public static function reload($data)
{
self::$installed = $data;
self::$installedByVendor = array();
}




private static function getInstalled()
{
if (null === self::$canGetVendors) {
self::$canGetVendors = method_exists('Composer\Autoload\ClassLoader', 'getRegisteredLoaders');
}

$installed = array();

if (self::$canGetVendors) {
foreach (ClassLoader::getRegisteredLoaders() as $vendorDir => $loader) {
if (isset(self::$installedByVendor[$vendorDir])) {
$installed[] = self::$installedByVendor[$vendorDir];
} elseif (is_file($vendorDir.'/composer/installed.php')) {
$installed[] = self::$installedByVendor[$vendorDir] = require $vendorDir.'/composer/installed.php';
}
}
}

$installed[] = self::$installed;

return $installed;
}
}
