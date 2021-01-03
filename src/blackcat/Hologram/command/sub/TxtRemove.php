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
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use blackcat\Hologram\Core;
use blackcat\Hologram\text\Text;
use blackcat\Hologram\HologramApi;

/**
 * Class TxtRemove
 * @package blackcat\Hologram\command\sub
 */
class TxtRemove extends HologramSubCommand {

  /** @var int response key */
  public const FT_NAME = 1;

  public function execute(string $default = ""): void {
    $pluginDescription = Core::get()->getDescription();
    $description = $this->lang->translateString("form.remove.description");
    $ftName = $this->lang->translateString("form.ftname");

    $custom = new CustomForm(function (Player $player, ?array $response) use ($pluginDescription) {
      if ($response !== null) {
        $level = $player->getLevel();
        if (!empty($response[self::FT_NAME])) {
          $ft = HologramApi::getFtByLevel($level, $response[self::FT_NAME]);
          if ($ft !== null) {
            if ($ft->isOwner($player)) {
              $ft->sendToLevel($level, Text::SEND_TYPE_REMOVE);
              HologramApi::removeFtByLevel($level, $ft->getName());
              $message = $this->lang->translateString("command.txt.remove.success", [
                $ft->getName()
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