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

namespace blackcat\Hologram\text;

use pocketmine\level\Level;
use pocketmine\Player;

/**
 * Interface Text
 * @package blackcat\Hologram\text
 */
interface Text {

  public const SEND_TYPE_ADD = 0;
  public const SEND_TYPE_EDIT = 1;
  public const SEND_TYPE_MOVE = 2;
  public const SEND_TYPE_REMOVE = 3;

  /**
   * @param Player $player
   * @param int $type
   * @return mixed
   */
  public function sendToPlayer(Player $player, int $type = Text::SEND_TYPE_ADD);

  /**
   * @param Player[] $players
   * @param int $type
   * @return mixed
   */
  public function sendToPlayers(array $players, int $type = Text::SEND_TYPE_ADD);

  /**
   * @param Level $level
   * @param int $type
   * @return mixed
   */
  public function sendToLevel(Level $level, int $type = Text::SEND_TYPE_ADD);

  /**
   * @return array[string key] = value
   */
  public function format(): array;

  public function __toString(): string;

}