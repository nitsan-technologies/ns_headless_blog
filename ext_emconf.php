<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "ns_headless_blog".
 *
 * Auto generated 20-03-2025 14:02
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
  'title' => 'ns_headless_blog – Easily Output TYPO3 Blog Content as JSON',
  'description' => 'ns_headless_blog TYPO3 extension helps you render blog content in clean JSON format for headless CMS integrations & modern frontend frameworks',
  'category' => 'plugin',
  'version' => '13.0.0',
  'state' => 'stable',
  'uploadfolder' => false,
  'clearcacheonload' => true,
  'author' => 'Team T3Planet',
  'author_email' => 'info@t3planet.de',
  'author_company' => 'T3Planet',
  'autoload' => [
    'psr-4' => [
      'NITSAN\\NsHeadlessBlog\\' => 'Classes'
    ]
  ],
  'constraints' => 
  array (
    'depends' => 
    array (
      'typo3' => '12.5.0-13.5.99',
      'frontend' => '12.0.0-13.5.99',
      'headless' => '4.0.0-4.5.9',
      'blog' => '12.0.0-13.5.99',
    ),
    'suggests' => 
    array (
      'headless' => '2.0.0-4.9.9',
    ),
    'conflicts' => 
    array (
    ),
  ),
);

