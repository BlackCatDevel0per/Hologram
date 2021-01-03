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

use jojoe77777\FormAPI\CustomForm;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use blackcat\Hologram\Core;
use blackcat\Hologram\data\FloatingTextData;
use blackcat\Hologram\text\Text;
use blackcat\Hologram\HologramApi;

/**
 * Class TxtMove
 * @package blackcat\Hologram\command\sub
 */
class TxtMove extends HologramSubCommand {

  /** @var int response key */
  public const FT_NAME = 1;

  public function execute(string $default = ""): void {
    $pluginDescription = Core::get()->getDescription();
    $description = $this->lang->translateString("form.move.description");
    $ftName = $this->lang->translateString("form.ftname");

    $custom = new CustomForm(function (Player $player, ?array $response) use ($pluginDescription) {
      if ($response !== null) {
        $level = $player->getLevel();
        if (!empty($response[self::FT_NAME])) {
          $ft = HologramApi::getFtByLevel($level, $response[self::FT_NAME]);
          if ($ft !== null) {
            if ($ft->isOwner($player)) {
              $ft
                ->setPosition(Position::fromObject($player->add(0, 2, 0), $level))
                ->sendToLevel($level, Text::SEND_TYPE_MOVE);
              FloatingTextData::make()->saveFtChange($ft);
              $message = $this->lang->translateString("command.txt.move.success", [
                $ft->getName(),
                $this->lang->translateString("form.move.here")// TODO: xyz specification(3.2.0~)
              ]);
              $player->sendMessage(TextFormat::GREEN . "[{$pluginDescription->getPrefix()}] $message");
            }else {
              $message = $this->lang->translateString("error.permission");
              $player->sendMessage(TextFormat::RED . "[{$pluginDescription->getPrefix()}] $message");
            }
          }else {
            $message = $this->lang->translateString("error.ftname.not.exists", [
              $response[self::FT_NAME]
            ]);
            $player->sendMessage(TextFormat::RED . "[{$pluginDescription->getPrefix()}] $message");
          }
        }else {
          $message = $this->lang->translateString("error.ftname.not.specified");
          $player->sendMessage(TextFormat::RED . "[{$pluginDescription->getPrefix()}] $message");
        }
      }
    });

    $custom->setTitle("[{$pluginDescription->getPrefix()}] /txt remove");
    $custom->addLabel($description);
    $custom->addInput($ftName, $ftName, $default);
    $this->player->sendForm($custom);
  }
}