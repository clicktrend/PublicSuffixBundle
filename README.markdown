Get registered domain from the Public Suffix List.

From country to country domain tld changes. There is no algorithm
which can detect domain names. PublicSuffixBundle grabs the registered
domain name from an valid URL depending on the registered domains list at
publicsuffix.org.

An excerpt from publicsuffix.org:

    A "public suffix" is one under which Internet users can directly register 
    names. Some examples of public suffixes are .com, .co.uk and pvt.k12.wy.us. 
    The Public Suffix List is a list of all known public suffixes.

    The Public Suffix List is an initiative of Mozilla, but is maintained as 
    a community resource. It is available for use in any software, but was 
    originally created to meet the needs of browser manufacturers.

This Bundle depends on the php library of dkim-reputation.org. It's uploaded
to Github by `Leth`

Installation
============

  1. Add this bundle to your prject's deps:

          [registered-domains-php]
              git=https://github.com/leth/registered-domains-php.git
              target=/registered-domains-php
          
          [ClicktrendPublicSuffixBundle]
              git=git://github.com/clicktrend/PublicSuffixBundle.git
              target=/bundles/Clicktrend/ClicktrendPublicSuffixBundle
              

  2. Add `PublicSuffixBundle` namespace to your autoloader:

          // app/autoload.php
          $loader->registerNamespaces(array(
             'Clicktrend\PublicSuffixBundle' => __DIR__.'/../vendor/bundles',
             // your other namespaces
          );

  3. Add this bundle to your application kernel:

          // app/AppKernel.php
          public function registerBundles()
          {
              return array(
                  // ...
                  new Clicktrend\PublicSuffixBundle\ClicktrendPublicSuffixBundle(),
                  // ...
              );
          }

  4. Install vendors:

          $ php bin/vendors install

Usage
=====

Download and update public suffix list:

          $ php app/console publicsuffix:update

Get service and parse URL:

          //TestCommand.php 
          $ps = $this->getContainer()->get('publicsuffix');
          $ps->parseURL($validUrl);
          
          print $ps->getHost();
          print $ps->getDomain();
          print $ps->getSubdomain();


