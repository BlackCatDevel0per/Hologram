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

namespace blackcat\Hologram\command\sub;

use pocketmine\Player;
use blackcat\Hologram\i18n\Lang;
use blackcat\Hologram\i18n\Language;

/**
 * Class HologramSubCommand
 * @package blackcat\Hologram\command\sub
 */
abstract class HologramSubCommand {

  /** @var Player */
  protected $player;
  /** @var Language */
  protected $lang;

  public function __construct(Player $player, string $default = "") {
    $this->player = $player;
    $this->lang = Lang::fromLocale($player->getLocale());
    $this->execute($default);
  }

  abstract public function execute(string $default = ""): void;
}