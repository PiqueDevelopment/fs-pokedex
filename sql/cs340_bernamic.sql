-- Group Number: 46
-- Group Names: Dylan Liu, Michael Bernardino, Brendon Tran

-- phpMyAdmin SQL Dump
-- version 5.2.1-1.el7.remi
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 30, 2024 at 09:00 PM
-- Server version: 10.6.19-MariaDB-log
-- PHP Version: 8.2.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cs340_bernamic`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`cs340_bernamic`@`%` PROCEDURE `rename_team` (IN `p_team_id` INT, IN `p_new_name` VARCHAR(255))   BEGIN
    DECLARE team_not_found CONDITION FOR SQLSTATE '45000'; -- Custom error condition
    DECLARE error_message VARCHAR(255);  -- Declare a variable for the error message

    -- Attempt to update the team name
    UPDATE Teams
    SET team_name = p_new_name
    WHERE team_id = p_team_id;

    -- Check if any rows were updated
    IF ROW_COUNT() = 0 THEN
        -- Generate the error message using CONCAT
        SET error_message = CONCAT('Team with ID ', p_team_id, ' not found.');
        
        -- Raise the exception with the generated message
        SIGNAL team_not_found SET MESSAGE_TEXT = error_message;
    END IF;
END$$

--
-- Functions
--
CREATE DEFINER=`cs340_bernamic`@`%` FUNCTION `get_pokemon_abilities` (`pokemon_id` INT) RETURNS VARCHAR(255) CHARSET utf8mb3 COLLATE utf8mb3_general_ci DETERMINISTIC BEGIN
    DECLARE abilities VARCHAR(255);

    SELECT GROUP_CONCAT(a.ability_name SEPARATOR ', ')
    INTO abilities
    FROM Pokemon_Ability pa
    JOIN Ability a ON pa.ability_id = a.ability_id
    WHERE pa.pokemon_id = pokemon_id;

    RETURN abilities;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `Ability`
--

CREATE TABLE `Ability` (
  `ability_id` int(11) NOT NULL,
  `ability_name` varchar(100) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `Ability`
--

INSERT INTO `Ability` (`ability_id`, `ability_name`, `description`) VALUES
(1, 'Aerilate', 'Turns Normal-type moves into Flying-type moves.'),
(2, 'Aftermath', 'Damages the attacker landing the finishing hit.'),
(3, 'Air Lock', 'Eliminates the effects of weather.'),
(4, 'Analytic', 'Boosts move power when the Pokémon moves last.'),
(5, 'Anger Point', 'Maxes Attack after taking a critical hit.'),
(6, 'Anger Shell', 'Lowers Defense/Sp. Def and raises Attack/Sp. Atk/Speed when HP drops below half.'),
(7, 'Anticipation', 'Senses a foe\'s dangerous moves.'),
(8, 'Arena Trap', 'Prevents the foe from fleeing.'),
(9, 'Armor Tail', 'Prevents opponent using priority moves.'),
(11, 'Aroma Veil', 'Protects allies from attacks that limit their move choices.'),
(12, 'As One', 'Combines Unnerve and Chilling Neigh/Grim Neigh'),
(13, 'Aura Break', 'Reduces power of Dark- and Fairy-type moves.'),
(14, 'Bad Dreams', 'Reduces a sleeping foe\'s HP.'),
(15, 'Ball Fetch', 'Retrieves a Poké Ball from a failed throw.'),
(16, 'Battery', 'Raises power of teammates\' Special moves.'),
(17, 'Battle Armor', 'The Pokémon is protected against critical hits.'),
(18, 'Battle Bond', 'Transform into Ash-Greninja after causing opponent to faint.'),
(19, 'Beads of Ruin', 'Lowers Special Defense of all Pokémon except itself.'),
(20, 'Beast Boost', 'The Pokémon boosts its most proficient stat each time it knocks out a Pokémon.'),
(21, 'Berserk', 'Raises Special Attack when HP drops below half.'),
(22, 'Big Pecks', 'Protects the Pokémon from Defense-lowering attacks.'),
(23, 'Blaze', 'Powers up Fire-type moves in a pinch.'),
(24, 'Bulletproof', 'Protects the Pokémon from ball and bomb moves.'),
(25, 'Cheek Pouch', 'Restores additional HP when a Berry is consumed.'),
(26, 'Chilling Neigh', 'Boosts Attack after knocking out a Pokémon.'),
(27, 'Chlorophyll', 'Boosts the Pokémon\'s Speed in sunshine.'),
(28, 'Clear Body', 'Prevents other Pokémon from lowering its stats.'),
(29, 'Cloud Nine', 'Eliminates the effects of weather.'),
(30, 'Color Change', 'Changes the Pokémon\'s type to the foe\'s move.'),
(31, 'Comatose', 'The Pokémon is always asleep but can still attack.'),
(32, 'Commander', 'Goes inside the mouth of an ally Dondozo if one is on the field.'),
(33, 'Competitive', 'Sharply raises Special Attack when the Pokémon\'s stats are lowered.'),
(34, 'Compound Eyes', 'The Pokémon\'s accuracy is boosted.'),
(35, 'Contrary', 'Makes stat changes have an opposite effect.'),
(36, 'Corrosion', 'The Pokémon can poison Steel and Poison types.'),
(37, 'Costar', 'Copies ally\'s stat changes on entering battle.'),
(38, 'Cotton Down', 'Lowers foe\'s Speed when hit.'),
(39, 'Cud Chew', 'Can eat the same Berry twice.'),
(40, 'Curious Medicine', 'Resets all stat changes upon entering battlefield.'),
(41, 'Cursed Body', 'May disable a move used on the Pokémon.'),
(42, 'Cute Charm', 'Contact with the Pokémon may cause infatuation.'),
(43, 'Damp', 'Prevents the use of self-destructing moves.'),
(44, 'Dancer', 'Copies the foe\'s Dance moves.'),
(45, 'Dark Aura', 'Raises power of Dark type moves for all Pokémon in battle.'),
(46, 'Dauntless Shield', 'Boosts Defense in battle.'),
(47, 'Dazzling', 'Protects the Pokémon from high-priority moves.'),
(48, 'Defeatist', 'Lowers stats when HP drops below half.'),
(49, 'Defiant', 'Sharply raises Attack when the Pokémon\'s stats are lowered.'),
(50, 'Delta Stream', 'Creates strong winds when the ability activates.'),
(51, 'Desolate Land', 'Turns the sunlight extremely harsh when the ability activates.'),
(52, 'Disguise', 'Avoids damage for one turn.'),
(53, 'Download', 'Adjusts power according to a foe\'s defenses.'),
(54, 'Dragon\'s Maw', 'Powers up Dragon-type moves.'),
(55, 'Drizzle', 'The Pokémon makes it rain when it enters a battle.'),
(56, 'Drought', 'Turns the sunlight harsh when the Pokémon enters a battle.'),
(57, 'Dry Skin', 'Reduces HP if it is hot. Water restores HP.'),
(58, 'Early Bird', 'The Pokémon awakens quickly from sleep.'),
(59, 'Earth Eater', 'Restores HP when hit by a Ground-type move.'),
(60, 'Effect Spore', 'Contact may poison or cause paralysis or sleep.'),
(61, 'Electric Surge', 'The Pokémon creates an Electric Terrain when it enters a battle.'),
(62, 'Electromorphosis', 'Doubles power of the next Electric-type move when hit by an attack.'),
(63, 'Embody Aspect', 'Boosts Attack/Defense/Sp. Def/Speed depending on the form.'),
(64, 'Emergency Exit', 'Switches out when HP falls below 50%.'),
(65, 'Fairy Aura', 'Raises power of Fairy type moves for all Pokémon in battle.'),
(66, 'Filter', 'Reduces damage from super-effective attacks.'),
(67, 'Flame Body', 'Contact with the Pokémon may burn the attacker.'),
(68, 'Flare Boost', 'Powers up special attacks when burned.'),
(69, 'Flash Fire', 'It powers up Fire-type moves if it\'s hit by one.'),
(70, 'Flower Gift', 'Powers up party Pokémon when it is sunny.'),
(71, 'Flower Veil', 'Prevents lowering of ally Grass-type Pokémon\'s stats.'),
(72, 'Fluffy', 'Halves damage from contact moves, but doubles damage from Fire-type moves.'),
(73, 'Forecast', 'Castform transforms with the weather.'),
(74, 'Forewarn', 'Determines what moves a foe has.'),
(75, 'Friend Guard', 'Reduces damage done to allies.'),
(76, 'Frisk', 'The Pokémon can check a foe\'s held item.'),
(77, 'Full Metal Body', 'Prevents other Pokémon from lowering its stats.'),
(78, 'Fur Coat', 'Reduces damage from physical moves.'),
(79, 'Gale Wings', 'Gives priority to Flying-type moves.'),
(80, 'Galvanize', 'Normal-type moves become Electric-type moves and their power boosted.'),
(81, 'Gluttony', 'Encourages the early use of a held Berry.'),
(82, 'Good as Gold', 'Gives immunity to status moves.'),
(83, 'Gooey', 'Contact with the Pokémon lowers the attacker\'s Speed stat.'),
(84, 'Gorilla Tactics', 'Boosts the Pokémon\'s Attack stat but only allows the use of the first selected move.'),
(85, 'Grass Pelt', 'Boosts the Defense stat in Grassy Terrain.'),
(86, 'Grassy Surge', 'The Pokémon creates a Grassy Terrain when it enters a battle.'),
(87, 'Grim Neigh', 'Boosts Special Attack after knocking out a Pokémon.'),
(88, 'Guard Dog', 'Boosts Attack if intimidated, and prevents being forced to switch out.'),
(89, 'Gulp Missile', 'Returns with a catch in its mouth after using Surf or Dive.'),
(90, 'Guts', 'Boosts Attack if there is a status problem.'),
(91, 'Hadron Engine', 'Creates an Electric Terrain when entering battle, and boosts Special Attack while active.'),
(92, 'Harvest', 'May create another Berry after one is used.'),
(93, 'Healer', 'May heal an ally\'s status conditions.'),
(94, 'Heatproof', 'Weakens the power of Fire-type moves.'),
(95, 'Heavy Metal', 'Doubles the Pokémon\'s weight.'),
(96, 'Honey Gather', 'The Pokémon may gather Honey from somewhere.'),
(97, 'Hospitality', 'Partially restores an ally\'s HP when it enters a battle.'),
(98, 'Huge Power', 'Raises the Pokémon\'s Attack stat.'),
(99, 'Hunger Switch', 'Changes forms each turn.'),
(100, 'Hustle', 'Boosts the Attack stat, but lowers accuracy.'),
(101, 'Hydration', 'Heals status problems if it is raining.'),
(102, 'Hyper Cutter', 'Prevents other Pokémon from lowering Attack stat.'),
(103, 'Ice Body', 'The Pokémon gradually regains HP in a hailstorm.'),
(104, 'Ice Face', 'Avoids damage from Physical moves for one turn.'),
(105, 'Ice Scales', 'Halves damage from Special moves.'),
(106, 'Illuminate', 'Raises the likelihood of meeting wild Pokémon.'),
(107, 'Illusion', 'Enters battle disguised as the last Pokémon in the party.'),
(108, 'Immunity', 'Prevents the Pokémon from getting poisoned.'),
(109, 'Imposter', 'It transforms itself into the Pokémon it is facing.'),
(110, 'Infiltrator', 'Passes through the foe\'s barrier and strikes.'),
(111, 'Innards Out', 'Deals damage upon fainting.'),
(112, 'Inner Focus', 'The Pokémon is protected from flinching.'),
(113, 'Insomnia', 'Prevents the Pokémon from falling asleep.'),
(114, 'Intimidate', 'Lowers the foe\'s Attack stat.'),
(115, 'Intrepid Sword', 'Boosts Attack in battle.'),
(116, 'Iron Barbs', 'Inflicts damage to the Pokémon on contact.'),
(117, 'Iron Fist', 'Boosts the power of punching moves.'),
(118, 'Justified', 'Raises Attack when hit by a Dark-type move.'),
(119, 'Keen Eye', 'Prevents other Pokémon from lowering accuracy.'),
(120, 'Klutz', 'The Pokémon can\'t use any held items.'),
(121, 'Leaf Guard', 'Prevents problems with status in sunny weather.'),
(122, 'Levitate', 'Gives immunity to Ground type moves.'),
(123, 'Libero', 'Changes the Pokémon\'s type to its last used move.'),
(124, 'Light Metal', 'Halves the Pokémon\'s weight.'),
(125, 'Lightning Rod', 'Draws in all Electric-type moves to up Sp. Attack.'),
(126, 'Limber', 'The Pokémon is protected from paralysis.'),
(127, 'Lingering Aroma', 'Contact changes the attacker\'s Ability to Lingering Aroma.'),
(128, 'Liquid Ooze', 'Damages attackers using any draining move.'),
(129, 'Liquid Voice', 'All sound-based moves become Water-type moves.'),
(130, 'Long Reach', 'The Pokémon uses its moves without making contact with the target.'),
(131, 'Magic Bounce', 'Reflects status-changing moves.'),
(132, 'Magic Guard', 'Protects the Pokémon from indirect damage.'),
(133, 'Magician', 'The Pokémon steals the held item of a Pokémon it hits with a move.'),
(134, 'Magma Armor', 'Prevents the Pokémon from becoming frozen.'),
(135, 'Magnet Pull', 'Prevents Steel-type Pokémon from escaping.'),
(136, 'Marvel Scale', 'Ups Defense if there is a status problem.'),
(137, 'Mega Launcher', 'Boosts the power of aura and pulse moves.'),
(138, 'Merciless', 'The Pokémon\'s attacks become critical hits if the target is poisoned.'),
(139, 'Mimicry', 'Changes type depending on the terrain.'),
(140, 'Mind\'s Eye', 'Ignores opponent\'s Evasiveness, and allows Normal- and Fighting-type attacks to hit Ghosts.'),
(141, 'Minus', 'Ups Sp. Atk if another Pokémon has Plus or Minus.'),
(142, 'Mirror Armor', 'Reflects any stat-lowering effects.'),
(143, 'Misty Surge', 'The Pokémon creates a Misty Terrain when it enters a battle.'),
(144, 'Mold Breaker', 'Moves can be used regardless of Abilities.'),
(145, 'Moody', 'Raises one stat and lowers another.'),
(146, 'Motor Drive', 'Raises Speed if hit by an Electric-type move.'),
(147, 'Moxie', 'Boosts Attack after knocking out any Pokémon.'),
(148, 'Multiscale', 'Reduces damage when HP is full.'),
(149, 'Multitype', 'Changes type to match the held Plate.'),
(150, 'Mummy', 'Contact with this Pokémon spreads this Ability.'),
(151, 'Mycelium Might', 'Status moves go last, but are not affected by the opponent\'s ability.'),
(152, 'Natural Cure', 'All status problems heal when it switches out.'),
(153, 'Neuroforce', 'Powers up moves that are super effective.'),
(154, 'Neutralizing Gas', 'Neutralizes abilities of all Pokémon in battle.'),
(155, 'No Guard', 'Ensures attacks by or against the Pokémon land.'),
(156, 'Normalize', 'All the Pokémon\'s moves become the Normal type.'),
(157, 'Oblivious', 'Prevents it from becoming infatuated.'),
(158, 'Opportunist', 'Copies stat boosts by the opponent.'),
(159, 'Orichalcum Pulse', 'Turns the sunlight harsh when entering battle, and boosts Attack while active.'),
(160, 'Overcoat', 'Protects the Pokémon from weather damage.'),
(161, 'Overgrow', 'Powers up Grass-type moves in a pinch.'),
(162, 'Own Tempo', 'Prevents the Pokémon from becoming confused.'),
(163, 'Parental Bond', 'Allows the Pokémon to attack twice.'),
(164, 'Pastel Veil', 'Prevents the Pokémon and its allies from being poisoned.'),
(165, 'Perish Body', 'When hit by a move that makes direct contact, the Pokémon and the attacker will faint after three turns unless they switch out of battle.'),
(166, 'Pickpocket', 'Steals an item when hit by another Pokémon.'),
(167, 'Pickup', 'The Pokémon may pick up items.'),
(168, 'Pixilate', 'Turns Normal-type moves into Fairy-type moves.'),
(169, 'Plus', 'Ups Sp. Atk if another Pokémon has Plus or Minus.'),
(170, 'Poison Heal', 'Restores HP if the Pokémon is poisoned.'),
(171, 'Poison Point', 'Contact with the Pokémon may poison the attacker.'),
(172, 'Poison Puppeteer', 'Poisoned Pokémon also become confused.'),
(173, 'Poison Touch', 'May poison targets when a Pokémon makes contact.'),
(174, 'Power Construct', 'Changes form when HP drops below half.'),
(175, 'Power of Alchemy', 'The Pokémon copies the Ability of a defeated ally.'),
(176, 'Power Spot', 'Just being next to the Pokémon powers up moves.'),
(177, 'Prankster', 'Gives priority to a status move.'),
(178, 'Pressure', 'The Pokémon raises the foe\'s PP usage.'),
(179, 'Primordial Sea', 'Makes it rain heavily when the ability activates.'),
(180, 'Prism Armor', 'Reduces damage from super-effective attacks.'),
(181, 'Propeller Tail', 'Ignores moves and abilities that draw in moves.'),
(182, 'Protean', 'Changes the Pokémon\'s type to its last used move.'),
(183, 'Protosynthesis', 'Raises highest stat in harsh sunlight, or if holding Booster Energy.'),
(184, 'Psychic Surge', 'The Pokémon creates a Psychic Terrain when it enters a battle.'),
(185, 'Punk Rock', 'Boosts sound-based moves and halves damage from the same moves.'),
(186, 'Pure Power', 'Raises the Pokémon\'s Attack stat.'),
(187, 'Purifying Salt', 'Protects from status conditions and halves damage from Ghost-type moves.'),
(188, 'Quark Drive', 'Raises highest stat on Electric Terrain, or if holding Booster Energy.'),
(189, 'Queenly Majesty', 'Prevents use of priority moves.'),
(190, 'Quick Draw', 'Gives the Pokémon the chance to strike first.'),
(191, 'Quick Feet', 'Boosts Speed if there is a status problem.'),
(192, 'Rain Dish', 'The Pokémon gradually regains HP in rain.'),
(193, 'Rattled', 'Bug, Ghost or Dark type moves scare it and boost its Speed.'),
(194, 'Receiver', 'Inherits an ally\'s ability when it faints.'),
(195, 'Reckless', 'Powers up moves that have recoil damage.'),
(196, 'Refrigerate', 'Turns Normal-type moves into Ice-type moves.'),
(197, 'Regenerator', 'Restores a little HP when withdrawn from battle.'),
(198, 'Ripen', 'Doubles the effect of berries.'),
(199, 'Rivalry', 'Deals more damage to a Pokémon of same gender.'),
(200, 'RKS System', 'Changes type depending on held item.'),
(201, 'Rock Head', 'Protects the Pokémon from recoil damage.'),
(202, 'Rocky Payload', 'Powers up Rock-type moves.'),
(203, 'Rough Skin', 'Inflicts damage to the attacker on contact.'),
(204, 'Run Away', 'Enables a sure getaway from wild Pokémon.'),
(205, 'Sand Force', 'Boosts certain moves\' power in a sandstorm.'),
(206, 'Sand Rush', 'Boosts the Pokémon\'s Speed in a sandstorm.'),
(207, 'Sand Spit', 'Creates a sandstorm when hit by an attack.'),
(208, 'Sand Stream', 'The Pokémon summons a sandstorm in battle.'),
(209, 'Sand Veil', 'Boosts the Pokémon\'s evasion in a sandstorm.'),
(210, 'Sap Sipper', 'Boosts Attack when hit by a Grass-type move.'),
(211, 'Schooling', 'Changes Wishiwashi to School Form.'),
(212, 'Scrappy', 'Enables moves to hit Ghost-type Pokémon.'),
(213, 'Screen Cleaner', 'Nullifies effects of Light Screen, Reflect, and Aurora Veil.'),
(214, 'Seed Sower', 'Turns the ground into Grassy Terrain when the Pokémon is hit by an attack.'),
(215, 'Serene Grace', 'Boosts the likelihood of added effects appearing.'),
(216, 'Shadow Shield', 'Reduces damage when HP is full.'),
(217, 'Shadow Tag', 'Prevents the foe from escaping.'),
(218, 'Sharpness', 'Boosts the power of slicing moves.'),
(219, 'Shed Skin', 'The Pokémon may heal its own status problems.'),
(220, 'Sheer Force', 'Removes added effects to increase move damage.'),
(221, 'Shell Armor', 'The Pokémon is protected against critical hits.'),
(222, 'Shield Dust', 'Blocks the added effects of attacks taken.'),
(223, 'Shields Down', 'Changes stats when HP drops below half.'),
(224, 'Simple', 'Doubles all stat changes.'),
(225, 'Skill Link', 'Increases the frequency of multi-strike moves.'),
(226, 'Slow Start', 'Temporarily halves Attack and Speed.'),
(227, 'Slush Rush', 'Boosts the Pokémon\'s Speed stat in a hailstorm.'),
(228, 'Sniper', 'Powers up moves if they become critical hits.'),
(229, 'Snow Cloak', 'Raises evasion in a hailstorm.'),
(230, 'Snow Warning', 'The Pokémon summons a hailstorm in battle.'),
(231, 'Solar Power', 'In sunshine, Sp. Atk is boosted but HP decreases.'),
(232, 'Solid Rock', 'Reduces damage from super-effective attacks.'),
(233, 'Soul-Heart', 'Raises Special Attack when another Pokémon faints.'),
(234, 'Soundproof', 'Gives immunity to sound-based moves.'),
(235, 'Speed Boost', 'Its Speed stat is gradually boosted.'),
(236, 'Stakeout', 'Deals double damage to Pokémon switching in.'),
(237, 'Stall', 'The Pokémon moves after all other Pokémon do.'),
(238, 'Stalwart', 'Ignores moves and abilities that draw in moves.'),
(239, 'Stamina', 'Raises Defense when attacked.'),
(240, 'Stance Change', 'Changes form depending on moves used.'),
(241, 'Static', 'Contact with the Pokémon may cause paralysis.'),
(242, 'Steadfast', 'Raises Speed each time the Pokémon flinches.'),
(243, 'Steam Engine', 'Drastically raises Speed when hit by a Fire- or Water-type move.'),
(244, 'Steelworker', 'Powers up Steel-type moves.'),
(245, 'Steely Spirit', 'Powers up ally Pokémon\'s Steel-type moves.'),
(246, 'Stench', 'The stench may cause the target to flinch.'),
(247, 'Sticky Hold', 'Protects the Pokémon from item theft.'),
(248, 'Storm Drain', 'Draws in all Water-type moves to up Sp. Attack.'),
(249, 'Strong Jaw', 'Boosts the power of biting moves.'),
(250, 'Sturdy', 'It cannot be knocked out with one hit.'),
(251, 'Suction Cups', 'Negates all moves that force switching out.'),
(252, 'Super Luck', 'Heightens the critical-hit ratios of moves.'),
(253, 'Supersweet Syrup', 'Lowers opponent\'s Evasiveness when entering battle.'),
(254, 'Supreme Overlord', 'Attack and Special Attack are boosted for each party Pokémon that has been defeated.'),
(255, 'Surge Surfer', 'Doubles Speed during Electric Terrain.'),
(256, 'Swarm', 'Powers up Bug-type moves in a pinch.'),
(257, 'Sweet Veil', 'Prevents the Pokémon and allies from falling asleep.'),
(258, 'Swift Swim', 'Boosts the Pokémon\'s Speed in rain.'),
(259, 'Sword of Ruin', 'Lowers Defense of all Pokémon except itself.'),
(260, 'Symbiosis', 'The Pokémon can pass an item to an ally.'),
(261, 'Synchronize', 'Passes a burn, poison, or paralysis to the foe.'),
(262, 'Tablets of Ruin', 'Lowers Attack of all Pokémon except itself.'),
(263, 'Tangled Feet', 'Raises evasion if the Pokémon is confused.'),
(264, 'Tangling Hair', 'Contact with the Pokémon lowers the attacker\'s Speed stat.'),
(265, 'Technician', 'Powers up the Pokémon\'s weaker moves.'),
(266, 'Telepathy', 'Anticipates an ally\'s attack and dodges it.'),
(267, 'Tera Shell', 'Moves are not very effective when HP is full.'),
(268, 'Tera Shift', 'Transforms into Terastal Form in battle.'),
(269, 'Teraform Zero', 'Eliminates all effects of weather and terrain.'),
(270, 'Teravolt', 'Moves can be used regardless of Abilities.'),
(271, 'Thermal Exchange', 'Raises Attack when hit by a Fire-type move. Cannot be burned.'),
(272, 'Thick Fat', 'Ups resistance to Fire- and Ice-type moves.'),
(273, 'Tinted Lens', 'Powers up “not very effective” moves.'),
(274, 'Torrent', 'Powers up Water-type moves in a pinch.'),
(275, 'Tough Claws', 'Boosts the power of contact moves.'),
(276, 'Toxic Boost', 'Powers up physical attacks when poisoned.'),
(277, 'Toxic Chain', 'May cause bad poisoning.'),
(278, 'Toxic Debris', 'Scatters poison spikes at the feet of the opposing team when the Pokémon takes damage from physical moves.'),
(279, 'Trace', 'The Pokémon copies a foe\'s Ability.'),
(280, 'Transistor', 'Powers up Electric-type moves.'),
(281, 'Triage', 'Gives priority to restorative moves.'),
(282, 'Truant', 'Pokémon can\'t attack on consecutive turns.'),
(283, 'Turboblaze', 'Moves can be used regardless of Abilities.'),
(284, 'Unaware', 'Ignores any stat changes in the Pokémon.'),
(285, 'Unburden', 'Raises Speed if a held item is used.'),
(286, 'Unnerve', 'Makes the foe nervous and unable to eat Berries.'),
(287, 'Unseen Fist', 'Contact moves can strike through Protect/Detect.'),
(288, 'Vessel of Ruin', 'Lowers Special Attack of all Pokémon except itself.'),
(289, 'Victory Star', 'Boosts the accuracy of its allies and itself.'),
(290, 'Vital Spirit', 'Prevents the Pokémon from falling asleep.'),
(291, 'Volt Absorb', 'Restores HP if hit by an Electric-type move.'),
(292, 'Wandering Spirit', 'Swaps abilities with opponents on contact.'),
(293, 'Water Absorb', 'Restores HP if hit by a Water-type move.'),
(294, 'Water Bubble', 'Halves damage from Fire-type moves, doubles power of Water-type moves used, and prevents burns.'),
(295, 'Water Compaction', 'Sharply raises Defense when hit by a Water-type move.'),
(296, 'Water Veil', 'Prevents the Pokémon from getting a burn.'),
(297, 'Weak Armor', 'Physical attacks lower Defense and raise Speed.'),
(298, 'Well-Baked Body', 'Immune to Fire-type moves, and Defense is sharply boosted.'),
(299, 'White Smoke', 'Prevents other Pokémon from lowering its stats.'),
(300, 'Wimp Out', 'Switches out when HP drops below half.'),
(301, 'Wind Power', 'Doubles power of the next Electric-type move used, when hit by a wind move.'),
(302, 'Wind Rider', 'Takes no damage from wind moves, and boosts Attack if hit by one.'),
(303, 'Wonder Guard', 'Only supereffective moves will hit.'),
(304, 'Wonder Skin', 'Makes status-changing moves more likely to miss.'),
(305, 'Zen Mode', 'Changes form when HP drops below half.'),
(306, 'Zero to Hero', 'Transforms into its Hero Form when switching out.');

-- --------------------------------------------------------

--
-- Table structure for table `Pokemon`
--

CREATE TABLE `Pokemon` (
  `pokemon_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `gender` enum('Male/Female','Male','Female','Genderless') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `Pokemon`
