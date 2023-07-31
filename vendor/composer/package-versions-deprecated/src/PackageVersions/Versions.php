<?php

declare(strict_types=1);

namespace PackageVersions;

use Composer\InstalledVersions;
use OutOfBoundsException;

class_exists(InstalledVersions::class);

/**
 * This class is generated by composer/package-versions-deprecated, specifically by
 * @see \PackageVersions\Installer
 *
 * This file is overwritten at every run of `composer install` or `composer update`.
 *
 * @deprecated in favor of the Composer\InstalledVersions class provided by Composer 2. Require composer-runtime-api:^2 to ensure it is present.
 */
final class Versions
{
    /**
     * @deprecated please use {@see self::rootPackageName()} instead.
     *             This constant will be removed in version 2.0.0.
     */
    const ROOT_PACKAGE_NAME = 'loongdom/dberp';

    /**
     * Array of all available composer packages.
     * Dont read this array from your calling code, but use the \PackageVersions\Versions::getVersion() method instead.
     *
     * @var array<string, string>
     * @internal
     */
    const VERSIONS          = array (
  'brick/varexporter' => '0.3.7@3e263cd718d242594c52963760fee2059fd5833c',
  'composer/package-versions-deprecated' => '1.11.99.5@b4f54f74ef3453349c24a845d22392cd31e65f1d',
  'doctrine/annotations' => '1.13.2@5b668aef16090008790395c02c893b1ba13f7e08',
  'doctrine/cache' => '1.13.0@56cd022adb5514472cb144c087393c1821911d09',
  'doctrine/collections' => '1.6.8@1958a744696c6bb3bb0d28db2611dc11610e78af',
  'doctrine/common' => '3.3.0@c824e95d4c83b7102d8bc60595445a6f7d540f96',
  'doctrine/dbal' => '2.13.9@c480849ca3ad6706a39c970cdfe6888fa8a058b8',
  'doctrine/deprecations' => 'v1.0.0@0e2a4f1f8cdfc7a92ec3b01c9334898c806b30de',
  'doctrine/doctrine-laminas-hydrator' => '2.2.1@f35233ffdac1798a57be2f772ff8383e81ac3739',
  'doctrine/doctrine-module' => '4.4.2@8e47008bad04f1081f027eb6956f0a9440b152f6',
  'doctrine/doctrine-orm-module' => '3.2.2@45f700dc7af237ecef104f472e0ccf0f4bf0ed0b',
  'doctrine/event-manager' => '1.1.1@41370af6a30faa9dc0368c4a6814d596e81aba7f',
  'doctrine/inflector' => '2.0.4@8b7ff3e4b7de6b2c84da85637b59fd2880ecaa89',
  'doctrine/instantiator' => '1.4.1@10dcfce151b967d20fde1b34ae6640712c3891bc',
  'doctrine/lexer' => '1.2.3@c268e882d4dbdd85e36e4ad69e02dc284f89d229',
  'doctrine/orm' => '2.8.5@a6577b89a2b028b79550ef58d9f272debdd75da4',
  'doctrine/persistence' => '2.5.3@d7edf274b6d35ad82328e223439cc2bb2f92bd9e',
  'ezyang/htmlpurifier' => 'v4.14.0@12ab42bd6e742c70c0a52f7b82477fcd44e64b75',
  'laminas/laminas-authentication' => '2.11.0@51815691d862b82b749a4aa9c4f6ec078d579f74',
  'laminas/laminas-cache' => '2.13.2@fc3255833c1c687ed2c5312e9663ef062be155c9',
  'laminas/laminas-cache-storage-adapter-apc' => '1.0.1@8b375d994f6e67534f6ae6e995249e706faa30c1',
  'laminas/laminas-cache-storage-adapter-apcu' => '1.1.0@e182aab739d6b03992a9915cc3c7019391a94548',
  'laminas/laminas-cache-storage-adapter-blackhole' => '1.2.1@4af1053efd81785a292c2a9442871c075700345a',
  'laminas/laminas-cache-storage-adapter-dba' => '1.0.1@ad968d3d8a0350af8e6717be58bb96e5a9e77f3b',
  'laminas/laminas-cache-storage-adapter-ext-mongodb' => '1.2.0@72f68589cc8323fa688167a4720b795dd0907f4e',
  'laminas/laminas-cache-storage-adapter-filesystem' => '1.1.1@76fc488c3fa0ad442e4e70f807305c940d1bdcbc',
  'laminas/laminas-cache-storage-adapter-memcache' => '1.1.0@1d2a74e300a0fd0b8d0e0cb4e379a173ccad0088',
  'laminas/laminas-cache-storage-adapter-memcached' => '1.2.0@d05f33e43a352b85c6d0208e9cfbf2a59f02ede3',
  'laminas/laminas-cache-storage-adapter-memory' => '1.1.0@02c7a4a1118bbd47d1c0f0bfe1e8b140af79d2bd',
  'laminas/laminas-cache-storage-adapter-mongodb' => '1.0.1@ef4aa396b55533b8eb3e1d4126c39a78a22e49a6',
  'laminas/laminas-cache-storage-adapter-redis' => '1.2.0@de8a63d4a0ef1ccead401eb7fb6d75b57fa3f9ee',
  'laminas/laminas-cache-storage-adapter-session' => '1.1.0@74a275056cfca2300eb9a67cd1d917f7066b4113',
  'laminas/laminas-cache-storage-adapter-wincache' => '1.0.1@0f54599c5d9aff11b01adadd2742097f923170ba',
  'laminas/laminas-cache-storage-adapter-xcache' => '1.0.1@24049557aa796ec7527bcc8032ed68346232b219',
  'laminas/laminas-cache-storage-adapter-zend-server' => '1.0.1@8d0b0d219a048a92472d89a5e527990f3ea2decc',
  'laminas/laminas-captcha' => '2.12.0@b07e499a7df73795768aa89e0138757a7ddb9195',
  'laminas/laminas-code' => '3.5.1@b549b70c0bb6e935d497f84f750c82653326ac77',
  'laminas/laminas-component-installer' => '1.1.1@1fc6193b4984a476f050ac6bcbd64a3d47db7d1c',
  'laminas/laminas-config' => '3.7.0@e43d13dcfc273d4392812eb395ce636f73f34dfd',
  'laminas/laminas-console' => '2.8.0@478a6ceac3e31fb38d6314088abda8b239ee23a5',
  'laminas/laminas-crypt' => '3.8.0@0972bb907fd555c16e2a65309b66720acf2b8699',
  'laminas/laminas-db' => '2.15.0@1125ef2e55108bdfcc1f0030d3a0f9b895e09606',
  'laminas/laminas-development-mode' => '3.6.0@4f74da6f4b82e5060457cfb2fbd0ce452dfecd51',
  'laminas/laminas-di' => '2.6.1@239b22408a1f8eacda6fc2b838b5065c4cf1d88e',
  'laminas/laminas-escaper' => '2.10.0@58af67282db37d24e584a837a94ee55b9c7552be',
  'laminas/laminas-eventmanager' => '3.5.0@41f7209428f37cab9573365e361f4078209aaafa',
  'laminas/laminas-filter' => '2.14.0@98a126b8cd069a446054680c9be5f37a61f6dc17',
  'laminas/laminas-form' => '2.17.1@af231c26209fa0684af9e934e8ee5c085eb14cd0',
  'laminas/laminas-http' => '2.15.1@261f079c3dffcf6f123484db43c40e44c4bf1c79',
  'laminas/laminas-hydrator' => '4.3.1@cc5ea6b42d318dbac872d94e8dca2d3013a37ab5',
  'laminas/laminas-i18n' => '2.15.0@1654fcd6cd27c01a902b47fe71fa583ad227268c',
  'laminas/laminas-inputfilter' => '2.18.0@8c663d35926f8276b4bf1a2c571310eb285f80cb',
  'laminas/laminas-json' => '3.3.0@9a0ce9f330b7d11e70c4acb44d67e8c4f03f437f',
  'laminas/laminas-loader' => '2.8.0@d0589ec9dd48365fd95ad10d1c906efd7711c16b',
  'laminas/laminas-log' => '2.15.1@c46d9eb2ad226f9ed27ea3f5de4bbafa9b98368f',
  'laminas/laminas-math' => '3.5.0@146d8187ab247ae152e811a6704a953d43537381',
  'laminas/laminas-modulemanager' => '2.11.0@6acf5991d10b0b38a2edb08729ed48981b2a5dad',
  'laminas/laminas-mvc' => '3.3.3@7ff2bfbe64048aa83c6d1c7edcbab849123f0150',
  'laminas/laminas-mvc-console' => '1.3.0@90338c7b61a5fa8445c0a41925a4ae351459fa79',
  'laminas/laminas-mvc-form' => '1.2.0@9e03ded7e7605a5b1e34a2f187b14d7fd4f1e44f',
  'laminas/laminas-mvc-i18n' => '1.3.1@3f6c81d839507dee8bbf74a09a9bfc65ecd3bb88',
  'laminas/laminas-mvc-plugin-fileprg' => '1.2.0@2d6a64bf916b3f5f26a062b9c62d06af26ee483f',
  'laminas/laminas-mvc-plugin-flashmessenger' => '1.8.0@86bb1c654e5fafaf34d4deaa7dc9af721fbcb42d',
  'laminas/laminas-mvc-plugin-identity' => '1.3.0@d22e7fb74f0395828df5cd42ed55d23a98569ed1',
  'laminas/laminas-mvc-plugin-prg' => '1.5.0@f35eb80cbe8c0e1a5d8966e3b0a24fbaeb902f56',
  'laminas/laminas-mvc-plugins' => '1.2.0@ea91854e410fcf0451c8bc53062da215605cf5ad',
  'laminas/laminas-paginator' => '2.12.2@e2e5a17e2b6ca750e4a75b8f34763c63cc6bf8fa',
  'laminas/laminas-permissions-rbac' => '3.2.0@ce117f1d2fb8ec8ec6186633bf485a89149fe46f',
  'laminas/laminas-recaptcha' => '3.4.0@f3bdb2fcaf859b9f725f397dc1bc38b4a7696a71',
  'laminas/laminas-router' => '3.5.0@44759e71620030c93d99e40b394fe9fff8f0beda',
  'laminas/laminas-serializer' => '2.13.0@aa72a694d79f01ef1252b276ca9930158c3b877d',
  'laminas/laminas-servicemanager' => '3.13.0@6f96556ee314f9e0d57d83967c0087332836c31d',
  'laminas/laminas-servicemanager-di' => '1.2.1@abb2409f9dbf1b7c88f5dbe06bac726daa7c0325',
  'laminas/laminas-session' => '2.12.1@888c6a344e9a4c9f34ab6e09346640eac9be3fcf',
  'laminas/laminas-stdlib' => '3.10.1@0d669074845fc80a99add0f64025192f143ef836',
  'laminas/laminas-text' => '2.9.0@8879e75d03e09b0d6787e6680cfa255afd4645a7',
  'laminas/laminas-uri' => '2.9.1@7e837dc15c8fd3949df7d1213246fd7c8640032b',
  'laminas/laminas-validator' => '2.20.0@ba665f5a52763dda5a747c4ad826d2adf1510486',
  'laminas/laminas-view' => '2.20.0@2cd6973a3e042be3d244260fe93f435668f5c2b4',
  'laminas/laminas-zendframework-bridge' => '1.5.0@7f049390b756d34ba5940a8fb47634fbb51f79ab',
  'maennchen/zipstream-php' => '2.2.1@211e9ba1530ea5260b45d90c9ea252f56ec52729',
  'markbaker/complex' => '3.0.1@ab8bc271e404909db09ff2d5ffa1e538085c0f22',
  'markbaker/matrix' => '3.0.0@c66aefcafb4f6c269510e9ac46b82619a904c576',
  'myclabs/php-enum' => '1.8.3@b942d263c641ddb5190929ff840c68f78713e937',
  'nesbot/carbon' => '2.59.1@a9000603ea337c8df16cc41f8b6be95a65f4d0f5',
  'nikic/php-parser' => 'v4.14.0@34bea19b6e03d8153165d8f30bba4c3be86184c1',
  'phpoffice/phpspreadsheet' => '1.23.0@21e4cf62699eebf007db28775f7d1554e612ed9e',
  'psr/cache' => '1.0.1@d11b50ad223250cf17b86e38383413f5a6764bf8',
  'psr/container' => '1.1.2@513e0666f7216c7459170d56df27dfcefe1689ea',
  'psr/http-client' => '1.0.1@2dfb5f6c5eff0e91e20e913f8c5452ed95b86621',
  'psr/http-factory' => '1.0.1@12ac7fcd07e5b077433f5f2bee95b3a771bf61be',
  'psr/http-message' => '1.0.1@f6561bf28d520154e4b0ec72be95418abe6d9363',
  'psr/log' => '1.1.4@d49695b909c3b7628b6289db5479a1c204601f11',
  'psr/simple-cache' => '1.0.1@408d5eafb83c57f6365a3ca330ff23aa4a5fa39b',
  'symfony/console' => 'v5.4.10@4d671ab4ddac94ee439ea73649c69d9d200b5000',
  'symfony/deprecation-contracts' => 'v2.5.2@e8b495ea28c1d97b5e0c121748d6f9b53d075c66',
  'symfony/polyfill-ctype' => 'v1.26.0@6fd1b9a79f6e3cf65f9e679b23af304cd9e010d4',
  'symfony/polyfill-intl-grapheme' => 'v1.26.0@433d05519ce6990bf3530fba6957499d327395c2',
  'symfony/polyfill-intl-normalizer' => 'v1.26.0@219aa369ceff116e673852dce47c3a41794c14bd',
  'symfony/polyfill-mbstring' => 'v1.26.0@9344f9cb97f3b19424af1a21a3b0e75b0a7d8d7e',
  'symfony/polyfill-php73' => 'v1.26.0@e440d35fa0286f77fb45b79a03fedbeda9307e85',
  'symfony/polyfill-php80' => 'v1.26.0@cfa0ae98841b9e461207c13ab093d76b0fa7bace',
  'symfony/service-contracts' => 'v2.5.2@4b426aac47d6427cc1a1d0f7e2ac724627f5966c',
  'symfony/string' => 'v5.4.10@4432bc7df82a554b3e413a8570ce2fea90e94097',
  'symfony/translation' => 'v5.4.9@1639abc1177d26bcd4320e535e664cef067ab0ca',
  'symfony/translation-contracts' => 'v2.5.2@136b19dd05cdf0709db6537d058bcab6dd6e2dbe',
  'webimpress/safe-writer' => '2.2.0@9d37cc8bee20f7cb2f58f6e23e05097eab5072e6',
  'webmozart/assert' => '1.11.0@11cb2199493b2f8a3b53e7f19068fc6aac760991',
  'laminas/laminas-developer-tools' => '2.3.0@ac5df5672a1c2d218b26e85e59bd94312bd37bda',
  'symfony/var-dumper' => 'v5.4.9@af52239a330fafd192c773795520dc2dd62b5657',
  'loongdom/dberp' => 'dev-master@42755ebf540030a02d37ba93d6bfe4ed442707d1',
);

