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
use blackcat\Hologram\data\ConfigData;
use blackcat\Hologram\text\FloatingText;
use blackcat\Hologram\HologramApi;

/**
 * Class TxtAdd
 * @package blackcat\Hologram\command\sub
 */
class TxtAdd extends HologramSubCommand {

  /** @var int response key */
  public const NAME = 1;
  public const TITLE = 3;
  public const TEXT = 4;

  public function execute(string $default = ""): void {
    $pluginDescription = Core::get()->getDescription();
    $description = $this->lang->translateString("form.add.description");
    $ftName = $this->lang->translateString("form.ftname.unique");
    $indent = $this->lang->translateString("command.txt.usage.indent");
    $title = $this->lang->translateString("form.title");
    $text = $this->lang->translateString("form.text");

    $custom = new CustomForm(function (Player $player, ?array $response) use ($pluginDescription) {
      if ($response !== null) {
        $level = $player->getLevel();
        if (!empty($response[self::NAME])) {
          $exists = HologramApi::getFtByLevel($level, $response[self::NAME]);
          if ($exists === null) {
            $title = $player->isOp() ? $response[self::TITLE] : TextFormat::clean($response[self::TITLE]);
            $text = $player->isOp() ? $response[self::TEXT] : TextFormat::clean($response[self::TEXT]);
            $ft = new FloatingText($response[self::NAME], Position::fromObject($player->add(0, 1, 0), $level), $title, $text, $player->getName());
            $cd = ConfigData::make();
            if ($cd->checkCharLimit($ft->getTextsForCheck(FloatingText::CHECK_CHAR))) {
              if ($cd->checkFeedLimit($ft->getTextsForCheck(FloatingText::CHECK_FEED))) {
                $ft->sendToLevel($level);
                HologramApi::registerText($ft);
                $message = $this->lang->translateString("command.txt.add.success", [
                  TextFormat::clean($response[self::NAME])
                ]);
                $player->sendMessage(TextFormat::GREEN . "[{$pluginDescription->getPrefix()}] $message");
              }else {
                $message = $this->lang->translateString("error.config.limit.feed", [
                  $cd->getFeedLimit()
                ]);
                $player->sendMessage(TextFormat::RED . "[{$pluginDescription->getPrefix()}] $message");
              }
            }else {
              $message = $this->lang->translateString("error.config.limit.char", [
                $cd->getCharLimit()
              ]);
              $player->sendMessage(TextFormat::RED . "[{$pluginDescription->getPrefix()}] $message");
            }
          }else {
            $message = $this->lang->translateString("error.ftname.exists", [
              $response[self::NAME]
            ]);
            $player->sendMessage(TextFormat::RED . "[{$pluginDescription->getPrefix()}] $message");
          }
        }else {
          $message = $this->lang->translateString("error.ftname.not.specified");
          $player->sendMessage(TextFormat::RED . "[{$pluginDescription->getPrefix()}] $message");
        }
      }
    });

    $custom->setTitle("[{$pluginDescription->getPrefix()}] /txt add");
    $custom->addLabel($description);
    $custom->addInput($ftName, $ftName);
    $custom->addLabel($indent);
    $custom->addInput($title, $title);
    $custom->addInput($text, $text);
    $this->player->sendForm($custom);
  }
}