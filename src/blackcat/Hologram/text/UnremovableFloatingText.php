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

use pocketmine\level\Position;
use pocketmine\Player;
use blackcat\Hologram\data\Data;
use function sprintf;

/**
 * Class UnremovableFloatingText
 * @package blackcat\Hologram\text
 */
class UnremovableFloatingText extends FloatingText implements Text {

  /** @var string */
  protected $owner = "uft.json";

  public function __construct(string $name, Position $pos, string $title = "", string $text = "", int $eid = 0) {
    parent::__construct($name, $pos, $title, $text, $this->owner, $eid);
  }

  public function sendToPlayer(Player $player, int $type = Text::SEND_TYPE_ADD): FloatingText {
    $pks = $this->asPackets($type);
    foreach ($pks as $pk) {
      $player->sendDataPacket($pk);
    }
    return $this;
  }

  public function format(): array {
    return [
      Data::KEY_X => sprintf('%0.1f', $this->x),
      Data::KEY_Y => sprintf('%0.1f', $this->y),
      Data::KEY_Z => sprintf('%0.1f', $this->z),
      Data::KEY_TITLE => $this->title,
      Data::KEY_TEXT => $this->text
    ];
  }

  public function __toString(): string {
    return "UnremovableFloatingText(name=\"{$this->text}\", pos=\"x:{$this->x};y:{$this->y};z:{$this->z};level:{$this->level->getFolderName()}\", title=\"{$this->title}\", text=\"{$this->text}\", eid=\"{$this->eid}\")";
  }
}