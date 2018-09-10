<?php
$mem = new Memcached();
$mem->addServer("memcached1.udbhj2.cfg.use1.cache.amazonaws.com", 11211);

// initiate the memcached instance
//$cache = new \Memcached();
//$cache->addServer('localhost', '11211');

// get all stored memcached items

$keys = $mem->getAllKeys();
$mem->getDelayed($keys);

$store = $mem->fetchAll();

// delete by regex keys

$keys = $mem->getAllKeys();
$regex = 'product_.*';
foreach($keys as $item) {
    if(preg_match('/'.$regex.'/', $item)) {
        $cache->delete($item);
    }
}
?>
