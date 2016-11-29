<?php
 /*
  * This program is free software: you can redistribute it and/or modify
  * it under the terms of the GNU General Public License as published by
  * the Free Software Foundation, either version 3 of the License, or
  * (at your option) any later version.
  *
  * This program is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  * GNU General Public License for more details.
  *
  * You should have received a copy of the GNU General Public License
  * along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

//Spider a domain passed as an argument
 function crawlSite($URL){
  if ($URL){ 
    preg_match("/(https?:\/\/)?(www\.)?([^\.]+\.[^\/]+)/i", $URL, $matches);
    //$matches[3] holds the root domain without protocols or sub domains
    $URL = $matches[3];

    //Add root to known pages
    //$knownPages[0] = array(unvisited pages)
    //$knownPages[1] = array(visited pages)
    $knownPages[0][]= "/";

    //And, go...
    while(!empty($knownPages[0])){
      getLinkedURLs($URL, $knownPages, $siteStructureArray);
    }
    return $siteStructureArray;  
  }
}

// Find links to URLs on the same domain as $URL in $URL source
function getLinkedURLs($URL, &$knownPages, &$siteStructureArray){
  // Escape .'s
  $regexSafeURL = preg_replace('/\./', '\\\.', $URL);
  $thisPage = array_shift($knownPages[0]);  
  preg_match_all("/a href\s*=\s*\"(.*" . $regexSafeURL . ")?(\/[^\"\s\<]*)/i", curl_get_contents($URL . $thisPage), $matches);
  $knownPages[1][]= $thisPage;

  // $matches[2] contains our linked pages from $URL
  foreach($matches[2] as $linkedPage){
    // Store linked pages
    $linkArray[$linkedPage]++;
    // Add new pages to known pages, so we know to crawl it later
    if(!in_array($linkedPage, $knownPages[0])
    && !in_array($linkedPage, $knownPages[1])){
      $knownPages[0][] = $linkedPage;
    }
  }
  //Store all node info
  $siteStructureArray[$thisPage] = $linkArray;
}

//Return URL source contents as a string
function curl_get_contents($URL){
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_URL, $URL);

  $data = curl_exec($ch);

  curl_close($ch);

  return $data;
}

//Returns a JSON encoded string of site structure
function getGraphJson($siteStructure){
  foreach($siteStructure as $parent => $linkedArray){
    foreach($linkedArray as $child => $count){
      $tempArray['data']['id'] = $parent;
      $tempArray['data']['id'] = $child;
      $tempArray['data']['source'] = $parent;
      $tempArray['data']['target'] = $parent;
    }
  }
  return json_encode($tempArray);
}