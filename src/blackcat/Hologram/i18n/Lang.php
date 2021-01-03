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

use blackcat\Hologram\Core;
use blackcat\Hologram\data\ConfigData;
use function strtolower;

/**
 * Class Lang
 * @package blackcat\Hologram\language
 */
class Lang {

  public const DIR = "language";
  public const FALLBACK = "en_us";

  /** @var Lang */
  private static $instance;
  /** @var Language[] */
  private static $language;
  /** @var string */
  private static $consoleLang = self::FALLBACK;
  /** @var string[] */
  private static $available = [
    "en_us",
    "ru_ru",
  ];

  public function __construct(Core $core) {
    self::$instance = $this;
    self::$consoleLang = ConfigData::make()->getLocale();
    foreach (self::$available as $lang) {
      $core->saveResource(Lang::DIR . DIRECTORY_SEPARATOR . $lang . ".ini", true);
      $this->register(new Language($lang));
    }
  }

  /**
   * @param Language $language
   * @return Lang
   */
  public function register(Language $language): self {
    self::$language[$language->getLang()] = $language;
    return self::$instance;
  }

  /**
   * @param string $lang
   * @return Language
   */
  public static function fromLocale(string $lang): Language {
    $lLang = strtolower($lang);
    if (isset(self::$language[$lLang])) {
      return self::$language[$lLang];
    }else {
      return self::$language[self::FALLBACK];
    }
  }

  /**
   * @return Language
   */
  public static function fromConsole(): Language {
    return self::fromLocale(self::$consoleLang);
  }

  /**
   * @return Lang
   */
  public static function get(): self {
    return self::$instance;
  }
}
