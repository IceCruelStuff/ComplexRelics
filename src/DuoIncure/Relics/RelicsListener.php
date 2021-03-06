<?php

namespace DuoIncure\Relics;

use pocketmine\event\Listener;
use pocketmine\nbt\tag\StringTag;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\BlockBreakEvent;
use function in_array;
use function rand;

class RelicsListener implements Listener {

	/** @var Main */
	private $plugin;

	/**
	 * RelicsListener constructor.
	 * @param Main $plugin
	 */
	public function __construct(Main $plugin)
	{
		$this->plugin = $plugin;
	}

	/**
	 * @param PlayerInteractEvent $ev
	 */
	public function onInteract(PlayerInteractEvent $ev){
		$player = $ev->getPlayer();
		if($ev->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK || $ev->getAction() === PlayerInteractEvent::RIGHT_CLICK_AIR){
			$item = $ev->getItem();
			$nbt = $item->getNamedTag();
			if($nbt->hasTag(RelicFunctions::RELIC_TAG)){
				$relicType = $nbt->getTagValue(RelicFunctions::RELIC_TAG, StringTag::class);
				switch($relicType){
					case "common":
						$this->plugin->getRelicFunctions()->giveCorrespondingReward($player, $item, "common");
						break;
					case "rare":
						$this->plugin->getRelicFunctions()->giveCorrespondingReward($player, $item, "rare");
						break;
					case "epic":
						$this->plugin->getRelicFunctions()->giveCorrespondingReward($player, $item, "epic");
						break;
					case "legendary":
						$this->plugin->getRelicFunctions()->giveCorrespondingReward($player, $item, "legendary");
						break;
				}
			}
		}
	}

	/**
	 * @param BlockBreakEvent $ev
	 * @priority MONITOR
	 * @ignoreCancelled true
	 */
	public function onBreak(BlockBreakEvent $ev){
		$player = $ev->getPlayer();
		$continueRelics = false;
		$config = $this->plugin->getConfig()->getAll();
		$blockID = $ev->getBlock()->getId();
		$configBlocks = $config["block-ids"];
		$configWorlds = $config["worlds"];
		$levelName = $player->getLevel()->getName();
		if(in_array($blockID, $configBlocks) && ($configWorlds[0] == "*" OR in_array($levelName, $configWorlds))){
			$commonChance = $config["common"]["chance"] ?? 10;
			$rareChance = $config["rare"]["chance"] ?? 5;
			$epicChance = $config["epic"]["chance"] ?? 3;
			$legendaryChance = $config["legendary"]["chance"] ?? 1;
			$chance = rand(1, 100);
			if ($chance > $rareChance && $chance <= $commonChance) {
				$this->plugin->getRelicFunctions()->giveCorrespondingRelic($player, "common");
			} else if ($chance > $epicChance && $chance <= $rareChance) {
				$this->plugin->getRelicFunctions()->giveCorrespondingRelic($player, "rare");
			} else if ($chance > $legendaryChance && $chance <= $epicChance) {
				$this->plugin->getRelicFunctions()->giveCorrespondingRelic($player, "epic");
			} else if ($chance <= $legendaryChance) {
				$this->plugin->getRelicFunctions()->giveCorrespondingRelic($player, "legendary");
			}
		}
	}
}