--

INSERT INTO `Pokemon` (`pokemon_id`, `name`, `gender`) VALUES
(1, 'Bulbasaur', 'Male/Female'),
(2, 'Ivysaur', 'Male/Female'),
(3, 'Venusaur', 'Male/Female'),
(4, 'Charmander', 'Male/Female'),
(5, 'Charmeleon', 'Male/Female'),
(6, 'Charizard', 'Male/Female'),
(7, 'Squirtle', 'Male/Female'),
(8, 'Wartortle', 'Male/Female'),
(9, 'Blastoise', 'Male/Female'),
(10, 'Caterpie', 'Male/Female'),
(11, 'Metapod', 'Male/Female'),
(12, 'Butterfree', 'Male/Female'),
(13, 'Weedle', 'Male/Female'),
(14, 'Kakuna', 'Male/Female'),
(15, 'Beedrill', 'Male/Female'),
(16, 'Pidgey', 'Male/Female'),
(17, 'Pidgeotto', 'Male/Female'),
(18, 'Pidgeot', 'Male/Female'),
(19, 'Rattata', 'Male/Female'),
(20, 'Raticate', 'Male/Female'),
(21, 'Spearow', 'Male/Female'),
(22, 'Fearow', 'Male/Female'),
(23, 'Ekans', 'Male/Female'),
(24, 'Arbok', 'Male/Female'),
(25, 'Pikachu', 'Male/Female'),
(26, 'Raichu', 'Male/Female'),
(27, 'Sandshrew', 'Male/Female'),
(28, 'Sandslash', 'Male/Female'),
(29, 'Nidoran♀', 'Female'),
(30, 'Nidorina', 'Female'),
(31, 'Nidoqueen', 'Female'),
(32, 'Nidoran♂', 'Male'),
(33, 'Nidorino', 'Male'),
(34, 'Nidoking', 'Male'),
(35, 'Clefairy', 'Male/Female'),
(36, 'Clefable', 'Male/Female'),
(37, 'Vulpix', 'Male/Female'),
(38, 'Ninetales', 'Male/Female'),
(39, 'Jigglypuff', 'Male/Female'),
(40, 'Wigglytuff', 'Male/Female'),
(41, 'Zubat', 'Male/Female'),
(42, 'Golbat', 'Male/Female'),
(43, 'Oddish', 'Male/Female'),
(44, 'Gloom', 'Male/Female'),
(45, 'Vileplume', 'Male/Female'),
(46, 'Paras', 'Male/Female'),
(47, 'Parasect', 'Male/Female'),
(48, 'Venonat', 'Male/Female'),
(49, 'Venomoth', 'Male/Female'),
(50, 'Diglett', 'Male/Female'),
(51, 'Dugtrio', 'Male/Female'),
(52, 'Meowth', 'Male/Female'),
(53, 'Persian', 'Male/Female'),
(54, 'Psyduck', 'Male/Female'),
(55, 'Golduck', 'Male/Female'),
(56, 'Mankey', 'Male/Female'),
(57, 'Primeape', 'Male/Female'),
(58, 'Growlithe', 'Male/Female'),
(59, 'Arcanine', 'Male/Female'),
(60, 'Poliwag', 'Male/Female'),
(61, 'Poliwhirl', 'Male/Female'),
(62, 'Poliwrath', 'Male/Female'),
(63, 'Abra', 'Male/Female'),
(64, 'Kadabra', 'Male/Female'),
(65, 'Alakazam', 'Male/Female'),
(66, 'Machop', 'Male/Female'),
(67, 'Machoke', 'Male'),
(68, 'Machamp', 'Male'),
(69, 'Bellsprout', 'Male/Female'),
(70, 'Weepinbell', 'Male/Female'),
(71, 'Victreebel', 'Male/Female'),
(72, 'Tentacool', 'Male/Female'),
(73, 'Tentacruel', 'Male/Female'),
(74, 'Geodude', 'Male/Female'),
(75, 'Graveler', 'Male/Female'),
(76, 'Golem', 'Male/Female'),
(77, 'Ponyta', 'Male/Female'),
(78, 'Rapidash', 'Male/Female'),
(79, 'Slowpoke', 'Male/Female'),
(80, 'Slowbro', 'Male/Female'),
(81, 'Magnemite', 'Genderless'),
(82, 'Magneton', 'Genderless'),
(83, 'Farfetch\'d', 'Male/Female'),
(84, 'Doduo', 'Male/Female'),
(85, 'Dodrio', 'Male/Female'),
(86, 'Seel', 'Male/Female'),
(87, 'Dewgong', 'Male/Female'),
(88, 'Grimer', 'Male/Female'),
(89, 'Muk', 'Male/Female'),
(90, 'Shellder', 'Male/Female'),
(91, 'Cloyster', 'Male/Female'),
(92, 'Gastly', 'Male/Female'),
(93, 'Haunter', 'Male/Female'),
(94, 'Gengar', 'Male/Female'),
(95, 'Onix', 'Male/Female'),
(96, 'Drowzee', 'Male/Female'),
(97, 'Hypno', 'Male/Female'),
(98, 'Krabby', 'Male/Female'),
(99, 'Kingler', 'Male/Female'),
(100, 'Voltorb', 'Genderless'),
(101, 'Electrode', 'Genderless'),
(102, 'Exeggcute', 'Male/Female'),
(103, 'Exeggutor', 'Male/Female'),
(104, 'Cubone', 'Male/Female'),
(105, 'Marowak', 'Male/Female'),
(106, 'Hitmonlee', 'Male'),
(107, 'Hitmonchan', 'Male'),
(108, 'Lickitung', 'Male/Female'),
(109, 'Koffing', 'Male/Female'),
(110, 'Weezing', 'Male/Female'),
(111, 'Rhyhorn', 'Male/Female'),
(112, 'Rhydon', 'Male/Female'),
(113, 'Chansey', 'Female'),
(114, 'Tangela', 'Male/Female'),
(115, 'Kangaskhan', 'Female'),
(116, 'Horsea', 'Male/Female'),
(117, 'Seadra', 'Male/Female'),
(118, 'Goldeen', 'Male/Female'),
(119, 'Seaking', 'Male/Female'),
(120, 'Staryu', 'Genderless'),
(121, 'Starmie', 'Genderless'),
(122, 'Mr. Mime', 'Male'),
(123, 'Scyther', 'Male/Female'),
(124, 'Jynx', 'Female'),
(125, 'Electabuzz', 'Male/Female'),
(126, 'Magmar', 'Male/Female'),
(127, 'Pinsir', 'Male/Female'),
(128, 'Tauros', 'Male'),
(129, 'Magikarp', 'Male/Female'),
(130, 'Gyarados', 'Male/Female'),
(131, 'Lapras', 'Male/Female'),
(132, 'Ditto', 'Genderless'),
(133, 'Eevee', 'Male/Female'),
(134, 'Vaporeon', 'Male/Female'),
(135, 'Jolteon', 'Male/Female'),
(136, 'Flareon', 'Male/Female'),
(137, 'Porygon', 'Genderless'),
(138, 'Omanyte', 'Male/Female'),
(139, 'Omastar', 'Male/Female'),
(140, 'Kabuto', 'Male/Female'),
(141, 'Kabutops', 'Male/Female'),
(142, 'Aerodactyl', 'Male/Female'),
(143, 'Snorlax', 'Male/Female'),
(144, 'Articuno', 'Genderless'),
(145, 'Zapdos', 'Genderless'),
(146, 'Moltres', 'Genderless'),
(147, 'Dratini', 'Male/Female'),
(148, 'Dragonair', 'Male/Female'),
(149, 'Dragonite', 'Male/Female'),
(150, 'Mewtwo', 'Genderless'),
(151, 'Mew', 'Genderless');

