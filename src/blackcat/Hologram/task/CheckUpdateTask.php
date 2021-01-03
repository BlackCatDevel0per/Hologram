<?php

 #  _   _       _   _           _____             
 # | \ | |     | | (_)         |  __ \            
 # |  \| | __ _| |_ ___   _____| |  | | _____   __
 # | . ` |/ _` | __| \ \ / / _ \ |  | |/ _ \ \ / /
 # | |\  | (_| | |_| |\ V /  __/ |__| |  __/\ V / 
 # |_| \_|\__,_|\__|_| \_/ \___|_____/ \___| \_/  
 # Больше плагинов в https://vk.com/native_dev
 # По вопросам native.dev@mail.ru
 # Этот плагин форк(клон) плагина https://github.com/fuyutsuki/Texter

declare(strict_types = 1);

namespace blackcat\Hologram\task;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\VersionString;
use blackcat\Hologram\Core;
use function curl_init;
use function curl_setopt_array;
use function curl_exec;
use function curl_errno;
use function curl_error;
use function curl_close;
use function json_decode;

/**
 * CheckUpdateTaskClass
 */
class CheckUpdateTask extends AsyncTask {

    public function onRun() {
    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_URL => "https://api.github.com/repos/fuyutsuki/Hologram/releases",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_USERAGENT => "php_".PHP_VERSION,
      CURLOPT_SSL_VERIFYPEER => false
    ]);
    $json = curl_exec($curl);
    $errorNo = curl_errno($curl);
    if ($errorNo) {
      $error = curl_error($curl);
      throw new \Exception($error);
    }
    curl_close($curl);
    $data = json_decode($json, true);
    $this->setResult($data);
  }

  public function onCompletion(Server $server){
    $core = Core::get();
    if ($core->isEnabled()) {
      $data = $this->getResult();
      if (isset($data[0])) {
        $ver = new VersionString($data[0]["name"]);
        $core->compareVersion(true, $ver, $data[0]["html_url"]);
      }else {
        $core->compareVersion(false);
      }
    }
  }
}
