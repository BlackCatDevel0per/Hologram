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

namespace blackcat\Hologram\data;

use pocketmine\plugin\Plugin;
use pocketmine\utils\Config;

/**
 * Class UnremovableFloatingTextData
 * @package blackcat\Hologram\data
 */
class UnremovableFloatingTextData extends Config implements Data {

  /** @var UnremovableFloatingTextData */
  private static $instance;

  public function __construct(Plugin $plugin, string $path, string $file) {
    $plugin->saveResource($file);
    parent::__construct($path.$file, Config::JSON);
    $this->enableJsonOption(Data::JSON_OPTIONS);
    self::$instance = $this;
  }

  public function getData(): array {
    $data = [];
    $ufts = $this->getAll();
    foreach ($ufts as $levelName => $texts) {
      foreach ($texts as $textName => $val) {
        $data[] = [
          Data::KEY_NAME => (string) $textName,
          Data::KEY_LEVEL => (string) $levelName,
          Data::KEY_X => (float) $val[Data::KEY_X],
          Data::KEY_Y => (float) $val[Data::KEY_Y],
          Data::KEY_Z => (float) $val[Data::KEY_Z],
          Data::KEY_TITLE => (string) $val[Data::KEY_TITLE],
          Data::KEY_TEXT => (string) $val[Data::KEY_TEXT]
        ];
      }
    }
    return $data;
  }

  /**
   * @return UnremovableFloatingTextData
   */
  public static function make(): UnremovableFloatingTextData {
    return self::$instance;
  }
}