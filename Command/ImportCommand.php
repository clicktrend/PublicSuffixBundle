<?php

/*
 * Author: Kadir Yücel, Clicktrend Media GmbH, Germany
 * www.clicktrend-media.com
 */

namespace Clicktrend\PublicSuffixBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ImportCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
                ->setName('publicsuffix:update')
                ->setDescription('Import/Update publicsuffix TLD list.')
        //->addArgument('account', InputArgument::REQUIRED, 'What is your account name?')
        //->addOption('env', null, InputOption::VALUE_NONE, 'Environment')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $cmd = 'php '.$this->getContainer()->get('kernel')->getRootDir().'/../vendor/registered-domains-php/generateEffectiveTLDs.php';
        $process = new Process($cmd);
        $process->setTimeout(3600);
        $process->run();
        if(!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        $file = __DIR__.'/../Resources/config/tlds.xml';
        $fileTmp = tempnam("/tmp", "effectiveTLDs");

        if(false === @file_put_contents($fileTmp, $process->getOutput())) {
            throw new \RuntimeException(sprintf('Unable to write "%s"', $file));
        }
        
        $code = include_once $fileTmp;
        
        unlink($fileTmp);
        
        $doc = new \DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = true;
        
        $tlds = $doc->createElement("parameter");
        $tlds->setAttribute('key', 'publicsuffix.tlds');
        $tlds->setAttribute('type', 'collection');
        
        foreach($tldTree as $key => $tld) {
            $param = $doc->createElement("parameter");
            $param->setAttribute('key', $key);
            $param->setAttribute('type', 'collection');
            
            $tlds->appendChild($this->arrayToXml($doc, $param, $tld));
        }


        $params = $doc->createElement("parameters");
        $params->appendChild($tlds);
        
        $container = $doc->createElement("container");
        $container->setAttribute("xmlns", "http://symfony.com/schema/dic/services");
        $container->setAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
        $container->setAttribute("xsi:schemaLocation", "http://symfony.com/schema/dic/services/services-1.0.xsd");
        
        $container->appendChild($params);
        
        $doc->appendChild($container);
        
        if(false === @file_put_contents($file, $doc->saveXML())) {
            throw new \RuntimeException(sprintf('Unable to write "%s"', $file));
        }

        $output->writeln('<info>TLD list successfully updated.</info>');
    }

    private function arrayToXml(\DOMDocument $doc, \DOMElement $col, $array) {
        foreach($array as $key => $val) {
        
            $param = $doc->createElement("parameter");
            $param->setAttribute('key', $key);
            
            if(is_array($val)) {
                
                $param->setAttribute('type', 'collection');
                $col->appendChild($this->arrayToXml($doc, $param, $val));
            } else {
                $param->appendChild($doc->createTextNode($val));
                $col->appendChild($param);
            }    
        }
        
        return $col;
    }

}

