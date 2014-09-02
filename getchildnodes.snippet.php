<?php
/**
 * GetChildNodes
 *
 * DESCRIPTION
 *
 * This Snippet allows you to get comma separated child list
 *
 * PROPERTIES:
 *
 * &depth       	string  optional    Depth for child nodes search. 1 is default value
 * &templates       string  optional    Comma separated list of resources templates for which will be listed IDs
 * &parent          string  optional    An ID of a resource that will be used as root parent. If no ID is given, current Resource ID will be used
 * &context	        string  optional    If set, will display only tags for resources in given context. If no context is given, "web" context will be used
 * &toPlaceholder   string  optional    If set, output will return in placeholder with given name
 *
 * USAGE:
 *
 * [[!GetChildNodes? &depth=`5` &templates=`1,4` &parents=`1` &context=`web`]]
 *
 */

$currentResource = $modx->resource;
$resID = $modx->resource->get('id');
$depth = (int) $modx->getOption('depth', $scriptProperties, '1');
$templates = $modx->getOption('templates', $scriptProperties, '');
$parent =  $modx->getOption('parent', $scriptProperties, $resID);
$context = $modx->getOption('context', $scriptProperties, 'web');
$toPlaceholder = $modx->getOption('toPlaceholder', $scriptProperties, '');

$ids = $modx->getChildIds($parent, $depth, array('context' => $context));

//preparing templates list
$templates = explode(',', $templates);            // Explode fields to array
$templates = array_map('trim', $templates);       // Trim array's values
$templates = array_keys(array_flip($templates));  // Remove duplicate fields
$templates = array_filter($templates);            // Remove empty values from array


$query = $modx->newQuery('modResource');
$query->select('id');
$query->where(array(
   'id:IN' => $ids,
   'isfolder' => 0
));

if ($templates) {
    $query->where(array(
        'template:IN' => $templates
    ));
}
$docs = $modx->getCollection('modResource',$query);
$output = array();
foreach ($docs as $doc) {
    $output[] = $doc->get('id');
}
$output = implode(',',$output);
if (!empty($toPlaceholder)) {
    $modx->setPlaceholder($toPlaceholder, $output);
    return '';
}
return $output;
