<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Clicktrend\PublicSuffixBundle\PublicSuffix;

class PublicSuffix {
    
    protected $tldTree;
    
    protected $url;
    protected $host;
    protected $domain;
    protected $subdomain;

    function __construct($tldTree) {
        $this->tldTree = $tldTree;
    }
    
    public function parseURL($url) {
        $this->url = $url;
        $parts = parse_url($url);
        $this->host = $parts['host'];
        $this->domain = $this->getRegisteredDomain($this->host);
        $this->subdomain = substr($this->host, 0, strlen($this->host) - strlen($this->domain) - 1);
        $this->tld = substr($this->domain, strpos($this->domain, '.') + 1);
        
    }
    
    protected function getRegisteredDomain($signingDomain = NULL) {
        if(!function_exists('getRegisteredDomain'))
            throw new \RuntimeException('PublicSuffix lib not found. Check your configuration ');
        else if($signingDomain == NULL) 
            throw new \RuntimeException('Parse url before.');
        else
            return \getRegisteredDomain($signingDomain, $this->tldTree);
    }
    
    public function getHost() {
        return $this->host;
    }
    
    public function getUrl() {
        return $this->url;
    }
    
    public function getDomain() {
        return $this->domain;
    }
    
    public function getSubdomain() {
        return $this->subdomain;
    }
    
    public function getTld() {
        return $this->tld;
    }  
}