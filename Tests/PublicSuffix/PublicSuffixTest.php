<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Clicktrend\PublicSuffixBundle\Tests\PublicSuffix;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PublicSuffixTest extends WebTestCase
{
    protected $container;

    protected function setUp() {
        $this->container = static::createClient()->getContainer();
    }
    
    public function testDomain()
    {
        $ps = $this->container->get('publicsuffix');
        $ps->parseURL('http://www.test.domain.org.uk:80/index.html');
        
        $this->assertEquals('www.test.domain.org.uk', $ps->getHost());
        $this->assertEquals('domain.org.uk', $ps->getDomain());
        $this->assertEquals('www.test', $ps->getSubdomain());
    }
}