--
-- Triggers `Pokemon`
--
DELIMITER $$
CREATE TRIGGER `uppercase_pokemon_name` BEFORE UPDATE ON `Pokemon` FOR EACH ROW BEGIN
    SET NEW.name = CONCAT(UPPER(SUBSTRING(NEW.name, 1, 1)), LOWER(SUBSTRING(NEW.name, 2)));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `Pokemon_Ability`
--

CREATE TABLE `Pokemon_Ability` (
  `pokemon_id` int(11) NOT NULL,
  `ability_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `Pokemon_Ability`
--

INSERT INTO `Pokemon_Ability` (`pokemon_id`, `ability_id`) VALUES
(1, 27),
(1, 161),
(2, 27),
(2, 161),
(3, 27),
(3, 161),
(4, 23),
(4, 231),
(5, 23),
(5, 231),
(6, 23),
(6, 231),
(7, 192),
(7, 274),
(8, 192),
(8, 274),
(9, 192),
(9, 274),
(10, 204),
(10, 222),
(11, 219),
(12, 34),
(12, 273),
(13, 204),
(13, 222),
(14, 219),
(15, 228),
(15, 256),
(16, 22),
(16, 119),
(16, 263),
(17, 22),
(17, 119),
(17, 263),
(18, 22),
(18, 119),
(18, 263),
(19, 90),
(19, 100),
(19, 204),
(20, 90),
(20, 100),
(20, 204),
(21, 119),
(21, 228),
(22, 119),
(22, 228),
(23, 114),
(23, 219),
(23, 286),
(24, 114),
(24, 219),
(24, 286),
(25, 125),
(25, 241),
(26, 125),
(26, 241),
(27, 206),
(27, 209),
(28, 206),
(28, 209),
(29, 100),
(29, 171),
(29, 199),
(30, 100),
(30, 171),
(30, 199),
(31, 171),
(31, 199),
(31, 220),
(32, 100),
(32, 171),
(32, 199),
(33, 100),
(33, 171),
(33, 199),
(34, 171),
(34, 199),
(34, 220),
(35, 42),
(35, 75),
(35, 132),
(36, 42),
(36, 132),
(36, 284),
(37, 56),
(37, 69),
(38, 56),
(38, 69),
(39, 33),
(39, 42),
(39, 75),
(40, 33),
(40, 42),
(40, 76),
(41, 110),
(41, 112),
(42, 110),
(42, 112),
(43, 27),
(43, 204),
(44, 27),
(44, 246),
(45, 27),
(45, 60),
(46, 43),
(46, 57),
(46, 60),
(47, 43),
(47, 57),
(47, 60),
(48, 34),
(48, 204),
(48, 273),
(49, 222),
(49, 273),
(49, 304),
(50, 8),
(50, 205),
(50, 209),
(51, 8),
(51, 205),
(51, 209),
(52, 167),
(52, 265),
(52, 286),
(53, 126),
(53, 265),
(53, 286),
(54, 29),
(54, 43),
(54, 258),
(55, 29),
(55, 43),
(55, 258),
(56, 5),
(56, 49),
(56, 290),
(57, 5),
(57, 49),
(57, 290),
(58, 69),
(58, 114),
(58, 118),
(59, 69),
(59, 114),
(59, 118),
(60, 43),
(60, 258),
(60, 293),
(61, 43),
(61, 258),
(61, 293),
(62, 43),
(62, 258),
(62, 293),
(63, 112),
(63, 132),
(63, 261),
(64, 112),
(64, 132),
(64, 261),
(65, 112),
(65, 132),
(65, 261),
(66, 90),
(66, 155),
(66, 242),
(67, 90),
(67, 155),
(67, 242),
(68, 90),
(68, 155),
(68, 242),
(69, 27),
(69, 81),
(70, 27),
(70, 81),
(71, 27),
(71, 81),
(72, 28),
(72, 128),
(72, 192),
(73, 28),
(73, 128),
(73, 192),
(74, 201),
(74, 209),
(74, 250),
(75, 201),
(75, 209),
(75, 250),
(76, 201),
(76, 209),
(76, 250),
(77, 67),
(77, 69),
(77, 204),
(78, 67),
(78, 69),
(78, 204),
(79, 157),
(79, 162),
(79, 197),
(80, 157),
(80, 162),
(80, 197),
(81, 4),
(81, 135),
(81, 250),
(82, 4),
(82, 135),
(82, 250),
(83, 49),
(83, 112),
(83, 119),
(84, 58),
(84, 204),
(84, 263),
(85, 58),
(85, 204),
(85, 263),
(86, 101),
(86, 103),
(86, 272),
(87, 101),
(87, 103),
(87, 272),
(88, 173),
(88, 246),
(88, 247),
(89, 173),
(89, 246),
(89, 247),
(90, 160),
(90, 221),
(90, 225),
(91, 160),
(91, 221),
(91, 225),
(92, 122),
(93, 122),
(94, 41),
(95, 201),
(95, 250),
(95, 297),
(96, 74),
(96, 112),
(96, 113),
(97, 74),
(97, 112),
(97, 113),
(98, 102),
(98, 220),
(98, 221),
(99, 102),
(99, 220),
(99, 221),
(100, 2),
(100, 234),
(100, 241),
(101, 2),
(101, 234),
(101, 241),
(102, 27),
(102, 92),
(103, 27),
(103, 92),
(104, 17),
(104, 125),
(104, 201),
(105, 17),
(105, 125),
(105, 201),
(106, 126),
(106, 195),
(106, 285),
(107, 112),
(107, 117),
(107, 119),
(108, 29),
(108, 157),
(108, 162),
(109, 122),
(109, 154),
(109, 246),
(110, 122),
(110, 154),
(110, 246),
(111, 125),
(111, 195),
(111, 201),
(112, 125),
(112, 195),
(112, 201),
(113, 93),
(113, 152),
(113, 215),
(114, 27),
(114, 121),
(114, 197),
(115, 58),
(115, 112),
(115, 212),
(116, 43),
(116, 228),
(116, 258),
(117, 43),
(117, 171),
(117, 228),
(118, 125),
(118, 258),
(118, 296),
(119, 125),
(119, 258),
(119, 296),
(120, 4),
(120, 106),
(120, 152),
(121, 4),
(121, 106),
(121, 152),
(122, 66),
(122, 234),
(122, 265),
(123, 242),
(123, 256),
(123, 265),
(124, 57),
(124, 74),
(124, 157),
(125, 241),
(125, 290),
(126, 67),
(126, 290),
(127, 102),
(127, 144),
(127, 147),
(128, 5),
(128, 114),
(128, 220),
(129, 193),
(129, 258),
(130, 114),
(130, 147),
(131, 101),
(131, 221),
(131, 293),
(132, 109),
(132, 126),
(133, 7),
(133, 204),
(134, 101),
(134, 293),
(135, 191),
(135, 291),
(136, 69),
(136, 90),
(137, 4),
(137, 53),
(137, 279),
(138, 221),
(138, 258),
(138, 297),
(139, 221),
(139, 258),
(139, 297),
(140, 17),
(140, 258),
(140, 297),
(141, 17),
(141, 258),
(141, 297),
(142, 178),
(142, 201),
(142, 286),
(143, 81),
(143, 108),
(143, 272),
(144, 178),
(144, 229),
(145, 178),
(145, 241),
(146, 67),
(146, 178),
(147, 136),
(147, 219),
(148, 136),
(148, 219),
(149, 112),
(149, 148),
(150, 178),
(150, 286),
(151, 261);

-- --------------------------------------------------------

--
-- Table structure for table `Pokemon_Team`
--

CREATE TABLE `Pokemon_Team` (
  `pokemon_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `Pokemon_Team`
--

INSERT INTO `Pokemon_Team` (`pokemon_id`, `team_id`) VALUES
(1, 1),
(1, 4),
(2, 2),
(3, 3),
(4, 1),
(4, 4),
(5, 2),
(6, 3),
(7, 1),
(8, 2),
(9, 3),
(10, 4);

-- --------------------------------------------------------

--
-- Table structure for table `Pokemon_Type`
--

CREATE TABLE `Pokemon_Type` (
  `pokemon_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `Pokemon_Type`
--

INSERT INTO `Pokemon_Type` (`pokemon_id`, `type_id`) VALUES
(1, 5),
(1, 8),
(2, 5),
(2, 8),
(3, 5),
(3, 8),
(4, 2),
(5, 2),
(6, 2),
(6, 10),
(7, 3),
(8, 3),
(9, 3),
(10, 12),
(11, 12),
(12, 10),
(12, 12),
(13, 8),
(13, 12),
(14, 8),
(14, 12),
(15, 8),
(15, 12),
(16, 1),
(16, 10),
(17, 1),
(17, 10),
(18, 1),
(18, 10),
(19, 1),
(20, 1),
(21, 1),
(21, 10),
(22, 1),
(22, 10),
(23, 8),
(24, 8),
(25, 4),
(26, 4),
(27, 9),
(28, 9),
(29, 8),
(30, 8),
(31, 8),
(31, 9),
(32, 8),
(33, 8),
(34, 8),
(34, 9),
(35, 18),
(36, 18),
(37, 2),
(38, 2),
(39, 1),
(39, 18),
(40, 1),
(40, 18),
(41, 8),
(41, 10),
(42, 8),
(42, 10),
(43, 5),
(43, 8),
(44, 5),
(44, 8),
(45, 5),
(45, 8),
(46, 5),
(46, 12),
(47, 5),
(47, 12),
(48, 8),
(48, 12),
(49, 8),
(49, 12),
(50, 9),
(51, 9),
(52, 1),
(53, 1),
(54, 3),
(55, 3),
(56, 7),
(57, 7),
(58, 2),
(59, 2),
(60, 3),
(61, 3),
(62, 3),
(62, 7),
(63, 11),
(64, 11),
(65, 11),
(66, 7),
(67, 7),
(68, 7),
(69, 5),
(69, 8),
(70, 5),
(70, 8),
(71, 5),
(71, 8),
(72, 3),
(72, 8),
(73, 3),
(73, 8),
(74, 9),
(74, 13),
(75, 9),
(75, 13),
(76, 9),
(76, 13),
(77, 2),
(78, 2),
(79, 3),
(79, 11),
(80, 3),
(80, 11),
(81, 4),
(81, 17),
(82, 4),
(82, 17),
(83, 1),
(83, 10),
(84, 1),
(84, 10),
(85, 1),
(85, 10),
(86, 3),
(87, 3),
(87, 6),
(88, 8),
(89, 8),
(90, 3),
(91, 3),
(91, 6),
(92, 8),
(92, 14),
(93, 8),
(93, 14),
(94, 8),
(94, 14),
(95, 9),
(95, 13),
(96, 11),
(97, 11),
(98, 3),
(99, 3),
(100, 4),
(101, 4),
(102, 5),
(102, 11),
(103, 5),
(103, 11),
(104, 9),
(105, 9),
(106, 7),
(107, 7),
(108, 1),
(109, 8),
(110, 8),
(111, 9),
(111, 13),
(112, 9),
(112, 13),
(113, 1),
(114, 5),
(115, 1),
(116, 3),
(117, 3),
(118, 3),
(119, 3),
(120, 3),
(121, 3),
(121, 11),
(122, 11),
(122, 18),
(123, 10),
(123, 12),
(124, 6),
(124, 11),
(125, 4),
(126, 2),
(127, 12),
(128, 1),
(129, 3),
(130, 3),
(130, 10),
(131, 3),
(131, 6),
(132, 1),
(133, 1),
(134, 3),
(135, 4),
(136, 2),
(137, 1),
(138, 3),
(138, 13),
(139, 3),
(139, 13),
(140, 3),
(140, 13),
(141, 3),
(141, 13),
(142, 10),
(142, 13),
(143, 1),
(144, 6),
(144, 10),
(145, 4),
(145, 10),
(146, 2),
(146, 10),
(147, 15),
(148, 15),
(149, 10),
(149, 15),
(150, 11),
(151, 11);

-- --------------------------------------------------------

--
-- Table structure for table `Teams`
--

CREATE TABLE `Teams` (
  `team_id` int(11) NOT NULL,
  `team_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `Teams`
--

INSERT INTO `Teams` (`team_id`, `team_name`) VALUES
(1, 'New Team Name'),
(2, 'Flame Warriors'),
(3, 'Legendary Titans'),
(4, 'Thunderstrike Squad'),
(5, 'Shadow Seekers'),
(6, 'Steel Titans'),
(7, 'Jungle Guardians'),
(8, 'Electric Surge'),
(9, 'Crystal Defenders'),
(10, 'Dragon Slayers');

-- --------------------------------------------------------

--
-- Table structure for table `Type`
--

CREATE TABLE `Type` (
  `type_id` int(11) NOT NULL,
  `type_name` varchar(50) NOT NULL,
  `effectiveness` text DEFAULT NULL,
  `weaknesses` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `Type`
--

INSERT INTO `Type` (`type_id`, `type_name`, `effectiveness`, `weaknesses`) VALUES
(1, 'Normal', 'None', 'Fighting'),
(2, 'Fire', 'Strong against Grass, Bug, Ice, Steel', 'Weak against Water, Rock, Fire'),
(3, 'Water', 'Strong against Fire, Ground, Rock', 'Weak against Electric, Water, Grass'),
(4, 'Electric', 'Strong against Water, Flying', 'Weak against Ground'),
(5, 'Grass', 'Strong against Water, Ground, Rock', 'Weak against Fire, Flying, Bug, Ice, Poison'),
(6, 'Ice', 'Strong against Dragon, Flying, Grass, Ground', 'Weak against Fire, Fighting, Rock, Steel'),
(7, 'Fighting', 'Strong against Normal, Ice, Rock, Dark, Steel', 'Weak against Flying, Psychic, Fairy'),
(8, 'Poison', 'Strong against Fairy, Grass', 'Weak against Ground, Psychic'),
(9, 'Ground', 'Strong against Fire, Electric, Poison, Rock, Steel', 'Weak against Water, Ice, Grass'),
(10, 'Flying', 'Strong against Fighting, Bug, Grass', 'Weak against Electric, Ice, Rock'),
(11, 'Psychic', 'Strong against Fighting, Poison', 'Weak against Bug, Ghost, Dark'),
(12, 'Bug', 'Strong against Psychic, Dark, Grass', 'Weak against Fire, Flying, Rock'),
(13, 'Rock', 'Strong against Flying, Bug, Fire, Ice', 'Weak against Water, Grass, Fighting, Ground, Steel'),
(14, 'Ghost', 'Strong against Psychic, Ghost', 'Weak against Dark'),
(15, 'Dragon', 'Strong against Dragon', 'Weak against Ice, Dragon, Fairy'),
(16, 'Dark', 'Strong against Psychic, Ghost', 'Weak against Fighting, Bug, Fairy'),
(17, 'Steel', 'Strong against Ice, Rock, Fairy', 'Weak against Fire, Fighting, Ground'),
(18, 'Fairy', 'Strong against Fighting, Dragon, Dark', 'Weak against Steel, Poison');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Ability`
--
ALTER TABLE `Ability`
  ADD PRIMARY KEY (`ability_id`);

--
-- Indexes for table `Pokemon`
--
ALTER TABLE `Pokemon`
  ADD PRIMARY KEY (`pokemon_id`);

--
-- Indexes for table `Pokemon_Ability`
--
ALTER TABLE `Pokemon_Ability`
  ADD PRIMARY KEY (`pokemon_id`,`ability_id`),
  ADD KEY `ability_id` (`ability_id`),
  ADD KEY `pokemon_id` (`pokemon_id`);

--
-- Indexes for table `Pokemon_Team`
--
ALTER TABLE `Pokemon_Team`
  ADD PRIMARY KEY (`pokemon_id`,`team_id`),
  ADD KEY `team_id` (`team_id`),
  ADD KEY `pokemon_id` (`pokemon_id`);

--
-- Indexes for table `Pokemon_Type`
--
ALTER TABLE `Pokemon_Type`
  ADD PRIMARY KEY (`pokemon_id`,`type_id`),
  ADD KEY `type_id` (`type_id`),
  ADD KEY `pokemon_id` (`pokemon_id`);

--
-- Indexes for table `Teams`
--
ALTER TABLE `Teams`
  ADD PRIMARY KEY (`team_id`);

--
-- Indexes for table `Type`
--
ALTER TABLE `Type`
  ADD PRIMARY KEY (`type_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Ability`
--
ALTER TABLE `Ability`
  MODIFY `ability_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=307;

--
-- AUTO_INCREMENT for table `Pokemon`
--
ALTER TABLE `Pokemon`
  MODIFY `pokemon_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=152;

--
-- AUTO_INCREMENT for table `Teams`
--
ALTER TABLE `Teams`
  MODIFY `team_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `Type`
--
ALTER TABLE `Type`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Pokemon_Ability`
--
ALTER TABLE `Pokemon_Ability`
  ADD CONSTRAINT `Pokemon_Ability_ibfk_1` FOREIGN KEY (`pokemon_id`) REFERENCES `Pokemon` (`pokemon_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Pokemon_Ability_ibfk_2` FOREIGN KEY (`ability_id`) REFERENCES `Ability` (`ability_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Pokemon_Team`
--
ALTER TABLE `Pokemon_Team`
  ADD CONSTRAINT `Pokemon_Team_ibfk_1` FOREIGN KEY (`pokemon_id`) REFERENCES `Pokemon` (`pokemon_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Pokemon_Team_ibfk_2` FOREIGN KEY (`team_id`) REFERENCES `Teams` (`team_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Pokemon_Type`
--
ALTER TABLE `Pokemon_Type`
  ADD CONSTRAINT `Pokemon_Type_ibfk_1` FOREIGN KEY (`pokemon_id`) REFERENCES `Pokemon` (`pokemon_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Pokemon_Type_ibfk_2` FOREIGN KEY (`type_id`) REFERENCES `Type` (`type_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
