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

use jojoe77777\FormAPI\SimpleForm;
use pocketmine\Player;
use blackcat\Hologram\Core;
use blackcat\Hologram\text\FloatingText;
use blackcat\Hologram\HologramApi;

/**
 * Class TxtList
 * @package blackcat\Hologram\command\sub
 */
class TxtList extends HologramSubCommand {

  /** @var int response key */
  public const EDIT = 0;
  public const MOVE = 1;
  public const REMOVE = 2;

  public function execute(string $default = ""): void {
    $pluginDescription = Core::get()->getDescription();
    $description = $this->lang->translateString("form.list.description.1");

    $fts = HologramApi::getFtsByLevel($this->player->getLevel());
    $search = [];
    foreach ($fts as $name => $ft) {
      /** @var FloatingText $ft */
      if ($this->player->distance($ft) <= 10 && $ft->isOwner($this->player)) {
        $search[] = $ft;
      }
    }

    $list1 = new SimpleForm(function (Player $player, ?int $key) use ($pluginDescription, $search) {
      if ($key !== null) {
        $target = $search[$key];
        $description = $this->lang->translateString("form.list.description.2", [
          $target->getName()
        ]);
        $list2 = new SimpleForm(function (Player $player, ?int $key) use ($target) {
          if ($key !== null) {
            switch ($key) {
              case self::EDIT:
                new TxtEdit($player, $target->getName());
                break;
              case self::MOVE:
                new TxtMove($player, $target->getName());
                break;
              case self::REMOVE:
                new TxtRemove($player, $target->getName());
                break;
            }
          }
        });

        $list2->setTitle("[{$pluginDescription->getPrefix()}] /txt list");
        $list2->setContent($description);
        $list2->addButton("edit");
        $list2->addButton("move");
        $list2->addButton("remove");
        $player->sendForm($list2);
      }
    });

    $list1->setTitle("[{$pluginDescription->getPrefix()}] /txt list");
    $list1->setContent($description);
    foreach ($search as $ft) $list1->addButton($ft->getName());
    $this->player->sendForm($list1);
  }
}