<?php
$mem = new Memcached();
$mem->addServer("memcached1.udbhj2.cfg.use1.cache.amazonaws.com", 11211);

$result = $mem->get("blah");

if ($result) {
    echo $result;
} else {
    echo "No matching key found.  I'll add that now!";
    $mem->set("blah", "I am data!  I am held in memcached!") or die("Couldn't save anything to memcached...");
}
?>
