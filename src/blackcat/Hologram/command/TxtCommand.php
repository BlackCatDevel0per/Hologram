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

namespace blackcat\Hologram\command;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use blackcat\Hologram\command\sub\TxtAdd;
use blackcat\Hologram\command\sub\TxtEdit;
use blackcat\Hologram\command\sub\TxtList;
use blackcat\Hologram\command\sub\TxtMove;
use blackcat\Hologram\command\sub\TxtRemove;
use blackcat\Hologram\Core;
use blackcat\Hologram\data\ConfigData;
use blackcat\Hologram\i18n\Lang;

/**
 * Class TxtCommand
 * @package blackcat\Hologram\command
 */
class TxtCommand extends PluginCommand {

  public const NAME = "txt";

  public function __construct(Core $plugin) {
    $cl = Lang::fromConsole();
    $permission = ConfigData::make()->canUseOnlyOp() ? "hologram.command.*" : "hologram.command.txt";
    $description = $cl->translateString("command.txt.description");
    $usage = $cl->translateString("command.txt.usage");
    $this->setPermission($permission);
    $this->setUsage($usage);
    parent::__construct(self::NAME, $plugin);
  }

  public function execute(CommandSender $sender, string $commandLabel, array $args) {
    if (Core::get()->isDisabled() || !$this->testPermission($sender)) return false;
    if ($sender instanceof Player) {
      $pluginDescription = Core::get()->getDescription();
      $cd = ConfigData::make();
      $lang = Lang::fromLocale($sender->getLocale());
      if ($cd->checkWorldLimit($sender->getLevel()->getName())) {
        if (isset($args[0])) {
          switch ($args[0]) {
            case "add":
            case "a":
              new TxtAdd($sender);
              break;

            case "edit":
            case "e":
              new TxtEdit($sender);
              break;

            case "move":
            case "m":
              new TxtMove($sender);
              break;

            case "remove":
            case "r":
              new TxtRemove($sender);
              break;

            case "list":
            case "l":
              new TxtList($sender);
              break;

            default:
              $message = $lang->translateString("command.txt.usage");
              $sender->sendMessage("[{$pluginDescription->getPrefix()}] $message");
              break;
          }
        }else {
          $message = $lang->translateString("command.txt.usage");
          $sender->sendMessage("[{$pluginDescription->getPrefix()}] $message");
        }
      }else {
        $message = $lang->translateString("error.config.limit.world", [
          $sender->getLevel()->getName()
        ]);
        $sender->sendMessage(TextFormat::RED . "[{$pluginDescription->getPrefix()}] $message");
      }
    }else {
      $info = Lang::fromConsole()->translateString("error.console");
      Core::get()->getLogger()->info(TextFormat::RED.$info);
    }
    return true;
  }

}