    private function __construct()
    {
    }

    /**
     * @psalm-pure
     *
     * @psalm-suppress ImpureMethodCall we know that {@see InstalledVersions} interaction does not
     *                                  cause any side effects here.
     */
    public static function rootPackageName() : string
    {
        if (!self::composer2ApiUsable()) {
            return self::ROOT_PACKAGE_NAME;
        }

        return InstalledVersions::getRootPackage()['name'];
    }

    /**
     * @throws OutOfBoundsException If a version cannot be located.
     *
     * @psalm-param key-of<self::VERSIONS> $packageName
     * @psalm-pure
     *
     * @psalm-suppress ImpureMethodCall we know that {@see InstalledVersions} interaction does not
     *                                  cause any side effects here.
     */
    public static function getVersion(string $packageName): string
    {
        if (self::composer2ApiUsable()) {
            return InstalledVersions::getPrettyVersion($packageName)
                . '@' . InstalledVersions::getReference($packageName);
        }

        if (isset(self::VERSIONS[$packageName])) {
            return self::VERSIONS[$packageName];
        }

        throw new OutOfBoundsException(
            'Required package "' . $packageName . '" is not installed: check your ./vendor/composer/installed.json and/or ./composer.lock files'
        );
    }

    private static function composer2ApiUsable(): bool
    {
        if (!class_exists(InstalledVersions::class, false)) {
            return false;
        }

        if (method_exists(InstalledVersions::class, 'getAllRawData')) {
            $rawData = InstalledVersions::getAllRawData();
            if (count($rawData) === 1 && count($rawData[0]) === 0) {
                return false;
            }
        } else {
            $rawData = InstalledVersions::getRawData();
            if ($rawData === null || $rawData === []) {
                return false;
            }
        }

        return true;
    }
}
