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

/**
 * Interface Data
 * @package blackcat\Hologram\data
 */
interface Data {

  public const KEY_NAME = "NAME";
  public const KEY_LEVEL = "LEVEL";
  public const KEY_X = "Xvec";
  public const KEY_Y = "Yvec";
  public const KEY_Z = "Zvec";
  public const KEY_TITLE = "TITLE";
  public const KEY_TEXT = "TEXT";

  public const JSON_OPTIONS = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;

  public static function make();
}