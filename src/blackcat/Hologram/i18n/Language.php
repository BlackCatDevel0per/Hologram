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

namespace blackcat\Hologram\i18n;

use pocketmine\lang\BaseLang;
use blackcat\Hologram\Core;

/**
 * Class Language - simple wrapper for BaseLang
 * @package blackcat\Hologram\language
 */
class Language extends BaseLang {

  public function __construct(string $lang) {
    $path = Core::get()->getDataFolder().Lang::DIR.DIRECTORY_SEPARATOR;
    parent::__construct($lang, $path, Lang::FALLBACK);
  }
}