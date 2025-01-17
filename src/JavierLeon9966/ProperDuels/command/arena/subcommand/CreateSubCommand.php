<?php

declare(strict_types = 1);

namespace JavierLeon9966\ProperDuels\command\arena\subcommand;

use CortexPE\Commando\args\{RawStringArgument, Vector3Argument};
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;

use JavierLeon9966\ProperDuels\arena\Arena;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class CreateSubCommand extends BaseSubCommand{

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void{
		$arenaManager = $this->plugin->getArenaManager();
		if($arenaManager->has($args['arena'])){
			$sender->sendMessage(TextFormat::RED."An arena with the name '$args[arena]' already exists");
			return;
		}

		$level = $sender->getLevelNonNull();
		foreach(['firstSpawnPos', 'secondSpawnPos'] as $spawn){
			$pos = $args[$spawn]->floor();
			if(!$level->isInWorld($pos->x, $pos->y, $pos->z)){
				$sender->sendMessage(TextFormat::RED.'Cannot set positions outside of the world');
				return;
			}
		}

		$kitManager = $this->plugin->getKitManager();
		if(isset($args['kit']) and !$kitManager->has($args['kit'])){
			$sender->sendMessage(TextFormat::RED."No kit was found by the name '$args[kit]'");
			return;
		}

		$arenaManager->add(new Arena(
			$args['arena'],
			$level->getFolderName(),
			$args['firstSpawnPos'],
			$args['secondSpawnPos'],
			$args['kit'] ?? null
		));
		$sender->sendMessage("Added new arena '$args[arena]' successfully");
	}

	public function prepare(): void{
		$this->addConstraint(new InGameRequiredConstraint($this));

		$this->setPermission('properduels.command.arena.create');

		$this->registerArgument(0, new RawStringArgument('arena'));
		$this->registerArgument(1, new Vector3Argument('firstSpawnPos'));
		$this->registerArgument(2, new Vector3Argument('secondSpawnPos'));
		$this->registerArgument(3, new RawStringArgument('kit', true));
	}
}
