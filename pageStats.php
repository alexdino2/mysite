<?php
$mem = new Memcached();
$mem->addServer("memcached1.udbhj2.cfg.use1.cache.amazonaws.com", 11211);

$user_id=$mem->get('userid');

$result = $mem->get($user_id."pagestats");
//$result = $mem->get("networkdata");

if ($result) {
    echo $result;
    $mem->close();
} else {
    echo "No matching key found.  I'll add that now!";
    $mem->set("blah", "I am data!  I am held in memcached!") or die("Couldn't save anything to memcached...");
    $mem->close();
}
?>
